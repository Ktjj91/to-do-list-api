<?php

namespace App\Entity;

use App\Dto\TaskUpdateDto;
use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['task.read'])]
    private ?int $id = null;
    #[Groups(['task.read','task.create','task.update'])]
    #[ORM\Column(length: 255)]
    private ?string $title = null;


    #[Groups(['task.read','task.create','task.update'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['task.read','task.create','task.update'])]
    private ?\DateTime $dueDate = null;

    #[ORM\Column]
    #[Groups(['task.read','task.update'])]
    private ?bool $isDone = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?User $owner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    public function getDueDate(): ?\DateTime
    {
        return $this->dueDate;
    }

    public function setDueDate(\DateTime $dueDate): static
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function isDone(): ?bool
    {
        return $this->isDone;
    }

    public function setIsDone(bool $isDone): static
    {
        $this->isDone = $isDone;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

     public function updateFromDto(TaskUpdateDto $dto): void
 {
     if (null !== $dto->getTitle()) {
         $this->setTitle($dto->getTitle());
     }
     if (null !== $dto->getDescription()) {
         $this->setDescription($dto->getDescription());
     }
     if (null !== $dto->getDueDate()) {
         $this->setDueDate($dto->getDueDate());
     }
     if (null !== $dto->getIsDone()) {
         $this->setIsDone($dto->getIsDone());
     }
 }
}
