<?php

namespace App\Controller;

use App\Service\CartService;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EvenementController extends AbstractController
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[Route('/eventos', name: 'app_evenements')]
    public function index(EvenementRepository $evenementRepository): Response
    {
        $evenements = $evenementRepository->findAll();

        // Récupérer le nombre total d'articles
        $totalItems = $this->cartService->getTotalItems();
        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,
            'totalItems' => $totalItems,
        ]);
    }
}
