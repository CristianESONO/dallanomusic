<?php

// src/EventListener/CartSubscriber.php
namespace App\EventListener;

use App\Service\CartService;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class CartSubscriber
{
    private CartService $cartService;
    private Environment $twig;

    public function __construct(CartService $cartService, Environment $twig)
    {
        $this->cartService = $cartService;
        $this->twig = $twig;
    }

    public function onKernelController(ControllerEvent $event): void
    {
        // Ajouter le totalItems à toutes les vues Twig
        $totalItems = $this->cartService->getTotalItems();
        $this->twig->addGlobal('totalItems', $totalItems);
    }
}
