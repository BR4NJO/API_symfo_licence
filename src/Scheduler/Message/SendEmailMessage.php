<?php

namespace App\Scheduler\Message;

class SendEmailMessage
{
    private string $email;
    private array $games;

    public function __construct(string $email, array $games)
    {
        $this->email = $email;
        $this->games = $games;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getGames(): array
    {
        return $this->games;
    }
}
