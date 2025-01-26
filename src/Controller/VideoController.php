<?php

namespace App\Controller;

use App\Service\CartService;
use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideoController extends AbstractController
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[Route('/video', name: 'app_video')]
    public function index(VideoRepository $videoRepository): Response
    {
        $videos = $videoRepository->findAll();
         // Récupérer le nombre total d'articles
         $totalItems = $this->cartService->getTotalItems();
        return $this->render('video/index.html.twig', [
            'videos' => $videos,
            'totalItems' => $totalItems,
        ]);
    }

    #[Route('/video/{id}', name: 'single_video')]
    public function show(int $id, VideoRepository $videoRepository): Response
    {
         // Récupérer le nombre total d'articles
         $totalItems = $this->cartService->getTotalItems();
        $video = $videoRepository->find($id);

        if (!$video) {
            throw $this->createNotFoundException('Video not found');
        }

        return $this->render('video/single_video.html.twig', [
            'video' => $video,
            'totalItems' => $totalItems,
        ]);
    }
}

