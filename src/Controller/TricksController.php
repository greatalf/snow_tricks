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
    public function form(Figure $figure = null, Request $request, ObjectManager $manager)
    {        
        $figure = new Figure();        
        
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            foreach($figure->getVisuals() as $visual)
            {
                $this->videoUrlConvertissor($visual);

                $visual->setFigure($figure);
                $manager->persist($visual);
            }

            if(!$this->isHeadVisualValid($figure))
            {
            $this->addFlash(
                'danger',
                "L'URL de l'image d'affiche n'est pas une image valide(jpeg, jpg, png)"
            );
            return $this->redirectToRoute('tricks_new', [
                'slug' => $figure->getSlug()
                ]);
            }            
            $figure->setHeadVisual($figure->getHeadVisual());

            $dateCreate = (new \Datetime());
            $slug  = str_replace(' ', '-', (str_replace(' \'','-',$figure->getTitle())));

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

    /**
    * @Route("/tricks/{slug}/edit", name="tricks_edit")
    */
    public function edit(Figure $figure, Request $request, ObjectManager $manager)
    {
        // $visualKind->array_flip(Visual::VISUALKIND); 

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
            $slug = str_replace('\'', '-', (str_replace(' ','-',$figure->getTitle())));

            $figure->setSlug($slug);
            $figure->setModifiedAt($dateModified);

            $manager->persist($figure);
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
    }

    private function convertVideoUrl(Visual $visual, $regexPattern, $youtubeORdailymotion)
    {
        //comparaison de l'url fournie à une url vidéo
        preg_match('#(' . $regexPattern . ')([a-z0-9_]+)#mi', $visual->getUrl(), $matches);

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
            // dump($visual->getVisualKind());
            // dump($visual->setUrl($embedUrl));
            // die;
            return $visual->setUrl($embedUrl);
        }
        else
        {
            return false;
        }
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

    /**
     * @Route("/tricks/{slug}", name="tricks_show")
     */
    public function show(Figure $figure, Comment $comment = null)
    {     
        return $this->render('tricks/show.html.twig', ['figure' => $figure, 'comments' => $comment]);
    }

    // créer un private type dans entity visual, ce type peut être null, 
    // dans le controller, si match alors $visual->setType = 'vidéo', sinon $visual->setType = 'photo'
    // dans show.html.twig, boucler sur les visuals, si {{ visual.type }} est une vidéo, afficher {{ visual.url }} 
    // tel quel, sinon afficher {{ visual.url }} dans une balise <img>.
}
