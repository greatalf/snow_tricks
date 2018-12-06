<?php

namespace App\Controller;

use App\Entity\Visual;
use App\Entity\Figure;
use App\Entity\Comment;
use App\Form\VisualType;
use App\Entity\Category;
use App\Form\FigureType;
use App\Form\UploadType;
use App\Repository\FigureRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    */
    public function create(Figure $figure = null, Request $request, ObjectManager $manager)
    {        
        $figure = new Figure();        
        
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            foreach($figure->getVisuals() as $visual)
            {
                //traiter et efface tout ce qu'il y a après l'extension d'une image
                //ex : https://twistedsifter.files.wordpress.com/2011/01/perfect-tail-grab.jpg?w=799&h=533
                //https://cdn.shopify.com/s/files/1/0230/2239/files/Canadian_Bacon_Tim_Eddy_side_Fenelon_large.jpg?1819627745986436554
                $this->videoUrlConvertissor($visual);

                if((!$this->isImage($visual) && (!$this->isVideo($visual))))
                {
                    $this->addFlash(
                    'danger',
                    'Une des URLs remplies n\'est ni une image(jpeg, jpg, png, aspx), ni une vidéo Youtube ou Dailymotion'
                    );
                    return $this->redirectToRoute('tricks_create', [
                        'slug' => $figure->getSlug()
                        ]);
                }

                $visual->setFigure($figure);
                $manager->persist($visual);
            }

            if(!$this->isHeadVisualValid($figure))
            {
            $this->addFlash(
                'danger',
                "L'URL de l'image d'affiche n'est pas une image valide(jpeg, jpg, png)"
            );
            return $this->redirectToRoute('tricks_create', [
                'slug' => $figure->getSlug()
                ]);
            }            
            $figure->setHeadVisual($figure->getHeadVisual());

            $dateCreate = (new \Datetime());
            $slug = $this->slugify($figure->getTitle());

            $figure->setSlug($slug);
            $figure->setCreatedAt($dateCreate);

            $manager->persist($figure);
            $manager->flush();
            
            $this->addFlash(
                'success',
                'La figure ' . $figure->getTitle() . ' a bien été ajoutée!'
            );
            
            return $this->redirectToRoute('tricks_show', [
                'slug' => $figure->getSlug()
            ]);
        }
        return $this->render('tricks/create.html.twig', [
            'form' => $form->createView()
            ]);
        }
        
        
        
        
        
        
        //créer la route pour aller au form qui update qu'un seul visual prérempli par son id envoyé à la vue
        
        
        

        

        /**
    * @Route("/tricks/{slug}/edit", name="tricks_edit")
    */
    public function edit(Figure $figure, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            foreach($figure->getVisuals() as $visual)
            {                
                $this->videoUrlConvertissor($visual);
                
                if((!$this->isImage($visual) && (!$this->isVideo($visual))))
                {
                    $this->addFlash(
                    'danger',
                    'Une des URLs remplies n\'est ni une image(jpeg, jpg, png, aspx), ni une vidéo Youtube ou Dailymotion'
                );
                    return $this->redirectToRoute('tricks_edit', [
                        'slug' => $figure->getSlug()
                        ]);
                    }
                $visual->setFigure($figure);
                $manager->persist($visual);
                
            }

            if(!$this->isHeadVisualValid($figure))
            {
            $this->addFlash(
                'danger',
                "L'URL de l'image d'affiche n'est pas une image valide(jpeg, jpg, png, aspx)"
            );
            return $this->redirectToRoute('tricks_edit', [
                'slug' => $figure->getSlug()
                ]);
            }   
            $figure->setHeadVisual($figure->getHeadVisual());
            
            $dateModified = (new \Datetime());
            $slug = $this->slugify($figure->getTitle());
            
            $figure->setSlug($slug);
            $figure->setModifiedAt($dateModified);
            
            // $manager->persist($figure);
            // $manager->merge($figure);

            $manager->flush();
            
            $this->addFlash(
                'success',
                'La figure ' . $figure->getTitle() . ' a bien été modifiée!'
            );

            return $this->redirectToRoute('tricks_show', [
                'slug' => $figure->getSlug()
            ]);
        }
        
        return $this->render('tricks/edit.html.twig', [
            'form'   => $form->createView(),
            'figure' => $figure
            ]);
    }

    //créer la route pour aller au form qui supprime qu'un seul visual séléctionné par son id 
    
    /**
     * @Route("/tricks/{slug}/delete", name="tricks_delete")
     */    
    public function delete(Figure $figure, ObjectManager $manager)
    {     
        $manager->remove($figure);
        $manager->flush();
        // return new Response('Suppression');

        $this->addFlash(
                    'success',
                    'La figure a bien été supprimée'
                    );

        return $this->redirectToRoute('home', ['figure' => $figure]);
    }

    /**
     * @Route("/tricks/{slug}", name="tricks_show")
     */
    public function show(Figure $figure, Comment $comment = null)
    {     
            return $this->render('tricks/show.html.twig', ['figure' => $figure, 'comments' => $comment]);
    }







    private function isImage(Visual $visual)
    {
        $extTable = ['.jpg', '.jpeg', '.png', 'aspx'];
        $extensionJpgPng = (substr($visual->getUrl(), strlen($visual->getUrl())-4));
        $extensionJpeg = (substr($visual->getUrl(), strlen($visual->getUrl())-5));
        
        if(in_array($extensionJpgPng, $extTable) || in_array($extensionJpeg, $extTable))
        {
            $visual->setVisualKind('0');
            return true;
        }
        return false;
    }

    private function convertVideoUrl(Visual $visual, $regexPattern, $youtubeORdailymotion)
    {
        //comparaison de l'url fournie à une url vidéo
        preg_match('#(' . $regexPattern . ')([a-z0-9_-]+)#mi', $visual->getUrl(), $matches);

        //si c'est une vidéo
        if($matches !== [])
        {
            //transforme le type du visual en vidéo "1"
            $visual->setVisualKind('1');

            //récupère l'identifiant de la vidéo dans l'url fournie
            $videoID = $matches[2];
            
            //CONVERSION
            if($youtubeORdailymotion === 'dailymotion' || $regexPattern === "https:\/\/www\.dailymotion\.com\/embed\/video\/")
            {
                $embedUrl = 'https://www.dailymotion.com/embed/video/' . $videoID;
            }
            elseif($youtubeORdailymotion === 'youtube' || $regexPattern === "https:\/\/www\.youtube\.com\/embed\/")
            {
                $embedUrl = 'https://www.youtube.com/embed/' . $videoID;
            }
            return $visual->setUrl($embedUrl);
        }

        return false;
    }

    private function videoUrlConvertissor(Visual $visual)
    {
        $this->convertVideoUrl($visual, 'https:\/\/www\.youtube\.com\/embed\/', 'youtube');
        $this->convertVideoUrl($visual, 'https:\/\/www\.youtube\.com\/watch\?v=', 'youtube');
        $this->convertVideoUrl($visual, 'https:\/\/www\.youtube\.com\/watch\?reload=[0-9]+&v=', 'youtube');
        $this->convertVideoUrl($visual, 'https:\/\/youtu\.be\/', 'youtube');

        $this->convertVideoUrl($visual, 'https:\/\/www\.dailymotion\.com\/embed\/video\/',  'dailymotion');
        $this->convertVideoUrl($visual, 'https:\/\/www\.dailymotion\.com\/video\/', 'dailymotion');
        $this->convertVideoUrl($visual, 'https:\/\/dai\.ly\/', 'dailymotion');
    }

    private function isVideo(Visual $visual)
    {
        if($visual->getVisualKind() == '1')
        {
            return true;
        }
    }

    private function isHeadVisualValid(Figure $figure)
    {
        $extTable = ['jpg', 'jpeg', 'png', 'aspx'];
        $typeInfo = new \SplFileInfo($figure->getHeadVisual());
        
        if(in_array($typeInfo->getExtension(), $extTable) || in_array($typeInfo->getExtension(), $extTable))
        {
            return true;
        }
    }

    public function slugify($string)
    {
        return lcfirst(str_replace('\'', '-', (str_replace(' ','-',$string))));
    }
    
}
