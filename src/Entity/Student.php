<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StudentRepository")
 */
class Student
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
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $gender;

    /**
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createtime;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Resume", mappedBy="student", orphanRemoval=true)
     */
    private $resumes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Company", mappedBy="likedBy")
     */
    private $likes;

    /**
     * @ORM\Column(type="string", length=1023, nullable=true)
     */
    private $intro;

    public function __construct()
    {
        $this->resumes = new ArrayCollection();
        $this->likedBy = new ArrayCollection();
        $this->likes = new ArrayCollection();
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

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

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

    /**
     * @return Collection|Resume[]
     */
    public function getResumes(): Collection
    {
        return $this->resumes;
    }

    /**
     * @return array
     */
    public function getResumesArray(): array
    {
        $res = [];
        foreach ($this->resumes as $resume) {
            $res[] = [
                'resumeId' => $resume->getId(),
                'name' => $resume->getName(),
                'grade' => $resume->getGrade(),
                'gpa' => $resume->getGpa(),
                'major' => $resume->getMajor(),
                'intro' => $resume->getIntro(),
                'skills' => $resume->getSkills(),
                'experiences' => $resume->getExperiencesArray(),
                'pdf' => $resume->getPdf(),
                'lastEditTime' => $resume->getUpdatetime(),
            ];
        }
        return $res;
    }

    public function addResume(Resume $resume): self
    {
        if (!$this->resumes->contains($resume)) {
            $this->resumes[] = $resume;
            $resume->setStudent($this);
        }

        return $this;
    }

    public function removeResume(Resume $resume): self
    {
        if ($this->resumes->contains($resume)) {
            $this->resumes->removeElement($resume);
            // set the owning side to null (unless already changed)
            if ($resume->getStudent() === $this) {
                $resume->setStudent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Company[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Company $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->addLikedBy($this);
        }

        return $this;
    }

    public function removeLike(Company $like): self
    {
        if ($this->likes->contains($like)) {
            $this->likes->removeElement($like);
            $like->removeLikedBy($this);
        }

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
}
