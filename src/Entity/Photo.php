<?php

namespace App\Entity;

use App\Repository\PhotoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhotoRepository::class)]
class Photo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    private ?string $appartenance = null;

    #[ORM\Column]
    private ?\DateTime $date_time = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getAppartenance(): ?string
    {
        return $this->appartenance;
    }

    public function setAppartenance(string $appartenance): static
    {
        $this->appartenance = $appartenance;

        return $this;
    }

    public function getDateTime(): ?\DateTime
    {
        return $this->date_time;
    }

    public function setDateTime(\DateTime $date_time): static
    {
        $this->date_time = $date_time;

        return $this;
    }
}
