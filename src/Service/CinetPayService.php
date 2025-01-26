<?php

// src/Service/CinetPayService.php
namespace App\Service;

use App\Service\CartService;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CinetPayService
{
    private HttpClientInterface $client;
    private string $apikey;
    private string $siteId;
    private string $secretKey;
    private CartService $cartService;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $params, CartService $cartService)
    {
        $this->client = $client;
        $this->apikey = $params->get('cinetpay_api_key');
        $this->siteId = $params->get('cinetpay_site_id');
        $this->secretKey = $params->get('cinetpay_secret_key');
        $this->cartService = $cartService;
        
    }

    public function createPaymentLink($transactionData)
    {
        $cartItems = $this->cartService->getCartItems(); // Récupérer les éléments du panier
        $totalPrice = $this->cartService->calculateTotalPrice($cartItems); // Calculer le prix total du panier
        $totalItems = $this->cartService->getTotalItems(); // Nombre total d'articles dans le panier

        // Utiliser le prix total pour la transaction
        $transactionData['amount'] = $totalPrice;

        // Votre logique pour créer la requête vers CinetPay, ici nous utilisons $transactionData pour envoyer les infos
        $url = 'https://api.cinetpay.com/v1/transactions';
        
        $formData = [
            "transaction_id" => $transactionData['transaction_id'],
            "amount" => $totalPrice,
            "currency" => $transactionData['currency'],
            "customer_surname" => $transactionData['customer_name'],
            "customer_name" => $transactionData['customer_surname'],
            "description" => $transactionData['description'],
            "notify_url" => $transactionData['notify_url'],
            "return_url" => $transactionData['return_url'],
            "channels" => "ALL",
            "metadata" => "",
            "customer_email" => $transactionData['customer_email'],
            "customer_phone_number" => $transactionData['customer_phone_number'],
            "customer_address" => $transactionData['customer_address'],
            "customer_city" => $transactionData['customer_city'],
            "customer_country" => $transactionData['customer_country'],
            "customer_state" => $transactionData['customer_state'],
            "customer_zip_code" => $transactionData['customer_zip_code']
        ];

        $response = $this->client->request('POST', $url, [
            'json' => $formData,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apikey
            ]
        ]);

        $data = $response->toArray();
        
        if ($data['code'] == '201') {
            return $data['data']['payment_url'];  // URL de paiement générée
        } else {
            throw new \Exception('Erreur lors de la création du lien de paiement');
        }
    }
}
