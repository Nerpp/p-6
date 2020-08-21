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
    private $createDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedate;

    /**
     * @ORM\OneToOne(targetEntity=TrickGroup::class, inversedBy="trick", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $trickgroupid;

    /**
     * @ORM\OneToMany(targetEntity=Pic::class, mappedBy="trick_id")
     */
    private $pics;

    /**
     * @ORM\OneToMany(targetEntity=Comments::class, mappedBy="trick_id", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=Videos::class, mappedBy="trick_id", orphanRemoval=true)
     */
    private $videos;

    public function __construct()
    {
        $this->pics = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->videos = new ArrayCollection();
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
        return $this->createDate;
    }

    public function setCreateDate(\DateTimeInterface $createDate): self
    {
        $this->createDate = $createDate;

        return $this;
    }

    public function getUpdatedate(): ?\DateTimeInterface
    {
        return $this->updatedate;
    }

    public function setUpdatedate(?\DateTimeInterface $updatedate): self
    {
        $this->updatedate = $updatedate;

        return $this;
    }

    public function getTrickgroupid(): ?TrickGroup
    {
        return $this->trickgroupid;
    }

    public function setTrickgroupid(TrickGroup $trickgroupid): self
    {
        $this->trickgroupid = $trickgroupid;

        return $this;
    }

    /**
     * @return Collection|Pic[]
     */
    public function getPics(): Collection
    {
        return $this->pics;
    }

    public function addPic(Pic $pic): self
    {
        if (!$this->pics->contains($pic)) {
            $this->pics[] = $pic;
            $pic->setTrickId($this);
        }

        return $this;
    }

    public function removePic(Pic $pic): self
    {
        if ($this->pics->contains($pic)) {
            $this->pics->removeElement($pic);
            // set the owning side to null (unless already changed)
            if ($pic->getTrickId() === $this) {
                $pic->setTrickId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setTrickId($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getTrickId() === $this) {
                $comment->setTrickId(null);
            }
        }

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
}
