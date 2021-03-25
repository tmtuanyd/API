<?php


namespace App\Serializer;


use ApiPlatform\Core\Exception\RuntimeException;
use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
//su dung serializer cho thang admin (cho tung quyen)
class UserContextBuild implements SerializerContextBuilderInterface
{
    /**
     * @var SerializerContextBuilderInterface
     */
    private $decorated;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(
        SerializerContextBuilderInterface $decorated,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->decorated = $decorated;
        $this->authorizationChecker = $authorizationChecker;
    }
    /**
     * @param Request $request
     * @param bool $normalization
     * @param array|null $extractedAttributes
     * @return array
     */
    public function createFromRequest(
        Request $request,
        bool $normalization,
        array $extractedAttributes = null
    ): array
    {
        // TODO: Implement createFromRequest() method.
        $context = $this->decorated->createFromRequest(
            $request, $normalization, $extractedAttributes
        );
        //class being serialized/deserialized
        $resourceClass = $context['resource_class'] ?? null; //default to null if not set
        if(
            User::class === $resourceClass &&
            isset($context['groups']) &&
            $normalization === true &&
            $this->authorizationChecker->isGranted(User::ROLE_ADMIN)
        )
        {
            $context['groups'][] = 'get-admin';
        }
        return $context;

    }
}