<?php


namespace App\Serializer;


use App\Entity\User;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;
use Symfony\Component\Serializer\SerializerInterface;

class UserAttributeNormalizer implements ContextAwareNormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;
    const USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED = 'USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED';

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {

        $this->tokenStorage = $tokenStorage;
    }
    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        // TODO: Implement supportsNormalization() method.
        if(isset($context[self::USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED])){
            return false;
        }
        return $data instanceof User;
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        // TODO: Implement normalize() method.
        if ($this->isUserHimseft($object)) {
            $context['groups'][] = 'get-owner';
        }

        //Now continue with serialization
        return $this->passOn($object, $format, $context);
    }

    private function isUserHimseft($object)
    {
        return $object->getUsername() === $this->tokenStorage->getToken()->getUsername();
    }

    private function passOn($object, ?string $format, array $context)
    {
        if($this->serializer instanceof NormalizableInterface)
        {
            throw new \LogicException(sprintf('Cannot normalize object "$s" because the injected serializer is not a normalizer.', $object));
        }
        $context[self::USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED] = true;
        return $this->serializer->normalize($object, $format, $context);
    }

}