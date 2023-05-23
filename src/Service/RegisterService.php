<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\PlanRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Twig\Environment;

class RegisterService
{
    public function __construct(
        string $imageDirectory,
        UserPasswordHasherInterface $passwordHasher,
        MailService $mailService,
        EntityManagerInterface $em,
        Environment $twig,
        UserRepository $userRepository,
        FileService $fs,
        PlanRepository $planRepository
    ) {
        $this->passwordHasher = $passwordHasher;
        $this->mailService = $mailService;
        $this->em = $em;
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->fs = $fs;
        $this->imageDirectory = $imageDirectory;
        $this->planRepository = $planRepository;
    }
    public function register(User $user): void {

        $default_plan = $this->planRepository->findOneBy(['name'=>'Basic']);
        $token = Uuid::uuid4();
        $date = new DateTimeImmutable('now');
        $hash = $this->passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hash);
        $user->setCreatedAt($date);
        $user->setToken($token);
        $user->setPlan($default_plan);
        $this->em->persist($user);
        $this->em->flush();
        $this->sendEmailConfirmation($user, $token);
    }

    private function sendEmailConfirmation(User $user, string $token): void {
        $emailTemplate = $this->twig->render('emails/accountVerification.html.twig', [
            'token' =>  $token,
            'firstname' => $user->getFirstname()
        ]);
        $this->mailService->sendMail([
            'sender' => array('name' => 'ESGI', 'email' => 'ali.fatoori@gmail.com'),
            'htmlContent' => $emailTemplate,
            'to' => array(
                array('email' => $user->getEmail(), 'name' => "{$user->getFirstname()} {$user->getLastname()}")
            ),
            'subject' => 'Email confirmation'
        ]);
    }

    public function checkUserToken(string $token) {
        return $this->userRepository->findOneBy(['token' => $token]);
    }
}