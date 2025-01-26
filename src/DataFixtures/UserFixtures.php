<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création d'un utilisateur avec rôle ADMIN
        $user = new User();
        $user->setEmail('admin@admin.com');
        $user->setRoles(['ROLE_ADMIN']);

        // Hachage et attribution du mot de passe
        $password = $this->passwordHasher->hashPassword($user, 'admin');
        $user->setPassword($password);

         // Définir d'autres propriétés si nécessaire
         $user->setCreatedAt(new \DateTime()); // Assurez-vous que 'createdAt' existe dans l'entité User


        // Sauvegarder l'utilisateur en base de données
        $manager->persist($user);

        // Enregistrer toutes les entités créées dans la base de données
        $manager->flush();
    }
}
