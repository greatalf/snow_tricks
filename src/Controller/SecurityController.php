<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/registration", name="security_registration")
     */
    public function registration(Request $request, Objectmanager $manager, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer)
    {
        $user = new User;
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {            
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $user->setSlug(lcfirst(str_replace('\'', '-', (str_replace(' ','-', $user->getUsername())))));

            $manager->persist($user);
            $manager->flush();

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
     * @Route("/admin/profil", name="security_profil")
     */
    public function profil(Request $request, Objectmanager $manager, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer)
    {
        $user = $this->getUser();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {       
            $user = $form->getData();

            $avatar = $user->getAvatar();

            $file = $user->getAvatar()->getFile();

            $name = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move('../public/avatars', $name);
            $avatar->setName($name);

            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $user->setSlug(lcfirst(str_replace('\'', '-', (str_replace(' ','-', $user->getUsername())))));

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre profil a bien été mis à jour'
            );
            return $this->redirectToRoute('admin');
        }

        return $this->render('security/profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/admin", name="security_admin")
     */
    public function admin()
    {
        return $this->render('security/admin.html.twig', [
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
