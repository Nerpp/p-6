<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use App\Services\Pagination;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class FrontController extends AbstractController
{
  
    public function __construct()
    {
        $this->pagination = new Pagination;
    }

    /**
     * @Route("/", name="front_index")
     */
    public function index(TrickRepository $tricksRepository)
    {
        $iBdd = count($tricksRepository->findAll());
        $iLength = $this->pagination->tricksPagination(0, $iBdd);

        return $this->render('front/index.html.twig', [
            'tricks' => $tricksRepository->findBy(array(), array('id' => 'ASC'), $iLength, null),
            'pagination' => $iBdd,
        ]);
    }

    /**
     * @Route("/extended", name="front_pagination", methods={"GET","POST"})
     */
    public function pagination(TrickRepository $tricksRepository, Request $request)
    {
        $iBdd = count($tricksRepository->findAll());
        $paging = $request->query->get('length');
        $iLength = !is_null($paging) ? $this->pagination->tricksPagination($paging, $iBdd) : $this->pagination->tricksPagination(0, $iBdd);

        return $this->render('front/index.html.twig', [
            'tricks' => $tricksRepository->findBy(array(), array('id' => 'ASC'), $iLength, null),
            'pagination' => $iBdd,
        ]);
    }
}
