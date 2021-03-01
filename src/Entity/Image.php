<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 */
class Image
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

    //   /**
    //  * @ORM\ManyToMany(targetEntity=Trick::class, inversedBy="image")
    //  * @ORM\JoinColumn(nullable=false)
    //  */
    // private $trick;


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

    // public function getTrick(): ?Trick
    // {
    //     return $this->trick;
    // }

    // public function setTrick(?Trick $trick): self
    // {
    //     $this->trick = $trick;

    //     return $this;
    // }
}
