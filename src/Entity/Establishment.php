<?php

namespace App\Entity;

use App\Repository\EstablishmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EstablishmentRepository::class)]
#[ORM\Table(name: 'establishments')]
class Establishment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameFr = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $type = null; // Public, Private, etc.

    #[ORM\Column(type: Types::DECIMAL, precision: 3, scale: 1, nullable: true)]
    private ?string $rating = null;

    #[ORM\Column(nullable: true)]
    private ?int $students = null;

    #[ORM\Column(nullable: true)]
    private ?int $programs = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionFr = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $mission = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $missionFr = null;

    #[ORM\Column(nullable: true)]
    private ?int $foundedYear = null;

    #[ORM\Column]
    private bool $featured = false;

    #[ORM\Column]
    private bool $sponsored = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tuition = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $tuitionMin = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $tuitionMax = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $tuitionCurrency = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $acceptanceRate = null;

    #[ORM\Column(nullable: true)]
    private ?int $worldRanking = null;

    #[ORM\Column(nullable: true)]
    private ?int $qsRanking = null;

    #[ORM\Column(nullable: true)]
    private ?int $timesRanking = null;

    #[ORM\Column(nullable: true)]
    private ?int $arwuRanking = null;

    #[ORM\Column(nullable: true)]
    private ?int $usNewsRanking = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $popularPrograms = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $applicationDeadline = null;

    #[ORM\Column]
    private bool $scholarships = false;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $scholarshipTypes = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $scholarshipDescription = null;

    #[ORM\Column]
    private bool $housing = false;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $language = null;

    #[ORM\Column]
    private bool $aidvisorRecommended = false;

    #[ORM\Column]
    private bool $easyApply = false;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $universityType = null; // A, B, C

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $commissionRate = null;

    #[ORM\Column(nullable: true)]
    private ?int $freeApplications = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $visaSupport = null; // free, paid

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $countrySpecific = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $website = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $accreditations = null;

    #[ORM\Column]
    private bool $accommodation = false;

    #[ORM\Column]
    private bool $careerServices = false;

    #[ORM\Column]
    private bool $languageSupport = false;

    #[ORM\Column]
    private bool $isActive = true;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $admissionRequirements = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $admissionRequirementsFr = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $englishTestRequirements = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $academicRequirements = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $documentRequirements = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $visaRequirements = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $applicationFee = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $applicationFeeCurrency = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $livingCosts = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $livingCostsCurrency = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'establishment', targetEntity: Program::class, cascade: ['persist', 'remove'])]
    private Collection $programsList;

    public function __construct()
    {
        $this->programsList = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
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

    public function getNameFr(): ?string
    {
        return $this->nameFr;
    }

    public function setNameFr(?string $nameFr): static
    {
        $this->nameFr = $nameFr;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;
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

    public function getStudents(): ?int
    {
        return $this->students;
    }

    public function setStudents(?int $students): static
    {
        $this->students = $students;
        return $this;
    }

    public function getPrograms(): ?int
    {
        return $this->programs;
    }

    public function setPrograms(?int $programs): static
    {
        $this->programs = $programs;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
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

    public function getMission(): ?string
    {
        return $this->mission;
    }

    public function setMission(?string $mission): static
    {
        $this->mission = $mission;
        return $this;
    }

    public function getMissionFr(): ?string
    {
        return $this->missionFr;
    }

    public function setMissionFr(?string $missionFr): static
    {
        $this->missionFr = $missionFr;
        return $this;
    }

    public function getFoundedYear(): ?int
    {
        return $this->foundedYear;
    }

    public function setFoundedYear(?int $foundedYear): static
    {
        $this->foundedYear = $foundedYear;
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

    public function isSponsored(): bool
    {
        return $this->sponsored;
    }

    public function setSponsored(bool $sponsored): static
    {
        $this->sponsored = $sponsored;
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

    public function getTuitionMin(): ?string
    {
        return $this->tuitionMin;
    }

    public function setTuitionMin(?string $tuitionMin): static
    {
        $this->tuitionMin = $tuitionMin;
        return $this;
    }

    public function getTuitionMax(): ?string
    {
        return $this->tuitionMax;
    }

    public function setTuitionMax(?string $tuitionMax): static
    {
        $this->tuitionMax = $tuitionMax;
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

    public function getAcceptanceRate(): ?string
    {
        return $this->acceptanceRate;
    }

    public function setAcceptanceRate(?string $acceptanceRate): static
    {
        $this->acceptanceRate = $acceptanceRate;
        return $this;
    }

    public function getWorldRanking(): ?int
    {
        return $this->worldRanking;
    }

    public function setWorldRanking(?int $worldRanking): static
    {
        $this->worldRanking = $worldRanking;
        return $this;
    }

    public function getQsRanking(): ?int
    {
        return $this->qsRanking;
    }

    public function setQsRanking(?int $qsRanking): static
    {
        $this->qsRanking = $qsRanking;
        return $this;
    }

    public function getTimesRanking(): ?int
    {
        return $this->timesRanking;
    }

    public function setTimesRanking(?int $timesRanking): static
    {
        $this->timesRanking = $timesRanking;
        return $this;
    }

    public function getArwuRanking(): ?int
    {
        return $this->arwuRanking;
    }

    public function setArwuRanking(?int $arwuRanking): static
    {
        $this->arwuRanking = $arwuRanking;
        return $this;
    }

    public function getUsNewsRanking(): ?int
    {
        return $this->usNewsRanking;
    }

    public function setUsNewsRanking(?int $usNewsRanking): static
    {
        $this->usNewsRanking = $usNewsRanking;
        return $this;
    }

    public function getPopularPrograms(): ?array
    {
        return $this->popularPrograms;
    }

    public function setPopularPrograms(?array $popularPrograms): static
    {
        $this->popularPrograms = $popularPrograms;
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

    public function isScholarships(): bool
    {
        return $this->scholarships;
    }

    public function setScholarships(bool $scholarships): static
    {
        $this->scholarships = $scholarships;
        return $this;
    }

    public function getScholarshipTypes(): ?array
    {
        return $this->scholarshipTypes;
    }

    public function setScholarshipTypes(?array $scholarshipTypes): static
    {
        $this->scholarshipTypes = $scholarshipTypes;
        return $this;
    }

    public function getScholarshipDescription(): ?string
    {
        return $this->scholarshipDescription;
    }

    public function setScholarshipDescription(?string $scholarshipDescription): static
    {
        $this->scholarshipDescription = $scholarshipDescription;
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

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): static
    {
        $this->language = $language;
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

    public function getUniversityType(): ?string
    {
        return $this->universityType;
    }

    public function setUniversityType(?string $universityType): static
    {
        $this->universityType = $universityType;
        return $this;
    }

    public function getCommissionRate(): ?string
    {
        return $this->commissionRate;
    }

    public function setCommissionRate(?string $commissionRate): static
    {
        $this->commissionRate = $commissionRate;
        return $this;
    }

    public function getFreeApplications(): ?int
    {
        return $this->freeApplications;
    }

    public function setFreeApplications(?int $freeApplications): static
    {
        $this->freeApplications = $freeApplications;
        return $this;
    }

    public function getVisaSupport(): ?string
    {
        return $this->visaSupport;
    }

    public function setVisaSupport(?string $visaSupport): static
    {
        $this->visaSupport = $visaSupport;
        return $this;
    }

    public function getCountrySpecific(): ?array
    {
        return $this->countrySpecific;
    }

    public function setCountrySpecific(?array $countrySpecific): static
    {
        $this->countrySpecific = $countrySpecific;
        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): static
    {
        $this->website = $website;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;
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

    public function isAccommodation(): bool
    {
        return $this->accommodation;
    }

    public function setAccommodation(bool $accommodation): static
    {
        $this->accommodation = $accommodation;
        return $this;
    }

    public function isCareerServices(): bool
    {
        return $this->careerServices;
    }

    public function setCareerServices(bool $careerServices): static
    {
        $this->careerServices = $careerServices;
        return $this;
    }

    public function isLanguageSupport(): bool
    {
        return $this->languageSupport;
    }

    public function setLanguageSupport(bool $languageSupport): static
    {
        $this->languageSupport = $languageSupport;
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

    /**
     * @return Collection<int, Program>
     */
    public function getProgramsList(): Collection
    {
        return $this->programsList;
    }

    public function addProgramsList(Program $programsList): static
    {
        if (!$this->programsList->contains($programsList)) {
            $this->programsList->add($programsList);
            $programsList->setEstablishment($this);
        }

        return $this;
    }

    public function removeProgramsList(Program $programsList): static
    {
        if ($this->programsList->removeElement($programsList)) {
            // set the owning side to null (unless already changed)
            if ($programsList->getEstablishment() === $this) {
                $programsList->setEstablishment(null);
            }
        }

        return $this;
    }

    public function getRankings(): array
    {
        return [
            'qs' => $this->qsRanking,
            'times' => $this->timesRanking,
            'arwu' => $this->arwuRanking,
            'usNews' => $this->usNewsRanking
        ];
    }

    public function getTuitionRange(): array
    {
        return [
            'min' => $this->tuitionMin,
            'max' => $this->tuitionMax,
            'currency' => $this->tuitionCurrency
        ];
    }

    public function getAdmissionRequirements(): ?array
    {
        return $this->admissionRequirements;
    }

    public function setAdmissionRequirements(?array $admissionRequirements): static
    {
        $this->admissionRequirements = $admissionRequirements;
        return $this;
    }

    public function getAdmissionRequirementsFr(): ?array
    {
        return $this->admissionRequirementsFr;
    }

    public function setAdmissionRequirementsFr(?array $admissionRequirementsFr): static
    {
        $this->admissionRequirementsFr = $admissionRequirementsFr;
        return $this;
    }

    public function getEnglishTestRequirements(): ?array
    {
        return $this->englishTestRequirements;
    }

    public function setEnglishTestRequirements(?array $englishTestRequirements): static
    {
        $this->englishTestRequirements = $englishTestRequirements;
        return $this;
    }

    public function getAcademicRequirements(): ?array
    {
        return $this->academicRequirements;
    }

    public function setAcademicRequirements(?array $academicRequirements): static
    {
        $this->academicRequirements = $academicRequirements;
        return $this;
    }

    public function getDocumentRequirements(): ?array
    {
        return $this->documentRequirements;
    }

    public function setDocumentRequirements(?array $documentRequirements): static
    {
        $this->documentRequirements = $documentRequirements;
        return $this;
    }

    public function getVisaRequirements(): ?array
    {
        return $this->visaRequirements;
    }

    public function setVisaRequirements(?array $visaRequirements): static
    {
        $this->visaRequirements = $visaRequirements;
        return $this;
    }

    public function getApplicationFee(): ?string
    {
        return $this->applicationFee;
    }

    public function setApplicationFee(?string $applicationFee): static
    {
        $this->applicationFee = $applicationFee;
        return $this;
    }

    public function getApplicationFeeCurrency(): ?string
    {
        return $this->applicationFeeCurrency;
    }

    public function setApplicationFeeCurrency(?string $applicationFeeCurrency): static
    {
        $this->applicationFeeCurrency = $applicationFeeCurrency;
        return $this;
    }

    public function getLivingCosts(): ?string
    {
        return $this->livingCosts;
    }

    public function setLivingCosts(?string $livingCosts): static
    {
        $this->livingCosts = $livingCosts;
        return $this;
    }

    public function getLivingCostsCurrency(): ?string
    {
        return $this->livingCostsCurrency;
    }

    public function setLivingCostsCurrency(?string $livingCostsCurrency): static
    {
        $this->livingCostsCurrency = $livingCostsCurrency;
        return $this;
    }

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $languages = null;

    #[ORM\Column(name: 'youtube_videos', type: Types::JSON, nullable: true)]
    private ?array $youtubeVideos = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $brochures = null;

    #[ORM\Column(name: 'campus_locations', type: Types::JSON, nullable: true)]
    private ?array $campusLocations = null;

    #[ORM\Column(name: 'campus_photos', type: Types::JSON, nullable: true)]
    private ?array $campusPhotos = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $status = 'draft';

    #[ORM\Column(name: 'seo_title', length: 255, nullable: true)]
    private ?string $seoTitle = null;

    #[ORM\Column(name: 'seo_description', type: Types::TEXT, nullable: true)]
    private ?string $seoDescription = null;

    #[ORM\Column(name: 'seo_keywords', type: Types::JSON, nullable: true)]
    private ?array $seoKeywords = null;

    #[ORM\Column(name: 'seo_image_alt', type: Types::TEXT, nullable: true)]
    private ?string $seoImageAlt = null;

    #[ORM\Column(name: 'service_pricing', type: Types::JSON, nullable: true)]
    private ?array $servicePricing = null;

    #[ORM\Column(name: 'multi_intakes', type: Types::JSON, nullable: true)]
    private ?array $multiIntakes = null;

    public function getLanguages(): ?array
    {
        return $this->languages;
    }

    public function setLanguages(?array $languages): static
    {
        $this->languages = $languages;
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

    public function getCampusLocations(): ?array
    {
        return $this->campusLocations;
    }

    public function setCampusLocations(?array $campusLocations): static
    {
        $this->campusLocations = $campusLocations;
        return $this;
    }

    public function getCampusPhotos(): ?array
    {
        return $this->campusPhotos;
    }

    public function setCampusPhotos(?array $campusPhotos): static
    {
        $this->campusPhotos = $campusPhotos;
        return $this;
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

    public function getSeoImageAlt(): ?string
    {
        return $this->seoImageAlt;
    }

    public function setSeoImageAlt(?string $seoImageAlt): static
    {
        $this->seoImageAlt = $seoImageAlt;
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

    public function getMultiIntakes(): ?array
    {
        return $this->multiIntakes;
    }

    public function setMultiIntakes(?array $multiIntakes): static
    {
        $this->multiIntakes = $multiIntakes;
        return $this;
    }
}
