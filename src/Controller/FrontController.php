<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class FrontController extends AbstractController
{

    
    /**
     * @Route("/", name="front_index")
     */
    public function index(TrickRepository $tricksRepository)
    {
        return $this->render('front/index.html.twig', [
            'tricks' => $tricksRepository->findBy(array(),array('id'=> 'ASC'),$limit=10,$offset=null),
        ]);
    }
}
