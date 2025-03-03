<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['category:read', 'video_game:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['category:read', 'video_game:read'])]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: VideoGame::class, mappedBy: 'categories')]
    #[MaxDepth(1)]
    #[Groups(['category:read'])]
    private Collection $videoGames;

    public function __construct()
    {
        $this->videoGames = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getVideoGames(): Collection
    {
        return $this->videoGames;
    }

    public function addVideoGame(VideoGame $videoGame): static
    {
        if (!$this->videoGames->contains($videoGame)) {
            $this->videoGames->add($videoGame);
        }

        return $this;
    }

    public function removeVideoGame(VideoGame $videoGame): static
    {
        $this->videoGames->removeElement($videoGame);

        return $this;
    }
}
