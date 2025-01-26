<?php

namespace App\Controller;

use App\Service\CartService;
use App\Entity\ContactMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[Route('/contacto', name: 'contact_index', methods: ['GET'])]
    public function index(): Response
    {
        // Récupérer le nombre total d'articles
        $totalItems = $this->cartService->getTotalItems();

        return $this->render('contact/index.html.twig', [
            'totalItems' => $totalItems,
        ]);
    }

    #[Route('/contact', name: 'contact_send', methods: ['POST'])]
    public function send(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        // Récupération des données du formulaire
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $subject = $request->request->get('subject', 'Contact Form');
        $message = $request->request->get('message');

        // Vérification des champs obligatoires
        if (empty($name) || empty($email) || empty($message)) {
            $this->addFlash('error', 'Veuillez remplir tous les champs obligatoires.');
            return $this->redirectToRoute('contact_index');
        }

        // Création et enregistrement de l'entité ContactMessage
        $contactMessage = new ContactMessage();
        $contactMessage->setName($name);
        $contactMessage->setEmail($email);
        $contactMessage->setSubject($subject);
        $contactMessage->setMessage($message);
        $contactMessage->setSentAt(new \DateTime());

        $entityManager->persist($contactMessage);
        $entityManager->flush();

        // Envoi d'un e-mail à l'administrateur
        $emailToAdmin = (new Email())
            ->from($email) // Adresse e-mail de l'utilisateur
            ->to('lebosscristian@gmail.com') // Adresse e-mail de l'administrateur
            ->subject("Nouveau message de $name : $subject")
            ->text("Nom: $name\nEmail: $email\n\nMessage:\n$message");

        $mailer->send($emailToAdmin);

        // Message de confirmation pour l'utilisateur
        $this->addFlash('success', 'Votre message a été envoyé avec succès.');

        // Redirection vers la page de contact
        return $this->redirectToRoute('contact_index');
    }
}
