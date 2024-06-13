<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Repository\WishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/wishes', name: 'wishes_')]
class WishController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function list(WishRepository $wishRepository): Response
    {
        //tip 1: find all but not order
        // $wishes = $wishRepository->findAll();

        //tip 2 : find with order, we need configure function findRecently() in WishRepository
        //$wishes = $wishRepository->findRecently();

        //tip 3 : find with order
        $wishes = $wishRepository->findBy(["isPublished" => true], ["dateCreated" => "DESC"]);
        return $this->render('wishes/list.html.twig', [
            "wishes" => $wishes
        ]);
    }

    #[Route('/detail/{id}', name: 'detail', requirements: ['id' => '\d+'])]
    public function detail(int $id, WishRepository $wishRepository): Response
    {
        $wish = $wishRepository->find($id);

        if(!$wish){
            throw $this->createNotFoundException("oops ! Not found !");
        }
        return $this->render('wishes/detail.html.twig', [
            "wish" =>$wish
        ]);
    }








}
