<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $user = new User();
        $user->setEmail("root@gmail.com");
        $password = $this->passwordHasher->hashPassword($user, "root");
        $user->setPassword($password);
        $manager->persist($user);

        for ($i = 0; $i <= 10; $i++) {
            $task = new Task();
            $task->setTitle("Task " . $i);
            $task->setDescription("Description " . $i);
            $task->setDueDate(new \DateTime());
            $task->setIsDone(false);
            $task->setowner($user);
            $manager->persist($task);
        }
        $manager->flush();
    }
}
