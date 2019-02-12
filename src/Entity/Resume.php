<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Psr\Log\LoggerInterface;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Student", inversedBy="resumes")
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

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $grade;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $gpa;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $major;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $skills;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pdf;

    /**
     * @ORM\Column(type="string", length=1023, nullable=true)
     */
    private $intro;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $resumeName;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Experience", mappedBy="resume", orphanRemoval=true)
     */
    private $experiences;

    public function __construct()
    {
        $this->companiesSent = new ArrayCollection();
        $this->experiences = new ArrayCollection();
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

    public function getGrade(): ?string
    {
        return $this->grade;
    }

    public function setGrade(string $grade): self
    {
        $this->grade = $grade;

        return $this;
    }

    public function getGpa(): ?float
    {
        return $this->gpa;
    }

    public function setGpa(float $gpa): self
    {
        $this->gpa = $gpa;

        return $this;
    }

    public function getMajor(): ?string
    {
        return $this->major;
    }

    public function setMajor(?string $major): self
    {
        $this->major = $major;

        return $this;
    }

    public function getSkills(): ?string
    {
        return $this->skills;
    }

    public function setSkills(?string $skills): self
    {
        $this->skills = $skills;

        return $this;
    }

    public function getPdf(): ?string
    {
        return $this->pdf;
    }

    public function setPdf(?string $pdf): self
    {
        $this->pdf = $pdf;

        return $this;
    }

    public function getIntro(): ?string
    {
        return $this->intro;
    }

    public function setIntro(?string $intro): self
    {
        $this->intro = $intro;

        return $this;
    }

    public function getResumeName(): ?string
    {
        return $this->resumeName;
    }

    public function setResumeName(string $resumeName): self
    {
        $this->resumeName = $resumeName;

        return $this;
    }

    /**
     * @return Collection|Experience[]
     */
    public function getExperiences(): Collection
    {
        return $this->experiences;
    }

    /**
     * @return array
     */
    public function getExperiencesArray(): array
    {
        $res = [];
        if (null === $this->experiences) {
            return [];
        }

        foreach ($this->experiences as $experience) {
            /** @var Experience $e */
            $e = $experience;
            $res[] = [
                'id' => $e->getId(),
                'companyName' => $e->getCompanyName(),
                'type' => $e->getType(),
                'description' => $e->getDescription(),
                'timePeriod' => $e->getTimePeriod(),
            ];
        }
        return $res;
    }

    public function addExperience(Experience $experience): self
    {
        if (!$this->experiences->contains($experience)) {
            $this->experiences[] = $experience;
            $experience->setResume($this);
        }

        return $this;
    }

    public function removeExperience(Experience $experience): self
    {
        if ($this->experiences->contains($experience)) {
            $this->experiences->removeElement($experience);
            // set the owning side to null (unless already changed)
            if ($experience->getResume() === $this) {
                $experience->setResume(null);
            }
        }

        return $this;
    }
}
