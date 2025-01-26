<?php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MusicController extends AbstractController
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[Route('/music', name: 'app_music')]
    public function index(): Response
    {
         // Récupérer le nombre total d'articles
         $totalItems = $this->cartService->getTotalItems();
        return $this->render('music/index.html.twig', [
            'totalItems' => $totalItems,
        ]);
    }
}
