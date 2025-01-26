<?php

namespace App\Controller;

use App\Service\CartService;
use App\Repository\AlbumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;

class AlbumController extends AbstractController
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }


    #[Route('/album', name: 'app_album')]
    public function index(AlbumRepository $albumRepository, PaginatorInterface $paginator, Request $request): Response
    {
         // Récupérer le nombre total d'articles
         $totalItems = $this->cartService->getTotalItems();
        $query = $albumRepository->findAll();
        $albums = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10 // Nombre d'albums par page
        );
        return $this->render('album/index.html.twig', [
            'albums' => $albums,
            'totalItems' => $totalItems,
        ]);
    }

    #[Route('/album/{id}', name: 'single_album')]
    public function show(int $id, AlbumRepository $albumRepository): Response
    {
        $album = $albumRepository->find($id);
        // Récupérer le nombre total d'articles
        $totalItems = $this->cartService->getTotalItems();

        if (!$album) {
            throw $this->createNotFoundException('Album not found');
        }

        // Ajoutez ici la logique pour récupérer les pistes associées à l'album
        $musics = $album->getMusics()->toArray(); // Assurez-vous que cette méthode existe dans votre entité Album

        //dd($musics);
        return $this->render('album/single_album.html.twig', [
            'album' => $album,
            'musics' => $musics,
            'totalItems' => $totalItems,
        ]);
    }

   
}
