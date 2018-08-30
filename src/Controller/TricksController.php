<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\FigureRepository;
use App\Entity\Figure;
use App\Entity\Category;
use App\Form\FigureType;

class TricksController extends AbstractController
{ 
    /**
     *@Route("/", name="home")
     */
    public function home(FigureRepository $repo)
    {        
        $figures = $repo->findAll();

        return $this->render('tricks/home.html.twig', ['figures' => $figures]);
    }

    /**
    * @Route("/tricks/new", name="tricks_create")
    * @Route("/tricks/{id}/edit", name="tricks_edit")
    */
    public function form(Figure $figure = null, Request $request, ObjectManager $manager)
    {
        if(!$figure)
        {
            $figure = new Figure();            
        }

        $form = $this->createForm(FigureType::class, $figure);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if(!$figure->getId())
            {
                $figure->setCreatedAt(new \DateTime());
                
            }
            $manager->persist($figure);
                $manager->flush();

                return $this->redirectToRoute('tricks_show', ['id' => $figure->getId()]);
        }

        return $this->render('tricks/create.html.twig', [
            'formFigure' => $form->createView(),
            'editMode' => $figure->getId() !== null]);
    }

    /**
     * @Route("/tricks/{id}", name="tricks_show")
     */
    public function show(Figure $figure)
    {       
        return $this->render('tricks/show.html.twig', ['figure' => $figure]);
    }
}
