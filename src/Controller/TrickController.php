<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Videos;
use App\Entity\Trick;
use App\Entity\Images;
use App\Form\CommentsType;
use App\Form\TrickType;
use App\Form\EditType;
use App\Repository\ImagesRepository;
use App\Repository\CommentsRepository;
use App\Repository\VideosRepository;
use App\Repository\TrickRepository;
use App\Services\Cleaner;
use App\Services\Pagination;
use App\Services\VideoAdmin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
    public function new(Request $request, TrickRepository $trickRepository): Response
    {
        $user = $this->security->getUser();

        if (!$user) {
            $this->addFlash('failed', 'You must be connected for create a trick !');
            return $this->redirectToRoute('app_login');
        }
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        $user = $this->security->getUser();
        if ($form->isSubmitted() && $form->isValid()) {

            $check = $trickRepository
                ->findOneBy([
                    'name' => $trick->getName(),
                ]);

            if ($check !== null) {
                $this->addFlash('failed', 'Le trick existe déjà !');
                return $this->redirectToRoute('trick_new');
            }

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
                        $this->addFlash('failed', 'An error happened with the image !');
                        return $this->redirectToRoute('trick_new');
                    }
                }
                $image = new Images();
                $image->setSource($filename);
                $trick->addImage($image);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $videos = new Videos;
            foreach ($trick->getVideos() as $video) {
                $embedVideos = $this->adminVideo->addEmbed($video->getUrl());
                $videos->setUrl($embedVideos);
                $trick->addVideo($videos);
                $entityManager->persist($trick);
            }


            $trick->setUser($user);
            $trick->setSlug($this->clean->delAccent($trick->getName()));
            $entityManager->persist($trick);
            $entityManager->flush();

            $this->addFlash('success', 'Le trick a bien été ajouté!');

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
    public function show(Trick $trick, Request $request, CommentsRepository $commentsRepository): Response
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

        if ($paging !==  null) {
            $length = $this->pagination->commentsPagination($paging, $bdd);
        } else {
            $length = $this->pagination->commentsPagination(0, $bdd);
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'comments' => $commentsRepository->findBy(["trick" => $trick], ['creation_date' => 'DESC'], $length, null),
            'formComments' => $form->createView(),

        ]);
    }

    /**
     * @Route("/{slug}/edit", name="trick_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Trick $trick, VideosRepository $videosRepository): Response
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

                $getVideos = $form->get('videos')->getData();

                foreach ($getVideos as $video) {
                    $videoTreated = $this->adminVideo->addEmbed($video->getUrl());
                    $checkVideo = $videosRepository
                    ->findOneBy([
                        'url' =>$video->getUrl(),
                    ]);
                   
                    if ($checkVideo === null) {
                        $video->setUrl($videoTreated);
                        $trick->addVideo($video);
                        $entityManager->persist($trick);
                    }
                }

                $entityManager->flush();
                $this->addFlash('success', 'Le trick a bien été modifié !');
                return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
            }
        } else {
            return $this->redirectToRoute('front_index');
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("image_principale/{idSeeked}", name="image_featured")
     */
    public function changeFeature(int $idSeeked, ImagesRepository $imageRepository): Response
    {
        $user = $this->security->getUser();

        if ($user) {
            $img = $imageRepository
                ->find($idSeeked);

            $trick = $img->getTrick();

            foreach ($trick->getImages() as $image) {

                if ($image->getId() === $idSeeked) {
                    $image->setFeatured(true);
                } else {
                    $image->setFeatured(false);
                }
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return  $this->redirectToRoute('trick_edit', ['slug' => $trick->getSlug()]);
        }
    }

    /**
     * @Route("/{slug}/delete", name="trick_delete")
     */
    public function delete(Trick $trick): Response
    {
        $user = $this->getUser();
        if ($user) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($trick);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Le trick a bien été supprimé !');

        return $this->redirectToRoute('front_index');
    }

    /**
     * @Route("image/{idDelete}/delete", name="image_delete")
     */
    public function deleteImage(int $idDelete, ImagesRepository $imageRepository): Response
    {

        $user = $this->security->getUser();
        if ($user) {
            $img = $imageRepository
                ->find($idDelete);

            $nom = $img->getSource();
            unlink($this->getParameter('images_directory') . '/' . $nom);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($img);
            $entityManager->flush();

            $trick = $img->getTrick();
            return  $this->redirectToRoute('trick_edit', ['slug' => $trick->getSlug()]);
        }
        return $this->redirectToRoute('front_index');
    }



    /**
     * @Route("imageShow/{idDelete}/delete", name="image_delete_show", methods={"GET","POST"})
     * 
     */
    public function deleteImageShow(int $idDelete): Response
    {
        $user = $this->security->getUser();

        if ($user) {
            $imageRepository = $this->getDoctrine()->getRepository(Images::class);
            $image = $imageRepository->findOneBy(['id' => $idDelete]);
            $trick = $image->getTrick();
            $nom = $image->getSource();
            unlink($this->getParameter('images_directory') . '/' . $nom);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($image);
            $entityManager->flush();
            $this->addFlash('success', 'L\'image a bien été supprimé !');
            return  $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
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
                $this->addFlash('success', 'Sucess image is edited !');
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
    public function deleteVideoShow(int $id, VideosRepository $videosRepository): Response
    {
        $user = $this->security->getUser();
        if ($user) {
            $video = $videosRepository
                ->find($id);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($video);
            $entityManager->flush();
            $trick = $video->getTrick();
            $this->addFlash('success', 'Success Video deleted !');
            return  $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }
        return $this->redirectToRoute('front_index');
    }


    /**
     * @Route("video/{id}/edit", name="video_edit")
     */
    public function editVideo(Request $request, int $id, VideosRepository $videosRepository, TrickRepository $trickRepository)
    {

        $user = $this->security->getUser();
        if ($user) {
            $video =  $videosRepository
                ->find($id);

            $trick = $video->getTrick();

            $data = $request->query->all("filter_form");
            $data["categories"] = $trick->getName();
            $form = $this->createForm(EditType::class, $data);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $requested =  $request->request->get('edit');

                $newTrick = $trickRepository
                    ->find($requested['name']);

                $videos = $video->setTrick($newTrick);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($videos);
                $entityManager->flush();

                $this->addFlash('success', 'Sucess video edited !');
                return  $this->redirectToRoute('trick_show', ['slug' => $newTrick->getSlug()]);
            }

            return $this->render('trick/edit-video.html.twig', [
                'video' => $video,
                'form' => $form->createView(),
            ]);
        }
        return $this->redirectToRoute('front_index');
    }
}
