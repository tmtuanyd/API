<?php


namespace App\Security;


use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserConfirmationService
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    )
    {

        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    public function confirmUser(string $confirmationToken)
    {
        $user = $this->userRepository->findOneBy(
            ['confirmationToken'=>$confirmationToken]
        );

        //User was found by confimation token
        if(!$user) {
            throw new NotFoundHttpException();
        }
        $user -> setEnabled(true);
        $user->setConfirmationToken(null);
        $this->entityManager->flush();
    }
}
