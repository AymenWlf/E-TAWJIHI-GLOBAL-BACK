<?php

namespace App\Entity;

use App\Repository\ProgramRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProgramRepository::class)]
#[ORM\Table(name: 'programs')]
class Program
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['application:list', 'application:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['application:list', 'application:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['program:read', 'program:list', 'admin:read', 'admin:list'])]
    private ?string $description = null;

    #[ORM\Column(name: 'description_fr', type: Types::TEXT, nullable: true)]
    #[Groups(['program:read', 'program:list', 'admin:read', 'admin:list'])]
    private ?string $descriptionFr = null;

    #[ORM\OneToMany(mappedBy: 'program', targetEntity: ProgramRequirement::class, cascade: ['persist', 'remove'])]
    private Collection $requirements;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $curriculum = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $curriculumFr = null;

    #[ORM\ManyToOne(targetEntity: Establishment::class, inversedBy: 'programsList')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['application:list', 'application:read'])]
    private ?Establishment $establishment = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $degree = null; // Bachelor's, Master's, PhD, etc.

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $duration = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $durationUnit = 'year';

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $language = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tuition = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $tuitionAmount = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $tuitionCurrency = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(length: 4, nullable: true)]
    private ?string $startYear = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $intake = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $applicationDeadline = null;

    #[ORM\Column]
    private bool $scholarships = false;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column]
    private bool $featured = false;

    #[ORM\Column]
    private bool $aidvisorRecommended = false;

    #[ORM\Column]
    private bool $easyApply = false;

    #[ORM\Column]
    private bool $housing = false;

    #[ORM\Column]
    private bool $oralExam = false;

    #[ORM\Column]
    private bool $writtenExam = false;

    #[ORM\Column(nullable: true)]
    private ?int $ranking = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['program:read', 'program:list', 'admin:read', 'admin:list'])]
    private ?string $studyType = null; // on-campus, online, hybrid

    #[ORM\Column(length: 10, nullable: true)]
    #[Groups(['program:read', 'program:list', 'admin:read', 'admin:list'])]
    private ?string $universityType = null; // A, B, C

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['program:read', 'program:list', 'admin:read', 'admin:list'])]
    private ?string $subject = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['program:read', 'program:list', 'admin:read', 'admin:list'])]
    private ?array $field = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['program:read', 'program:list', 'admin:read', 'admin:list'])]
    private ?string $studyLevel = null; // undergraduate, graduate, doctoral

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $languages = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $intakes = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $subjects = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $studyLevels = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $careerProspects = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $faculty = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $facilities = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $accreditations = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 3, scale: 1, nullable: true)]
    private ?string $rating = null;

    #[ORM\Column(nullable: true)]
    private ?int $reviews = null;

    #[ORM\Column]
    private bool $isActive = true;

    // Academic qualification requirements
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $academicQualifications = null; // Array of required academic qualifications

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $gradeRequirements = null; // Array of grade requirements by system

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $minimumGrade = null; // Minimum grade requirement

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $gradeSystem = null; // Grade system used (CGPA_4, CGPA_20, Percentage, etc.)

    #[ORM\Column]
    private bool $requiresAcademicQualification = false; // Whether program requires academic qualification

    // GPA specific fields
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $gpaScale = null; // GPA scale used by the program (4.0, 5.0, 10.0, 20.0, 100.0)

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $gpaScore = null; // Minimum GPA score required

    #[ORM\Column]
    private bool $requiresGPA = false; // Whether program requires GPA

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    // Structured requirements for display
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $structuredRequirements = null;

    #[ORM\Column(length: 20, options: ["default" => 'draft'])]
    private ?string $status = 'draft';

    // Media fields
    #[ORM\Column(name: 'campus_photos', type: Types::JSON, nullable: true)]
    private ?array $campusPhotos = null;

    #[ORM\Column(name: 'campus_locations', type: Types::JSON, nullable: true)]
    private ?array $campusLocations = null;

    #[ORM\Column(name: 'youtube_videos', type: Types::JSON, nullable: true)]
    private ?array $youtubeVideos = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $brochures = null;

    // SEO fields
    #[ORM\Column(name: 'seo_title', length: 255, nullable: true)]
    private ?string $seoTitle = null;

    #[ORM\Column(name: 'seo_description', type: Types::TEXT, nullable: true)]
    private ?string $seoDescription = null;

    #[ORM\Column(name: 'seo_keywords', type: Types::JSON, nullable: true)]
    private ?array $seoKeywords = null;

    #[ORM\Column(name: 'multi_intakes', type: Types::JSON, nullable: true)]
    private ?array $multiIntakes = null;

    #[ORM\Column(name: 'service_pricing', type: Types::JSON, nullable: true)]
    private ?array $servicePricing = null;

    #[ORM\Column(name: 'program_type', length: 10, nullable: true)]
    #[Groups(['program:read', 'program:list', 'admin:read', 'admin:list'])]
    private ?string $programType = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->requirements = new ArrayCollection();
    }

    /**
     * @return Collection<int, ProgramRequirement>
     */
    public function getRequirements(): Collection
    {
        return $this->requirements;
    }

    public function addRequirement(ProgramRequirement $requirement): self
    {
        if (!$this->requirements->contains($requirement)) {
            $this->requirements->add($requirement);
            $requirement->setProgram($this);
        }
        return $this;
    }

    public function removeRequirement(ProgramRequirement $requirement): self
    {
        if ($this->requirements->removeElement($requirement)) {
            if ($requirement->getProgram() === $this) {
                $requirement->setProgram(null);
            }
        }
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;
        return $this;
    }

    public function getEstablishment(): ?Establishment
    {
        return $this->establishment;
    }

    public function setEstablishment(?Establishment $establishment): static
    {
        $this->establishment = $establishment;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;
        return $this;
    }

    public function getDegree(): ?string
    {
        return $this->degree;
    }

    public function setDegree(?string $degree): static
    {
        $this->degree = $degree;
        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(?string $duration): static
    {
        $this->duration = $duration;
        return $this;
    }

    public function getDurationUnit(): ?string
    {
        return $this->durationUnit;
    }

    public function setDurationUnit(?string $durationUnit): static
    {
        $this->durationUnit = $durationUnit;
        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): static
    {
        $this->language = $language;
        return $this;
    }

    public function getTuition(): ?string
    {
        return $this->tuition;
    }

    public function setTuition(?string $tuition): static
    {
        $this->tuition = $tuition;
        return $this;
    }

    public function getTuitionAmount(): ?string
    {
        return $this->tuitionAmount;
    }

    public function setTuitionAmount(?string $tuitionAmount): static
    {
        $this->tuitionAmount = $tuitionAmount;
        return $this;
    }

    public function getTuitionCurrency(): ?string
    {
        return $this->tuitionCurrency;
    }

    public function setTuitionCurrency(?string $tuitionCurrency): static
    {
        $this->tuitionCurrency = $tuitionCurrency;
        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getStartYear(): ?string
    {
        return $this->startYear;
    }

    public function setStartYear(?string $startYear): static
    {
        $this->startYear = $startYear;
        return $this;
    }

    public function getIntake(): ?string
    {
        return $this->intake;
    }

    public function setIntake(?string $intake): static
    {
        $this->intake = $intake;
        return $this;
    }

    public function getApplicationDeadline(): ?\DateTimeInterface
    {
        return $this->applicationDeadline;
    }

    public function setApplicationDeadline(?\DateTimeInterface $applicationDeadline): static
    {
        $this->applicationDeadline = $applicationDeadline;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }


    public function isScholarships(): bool
    {
        return $this->scholarships;
    }

    public function setScholarships(bool $scholarships): static
    {
        $this->scholarships = $scholarships;
        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;
        return $this;
    }

    public function isFeatured(): bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): static
    {
        $this->featured = $featured;
        return $this;
    }

    public function isAidvisorRecommended(): bool
    {
        return $this->aidvisorRecommended;
    }

    public function setAidvisorRecommended(bool $aidvisorRecommended): static
    {
        $this->aidvisorRecommended = $aidvisorRecommended;
        return $this;
    }

    public function isEasyApply(): bool
    {
        return $this->easyApply;
    }

    public function setEasyApply(bool $easyApply): static
    {
        $this->easyApply = $easyApply;
        return $this;
    }

    public function isHousing(): bool
    {
        return $this->housing;
    }

    public function setHousing(bool $housing): static
    {
        $this->housing = $housing;
        return $this;
    }

    public function isOralExam(): bool
    {
        return $this->oralExam;
    }

    public function setOralExam(bool $oralExam): static
    {
        $this->oralExam = $oralExam;
        return $this;
    }

    public function isWrittenExam(): bool
    {
        return $this->writtenExam;
    }

    public function setWrittenExam(bool $writtenExam): static
    {
        $this->writtenExam = $writtenExam;
        return $this;
    }

    public function getRanking(): ?int
    {
        return $this->ranking;
    }

    public function setRanking(?int $ranking): static
    {
        $this->ranking = $ranking;
        return $this;
    }

    public function getStudyType(): ?string
    {
        return $this->studyType;
    }

    public function setStudyType(?string $studyType): static
    {
        $this->studyType = $studyType;
        return $this;
    }

    public function getUniversityType(): ?string
    {
        return $this->universityType;
    }

    public function setUniversityType(?string $universityType): static
    {
        $this->universityType = $universityType;
        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): static
    {
        $this->subject = $subject;
        return $this;
    }

    public function getField(): ?array
    {
        return $this->field;
    }

    public function setField(?array $field): static
    {
        $this->field = $field;
        return $this;
    }

    public function getStudyLevel(): ?string
    {
        return $this->studyLevel;
    }

    public function setStudyLevel(?string $studyLevel): static
    {
        $this->studyLevel = $studyLevel;
        return $this;
    }

    public function getLanguages(): ?array
    {
        return $this->languages;
    }

    public function setLanguages(?array $languages): static
    {
        $this->languages = $languages;
        return $this;
    }

    public function getIntakes(): ?array
    {
        return $this->intakes;
    }

    public function setIntakes(?array $intakes): static
    {
        $this->intakes = $intakes;
        return $this;
    }

    public function getSubjects(): ?array
    {
        return $this->subjects;
    }

    public function setSubjects(?array $subjects): static
    {
        $this->subjects = $subjects;
        return $this;
    }

    public function getStudyLevels(): ?array
    {
        return $this->studyLevels;
    }

    public function setStudyLevels(?array $studyLevels): static
    {
        $this->studyLevels = $studyLevels;
        return $this;
    }

    public function getCurriculum(): ?array
    {
        return $this->curriculum;
    }

    public function setCurriculum(?array $curriculum): static
    {
        $this->curriculum = $curriculum;
        return $this;
    }

    public function getCareerProspects(): ?string
    {
        return $this->careerProspects;
    }

    public function setCareerProspects(?string $careerProspects): static
    {
        $this->careerProspects = $careerProspects;
        return $this;
    }

    public function getFaculty(): ?array
    {
        return $this->faculty;
    }

    public function setFaculty(?array $faculty): static
    {
        $this->faculty = $faculty;
        return $this;
    }

    public function getFacilities(): ?array
    {
        return $this->facilities;
    }

    public function setFacilities(?array $facilities): static
    {
        $this->facilities = $facilities;
        return $this;
    }

    public function getAccreditations(): ?array
    {
        return $this->accreditations;
    }

    public function setAccreditations(?array $accreditations): static
    {
        $this->accreditations = $accreditations;
        return $this;
    }

    public function getRating(): ?string
    {
        return $this->rating;
    }

    public function setRating(?string $rating): static
    {
        $this->rating = $rating;
        return $this;
    }

    public function getReviews(): ?int
    {
        return $this->reviews;
    }

    public function setReviews(?int $reviews): static
    {
        $this->reviews = $reviews;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getAcademicQualifications(): ?array
    {
        return $this->academicQualifications;
    }

    public function setAcademicQualifications(?array $academicQualifications): static
    {
        $this->academicQualifications = $academicQualifications;
        return $this;
    }

    public function getGradeRequirements(): ?array
    {
        return $this->gradeRequirements;
    }

    public function setGradeRequirements(?array $gradeRequirements): static
    {
        $this->gradeRequirements = $gradeRequirements;
        return $this;
    }

    public function getMinimumGrade(): ?string
    {
        return $this->minimumGrade;
    }

    public function setMinimumGrade(?string $minimumGrade): static
    {
        $this->minimumGrade = $minimumGrade;
        return $this;
    }

    public function getGradeSystem(): ?string
    {
        return $this->gradeSystem;
    }

    public function setGradeSystem(?string $gradeSystem): static
    {
        $this->gradeSystem = $gradeSystem;
        return $this;
    }

    public function isRequiresAcademicQualification(): bool
    {
        return $this->requiresAcademicQualification;
    }

    public function setRequiresAcademicQualification(bool $requiresAcademicQualification): static
    {
        $this->requiresAcademicQualification = $requiresAcademicQualification;
        return $this;
    }

    public function getGpaScale(): ?string
    {
        return $this->gpaScale;
    }

    public function setGpaScale(?string $gpaScale): static
    {
        $this->gpaScale = $gpaScale;
        return $this;
    }

    public function getGpaScore(): ?string
    {
        return $this->gpaScore;
    }

    public function setGpaScore(?string $gpaScore): static
    {
        $this->gpaScore = $gpaScore;
        return $this;
    }

    public function isRequiresGPA(): bool
    {
        return $this->requiresGPA;
    }

    public function setRequiresGPA(bool $requiresGPA): static
    {
        $this->requiresGPA = $requiresGPA;
        return $this;
    }




    /**
     * Get requirements by type
     */

    /**
     * Get requirements by type and subtype
     */

    /**
     * Check if program has a specific requirement type
     */

    /**
     * Get all active requirement types for this program
     */

    /**
     * Get requirements as array for API serialization
     */

    public function getNameFr(): ?string
    {
        return $this->nameFr;
    }

    public function setNameFr(?string $nameFr): static
    {
        $this->nameFr = $nameFr;
        return $this;
    }

    public function getDescriptionFr(): ?string
    {
        return $this->descriptionFr;
    }

    public function setDescriptionFr(?string $descriptionFr): static
    {
        $this->descriptionFr = $descriptionFr;
        return $this;
    }

    public function getCurriculumFr(): ?string
    {
        return $this->curriculumFr;
    }

    public function setCurriculumFr(?string $curriculumFr): static
    {
        $this->curriculumFr = $curriculumFr;
        return $this;
    }

    public function getStructuredRequirements(): ?array
    {
        return $this->structuredRequirements;
    }

    public function setStructuredRequirements(?array $structuredRequirements): static
    {
        $this->structuredRequirements = $structuredRequirements;
        return $this;
    }

    /**
     * Get structured requirements by language
     */
    public function getStructuredRequirementsByLanguage(string $language = 'en'): array
    {
        if (!$this->structuredRequirements) {
            return [];
        }

        $requirements = [];
        foreach ($this->structuredRequirements as $category => $categoryData) {
            $requirements[$category] = [
                'title' => $categoryData['title'][$language] ?? $categoryData['title']['en'] ?? $category,
                'items' => []
            ];

            if (isset($categoryData['items'])) {
                foreach ($categoryData['items'] as $item) {
                    $requirements[$category]['items'][] = [
                        'name' => $item['name'][$language] ?? $item['name']['en'] ?? $item['name'],
                        'description' => $item['description'][$language] ?? $item['description']['en'] ?? $item['description'] ?? null,
                        'required' => $item['required'] ?? true,
                        'type' => $item['type'] ?? 'document'
                    ];
                }
            }
        }

        return $requirements;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;
        return $this;
    }

    // Media getters and setters
    public function getCampusPhotos(): ?array
    {
        return $this->campusPhotos;
    }

    public function setCampusPhotos(?array $campusPhotos): static
    {
        $this->campusPhotos = $campusPhotos;
        return $this;
    }

    public function getCampusLocations(): ?array
    {
        return $this->campusLocations;
    }

    public function setCampusLocations(?array $campusLocations): static
    {
        $this->campusLocations = $campusLocations;
        return $this;
    }

    public function getYoutubeVideos(): ?array
    {
        return $this->youtubeVideos;
    }

    public function setYoutubeVideos(?array $youtubeVideos): static
    {
        $this->youtubeVideos = $youtubeVideos;
        return $this;
    }

    public function getBrochures(): ?array
    {
        return $this->brochures;
    }

    public function setBrochures(?array $brochures): static
    {
        $this->brochures = $brochures;
        return $this;
    }

    // SEO getters and setters
    public function getSeoTitle(): ?string
    {
        return $this->seoTitle;
    }

    public function setSeoTitle(?string $seoTitle): static
    {
        $this->seoTitle = $seoTitle;
        return $this;
    }

    public function getSeoDescription(): ?string
    {
        return $this->seoDescription;
    }

    public function setSeoDescription(?string $seoDescription): static
    {
        $this->seoDescription = $seoDescription;
        return $this;
    }

    public function getSeoKeywords(): ?array
    {
        return $this->seoKeywords;
    }

    public function setSeoKeywords(?array $seoKeywords): static
    {
        $this->seoKeywords = $seoKeywords;
        return $this;
    }

    public function getMultiIntakes(): ?array
    {
        return $this->multiIntakes;
    }

    public function setMultiIntakes(?array $multiIntakes): static
    {
        $this->multiIntakes = $multiIntakes;
        return $this;
    }

    public function getServicePricing(): ?array
    {
        return $this->servicePricing;
    }

    public function setServicePricing(?array $servicePricing): static
    {
        $this->servicePricing = $servicePricing;
        return $this;
    }

    public function getProgramType(): ?string
    {
        return $this->programType;
    }

    public function setProgramType(?string $programType): static
    {
        $this->programType = $programType;
        return $this;
    }
}
