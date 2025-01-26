<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name'),
            NumberField::new('price')->setNumDecimals(2),
            ImageField::new('imageUrl')
                ->setBasePath('/uploads/images') // Assurez-vous que ce chemin correspond à votre configuration d'upload
                ->setUploadDir('public/uploads/images')
                ->setRequired(false),
            TextEditorField::new('description')->setRequired(false),
            NumberField::new('stockQuantity'),
            BooleanField::new('isOnSale'),
            NumberField::new('salePrice')->setNumDecimals(2)->setRequired(false),
            TextField::new('category')->setRequired(false),
        ];
    }
}
