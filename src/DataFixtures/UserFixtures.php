<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Repository\PlanRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Yaml\Yaml;

class UserFixtures extends Fixture
{
    public function __construct(UserPasswordHasherInterface $passwordHasher,PlanRepository $planRepository) {
        $this->passwordHasher = $passwordHasher;
        $this->planRepository = $planRepository;
    }
    public function load(ObjectManager $manager): void
    {
        $default_plan = $this->planRepository->findOneBy(['name'=>'Basic']);
        $userIndex = 0;
        $users = Yaml::parseFile(__DIR__ . '/users.yaml');
        foreach ($users as $userData) {
            $user = new User();
            $roles = [...$user->getRoles(),...$userData["roles"]];
            $createdAt = new \DateTimeImmutable($userData['created_at']);
            $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['password']);
            $user->setUsername($userData['username']);
            $user->setFirstname($userData['firstname']);
            $user->setLastname($userData['lastname']);
            $user->setEmail($userData['email']);
            $user->setPassword($hashedPassword);
            $user->setToken(Uuid::uuid4());
            $user->setRoles($roles);
            $user->setCreatedAt($createdAt);
            $user->setPlan($default_plan);
            $user->setNbToke(0);
            $manager->persist($user);
            $userIndex++;
            $this->addReference('user_' . $userIndex, $user);
        }

        $manager->flush();
    }
}

