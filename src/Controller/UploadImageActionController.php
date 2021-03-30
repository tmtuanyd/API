<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\Exception\ValidationException;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Image;
use App\Form\ImageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;


class UploadImageActionController extends AbstractController
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    )
    {

        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }
   public function __invoke(Request $request)
   {
       // Create a new image instance
       $image = new Image();
       //validate the form
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);
        var_dump($form->isSubmitted());
        if($form->isSubmitted() && $form->isValid())
        {
            //persist the new image entity
            $this->entityManager->persist($image);
            $this->entityManager->flush();
            $image->setFile(null);
            return $image;
        }

       //uploading done for us in background by VichUploader
       //throw an validation exception, that means something went wrong during form validation
       throw new ValidationException(
           $this->validator->validate($image)
       );
   }
}
