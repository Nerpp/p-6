<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Video;
use App\Entity\Trick;
use App\Entity\Images;
use App\Form\CommentsType;
use App\Form\TrickType;
use App\Form\EditType;
use App\Repository\ImagesRepository;
use App\Repository\CommentsRepository;
use App\Repository\VideoRepository;
use App\Repository\TrickRepository;
use App\Services\Cleaner;
use App\Services\Pagination;
use App\Services\VideoAdmin;
use Proxies\__CG__\App\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;



class TrickController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->clean = new Cleaner;
        $this->adminVideo = new VideoAdmin;
        $this->pagination = new Pagination;
        $this->security = $security;
    }


     /**
      * @Route("/new", name="trick_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
       
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        $user = $this->security->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $files = $form->get('image')->getData();
            foreach ($files as $image) {

                $filename =  $this->clean->delAccent($trick->getName());
               
                $filename = $filename . "_" . md5(uniqid()) . "." . $image->guessExtension();
                
                if ($image) {
                    try {
                        $image->move(
                            $this->getParameter('images_directory'),
                            $filename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                }
                $image = new Images();
                
                $image->setSource($filename);
                
                $trick->addImage($image);

            }

            foreach ($trick->getVideo() as $video) {
                $video =  $this->adminVideo->addEmbed($video);
                $entityManager->persist($video);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $trick->setUser($user);
            $trick->setSlug($this->clean->delAccent($trick->getName()));
            $entityManager->persist($trick);
            $entityManager->flush();

            return $this->redirectToRoute('front_index');
        }

        return $this->render('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="trick_show", methods={"GET","POST"})
     */
    public function show(Trick $trick, Request $request,CommentsRepository $commentsRepository): Response
    {
        $comment = new Comments();
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($user)->setTrick($trick);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
        }

        $bdd = count($commentsRepository->findAll());
        $paging = $request->query->get('length');

        if($paging !==  null){
            $length = $this->pagination->commentsPagination($paging,$bdd);
        }else{
            $length = $this->pagination->commentsPagination(0,$bdd);
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'comments' => $commentsRepository->findBy(["trick" => $trick],['id'=> 'DESC'],$limit=$length,$offset=null),
            'formComments' => $form->createView(),

        ]);
    }

    /**
     * @Route("/{slug}/edit", name="trick_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Trick $trick,CommentsRepository $commentsRepository): Response
    {
        $user = $this->security->getUser();
   
        if ($user) {
            $form = $this->createForm(TrickType::class, $trick);
            $form->handleRequest($request);
           
            if ($form->isSubmitted() && $form->isValid()) {
               
                $entityManager = $this->getDoctrine()->getManager();
                $files = $form->get('image')->getData();
             
                foreach ($files as $image) {
                    
                    $filename =  $this->clean->delAccent($trick->getName());
                    $filename = $filename . "_" . md5(uniqid()) . "." . $image->guessExtension();
                    if ($image) {
                        try {
                            $image->move(
                                $this->getParameter('images_directory'),
                                $filename
                            );
                        } catch (FileException $e) {
                            // ... handle exception if something happens during file upload
                        }
                    }
                    $image = new Images();
                    $image->setSource($filename);
                    $trick->setSlug($this->clean->delAccent($trick->getName()));
                    $trick->addImage($image);
                }
                 // $getVideos = $form->get('video')->getData();
                 $getVideos = $trick->getVideo();
                foreach ($getVideos as $video) {
                   
                    $videoTreated = $this->adminVideo->addEmbed($video->getUrl());
                    $videos = new Video();
                    $videos->setUrl($videoTreated);
                    $trick->addVideo($video);
                }

                $entityManager->flush();

                return $this->redirectToRoute('front_index');
            }
           
        }else{
            return $this->redirectToRoute('front_index');
        }

        $bdd = count($commentsRepository->findAll());
        $length = $this->pagination->commentsPagination(0,$bdd);
        
        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'comments' => $commentsRepository->findBy([],array('id'=> 'ASC'),$limit=$length,$offset=null),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("image_principale/{id}", name="image_featured")
     */
    public function changeFeature(Request $request, int $id, ImagesRepository $imageRepository):Response
    {

        $img = $imageRepository
            ->find($id);

        $trick = $img->getTrick();

        foreach ($trick->getImages() as $image) {
            
            if ($image->getId() === $id) {
                $image->setFeatured(true);
            }else{
                $image->setFeatured(false);
            }
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return  $this->redirectToRoute('trick_edit', ['slug' => $trick->getSlug()]);
    }

    /**
     * @Route("/{slug}/delete", name="trick_delete")
     */
    public function delete(Request $request, Trick $trick): Response
    {
        $user = $this->getUser();
        if ($user) {
           
            // if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {
                
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($trick);
                $entityManager->flush();
            // }
        }
       
        return $this->redirectToRoute('front_index');
    }

     /**
     * @Route("image/{id}/delete", name="image_delete")
     */
    public function deleteImage(Request $request): Response
    {
       $image = new Image();
        $data = json_decode($request->getContent(), true);
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])) {
            $nom = $image->getSource();
            unlink($this->getParameter('images_directory') . '/' . $nom);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($image);
            $entityManager->flush();
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token invalid'], 400);
        }

        return $this->redirectToRoute('front_index');
    }

  

    /**
     * @Route("imageShow/{id}/delete", name="image_delete_show", methods={"GET","POST"})
     * 
     */
    public function deleteImageShow(Request $request,int $id): Response
    {
        $user = $this->security->getUser();

        if ($user) {
            $imageRepository = $this->getDoctrine()->getRepository(Images::class);
            $image = $imageRepository->findOneBy(['id' => $id]);
            
            $trick = $image->getTrick();

            // if ($this->isCsrfTokenValid('delete' . $image->getId(), $request->request->get('_token'))) {

                $nom = $image->getSource();
                unlink($this->getParameter('images_directory') . '/' . $nom);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($image);
                $entityManager->flush();

              return  $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
            // }   
        }
        
       return $this->redirectToRoute('front_index');

    }

        /**
     * @Route("image/{id}/edit", name="image_edit")
     */
    public function editImage(Request $request, int $id, ImagesRepository $imageRepository, TrickRepository $trickRepository)
    {

        $user = $this->security->getUser();

      
        if ($user) {

            $img = $imageRepository
            ->find($id);
          
            $trick = $img->getTrick();

            $data = $request->query->all("filter_form");
            $data["categories"] = $trick->getName();
            $form = $this->createForm(EditType::class, $data);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
               
                $requested =  $request->request->get('edit');

                $newTrick = $trickRepository
                        ->find($requested['name']);
  
               $image = $img->setTrick($newTrick);
               
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($image);
                $entityManager->flush();

                return  $this->redirectToRoute('trick_show', ['slug' => $newTrick->getSlug()]);
                
            }

            return $this->render('trick/edit-image.html.twig', [
                'image' => $img,
                'form' => $form->createView(),
            ]);
 
        }
        return $this->redirectToRoute('front_index');

    }

    /**
     * @Route("videos/{id}/delete", name="video_delete_show")
     */
    public function deleteVideoShow(Request $request,int $id,VideoRepository $videoRepository):Response
    {
        $user = $this->security->getUser();

        if ($user) {

            dd($id);
            
        }
        return $this->redirectToRoute('front_index');
    }


    
}
