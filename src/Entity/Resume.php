<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ResumeRepository")
 */
class Resume
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\student", inversedBy="resumes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $student;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createtime;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatetime;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Company", mappedBy="receivedResumes")
     */
    private $companiesSent;

    public function __construct()
    {
        $this->companiesSent = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudent(): ?student
    {
        return $this->student;
    }

    public function setStudent(?student $student): self
    {
        $this->student = $student;

        return $this;
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

    public function getCreatetime(): ?\DateTimeInterface
    {
        return $this->createtime;
    }

    public function setCreatetime(\DateTimeInterface $createtime): self
    {
        $this->createtime = $createtime;

        return $this;
    }

    public function getUpdatetime(): ?\DateTimeInterface
    {
        return $this->updatetime;
    }

    public function setUpdatetime(\DateTimeInterface $updatetime): self
    {
        $this->updatetime = $updatetime;

        return $this;
    }

    /**
     * @return Collection|Company[]
     */
    public function getCompaniesSent(): Collection
    {
        return $this->companiesSent;
    }

    public function addCompaniesSent(Company $companiesSent): self
    {
        if (!$this->companiesSent->contains($companiesSent)) {
            $this->companiesSent[] = $companiesSent;
            $companiesSent->addReceivedResume($this);
        }

        return $this;
    }

    public function removeCompaniesSent(Company $companiesSent): self
    {
        if ($this->companiesSent->contains($companiesSent)) {
            $this->companiesSent->removeElement($companiesSent);
            $companiesSent->removeReceivedResume($this);
        }

        return $this;
    }
}
