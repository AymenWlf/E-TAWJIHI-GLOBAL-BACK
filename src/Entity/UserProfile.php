<?php

namespace App\Entity;

use App\Repository\UserProfileRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: UserProfileRepository::class)]
#[ORM\Table(name: 'user_profiles')]
class UserProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'profile')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $nationality = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $whatsapp = null;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $phoneCountry = null;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $whatsappCountry = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $passportNumber = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $passportExpirationDate = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $cinNumber = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $gender = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $maritalStatus = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $countryOfBirth = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $cityOfBirth = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $alternateEmail = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $religion = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $nativeLanguage = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $chineseName = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $wechatId = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $skypeNo = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $emergencyContactName = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $emergencyContactGender = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $emergencyContactRelationship = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $emergencyContactPhone = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $emergencyContactEmail = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $emergencyContactAddress = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $hasWorkExperience = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $workCompany = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $workPosition = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $workStartDate = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $workEndDate = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $workDescription = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $address = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $postalCode = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateOfBirth = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $studyLevel = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $fieldOfStudy = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $preferredCountry = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $startDate = null;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $preferredCurrency = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $annualBudget = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $scholarshipRequired = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $languagePreferences = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $onboardingProgress = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $step2Validated = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $step4Validated = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $step5Validated = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $preferredDestinations = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $preferredIntakes = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $preferredSubjects = null;

    // China-specific fields
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $chinaFamilyMembers = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'userProfile', targetEntity: Qualification::class, cascade: ['persist', 'remove'])]
    private Collection $qualifications;

    #[ORM\OneToMany(mappedBy: 'userProfile', targetEntity: Document::class, cascade: ['persist', 'remove'])]
    private Collection $documents;

    #[ORM\OneToMany(mappedBy: 'userProfile', targetEntity: Application::class, cascade: ['persist', 'remove'])]
    private Collection $applications;

    #[ORM\OneToMany(mappedBy: 'userProfile', targetEntity: Shortlist::class, cascade: ['persist', 'remove'])]
    private Collection $shortlist;

    public function __construct()
    {
        $this->qualifications = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->applications = new ArrayCollection();
        $this->shortlist = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getFullName(): string
    {
        return trim($this->firstName . ' ' . $this->lastName);
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

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getNationality(): ?array
    {
        return $this->nationality;
    }

    public function setNationality(?array $nationality): self
    {
        $this->nationality = $nationality;
        return $this;
    }

    public function getPassportNumber(): ?string
    {
        return $this->passportNumber;
    }

    public function setPassportNumber(?string $passportNumber): self
    {
        $this->passportNumber = $passportNumber;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getWhatsapp(): ?string
    {
        return $this->whatsapp;
    }

    public function setWhatsapp(?string $whatsapp): self
    {
        $this->whatsapp = $whatsapp;
        return $this;
    }

    public function getPhoneCountry(): ?string
    {
        return $this->phoneCountry;
    }

    public function setPhoneCountry(?string $phoneCountry): self
    {
        $this->phoneCountry = $phoneCountry;
        return $this;
    }

    public function getWhatsappCountry(): ?string
    {
        return $this->whatsappCountry;
    }

    public function setWhatsappCountry(?string $whatsappCountry): self
    {
        $this->whatsappCountry = $whatsappCountry;
        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;
        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;
        return $this;
    }

    public function getStudyLevel(): ?string
    {
        return $this->studyLevel;
    }

    public function setStudyLevel(?string $studyLevel): self
    {
        $this->studyLevel = $studyLevel;
        return $this;
    }

    public function getFieldOfStudy(): ?string
    {
        return $this->fieldOfStudy;
    }

    public function setFieldOfStudy(?string $fieldOfStudy): self
    {
        $this->fieldOfStudy = $fieldOfStudy;
        return $this;
    }

    public function getPreferredCountry(): ?string
    {
        return $this->preferredCountry;
    }

    public function setPreferredCountry(?string $preferredCountry): self
    {
        $this->preferredCountry = $preferredCountry;
        return $this;
    }

    public function getStartDate(): ?string
    {
        return $this->startDate;
    }

    public function setStartDate(?string $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getPreferredCurrency(): ?string
    {
        return $this->preferredCurrency;
    }

    public function setPreferredCurrency(?string $preferredCurrency): self
    {
        $this->preferredCurrency = $preferredCurrency;
        return $this;
    }

    public function getAnnualBudget(): ?string
    {
        return $this->annualBudget;
    }

    public function setAnnualBudget(?string $annualBudget): self
    {
        $this->annualBudget = $annualBudget;
        return $this;
    }

    public function isScholarshipRequired(): ?bool
    {
        return $this->scholarshipRequired;
    }

    public function setScholarshipRequired(?bool $scholarshipRequired): self
    {
        $this->scholarshipRequired = $scholarshipRequired;
        return $this;
    }

    public function getLanguagePreferences(): ?array
    {
        return $this->languagePreferences;
    }

    public function setLanguagePreferences(?array $languagePreferences): self
    {
        $this->languagePreferences = $languagePreferences;
        return $this;
    }

    public function getOnboardingProgress(): ?array
    {
        return $this->onboardingProgress;
    }

    public function setOnboardingProgress(?array $onboardingProgress): self
    {
        $this->onboardingProgress = $onboardingProgress;
        return $this;
    }

    public function getStep2Validated(): ?bool
    {
        return $this->step2Validated;
    }

    public function setStep2Validated(?bool $step2Validated): self
    {
        $this->step2Validated = $step2Validated;
        return $this;
    }

    public function getStep4Validated(): ?bool
    {
        return $this->step4Validated;
    }

    public function setStep4Validated(?bool $step4Validated): self
    {
        $this->step4Validated = $step4Validated;
        return $this;
    }

    public function getStep5Validated(): ?bool
    {
        return $this->step5Validated;
    }

    public function setStep5Validated(?bool $step5Validated): self
    {
        $this->step5Validated = $step5Validated;
        return $this;
    }

    public function getPreferredDestinations(): ?array
    {
        return $this->preferredDestinations;
    }

    public function setPreferredDestinations(?array $preferredDestinations): self
    {
        $this->preferredDestinations = $preferredDestinations;
        return $this;
    }

    public function getPreferredIntakes(): ?array
    {
        return $this->preferredIntakes;
    }

    public function setPreferredIntakes(?array $preferredIntakes): self
    {
        $this->preferredIntakes = $preferredIntakes;
        return $this;
    }

    public function getPreferredSubjects(): ?array
    {
        return $this->preferredSubjects;
    }

    public function setPreferredSubjects(?array $preferredSubjects): self
    {
        $this->preferredSubjects = $preferredSubjects;
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

    /**
     * @return Collection<int, Qualification>
     */
    public function getQualifications(): Collection
    {
        return $this->qualifications;
    }

    public function addQualification(Qualification $qualification): self
    {
        if (!$this->qualifications->contains($qualification)) {
            $this->qualifications->add($qualification);
            $qualification->setUserProfile($this);
        }
        return $this;
    }

    public function removeQualification(Qualification $qualification): self
    {
        if ($this->qualifications->removeElement($qualification)) {
            if ($qualification->getUserProfile() === $this) {
                $qualification->setUserProfile(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setUserProfile($this);
        }
        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->removeElement($document)) {
            if ($document->getUserProfile() === $this) {
                $document->setUserProfile(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Application>
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Application $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications->add($application);
            $application->setUserProfile($this);
        }
        return $this;
    }

    public function removeApplication(Application $application): self
    {
        if ($this->applications->removeElement($application)) {
            if ($application->getUserProfile() === $this) {
                $application->setUserProfile(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Shortlist>
     */
    public function getShortlist(): Collection
    {
        return $this->shortlist;
    }

    public function addShortlist(Shortlist $shortlist): self
    {
        if (!$this->shortlist->contains($shortlist)) {
            $this->shortlist->add($shortlist);
            $shortlist->setUserProfile($this);
        }
        return $this;
    }

    public function removeShortlist(Shortlist $shortlist): self
    {
        if ($this->shortlist->removeElement($shortlist)) {
            if ($shortlist->getUserProfile() === $this) {
                $shortlist->setUserProfile(null);
            }
        }
        return $this;
    }

    public function getPassportExpirationDate(): ?\DateTimeInterface
    {
        return $this->passportExpirationDate;
    }

    public function setPassportExpirationDate(?\DateTimeInterface $passportExpirationDate): self
    {
        $this->passportExpirationDate = $passportExpirationDate;
        return $this;
    }

    public function getCinNumber(): ?string
    {
        return $this->cinNumber;
    }

    public function setCinNumber(?string $cinNumber): self
    {
        $this->cinNumber = $cinNumber;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
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

    public function getMaritalStatus(): ?string
    {
        return $this->maritalStatus;
    }

    public function setMaritalStatus(?string $maritalStatus): self
    {
        $this->maritalStatus = $maritalStatus;
        return $this;
    }

    public function getCountryOfBirth(): ?string
    {
        return $this->countryOfBirth;
    }

    public function setCountryOfBirth(?string $countryOfBirth): self
    {
        $this->countryOfBirth = $countryOfBirth;
        return $this;
    }

    public function getCityOfBirth(): ?string
    {
        return $this->cityOfBirth;
    }

    public function setCityOfBirth(?string $cityOfBirth): self
    {
        $this->cityOfBirth = $cityOfBirth;
        return $this;
    }

    public function getAlternateEmail(): ?string
    {
        return $this->alternateEmail;
    }

    public function setAlternateEmail(?string $alternateEmail): self
    {
        $this->alternateEmail = $alternateEmail;
        return $this;
    }

    public function getReligion(): ?string
    {
        return $this->religion;
    }

    public function setReligion(?string $religion): self
    {
        $this->religion = $religion;
        return $this;
    }

    public function getNativeLanguage(): ?string
    {
        return $this->nativeLanguage;
    }

    public function setNativeLanguage(?string $nativeLanguage): self
    {
        $this->nativeLanguage = $nativeLanguage;
        return $this;
    }

    public function getChineseName(): ?string
    {
        return $this->chineseName;
    }

    public function setChineseName(?string $chineseName): self
    {
        $this->chineseName = $chineseName;
        return $this;
    }

    public function getWechatId(): ?string
    {
        return $this->wechatId;
    }

    public function setWechatId(?string $wechatId): self
    {
        $this->wechatId = $wechatId;
        return $this;
    }

    public function getSkypeNo(): ?string
    {
        return $this->skypeNo;
    }

    public function setSkypeNo(?string $skypeNo): self
    {
        $this->skypeNo = $skypeNo;
        return $this;
    }

    public function getEmergencyContactName(): ?string
    {
        return $this->emergencyContactName;
    }

    public function setEmergencyContactName(?string $emergencyContactName): self
    {
        $this->emergencyContactName = $emergencyContactName;
        return $this;
    }

    public function getEmergencyContactGender(): ?string
    {
        return $this->emergencyContactGender;
    }

    public function setEmergencyContactGender(?string $emergencyContactGender): self
    {
        $this->emergencyContactGender = $emergencyContactGender;
        return $this;
    }

    public function getEmergencyContactRelationship(): ?string
    {
        return $this->emergencyContactRelationship;
    }

    public function setEmergencyContactRelationship(?string $emergencyContactRelationship): self
    {
        $this->emergencyContactRelationship = $emergencyContactRelationship;
        return $this;
    }

    public function getEmergencyContactPhone(): ?string
    {
        return $this->emergencyContactPhone;
    }

    public function setEmergencyContactPhone(?string $emergencyContactPhone): self
    {
        $this->emergencyContactPhone = $emergencyContactPhone;
        return $this;
    }

    public function getEmergencyContactEmail(): ?string
    {
        return $this->emergencyContactEmail;
    }

    public function setEmergencyContactEmail(?string $emergencyContactEmail): self
    {
        $this->emergencyContactEmail = $emergencyContactEmail;
        return $this;
    }

    public function getEmergencyContactAddress(): ?string
    {
        return $this->emergencyContactAddress;
    }

    public function setEmergencyContactAddress(?string $emergencyContactAddress): self
    {
        $this->emergencyContactAddress = $emergencyContactAddress;
        return $this;
    }

    public function getHasWorkExperience(): ?bool
    {
        return $this->hasWorkExperience;
    }

    public function setHasWorkExperience(?bool $hasWorkExperience): self
    {
        $this->hasWorkExperience = $hasWorkExperience;
        return $this;
    }

    public function getWorkCompany(): ?string
    {
        return $this->workCompany;
    }

    public function setWorkCompany(?string $workCompany): self
    {
        $this->workCompany = $workCompany;
        return $this;
    }

    public function getWorkPosition(): ?string
    {
        return $this->workPosition;
    }

    public function setWorkPosition(?string $workPosition): self
    {
        $this->workPosition = $workPosition;
        return $this;
    }

    public function getWorkStartDate(): ?\DateTimeInterface
    {
        return $this->workStartDate;
    }

    public function setWorkStartDate(?\DateTimeInterface $workStartDate): self
    {
        $this->workStartDate = $workStartDate;
        return $this;
    }

    public function getWorkEndDate(): ?\DateTimeInterface
    {
        return $this->workEndDate;
    }

    public function setWorkEndDate(?\DateTimeInterface $workEndDate): self
    {
        $this->workEndDate = $workEndDate;
        return $this;
    }

    public function getWorkDescription(): ?string
    {
        return $this->workDescription;
    }

    public function setWorkDescription(?string $workDescription): self
    {
        $this->workDescription = $workDescription;
        return $this;
    }

    public function getChinaFamilyMembers(): ?array
    {
        return $this->chinaFamilyMembers;
    }

    public function setChinaFamilyMembers(?array $chinaFamilyMembers): self
    {
        $this->chinaFamilyMembers = $chinaFamilyMembers;
        return $this;
    }
}
