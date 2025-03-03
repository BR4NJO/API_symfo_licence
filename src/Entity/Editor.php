<?php

namespace App\Entity;

use App\Repository\EditorRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: EditorRepository::class)]
class Editor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['editor:read', 'video_game:read'])] 
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['editor:read', 'editor:write', 'video_game:read'])] 
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['editor:read', 'editor:write'])] 
    private ?string $country = null;

    #[ORM\OneToMany(mappedBy: 'editor', targetEntity: VideoGame::class, orphanRemoval: true)]
    #[MaxDepth(1)]
    #[Groups(['editor:read'])] 
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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

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
            $videoGame->setEditor($this);
        }

        return $this;
    }

    public function removeVideoGame(VideoGame $videoGame): static
    {
        if ($this->videoGames->removeElement($videoGame)) {
            if ($videoGame->getEditor() === $this) {
                $videoGame->setEditor(null);
            }
        }

        return $this;
    }
}
