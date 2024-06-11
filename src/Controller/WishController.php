<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/wishes', name: 'wishes_')]
class WishController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function list(): Response
    {
        return $this->render('wishes/list.html.twig');
    }

    #[Route('/{id}', name: 'detail', requirements: ['id' => '\d+'])]
    public function detail(): Response
    {
        return $this->render('wishes/detail.html.twig');
    }








}
