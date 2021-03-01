<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Image;
use App\Entity\Video;
use App\Entity\Trick;
use App\Form\CommentsType;
use App\Form\TrickType;
use App\Repository\CommentsRepository;
use App\Repository\TrickRepository;
use App\Services\Cleaner;
use App\Services\Pagination;
use App\Services\VideoAdmin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;



class TrickController extends AbstractController
{
    public function __construct()
    {
        $this->clean = new Cleaner;
        $this->adminVideo = new VideoAdmin;
        $this->pagination = new Pagination;
    }

    
    /**
     * @Route("/new", name="trick_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        $user = $this->getUser();

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
                $image = new Image();
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
    public function show(Trick $trick, Request $request, Comments $comment,CommentsRepository $commentsRepository): Response
    {
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
            'comments' => $commentsRepository->findBy(array(),array('id'=> 'ASC'),$limit=$length,$offset=null),
            'formComments' => $form->createView(),

        ]);
    }

    /**
     * @Route("/{slug}/edit", name="trick_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Trick $trick,CommentsRepository $commentsRepository): Response
    {
        $user = $this->getUser();
        
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

                    $image = new Image();
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
            'comments' => $commentsRepository->findBy(array(),array('id'=> 'ASC'),$limit=$length,$offset=null),
            'form' => $form->createView(),
        ]);
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
    public function deleteImage(Request $request, Image $image): Response
    {
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
     * @Route("imageShow/{id}/delete", name="image_delete_Show")
     * 
     */
    public function deleteImageShow(Request $request,Image $image,CommentsRepository $commentsRepository): Response
    {
       
        $user = $this->getUser();

        $image->getTrick();

        if ($user) {
            if ($this->isCsrfTokenValid('delete' . $image->getId(), $request->request->get('_token'))) {
                $nom = $image->getSource();
                unlink($this->getParameter('images_directory') . '/' . $nom);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($image);
                $entityManager->flush();
            }
        }
        
        $bdd = count($commentsRepository->findAll());
        $paging = $request->query->get('length');

        if($paging !==  null){
            $length = $this->pagination->commentsPagination($paging,$bdd);
        }else{
            $length = $this->pagination->commentsPagination(0,$bdd);
        }

        $comment = new Comments();
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        return $this->render('trick/show.html.twig', [
            // 'trick' => $trick,
            'comments' => $commentsRepository->findBy(array(),array('id'=> 'ASC'),$limit=$length,$offset=null),
            'formComments' => $form->createView(),

        ]);
    }

    
}
