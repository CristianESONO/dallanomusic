<?php

namespace App\Controller;

use App\Service\CartService;
use App\Repository\GalleryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GalleryController extends AbstractController
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    

    #[Route('/gallery/{id}/like/{image}', name: 'gallery_like', methods: ['POST'])]
    public function likeImage(Request $request, $id, $image, GalleryRepository $galleryRepository, EntityManagerInterface $entityManager): Response
    {
        $gallery = $galleryRepository->find($id);
      
        if (!$gallery) {
            return new JsonResponse(['error' => 'Galerie non trouvée'], 404);
        }

        if ($gallery->getLikes() === null) {
            $gallery->setLikes([]);
        }

        $gallery->addLikeToImage($image);
        $entityManager->persist($gallery);
        $entityManager->flush();

        $likes = $gallery->getLikes();
        $likeCount = isset($likes[$image]) ? $likes[$image] : 0;

        // Si c'est une requête AJAX, renvoyer du JSON
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'success' => true,
                'likes' => $likeCount
            ]);
        }

        // Sinon, rediriger
        return $this->redirectToRoute('app_gallery');
    }
    
    

    #[Route('/galeria', name: 'app_gallery')]
    public function index(GalleryRepository $galleryRepository): Response
    {
        // Récupérer le nombre total d'articles
        $totalItems = $this->cartService->getTotalItems();
        //$galleries = $galleryRepository->findAllOrderedByDate();
        
        // Récupérer uniquement la dernière galerie ajoutée
        $latestGallery = $galleryRepository->findOneBy([], ['uploadedAt' => 'DESC']);
         // Passer la dernière galerie comme un tableau
        $galleries = $latestGallery ? [$latestGallery] : [];

        return $this->render('gallery/index.html.twig', [
            'galleries' => $galleries,
            'totalItems' => $totalItems,
        ]);
    }
}
