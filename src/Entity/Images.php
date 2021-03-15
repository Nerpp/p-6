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
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="images", cascade={"persist", "remove"})
     */
    private $user;

    // private $image;

    public function getId(): ?int
    {
        return $this->id;
    }

     /**
      * @return mixed
      */
    public function getSource(): ?string
    {
        return $this->source;
    }

     /**
       * @param mixed $source
       */
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

    
     public function __toString() {
         return $this->source;
     }
}
