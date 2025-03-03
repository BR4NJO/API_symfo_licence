<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\VideoGame;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

#[AsCommand(
    name: 'app:send-newsletter',
    description: 'Envoie un email aux abonnés de la newsletter avec les jeux à venir.',
)]
class SendNewsletterCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MailerInterface $mailer
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->entityManager->getRepository(User::class)->findBy([
            'subscriptionToNewsletter' => true
        ]);

        $dateDebut = new \DateTime();
        $dateFin = (new \DateTime())->modify('+7 days');

        $jeux = $this->entityManager->getRepository(VideoGame::class)->createQueryBuilder('v')
            ->where('v.releaseDate BETWEEN :start AND :end')
            ->setParameter('start', $dateDebut)
            ->setParameter('end', $dateFin)
            ->getQuery()
            ->getResult();

        if (empty($jeux)) {
            $output->writeln('Aucun jeu à venir cette semaine.');
            return Command::SUCCESS;
        }

        foreach ($users as $user) {
            $email = (new TemplatedEmail())
                ->from(new Address('no-reply@monsite.com', 'Mon Site'))
                ->to($user->getEmail())
                ->subject('🎮 Découvrez les jeux à venir cette semaine !')
                ->htmlTemplate('emails/newsletter.html.twig')
                ->context([
                    'user' => $user,
                    'jeux' => $jeux,
                ]);

            $this->mailer->send($email);
        }

        $output->writeln('Newsletter envoyée avec succès !');
        return Command::SUCCESS;
    }
}
