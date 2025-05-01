<?php

namespace App\Controller;

use App\Dto\TaskUpdateDto;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Requirement\Requirement;

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
        $user = $this->getUser();
        if(!$user){
            return  $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $tasks = $this->taskRepository->findALlTasksByUser($user);
        return $this->json(['data' => $tasks],context: ['groups' => 'task.read']);
    }

    #[Route('/{id}', name:'app_task_gettask', requirements: ['id' =>Requirement::DIGITS], methods: ['GET'])]
    public function getTask(int $id): JsonResponse
    {
        $task = $this->taskRepository->getOneByUserAndIdOrFail($this->getUser() ,$id);
        return $this->json(['data' => $task],context: ['groups' => 'task.read']);
    }

    #[Route('/', methods: ['POST'])]
    public function createTask
    (
        UrlGeneratorInterface $urlGenerator,
        #[MapRequestPayload(
            serializationContext: ['groups' => 'task.create'],
        )]
        Task $task
    )
    : JsonResponse
    {
        $user = $this->getUser();
        $task->setIsDone(false);
        $task->setDueDate(new \DateTime());
        $task->setOwner($user);
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $location = $urlGenerator->generate(
            'app_task_gettask',
            ['id' => $task->getId()])
        ;

        return $this->json(
            '',
            Response::HTTP_CREATED,
            headers: ['Location' => $location],
            context: ['groups' => 'task.read']
        );
    }

    #[Route('/{id}', methods: ['PATCH'])]
    public function updateTask(
        Task $task,
        #[MapRequestPayload()]
        TaskUpdateDto $taskUpdateDto
    ): JsonResponse
    {
        $task->updateFromDto($taskUpdateDto);
        $this->entityManager->flush();
        return $this->json('', Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function deleteTask(int $id): JsonResponse {

        $task = $this->taskRepository->find($id);
        $user = $this->getUser();

        if(!$task){
            throw new NotFoundHttpException("Task #{$id} not found.");
        }

        if($user->getId() !== $task->getOwner()->getId()){
            throw new AccessDeniedHttpException("You don't own this task.");
        }

        $this->entityManager->remove($task);
        $this->entityManager->flush();

        return $this->json('', Response::HTTP_NO_CONTENT, [], ['groups' => 'task.read']);
    }
}
