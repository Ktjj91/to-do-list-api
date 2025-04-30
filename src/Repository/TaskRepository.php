<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    //    /**
    //     * @return Task[] Returns an array of Task objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }


    /**
     * @param User $user
     * @return Task[]
     */
        public function findALlTasksByUser(User $user): array
        {
            return $this->createQueryBuilder('t')
                ->andWhere('t.owner = :owner')
                ->setParameter('owner', $user)
                ->getQuery()
                ->getResult()
            ;
        }

        public function getOneByUserAndIdOrFail(User $user,int $id): Task
        {
          $task = $this->findOneBy([
                'owner' => $user,
                'id' => $id
            ]);

            if (!$task) {
                throw new NotFoundHttpException("Task #$id not found for this user.");
            }
            return $task;
        }
}
