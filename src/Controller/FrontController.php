<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use App\Services\Pagination;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class FrontController extends AbstractController
{
    protected $iLength;
    protected $iBdd;

    public function __construct()
    {
        $this->pagination = new Pagination;
    }

    /**
     * @Route("/", name="front_index")
     */
    public function index(TrickRepository $tricksRepository)
    {
        $this->iBdd = count($tricksRepository->findAll());
        $this->iLength = $this->pagination->tricksPagination(0, $this->_iBdd);

        return $this->render('front/index.html.twig', [
            'tricks' => $tricksRepository->findBy(array(), array('id' => 'ASC'), $this->_iLength, null),
            'pagination' => $this->_iBdd,
        ]);
    }

    /**
     * @Route("/extended", name="front_pagination", methods={"GET","POST"})
     */
    public function pagination(TrickRepository $tricksRepository, Request $request)
    {
        $this->iBdd = count($tricksRepository->findAll());
        $paging = $request->query->get('length');
        $this->iLength = !is_null($paging) ? $this->pagination->tricksPagination($paging, $this->_iBdd) : $this->pagination->tricksPagination(0, $this->_iBdd);

        return $this->render('front/index.html.twig', [
            'tricks' => $tricksRepository->findBy(array(), array('id' => 'ASC'), $this->_iLength, null),
            'pagination' => $this->_iBdd,
        ]);
    }
}
