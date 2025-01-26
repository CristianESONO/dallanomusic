<?php

namespace App\Service;

use Paydunya\Checkout\CheckoutInvoice;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PayDunyaService
{
    private string $publicKey;
    private string $privateKey;
    private string $token;
    private string $mode;
    private string $masterKey;
    private $payDunyaCheckoutInvoice;

     public function __construct(ParameterBagInterface $params)
    {
        // Initialisation de PaydunyaCheckoutInvoice
        $this->payDunyaCheckoutInvoice = new CheckoutInvoice();
        
        $this->publicKey = $params->get('paydunya_public_key');
        $this->privateKey  = $params->get('paydunya_private_key');
        $this->token  = $params->get('paydunya_token');
        $this->masterKey  = $params->get('paydunya_master_key');
        $this->mode  = $params->get('paydunya_mode');
    }


    public function createInvoice(array $items, float $totalAmount, string $returnUrl, string $cancelUrl): ?string
    {
        //dd($items);
        // Ajout des articles à la facture
        foreach ($items as $item) {
            $product = $item['product'];  // Récupérer l'objet Product
            $quantity = $item['quantity'];  // Récupérer la quantité

            // Vérifiez les valeurs des produits et des quantités
            //dd($product->getName(), $product->getPrice(), $quantity);

            $this->payDunyaCheckoutInvoice->addItem(
                $product->getName(),            // Utiliser getName() pour obtenir le nom
                $quantity,                      // Utiliser la quantité
                $product->getPrice(),           // Utiliser getPrice() pour obtenir le prix unitaire
                $product->getPrice() * $quantity // Calculer le total pour cet article
            );
        }

        // Configuration des URLs
        $this->payDunyaCheckoutInvoice->setTotalAmount($totalAmount);
        $this->payDunyaCheckoutInvoice->setCancelUrl($cancelUrl);
        $this->payDunyaCheckoutInvoice->setReturnUrl($returnUrl);

        // Création de la facture
        if ($this->payDunyaCheckoutInvoice->create()) {
            $invoiceUrl = $this->payDunyaCheckoutInvoice->getInvoiceUrl();
            //dd($invoiceUrl);  // Vérifiez si l'URL est bien générée
            return $invoiceUrl;
        }

        // En cas d'échec
        return null;
    }
}
