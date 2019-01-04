<?php

namespace App\Controller;

use App\Repository\FigureRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{ 
    /**
     *@Route("/", name="home")
     */
    public function home(FigureRepository $repo)
    {        
        $figures = $repo->findAll();

        return $this->render('tricks/home.html.twig', ['figures' => $figures]);
    }
}
