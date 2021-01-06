<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class FrontController extends AbstractController
{
    /**
     * @Route("/{slug}", name="front_index")
     * @ParamConverter("page")
     */
    public function index(TrickRepository $trickRepository)
    {
        
        return $this->render('front/index.html.twig', [
            'tricks' => $trickRepository->getLastTricks(),
        ]);
    }
}
