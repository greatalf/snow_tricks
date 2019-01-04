<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Avatar;
use App\Form\EditProfileType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use App\ToolDevice\Slugification;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/registration", name="security_registration")
     */
    public function registration(Request $request, Objectmanager $manager, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer, TokenGeneratorInterface $generator)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {         
            $user = $form->getData();

            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $slugificator = new Slugification();
            $user->setSlug($slugificator->slugify($user->getUsername()));

            $token = $generator->generateToken();
            $user->setToken($token);
            $user->setConfirmed('0');

            $manager->persist($user);
            $manager->flush();
            
            $message = (new \Swift_Message('Hello Email'))
            ->setFrom('dev.adm974@gmail.com')
            ->setTo($user->getEmail())
            ->setBody('Bravo ' . $user->getUsername() . ', votre inscription a été prise en compte.
                        Cliquez sur le lien suivant pour activer votre inscription : 
                        <a href="http://localhost:8000/confirm?user=' . $user->getId() . '&token=' . $token . '">LIEN</a>', 'text/html');
                
                $mailer->send($message);

                $this->addFlash(
                'success',
                'Un email de confirmation vous a été envoyé à l\'adresse ' .  $user->getEmail()
            );
            return $this->redirectToRoute('home');
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/profil/edit", name="security_edit_profil")
     * @IsGranted("ROLE_USER")
     * 
     */
    public function profil(Request $request, Objectmanager $manager, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer)
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $user);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {       
            $path = $this->getParameter('uploads_directory');
            $user = $form->getData();
            
            $avatar = $user->getAvatar();
            $file = $user->getAvatar()->getFile();
            
            if($file !== NULL)
            {                
                $manager->remove($avatar);
                $manager->flush();
                $avatar = new Avatar;

                $name = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($path, $name);
                $avatar->setName($name)
                        ->setUser($user);

                $user->setAvatar($avatar);
                $manager->persist($avatar);
            }

            $manager->persist($user);
            $manager->flush();
            
            $this->addFlash(
                'success',
                'Votre profil a bien été mis à jour'
            );
            
            return $this->redirectToRoute('security_admin');
        }

        return $this->render('security/editProfile.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("admin/account/password-update", name="security_update_password")
     * @IsGranted("ROLE_USER")
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder, Objectmanager $manager)
    {
        $passwordUpdate = new PasswordUpdate();
        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if(!password_verify($passwordUpdate->getOldPass(), $user->getPassword()))
            {
                $form->get('oldPass')->addError(new FormError("Le mot de passe actuel est invalide"));
            }
            else
            {
                $newPassword = $passwordUpdate->getNewPass();
                $hash = $encoder->encodePassword($user, $newPassword);
                $user->setPassword($hash);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Votre mot de passe a été modifié avec succès'
                    );
                return $this->redirectToRoute('security_admin');
            }
        }

        return $this->render('security/password.html.twig', [
            'form' => $form->createView()
            ]);
    }

    /**
     * @Route("/confirm", name="security_confirm_user")
     */
    public function confirm(Request $request)
    {
        $token = $request->get('token');

        if (!$token) {
            return new Response(new InvalidCsrfTokenException());
        }

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => $request->get('user')]);

        if (!$user)
        {
            throw $this->createNotFoundException();
        }

        if ($user->getToken() === $token)
        {
            $user->setConfirmed('1');
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Votre compte a bien été activé');
        }
        return $this->redirectToRoute('security_connexion');
    }

    /**
     * @Route("/admin", name="security_admin")
     * @IsGranted("ROLE_USER")
     */
    public function admin()
    {
        return $this->render('security/user.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/connexion", name="security_connexion")
     */ 
    public function connexion(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('security/connexion.html.twig', [
            'hasError' => $error !== null,
            'username' => $username,
        ]);
    }

    /**
     * @Route("/deconnexion", name="security_deconnexion")
     */ 
    public function deconnexion()
    {
        $this->addFlash(
                'success',
                'Vous avez bien été déconnecté'
        );
    }


}
