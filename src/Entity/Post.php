<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $Artiste = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $DatePublication = null;

    #[ORM\Column]
    private ?bool $Visible = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Categorie = null;

    #[ORM\Column(length: 255)]
    private ?string $Url = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArtiste(): ?string
    {
        return $this->Artiste;
    }

    public function setArtiste(string $Artiste): static
    {
        $this->Artiste = $Artiste;

        return $this;
    }

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->DatePublication;
    }

    public function setDatePublication(\DateTimeInterface $DatePublication): static
    {
        $this->DatePublication = $DatePublication;

        return $this;
    }

    public function isVisible(): ?bool
    {
        return $this->Visible;
    }

    public function setVisible(bool $Visible): static
    {
        $this->Visible = $Visible;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->Categorie;
    }

    public function setCategorie(?string $Categorie): static
    {
        $this->Categorie = $Categorie;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->Url;
    }

    public function setUrl(string $Url): static
    {
        $this->Url = $Url;

        return $this;
    }
}
