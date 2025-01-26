<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Transaction;
use App\Service\PayDunyaService;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PaymentController extends AbstractController
{
    private $cartService;
    private $paydunyaService;
    private $entityManager;
    private $passwordHasher;
    private $params;

    public function __construct(
        CartService $cartService, 
        PayDunyaService $paydunyaService,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        ParameterBagInterface $params
    )
    {
        $this->cartService = $cartService;
        $this->paydunyaService = $paydunyaService;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->params = $params;
    }

    #[Route('/payment/create', name: 'payment_create', methods: ['POST'])]
    public function createPayment(Request $request): Response
    {
         // Récupérer les variables d'environnement via ParameterBagInterface
         $masterKey = $this->params->get('paydunya_master_key');
         $publicKey = $this->params->get('paydunya_public_key');
         $privateKey = $this->params->get('paydunya_private_key');
         $token = $this->params->get('paydunya_token');
         $mode = $this->params->get('paydunya_mode');

        // Configuration de PayDunya
        \Paydunya\Setup::setMasterKey($masterKey);
        \Paydunya\Setup::setPublicKey($publicKey);
        \Paydunya\Setup::setPrivateKey($privateKey);
        \Paydunya\Setup::setToken($token);
        \Paydunya\Setup::setMode($mode); // Optionnel en mode test. Utilisez cette option pour les paiements tests.
    
        // Configuration des informations de votre service/entreprise
        \Paydunya\Checkout\Store::setName("Dallano Music Shop");
        \Paydunya\Checkout\Store::setTagline("L'élégance n'a pas de prix");
        \Paydunya\Checkout\Store::setPhoneNumber("781855138");
        \Paydunya\Checkout\Store::setPostalAddress("Dakar Plateau - Etablissement kheweul");
        \Paydunya\Checkout\Store::setWebsiteUrl("http://www.chez-sandra.sn");
        \Paydunya\Checkout\Store::setLogoUrl("http://www.chez-sandra.sn/logo.png");
        \Paydunya\Checkout\Store::setCallbackUrl("http://magasin-le-choco.com/callback_url.php");
        
        // Utiliser les routes Symfony pour les URLs de retour et d'annulation
        $returnUrl = $this->generateUrl('payment_success', [], true);
        $cancelUrl = $this->generateUrl('payment_cancel', [], true);
        \Paydunya\Checkout\Store::setCancelUrl($cancelUrl);  // URL d'annulation
        \Paydunya\Checkout\Store::setReturnUrl($returnUrl);  // URL de retour
    
        // Récupérer les données du formulaire
        $userName = $request->request->get('userName');
        $userEmail = $request->request->get('userEmail');
        $userPhoneNumber = $request->request->get('userPhoneNumber');
        $userPassword = $request->request->get('userPassword');
    
        // Vérifier si l'utilisateur existe déjà
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $userEmail]);
    
        if (!$user) {
            // Créer un nouvel utilisateur
            $user = new User();
            $user->setUsername($userName)
                 ->setEmail($userEmail)
                 ->setPhone($userPhoneNumber)
                 ->setRoles(['ROLE_USER'])
                 ->setCreatedAt(new \DateTimeImmutable());
    
            // Hasher le mot de passe
            $hashedPassword = $this->passwordHasher->hashPassword($user, $userPassword);
            $user->setPassword($hashedPassword);
    
            // Sauvegarder l'utilisateur
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    
        // Récupérer les articles du panier et calculer le total
        $cartItems = $this->cartService->getCartItems();
        $totalPrice = 0;
        foreach ($cartItems as $item) {
            $totalPrice += $item['product']->getPrice() * $item['quantity'];
        }
    
        // Créer une facture PayDunya
        $invoice = new \Paydunya\Checkout\CheckoutInvoice();
        foreach ($cartItems as $item) {
            $product = $item['product'];
            $quantity = $item['quantity'];
            $invoice->addItem($product->getName(), $quantity, $product->getPrice(), $product->getPrice() * $quantity);
        }
        $invoice->setTotalAmount($totalPrice);
    
        // Création de l'URL de paiement
        if ($invoice->create()) {
            $url_facture = $invoice->getInvoiceUrl();
             // Créer une transaction
            $transaction = new Transaction();
            $transaction->setDate(new \DateTime());
            $transaction->setTotalPrice($totalPrice);
            
            // Préparer la liste des produits sous forme de tableau
            $productList = [];
            foreach ($cartItems as $item) {
                $productList[] = [
                    'name' => $item['product']->getName(),
                    'price' => $item['product']->getPrice(),
                    'quantity' => $item['quantity'],
                ];
            }
            $transaction->setProducts($productList);

            // Associer la transaction à l'utilisateur
            $transaction->setUser($user);

            // Sauvegarder la transaction dans la base de données
            $this->entityManager->persist($transaction);
            $this->entityManager->flush();
            return $this->redirect($url_facture); // Rediriger vers l'URL de paiement PayDunya
        } else {
            // En cas d'échec de la création de la facture
            return $this->render('payment/error.html.twig', [
                'error' => $invoice->response_text,
            ]);
        }
    }
    
    #[Route('/payment/success', name: 'payment_success')]
    public function paymentSuccess(): Response
    {
        return $this->render('payment/success.html.twig');
    }

    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function paymentCancel(): Response
    {
        return $this->render('payment/cancel.html.twig');
    }
}
