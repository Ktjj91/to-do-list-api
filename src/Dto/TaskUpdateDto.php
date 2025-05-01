<?php

namespace App\Dto;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;


class TaskUpdateDto
{

    #[Groups('task.update')]
    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    #[Assert\Length(min: 1, max: 255)]
    private ?string $title = null;
    #[Groups('task.update')]
    #[Assert\Length(max:1000)]
    private ?string $description = null;
    #[Groups('task.update')]
    private ?string $dueDate = null;
    #[Groups('task.update')]
    #[Assert\Type('boolean')]
    private ?bool  $isDone = null;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getDueDate(): ?string
    {
        return $this->dueDate;
    }

    public function getIsDone(): ?bool
    {
        return $this->isDone;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function setDueDate(?string $dueDate): void
    {
        $this->dueDate = $dueDate;
    }

    public function setIsDone(?bool $isDone): void
    {
        $this->isDone = $isDone;
    }






}