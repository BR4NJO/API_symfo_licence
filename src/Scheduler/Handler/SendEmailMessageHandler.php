<?php

namespace App\Scheduler\Handler;
use App\Scheduler\Message\SendEmailMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;


class SendEmailMessageHandler 
{
    private MailerInterface $mailer;
    private Environment $twig;

    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function __invoke(SendEmailMessage $message)
    {
        $htmlContent = $this->twig->render('emails/newsletter.html.twig', [
            'games' => $message->getGames()
        ]);

        $email = (new Email())
            ->from('noreply@example.com')
            ->to($message->getEmail())
            ->subject('Newsletter des sorties jeux vidéo')
            ->html($htmlContent);

        $this->mailer->send($email);
    }
}
