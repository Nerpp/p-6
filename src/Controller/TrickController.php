<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Trick;

use App\Form\TrickType;
use App\Form\VideosType;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Route("/trick")
 */
class TrickController extends AbstractController
{
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

        if ($form->isSubmitted() && $form->isValid()) {

            //on récupere les images transmises
            $entityManager = $this->getDoctrine()->getManager();
            $images = $form->get('images')->getData();
            //on boucle sur les images pour traiter le array
            foreach ($images as $image){
                //On génere un nouveau nom de fichier unique, guess extension repêre les extension .jpg etc..
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                //On copie le fichiers dans le dossier upload
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                //On stocke dans la Bdd le nom de l'image
                $img = new Images();
                $img->setName($fichier);
                $trick->addImage($img);

                //Videos
            }

            foreach ($trick->getVideos() as $video)
            {
                $entityManager->persist($video);
            }
            $trick->setCreateDate(new \DateTime());

            $entityManager->persist($trick);
            $entityManager->flush();

            return $this->redirectToRoute('trick_index');
        }

        return $this->render('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="trick_show", methods={"GET"})
     */
    public function show(Trick $trick): Response
    {
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="trick_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Trick $trick): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //on récupere les images transmises
            $images = $form->get('images')->getData();

            //on boucle sur les images pour traiter le array
            foreach ($images as $image){
                //On génere un nouveau nom de fichier unique, guess extension repêre les extension .jpg etc..
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                //On copie le fichiers dans le dossier upload
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                //On stocke dans la Bdd le nom de l'image
                $img = new Images();
                $img->setName($fichier);
                $trick->addImage($img);
            }
            $trick->setUpdateDate(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('trick_index');
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
        if ($this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($trick);
            $entityManager->flush();
        }

        return $this->redirectToRoute('trick_index');
    }

    /**
     * @Route("/supprime/image/{id}", name="trick_delete_image")
     */
    public function deleteImage(Images $image, Request $request){
        $data = json_decode($request->getContent(), true);
        // On vérifie si le token est valide
        if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])){
            // On récupère le nom de l'image
            $nom = $image->getName();
            // On supprime le fichier
            unlink($this->getParameter('images_directory').'/'.$nom);

            // On supprime l'entrée de la base
            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();

            // On répond en json
            return new JsonResponse(['success' => 1]);
        }else{
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }
}
