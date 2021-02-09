<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Image;
use App\Entity\Trick;
use App\Form\CommentsType;
use App\Form\TrickType;
use App\Repository\CommentsRepository;
use App\Repository\TrickRepository;
use App\Services\Cleaner;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class TrickController extends AbstractController
{
    public function __construct()
    {
        $this->clean = new Cleaner;
    }

    /**
     * @Route("/", name="trick_index", methods={"GET"})
     */
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('trick/index.html.twig', [
            'tricks' => $trickRepository->findAll(),
        ]);
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

            //TODO del v for embed
            foreach ($trick->getVideo() as $video) {
                // $url = " https://www.youtube.com/watch?v=VX96I7PO8YU ";
                // parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array );
                // echo $my_array['v'];

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
    public function show(Trick $trick, Request $request): Response
    {
        dump($trick);
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
        
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'formComments' => $form->createView(),

        ]);
    }

    /**
     * @Route("/{id}/edit", name="trick_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Trick $trick): Response
    {
        $user = $this->getUser();
        $userTrick = $trick->getUser();
        if ($user == $userTrick) {
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
                $entityManager->flush();

                return $this->redirectToRoute('trick_index');
            }
           
        }else{
        $comment = new Comments();
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);
        $user = $this->getUser();
            return $this->render('trick/show.html.twig', [
                'trick' => $trick,
                'formComments' => $form->createView(),
    
            ]); 
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="trick_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Trick $trick): Response
    {
        $user = $this->getUser();
        $userTrick = $trick->getUser();
        if ($user === $userTrick) {
            if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($trick);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('trick_index');
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

        return $this->redirectToRoute('trick_index');
    }
}
