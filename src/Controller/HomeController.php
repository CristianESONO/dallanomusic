<?php

namespace App\Controller;

use App\Service\CartService;
use App\Repository\AlbumRepository;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }


    #[Route('/inicio', name: 'app_home')]
    public function index(AlbumRepository $albumRepository,EvenementRepository $evenementRepository): Response
    {
        $albums = $albumRepository->findAll();
        $evenements = $evenementRepository->findAll();
        // Récupérer le nombre total d'articles
        $totalItems = $this->cartService->getTotalItems();
        return $this->render('home/index.html.twig', [
            'albumes' => $albums,
            'evenements' => $evenements,
            'totalItems' => $totalItems,
        ]);
    }
}
