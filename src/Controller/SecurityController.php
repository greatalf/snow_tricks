<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, ObjectManager $manager)
    {
		$user = new User();

		$form = $this->createForm(RegistrationType::class, $user);   

		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid())
		{
			$manager->persist($user);
			$manager->flush();
		}

		return $this->render('security/registration.html.twig', ['formUser' => $form->createView()]) ;    
    }
}
