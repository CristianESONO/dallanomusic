<?php

namespace App\Controller\Admin;

use App\Entity\PaymentGateway;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class PaymentGatewayCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PaymentGateway::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(), // ID affiché uniquement sur la liste
            TextField::new('api_key', 'API Key'), // Champ pour la clé API
            TextField::new('site_id', 'Site ID'), // Champ pour le Site ID
            TextField::new('secret_key', 'Secret Key'), // Champ pour la clé secrète
            DateTimeField::new('timestamp', 'Horodatage')->hideOnForm(),
        ];
    }
}
