<?php

// src/Controller/EmailController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class EmailController extends AbstractController
{
    /**
     * @Route("/send-email", name="send_email", methods={"POST"})
     */
    public function sendEmail(MailerInterface $mailer): Response
    {
        // Créer l'email
        $email = (new Email())
            ->from('ton-email@example.com')
            ->to('destinataire@example.com')
            ->subject('Test Email')
            ->text('Ceci est un test d\'email');

        $mailer->send($email);

        return new Response('Email envoyé avec succès !');
    }
}
