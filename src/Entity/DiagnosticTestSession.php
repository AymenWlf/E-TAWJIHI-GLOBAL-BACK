<?php

namespace App\Entity;

use App\Repository\DiagnosticTestSessionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiagnosticTestSessionRepository::class)]
#[ORM\Table(name: 'diagnostic_test_sessions')]
class DiagnosticTestSession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::JSON)]
    private array $answers = []; // {questionId: answer, ...}

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $scores = null; // {category: score, ...}

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $diagnosticResult = null; // Résultat généré par E-DVISOR

    #[ORM\Column(length: 20, options: ['default' => 'in_progress'])]
    private string $status = 'in_progress'; // 'in_progress', 'completed', 'abandoned'

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $currentQuestionIndex = 0;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $completedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->startedAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->answers = [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getAnswers(): array
    {
        return $this->answers;
    }

    public function setAnswers(array $answers): static
    {
        $this->answers = $answers;
        return $this;
    }

    public function addAnswer(int $questionId, $answer): static
    {
        $this->answers[$questionId] = $answer;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getScores(): ?array
    {
        return $this->scores;
    }

    public function setScores(?array $scores): static
    {
        $this->scores = $scores;
        return $this;
    }

    public function getDiagnosticResult(): ?string
    {
        return $this->diagnosticResult;
    }

    public function setDiagnosticResult(?string $diagnosticResult): static
    {
        $this->diagnosticResult = $diagnosticResult;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        if ($status === 'completed' && !$this->completedAt) {
            $this->completedAt = new \DateTime();
        }
        return $this;
    }

    public function getCurrentQuestionIndex(): int
    {
        return $this->currentQuestionIndex;
    }

    public function setCurrentQuestionIndex(int $currentQuestionIndex): static
    {
        $this->currentQuestionIndex = $currentQuestionIndex;
        return $this;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeInterface $startedAt): static
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function getCompletedAt(): ?\DateTimeInterface
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeInterface $completedAt): static
    {
        $this->completedAt = $completedAt;
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
}

