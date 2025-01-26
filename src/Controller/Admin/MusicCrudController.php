<?php

namespace App\Controller\Admin;

use App\Entity\Music;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Fields;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Vich\UploaderBundle\Form\Type\VichFileType;

class MusicCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Music::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title'),
            TextField::new('artist'),
            DateTimeField::new('releaseDate'),
            TextField::new('genre'),
            TextareaField::new('description'),
            ImageField::new('coverImage')
                ->setBasePath('/uploads/images') // chemin d'accès aux images
                ->setUploadDir('public/uploads/images') // dossier où les images sont stockées
                ->setRequired(false),
            // Champ pour l'upload du fichier audio
            TextField::new('audioFile')
                ->setFormType(VichFileType::class)
                ->setLabel('Upload Audio File'),
            
            // Afficher le nom du fichier après upload
            TextField::new('file')
                ->setLabel('Audio File Name')
                ->onlyOnIndex(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Music');
    }
}
