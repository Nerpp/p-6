<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="front_index")
     */
    public function index(TrickRepository $trickRepository)
    {
        $this->generateUrl('a', ['page' => 2, 'category' => 'Symfony']);


        // return $this->render('front/index.html.twig', [
        //     'tricks' => $trickRepository->getLastTricks(),
        // ]);
    }
}
