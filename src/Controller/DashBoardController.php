<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\ContactMessage;
use App\Entity\Gallery;
use App\Entity\Evenement; // Ajouter cette ligne
use App\Entity\Music;
use App\Entity\Product; // Ajouter cette ligne
use App\Entity\Review;
use App\Entity\PaymentGateway;
use App\Entity\User;
use App\Entity\Video;
use App\Controller\Admin\GalleryCrudController;
use App\Controller\Admin\EvenementCrudController; // Ajouter cette ligne
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItems;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ADMIN')]
class DashBoardController extends AbstractDashboardController
{

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Rediriger vers une entité spécifique si vous le souhaitez
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(GalleryCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Dallano Music Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        // Manage Users
        yield MenuItem::linkToCrud('Users', 'fa fa-users', User::class);

        // Manage Blog Posts
        yield MenuItem::linkToCrud('Blog Posts', 'fa fa-blog', BlogPost::class);

        // Manage Comments
        yield MenuItem::linkToCrud('Comments', 'fa fa-comments', Comment::class);

        // Manage Videos
        yield MenuItem::linkToCrud('Videos', 'fa fa-video', Video::class);

        // Manage Music
        yield MenuItem::linkToCrud('Music', 'fa fa-music', Music::class);

        // Manage Albums
        yield MenuItem::linkToCrud('Albums', 'fa fa-compact-disc', Album::class);

        // Manage Reviews
        yield MenuItem::linkToCrud('Reviews', 'fa fa-star', Review::class);

        // Manage Galleries
        yield MenuItem::linkToCrud('Galleries', 'fa fa-images', Gallery::class);

         // Manage Evenements (nouvelle entrée)
         yield MenuItem::linkToCrud('Evenements', 'fa fa-calendar', Evenement::class);

         // Manage Products (nouvelle entrée)
        yield MenuItem::linkToCrud('Products', 'fa fa-box', Product::class);

        // Manage Contact Messages
        yield MenuItem::linkToCrud('Contact Messages', 'fa fa-envelope', ContactMessage::class);

        // Manage PaymentGateway
        yield MenuItem::linkToCrud('Payment Gateways', 'fa fa-credit-card', PaymentGateway::class);
    }
}
