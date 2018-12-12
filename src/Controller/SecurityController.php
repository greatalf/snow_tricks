<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecurityController extends AbstractController
{
    /**
     * @Route("/registration", name="security_registration")
     */
    public function registration(Request $request, Objectmanager $manager, UserPasswordEncoderInterface $encoder)
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
            return $this->redirectToRoute('security_connexion');
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/connexion", name="security_connexion")
     */ 
    public function connexion()
    {
        return $this->render('security/connexion.html.twig');
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
