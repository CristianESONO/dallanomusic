<?php

// src/Service/CartService.php
namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use App\Repository\ProductRepository;


class CartService
{
    private $session;
    private $productRepository;

    public function __construct(RequestStack $requestStack, ProductRepository $productRepository)
    {
        $this->session = $requestStack->getSession();  // Obtenir la session à partir de la requête courante
        $this->productRepository = $productRepository; // Pour récupérer les produits
    }

     // Récupérer le nombre total d'articles dans le panier
     public function getTotalItems(): int
    {
        $cart = $this->session->get('cart', []);
        return array_sum($cart);
    }

    // src/Service/CartService.php
    public function addProduct(int $productId, int $quantity = 1): void
    {
        $cart = $this->session->get('cart', []);
        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }
        $this->session->set('cart', $cart);
    }

      // Récupérer les éléments du panier
      public function getCartItems(): array
      {
          $cart = $this->session->get('cart', []);
          $cartItems = [];
  
          foreach ($cart as $id => $quantity) {
              $product = $this->productRepository->find($id);
              if ($product) {
                  $cartItems[] = [
                      'product' => $product,
                      'quantity' => $quantity
                  ];
              }
          }
  
          return $cartItems;
      }
  
 // Diminuer la quantité d'un produit dans le panier
    public function decreaseProduct(int $productId): void
    {
        $cart = $this->session->get('cart', []);
        
        if (isset($cart[$productId])) {
            $cart[$productId] -= 1;
            if ($cart[$productId] <= 0) {
                unset($cart[$productId]); // Supprime le produit si sa quantité devient 0 ou moins
            }
            $this->session->set('cart', $cart);
        }
    }
    // src/Service/CartService.php
    public function removeProduct(int $productId): void
    {
        $cart = $this->session->get('cart', []);
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            $this->session->set('cart', $cart);
        }
    }


}
