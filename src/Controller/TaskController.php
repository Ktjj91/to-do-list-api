<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/tasks')]
final class TaskController extends AbstractController
{
    public function __construct(private readonly TaskRepository $taskRepository,
    private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/', methods: ['GET'])]
    public function getAllTasks(): JsonResponse
    {
        $tasks = $this->taskRepository->findAll();
        return $this->json(['data' => $tasks],context: ['groups' => 'task.read']);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getTask(int $id): JsonResponse
    {
        $task = $this->taskRepository->find($id);
        if(!$task){
            throw new NotFoundHttpException();
        }
        return $this->json(['data' => $task],context: ['groups' => 'task.read']);
    }

    #[Route('/', methods: ['POST'])]
    public function createTask
    (
        #[MapRequestPayload(
            serializationContext: ['groups' => 'task.create'],
        )]
        Task $task
    )
    : JsonResponse
    {
        $task->setIsDone(false);
        $task->setDueDate(new \DateTime());
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $this->json([],Response::HTTP_CREATED,context: ['groups' => 'task.read']);
    }

    #[Route('/{id}', methods: ['PATCH'])]
    public function updateTask(int $id,
                               #[MapRequestPayload(
                                   serializationContext: ['groups' => 'task.update'],
                               )]
                               Task $incoming
    ): JsonResponse
    {
        $existingTask = $this->taskRepository->find($id);
        if (!$existingTask) {
            throw new NotFoundHttpException("Task #{$id} not found.");

        }
        if (null !== $incoming->getTitle()) {
            $existingTask->setTitle($incoming->getTitle());
        }
        if (null !== $incoming->getDescription()) {
            $existingTask->setDescription($incoming->getDescription());
        }
        if (null !== $incoming->getDueDate()) {
            $existingTask->setDueDate($incoming->getDueDate());
        }
        if (null !== $incoming->isDone()) {
            $existingTask->setIsDone($incoming->isDone());
        }
        $this->entityManager->flush();

        return $this->json([], Response::HTTP_NO_CONTENT, [], ['groups' => 'task.read']
        );
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function deleteTask(int $id): JsonResponse {

        $task = $this->taskRepository->find($id);

        if(!$task){
            throw new NotFoundHttpException("Task #{$id} not found.");
        }

        $this->entityManager->remove($task);
        $this->entityManager->flush();

        return $this->json([], Response::HTTP_NO_CONTENT, [], ['groups' => 'task.read']);
    }
}
