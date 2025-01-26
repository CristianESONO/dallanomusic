<?php

namespace App\Controller\Admin;

use App\Entity\Album;
use App\Entity\Music;
use App\Entity\Video;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class AlbumCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Album::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title'),
            TextField::new('artist'),
            TextEditorField::new('description'),
            ImageField::new('image')
                ->setBasePath('/uploads/images') // Assurez-vous que ce chemin correspond à votre configuration d'upload
                ->setUploadDir('public/uploads/images')
                ->setRequired(false),
            DateTimeField::new('publishedAt'),
            AssociationField::new('musics')
                ->setCrudController(MusicCrudController::class)
                ->setFormTypeOptions(['by_reference' => false])
                ->setRequired(false),
            AssociationField::new('videos')
                ->setCrudController(VideoCrudController::class)
                ->setFormTypeOptions(['by_reference' => false])
                ->setRequired(false),
        ];
    }
}
