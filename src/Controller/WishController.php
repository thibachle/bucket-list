<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use App\Services\Censurator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/wishes', name: 'wishes_')]
class WishController extends AbstractController
{

    #[Route('/list', name: "list_home")]
    #[Route('', name: 'list')]
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

    #[Route('/create', name: 'create')]
    #[Route('/update/{id}', name: 'update')]
    public function create(
        EntityManagerInterface $entityManager,
        Request $request,
        WishRepository $wishRepository,
        int $id = null,
        Censurator $censurator
    ): Response

    {

        if($id){
            $wish = $wishRepository->find($id);
        }else{
            $wish = new Wish();
            $wish->setPublished(true);
            $wish->setDateUpdated(new  \DateTime());
        }

        $wishForm = $this->createForm(WishType::class, $wish);
        $wishForm->handleRequest($request);


        if($wishForm->isSubmitted() && $wishForm->isValid()){
            dump($wish);
            //utilisateur du service pour censurator
            $wish->setDescription($censurator->purify($wish->getDescription()));
            $wish->setTitle($censurator->purify($wish->getTitle()));

            $entityManager->persist($wish);
            $entityManager->flush();

            //je set les éléments non gérables par l'uitilisateur


            $this->addFlash('success', 'Ideal is successfully added !');

            return $this->redirectToRoute('wishes_detail', ['id' => $wish->getId()]);
        }


        return $this->render('wishes/create.html.twig', [
            'wishForm' => $wishForm
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'])]
    public function delete(int $id, WishRepository $wishRepository, EntityManagerInterface $entityManager): Response
    {
        $wish = $wishRepository->find($id);
        if($this->getUser() != $wish->getUser() && !$this->isGranted('ROLE_ADMIN')){
            throw $this->createNotFoundException('Not allow');
        }

        $entityManager->remove($wish);
        $entityManager->flush();

        $this->addFlash('success', 'Wish is deleted !');
        return  $this->redirectToRoute('wishes_list');
    }











}
