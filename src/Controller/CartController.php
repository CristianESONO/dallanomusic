<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\CartService;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CartController extends AbstractController
{

    private $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }


    #[Route('/cart', name: 'cart_view')]
    public function viewCart(): Response
    {

        $cartItems = $this->cartService->getCartItems();
        $totalItems = $this->cartService->getTotalItems();
        $totalPrice = 0;

        foreach ($cartItems as $item) {
            // Vérifiez si 'product' est bien un objet Product
            if ($item['product'] instanceof Product) {
                $totalPrice += $item['product']->getPrice() * $item['quantity'];
            } else {
                // Traiter le cas où 'product' n'est pas un objet Product
                // Vous pouvez loguer une erreur ou effectuer un traitement alternatif
                throw new \Exception('L\'élément du panier n\'est pas un objet Product');
            }
        }

        return $this->render('cart/index.html.twig', [
            'cartItems' => $cartItems,
            'totalItems' => $totalItems,
            'totalPrice' => $totalPrice,
        ]);
    
    }


    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function addToCart(int $id, Request $request): Response
    {
        $quantity = $request->request->getInt('quantity', 1);
        $this->cartService->addProduct($id, $quantity);

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'success' => true,
                'totalItems' => $this->cartService->getTotalItems()
            ]);
        }

        return $this->redirectToRoute('cart_view');
    }

    #[Route('/cart/decrease/{id}', name: 'cart_decrease')]
public function decreaseQuantity(int $id): Response
{
    $cartItems = $this->cartService->getCartItems();
    
    if (isset($cartItems[$id])) {
        if ($cartItems[$id]['quantity'] > 1) {
            // Diminuer la quantité
            $this->cartService->addProduct($id, -1);
        } else {
            // Supprimer le produit si la quantité atteint 0
            $this->cartService->removeProduct($id);
        }
    }

    if ($this->isXmlHttpRequest()) {
        return $this->json([
            'success' => true,
            'totalItems' => $this->cartService->getTotalItems()
        ]);
    }

    return $this->redirectToRoute('cart_view');
}
    

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function removeFromCart(int $id): Response
    {
        // Supprimer le produit du panier
        $this->cartService->removeProduct($id);

        return $this->redirectToRoute('cart_view');
    }



    // Méthode pour rediriger vers la page de paiement
    #[Route('/cart/payment', name: 'cart_payment')]
    public function payment(Request $request): Response
    {
         // Récupérer les éléments du panier (ici on suppose que vous avez une méthode pour les obtenir)
         $cartItems = $this->cartService->getCartItems(); // Remplacez par votre logique réelle pour obtenir le panier
         $totalPrice = $this->calculateTotalPrice($cartItems); // Calculer le prix total des éléments du panier
         $totalItems = $this->cartService->getTotalItems();

         // Passer les données à la vue
         return $this->render('cart/paiement.html.twig', [
             'cartItems' => $cartItems,
             'totalPrice' => $totalPrice,
             'totalItems' => $totalItems,
         ]);
    }


     /**
     * Calculer le prix total des éléments du panier
     */
    private function calculateTotalPrice(array $cartItems): float
    {
        $totalPrice = 0;
        
        foreach ($cartItems as $item) {
            // Vérifiez si 'product' est bien un objet Product
            if ($item['product'] instanceof Product) {
                $totalPrice += $item['product']->getPrice() * $item['quantity'];
            } else {
                // Traiter le cas où 'product' n'est pas un objet Product
                // Vous pouvez loguer une erreur ou effectuer un traitement alternatif
                throw new \Exception('L\'élément du panier n\'est pas un objet Product');
            }
        }

        return $totalPrice;
    }


   

    
    

    

}
