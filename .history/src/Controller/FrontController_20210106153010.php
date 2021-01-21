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
    public function index(TrickRepository $trickRepository)
    {
        $this->urlGenerator->generate('index', ['fron' => $request->attributes->get('company')]);
        return $this->render('front/index.html.twig', [
            'tricks' => $trickRepository->getLastTricks(),
        ]);
    }
}
