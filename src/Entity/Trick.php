<?php

namespace App\Entity;

use App\Repository\TrickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrickRepository::class)
 */
class Trick
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $create_date;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $update_date;



    /**
     * @ORM\OneToMany(targetEntity=Videos::class, mappedBy="trick_id", orphanRemoval=true)
     */
    private $videos;

    /**
     * @ORM\OneToMany(targetEntity=Comments::class, mappedBy="trick_id")
     */
    private $comments_id;

    /**
     * @ORM\OneToMany(targetEntity=Pictures::class, mappedBy="trick_id", orphanRemoval=true)
     */
    private $pictures_id;

    /**
     * @ORM\ManyToOne(targetEntity=TrickGroup::class, inversedBy="tricks")
     */
    private $groupe;

    public function __construct()
    {
        $this->videos = new ArrayCollection();
        $this->comments_id = new ArrayCollection();
        $this->pictures_id = new ArrayCollection();
        $this->create_date= new \DateTime();
        $this->update_date= new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreateDate(): ?\DateTimeInterface
    {
        return $this->create_date;
    }

    public function setCreateDate(\DateTimeInterface $create_date): self
    {
        $this->create_date = $create_date;

        return $this;
    }

    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->update_date;
    }

    public function setUpdateDate(?\DateTimeInterface $update_date): self
    {
        $this->update_date = $update_date;

        return $this;
    }



    /**
     * @return Collection|Videos[]
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Videos $video): self
    {
        if (!$this->videos->contains($video)) {
            $this->videos[] = $video;
            $video->setTrickId($this);
        }

        return $this;
    }

    public function removeVideo(Videos $video): self
    {
        if ($this->videos->contains($video)) {
            $this->videos->removeElement($video);
            // set the owning side to null (unless already changed)
            if ($video->getTrickId() === $this) {
                $video->setTrickId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getCommentsId(): Collection
    {
        return $this->comments_id;
    }

    public function addCommentsId(Comments $commentsId): self
    {
        if (!$this->comments_id->contains($commentsId)) {
            $this->comments_id[] = $commentsId;
            $commentsId->setTrickId($this);
        }

        return $this;
    }

    public function removeCommentsId(Comments $commentsId): self
    {
        if ($this->comments_id->contains($commentsId)) {
            $this->comments_id->removeElement($commentsId);
            // set the owning side to null (unless already changed)
            if ($commentsId->getTrickId() === $this) {
                $commentsId->setTrickId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Pictures[]
     */
    public function getPicturesId(): Collection
    {
        return $this->pictures_id;
    }

    public function addPicturesId(Pictures $picturesId): self
    {
        if (!$this->pictures_id->contains($picturesId)) {
            $this->pictures_id[] = $picturesId;
            $picturesId->setTrickId($this);
        }

        return $this;
    }

    public function removePicturesId(Pictures $picturesId): self
    {
        if ($this->pictures_id->contains($picturesId)) {
            $this->pictures_id->removeElement($picturesId);
            // set the owning side to null (unless already changed)
            if ($picturesId->getTrickId() === $this) {
                $picturesId->setTrickId(null);
            }
        }

        return $this;
    }

    public function getGroupe(): ?TrickGroup
    {
        return $this->groupe;
    }

    public function setGroupe(?TrickGroup $groupe): self
    {
        $this->groupe = $groupe;

        return $this;
    }
}
