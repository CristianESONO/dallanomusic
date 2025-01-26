<?php

namespace App\Controller;

use App\Service\CartService;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MarketController extends AbstractController
{

    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[Route('/tienda', name: 'app_market')]
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        // Récupérer les paramètres de filtre et de tri
        $category = $request->query->get('category');
        $sortBy = $request->query->get('sort', 'name');
        $minPrice = $request->query->get('minPrice');
        $maxPrice = $request->query->get('maxPrice');

        // Récupérer les produits filtrés et triés
        $products = $productRepository->findByFiltersAndSort($category, $sortBy, $minPrice, $maxPrice);

        // Récupérer les articles du panier
        $cartItems = $this->cartService->getCartItems();
        $totalItems = $this->cartService->getTotalItems();

        return $this->render('market/index.html.twig', [
            'products' => $products,
            'cartItems' => $cartItems,
            'totalItems' => $totalItems,
        ]);
    }

}
