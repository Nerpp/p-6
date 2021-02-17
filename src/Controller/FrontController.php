<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use App\Services\Pagination;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Services\Cleaner;


class FrontController extends AbstractController
{
  
    /**
     * @Route("/", name="front_index")
     */
    public function index(TrickRepository $tricksRepository,Pagination $pagination)
    {
        $clean = new Cleaner();
        dump($clean->delAccent('étè éà'));

        $bdd = count($tricksRepository->findAll());
        $length = $pagination->tricksPagination(0,$bdd);

        return $this->render('front/index.html.twig', [
            'tricks' => $tricksRepository->findBy(array(),array('id'=> 'ASC'),$limit=$length,$offset=null),
            'pagination' => $bdd,
        ]);
    }

     /**
     * @Route("/extended", name="front_pagination", methods={"GET","POST"})
     */
    public function pagination(TrickRepository $tricksRepository,Request $request,Pagination $pagination)
    {
        $bdd = count($tricksRepository->findAll());
        $paging = $request->query->get('length');

        if($paging !==  null){
            $length = $pagination->tricksPagination($paging,$bdd);
        }else{
            $length = $pagination->tricksPagination(0,$bdd);
        }

        return $this->render('front/index.html.twig', [
            'tricks' => $tricksRepository->findBy(array(),array('id'=> 'ASC'),$limit= $length,$offset=null),
            'pagination' => $bdd,
        ]);
    }


}
