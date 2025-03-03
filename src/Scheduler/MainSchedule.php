<?php

namespace App\Scheduler;

use Symfony\Component\Scheduler\Attribute\AsCronTask;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Scheduler\Message\SendEmailMessage;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Repository\VideoGameRepository;

class MainSchedule
{
    private MessageBusInterface $bus;
    private UserRepository $userRepository;
    private VideoGameRepository $videoGameRepository;

    public function __construct(
        MessageBusInterface $bus,
        UserRepository $userRepository,
        VideoGameRepository $videoGameRepository
    ) {
        $this->bus = $bus;
        $this->userRepository = $userRepository;
        $this->videoGameRepository = $videoGameRepository;
    }

    #[AsCronTask('0 8 * * 1')] // Exécute tous les lundis à 8h30
    public function sendNewsletter(): void
    {
        $users = $this->userRepository->findBy(['newsletter' => true]);
        $games = $this->videoGameRepository->findUpcomingGames(7); // Fonction à implémenter

        foreach ($users as $user) {
            $this->bus->dispatch(new SendEmailMessage($user->getEmail(), $games));
        }
    }
}
