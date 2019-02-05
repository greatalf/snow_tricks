<?php

namespace App\Controller;

use App\Repository\FigureRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;

class HomeController extends AbstractController
{ 
    /**
     *@Route("/", name="home")
     */
    public function home(FigureRepository $repo)
    {        
        $figures = $repo->findAll();

        // $encoders = 'json';
        // $sessionVars = $this->get('session')->all();
        // $serializer = new Serializer;
        // var_dump($serializer->deserialize($sessionVars, Session::class, $encoders));
        // die();

        return $this->render('tricks/home.html.twig', ['figures' => $figures]);
    }
}
