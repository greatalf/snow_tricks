<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Figure;
use App\Entity\Visual;
use App\Entity\Comment;
use App\Entity\Category;
use App\Form\FigureType;
use App\Form\VisualType;
use App\Form\CommentType;
use App\Form\EditVisualType;
use App\Form\EditHeadVisualType;
use App\ToolDevice\Slugification;
use App\Repository\FigureRepository;
use App\Repository\CommentRepository;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TricksController extends AbstractController
{ 
    /**
    * @Route("/tricks/new", name="tricks_create")
    * @IsGranted("ROLE_USER")
    */
    public function create(Request $request, ObjectManager $manager)
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

                if((!$visual->isImage() && (!$visual->isVideo())))
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

            if(!$figure->isHeadVisualValid($figure))
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
            $figure->setAuthor($this->getUser());


            $dateCreate = (new \Datetime());
            $slugification = new Slugification;
            $slug = $slugification->slugify($figure->getTitle());

            $figure->setSlug($slug);
            $figure->setContent(nl2br($figure->getContent()));
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
        * @IsGranted("ROLE_USER")
        */
        public function edit(Figure $figure = NULL, Request $request, FigureRepository $repoFig)
        {
            // $figure = new Figure;
            $form = $this->createForm(FigureType::class, $figure);
            $form->handleRequest($request);
            
            // dd($figure);
            if($form->isSubmitted() && $form->isValid())
            { 
                $manager = $this->getDoctrine()->getManager();

                $figure = $repoFig->findOneBy(['slug' => $figure->getSlug()]);
                
                // $form->get('title')->addError(new FormError("Le titre ne peut pas être vide"));
                
                foreach($figure->getVisuals() as $visual)
                {                
                    $this->videoUrlConvertissor($visual);
                    
                    if((!$visual->isImage() && (!$visual->isVideo())))
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

                if(!$figure->isHeadVisualValid($figure))
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
                $figure->setContent(nl2br($figure->getContent()));
                $slugification = new Slugification;
                $slug = $slugification->slugify($figure->getTitle());
                
                $figure->setSlug($slug);
                $figure->setModifiedAt($dateModified);

                // Pour surmonter le UniqueEntity du titre
                $manager->merge($figure);
                
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

        /**
         * @Route("/tricks/{slug}/delete", name="tricks_delete")
         * @IsGranted("ROLE_USER")
         */    
        public function delete(Figure $figure, ObjectManager $manager)
        {     
            $manager->remove($figure);
            $manager->flush();

            $this->addFlash(
                        'success',
                        'La figure a bien été supprimée'
                        );

            return $this->redirectToRoute('home', ['figure' => $figure]);
        }

        /**
         * @Route("/tricks/{slug}", name="tricks_show")
         */
        public function show(Figure $figure = null, Comment $comment = null, ObjectManager $manager, Request $request, CommentRepository $repoCom, FigureRepository $repoFig)
        {     
            $comment = new Comment();
            $form = $this->createForm(CommentType::class, $comment);
            
            $figure = $repoFig->findOneBy(['slug' => $figure->getSlug()]);
            $form->handleRequest($request);
            
            if($form->isSubmitted() && $form->isValid())
            {            
                if($this->getUser() == null)
                {
                    return $this->redirectToRoute('security_connexion');
                }

                $comment->setCreatedAt(new \Datetime())
                        ->setContent($comment->getContent())
                        ->setFigure($figure)
                        ->setAuthor($this->getUser());

                $manager->persist($comment);
                $manager->flush();
                
                return $this->redirectToRoute('tricks_show', ['slug' => $figure->getSlug()]);
            }
            $limit = $this->getParameter('comment_per_page');
            $comments = ($figure != null) ? $repoCom->findBy(['figure' => $figure->getId()], ['createdAt' => 'DESC'], count($figure->getComments())) : '';

            return $this->render('tricks/show.html.twig', [
                'figure' => $figure,
                'comments' => $comments,
                'limitPerPage' => $this->getParameter('comment_per_page'),
                'CommentForm' => $form->createView()
            ]);
        }
        
        /**
        * @Route("/tricks/{slug}/editHeadVisual", name="head_visual_edit")
        * @IsGranted("ROLE_USER")
        */
        public function editHeadVisual(Figure $figure, Request $request, ObjectManager $manager)
        {
            $formEditHeadVisual = $this->createForm(EditHeadVisualType::class, $figure);
            $formEditHeadVisual->handleRequest($request);

            if($formEditHeadVisual->isSubmitted())
            {                
                if(!$figure->isHeadVisualValid($figure))
                {
                    $this->addFlash(
                        'danger',
                    "Cette URL ne présente pas une image valide(jpeg, jpg, png, aspx)"
                    );
                    return $this->render('security/editHeadVisual.html.twig', [
                    'formEditHeadVisual' => $formEditHeadVisual->createView(),
                    'figure'             => $figure
                    ]);
                }   

                $figure->setHeadVisual($figure->getHeadVisual());
               
                $dateModified = (new \Datetime());
                $figure->setModifiedAt($dateModified);

                $manager->flush();

                $this->addFlash(
                    'success',
                    'L\'image à la une de la figure ' . $figure->getTitle() . ' a bien été modifiée!'
                );
        
                return $this->redirectToRoute('tricks_show', [
                    'slug' => $figure->getSlug()
                    ]);
                }
            return $this->render('security/editHeadVisual.html.twig', [
            'formEditHeadVisual'   => $formEditHeadVisual->createView(),
            'figure' => $figure
                ]);
        }


        /**
         * @Route("/tricks/{slug}/deleteHeadVisual", name="head_visual_delete")
         * @IsGranted("ROLE_USER")
         */    
        public function deleteHeadVisual(Figure $figure, ObjectManager $manager)
        {     
            $figure->setHeadVisual('https://printablefreecoloring.com/image/transportation/drawing-snowboard-2.png');

            $dateModified = (new \Datetime());
            $figure->setModifiedAt($dateModified);

            $manager->flush();

            $this->addFlash(
                        'success',
                        'L\'image à la une a bien été supprimée et remplacée par une image par défaut'
                    );

            return $this->redirectToRoute('tricks_show', [
                    'slug' => $figure->getSlug()
                    ]);
        }

        /**
        * @Route("/tricks/{slug}/editVisual/{id}", name="visual_edit")
        * @IsGranted("ROLE_USER")
        */
        public function editVisual(Visual $visual = null, FigureRepository $repo, Request $request, ObjectManager $manager)
        {
            $formEditVisual = $this->createForm(VisualType::class, $visual);
            $formEditVisual->handleRequest($request);

            $figure = $repo->findOneById($visual->getFigure());
            
            if($formEditVisual->isSubmitted() && $formEditVisual->isValid())
            {
                $this->videoUrlConvertissor($visual);
                
                if((!$visual->isImage() && (!$visual->isVideo())))
                {
                    $this->addFlash(
                    'danger',
                    'L\'URL ne correspond ni à une image(jpeg, jpg, png, aspx), ni à une vidéo Youtube ou Dailymotion'
                        );

                    return $this->redirectToRoute('visual_edit', [
                        'id' => $figure->getId()
                        ]);
                }

                $visual->setFigure($figure);

                $dateModified = (new \Datetime());
                $figure->setModifiedAt($dateModified);

                $manager->flush();

                $this->addFlash(
                    'success',
                    'Le média de la figure ' . $figure->getTitle() . ' a bien été modifiée!'
                    );
        
                return $this->redirectToRoute('tricks_show', [
                    'slug' => $figure->getSlug()
                    ]);
            }

            return $this->render('security/editVisual.html.twig', [
                'formEditVisual'   => $formEditVisual->createView(),
                'visual' => $visual,
                'figure' => $figure
                ]);
        }

        /**
        * @Route("/tricks/{slug}/deleteVisual/{id}", name="visual_delete")
        * @IsGranted("ROLE_USER")
        */
        public function deleteVisual(Visual $visual = null, FigureRepository $repo, ObjectManager $manager)
        {            
            if($visual == NULL)
            {
                $this->addFlash(
                    'success',
                    'Le média n\'existe pas'
                );                
                return $this->render('security/404.html.twig');
            }
            $figure = $repo->findOneById($visual->getFigure());
            if($figure == NULL)
            {
                $this->addFlash(
                    'success',
                    'Le média n\'existe pas'
                );                
                return $this->render('404.html.twig');
            }               
                
            $manager->remove($visual);

            $dateModified = (new \Datetime());
            $figure->setModifiedAt($dateModified);

            $manager->flush();

            $this->addFlash(
                        'success',
                        'Le média de la figure a bien été supprimé'
                    );

            return $this->redirectToRoute('tricks_show', [
                    'slug' => $figure->getSlug()
                    ]);
        }

    public function convertVideoUrl(Visual $visual, $regexPattern, $youtubeORdailymotion)
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
}
