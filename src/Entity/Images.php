<?php

namespace App\Entity;

use App\Repository\ImagesRepository;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity(repositoryClass=ImagesRepository::class)
 */
class Images
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $source;

    /**
     * @ORM\ManyToOne(targetEntity=Trick::class, inversedBy="images", cascade={"persist"})
     */
    private $trick;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="images", cascade={"persist"})
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $featured = false;


    public function __toString() {
        return $this->source;
    }

    // private $image;

    public function getId(): ?int
    {
        return $this->id;
    }

     
    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getTrick(): ?trick
    {
        return $this->trick;
    }

    public function setTrick(?trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

   
    public function getFeatured(): ?bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): self
    {
        $this->featured = $featured;

        return $this;
    }

    
}
