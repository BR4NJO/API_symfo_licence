<?php

namespace App\Entity;

use App\Repository\VideoGameRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: VideoGameRepository::class)]
class VideoGame
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['video_game:read', 'video_game:write'])] 
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['video_game:read', 'video_game:write'])] 
    private ?string $title = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['video_game:read'])] 
    private ?\DateTimeInterface $releaseDate = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['video_game:write'])] 
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'videoGames')]
    #[MaxDepth(1)]
    #[Groups(['video_game:read'])] 
    private Collection $categories;

    #[ORM\ManyToOne(targetEntity: Editor::class, inversedBy: 'videoGames')]
    #[ORM\JoinColumn(nullable: false)]
    #[MaxDepth(1)]
    #[Groups(['video_game:read'])] 
    private ?Editor $editor = null;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTimeInterface $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function getEditor(): ?Editor
    {
        return $this->editor;
    }

    public function setEditor(?Editor $editor): static
    {
        $this->editor = $editor;

        return $this;
    }
}
