<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $email = null;

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private array $roles = [];

    #[ORM\Column(type: 'boolean')]
    #[Groups(['user:read', 'user:write'])] 
    private bool $subscriptionToNewsletter = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function eraseCredentials(): void {}

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function isSubscriptionToNewsletter(): bool
    {
        return $this->subscriptionToNewsletter;
    }

    public function setSubscriptionToNewsletter(bool $subscriptionToNewsletter): self
    {
        $this->subscriptionToNewsletter = $subscriptionToNewsletter;
        return $this;
    }
}
