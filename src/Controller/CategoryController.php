<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use App\Form\CategoryType;
use App\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class CategoryController extends AbstractController
{
    /**
     * @Route("admin/categories", name="category")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(Request $request, Objectmanager $manager)
    {
        $category = new Category;
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {            

            $manager->persist($category);
            $manager->flush();

            $this->addFlash(
                'success',
                'La catégorie ' .  $category->getName() . ' a bien été créée'
            );
            return $this->redirectToRoute('tricks_create');
        }

        return $this->render('category/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
