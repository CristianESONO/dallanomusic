<?php

namespace App\Controller\Admin;

use App\Entity\Gallery;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class GalleryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Gallery::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title', 'Title'),
            // CollectionField pour permettre le téléchargement de plusieurs images
            CollectionField::new('images', 'Images')
                ->setEntryType(FileType::class) // Type de chaque entrée
                ->setFormTypeOption('data_class', null) // Nécessaire pour éviter une erreur de type
                ->setFormTypeOption('allow_add', true) // Autorise l'ajout de nouveaux fichiers
                ->setFormTypeOption('allow_delete', true) // Autorise la suppression de fichiers
                ->setFormTypeOption('entry_options', ['label' => 'Image']), // Options pour chaque entrée
            TextareaField::new('description', 'Description'),
            DateTimeField::new('uploadedAt', 'Uploaded At'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Gallery')
            ->setDefaultSort(['uploadedAt' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('title');
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Gallery) {
            $images = [];
            foreach ($entityInstance->getImages() as $imageFile) {
                if ($imageFile) {
                    // Générer un nom unique pour chaque fichier
                    $newFilename = md5(uniqid()) . '.' . $imageFile->guessExtension();
                    try {
                        // Déplacer le fichier vers le répertoire d'uploads
                        $imageFile->move(
                            $this->getParameter('images_directory'), // Utilisez le paramètre défini dans les services
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // Gérer l'exception en cas de problème de téléchargement
                    }
                    // Ajouter le nom de fichier au tableau d'images
                    $images[] = $newFilename;
                }
            }
            // Stocker les noms des fichiers dans l'entité
            $entityInstance->setImages($images);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->persistEntity($entityManager, $entityInstance);
    }
}
