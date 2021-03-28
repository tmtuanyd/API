<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class ResetPasswordActionController
{
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var JWTTokenManagerInterface
     */
    private $tokenManager;

    public function __construct(
        ValidatorInterface $validator,
        UserPasswordEncoderInterface $userPasswordEncoder,
        EntityManagerInterface $entityManager,
        JWTTokenManagerInterface $tokenManager
    )
    {

        $this->validator = $validator;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->entityManager = $entityManager;
        $this->tokenManager = $tokenManager;
    }
    public function __invoke(User $data)
    {
        //$reset = new ResetPasswordActionController();
        //$reset();
//        var_dump(
//            $data->getNewPassword(),
//            $data->getNewRetypedPassword(),
//            $data->getOldPassword(),
//            $data->getRetypedPassword()
//        ); die;

        //validator is only called after we return the data from this action
        $this->validator->validate($data);
        $data->setPassword(
          $this->userPasswordEncoder->encodePassword(
              $data, $data->getNewPassword()
          )
        );
        //After password change, old tokens are still valid
        $data->setPasswordChangeDate(time());
        $this->entityManager->flush();
        $token = $this->tokenManager->create($data);
        return new JsonResponse(['token'=>$token]);
        //Only hear it checks for user current password, but we've just modified it
        //Entity is persisted automatically, only validation pass
    }
}
