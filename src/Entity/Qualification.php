<?php

namespace App\Entity;

use App\Repository\QualificationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: QualificationRepository::class)]
#[ORM\Table(name: 'qualifications')]
class Qualification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: UserProfile::class, inversedBy: 'qualifications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserProfile $userProfile = null;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $type = null; // 'academic', 'language', 'professional'

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $institution = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $field = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $grade = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $score = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $scoreType = null; // 'GPA', 'Percentage', 'Band', 'IELTS Academic', etc.

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $detailedScores = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $board = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $gradingScheme = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $englishScore = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $academicQualification = null;


    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $exactQualificationName = null;

    // Baccalaureate specific fields
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['qualification:read', 'qualification:write'])]
    private ?string $baccalaureateStream = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['qualification:read', 'qualification:write'])]
    private ?string $baccalaureateStreamOther = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $expiryDate = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserProfile(): ?UserProfile
    {
        return $this->userProfile;
    }

    public function setUserProfile(?UserProfile $userProfile): self
    {
        $this->userProfile = $userProfile;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getInstitution(): ?string
    {
        return $this->institution;
    }

    public function setInstitution(?string $institution): self
    {
        $this->institution = $institution;
        return $this;
    }

    public function getField(): ?string
    {
        return $this->field;
    }

    public function setField(?string $field): self
    {
        $this->field = $field;
        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getGrade(): ?string
    {
        return $this->grade;
    }

    public function setGrade(?string $grade): self
    {
        $this->grade = $grade;
        return $this;
    }

    public function getScore(): ?string
    {
        return $this->score;
    }

    public function setScore(?string $score): self
    {
        $this->score = $score;
        return $this;
    }

    public function getScoreType(): ?string
    {
        return $this->scoreType;
    }

    public function setScoreType(?string $scoreType): self
    {
        $this->scoreType = $scoreType;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDetailedScores(): ?array
    {
        return $this->detailedScores;
    }

    public function setDetailedScores(?array $detailedScores): self
    {
        $this->detailedScores = $detailedScores;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getBoard(): ?string
    {
        return $this->board;
    }

    public function setBoard(?string $board): self
    {
        $this->board = $board;
        return $this;
    }

    public function getGradingScheme(): ?string
    {
        return $this->gradingScheme;
    }

    public function setGradingScheme(?string $gradingScheme): self
    {
        $this->gradingScheme = $gradingScheme;
        return $this;
    }

    public function getEnglishScore(): ?string
    {
        return $this->englishScore;
    }

    public function setEnglishScore(?string $englishScore): self
    {
        $this->englishScore = $englishScore;
        return $this;
    }

    public function getAcademicQualification(): ?string
    {
        return $this->academicQualification;
    }

    public function setAcademicQualification(?string $academicQualification): self
    {
        $this->academicQualification = $academicQualification;
        return $this;
    }


    public function getExactQualificationName(): ?string
    {
        return $this->exactQualificationName;
    }

    public function setExactQualificationName(?string $exactQualificationName): self
    {
        $this->exactQualificationName = $exactQualificationName;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getExpiryDate(): ?\DateTimeInterface
    {
        return $this->expiryDate;
    }

    public function setExpiryDate(?\DateTimeInterface $expiryDate): self
    {
        $this->expiryDate = $expiryDate;
        return $this;
    }

    public function getBaccalaureateStream(): ?string
    {
        return $this->baccalaureateStream;
    }

    public function setBaccalaureateStream(?string $baccalaureateStream): self
    {
        $this->baccalaureateStream = $baccalaureateStream;
        return $this;
    }

    public function getBaccalaureateStreamOther(): ?string
    {
        return $this->baccalaureateStreamOther;
    }

    public function setBaccalaureateStreamOther(?string $baccalaureateStreamOther): self
    {
        $this->baccalaureateStreamOther = $baccalaureateStreamOther;
        return $this;
    }
}
