<?php

namespace App\Controller;

use App\Entity\TrickGroup;
use App\Form\TrickGroupType;
use App\Repository\TrickGroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/trick/group")
 */
class TrickGroupController extends AbstractController
{
    /**
     * @Route("/", name="trick_group_index", methods={"GET"})
     */
    public function index(TrickGroupRepository $trickGroupRepository): Response
    {
        return $this->render('trick_group/index.html.twig', [
            'trick_groups' => $trickGroupRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="trick_group_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $trickGroup = new TrickGroup();
        $form = $this->createForm(TrickGroupType::class, $trickGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trickGroup);
            $entityManager->flush();

            return $this->redirectToRoute('trick_group_index');
        }

        return $this->render('trick_group/new.html.twig', [
            'trick_group' => $trickGroup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="trick_group_show", methods={"GET"})
     */
    public function show(TrickGroup $trickGroup): Response
    {
        return $this->render('trick_group/show.html.twig', [
            'trick_group' => $trickGroup,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="trick_group_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TrickGroup $trickGroup): Response
    {
        $form = $this->createForm(TrickGroupType::class, $trickGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('trick_group_index');
        }

        return $this->render('trick_group/edit.html.twig', [
            'trick_group' => $trickGroup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="trick_group_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TrickGroup $trickGroup): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trickGroup->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($trickGroup);
            $entityManager->flush();
        }

        return $this->redirectToRoute('trick_group_index');
    }
}
