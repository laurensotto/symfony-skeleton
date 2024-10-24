<?php
declare(strict_types=1);

namespace App\Controller\V1;

use App\Constant\Role;
use App\Entity\User;
use App\Model\Factory\UserFactory;
use App\Model\Request\User\CreateUserRequest;
use App\Model\Response\Error\UnauthorizedResponse;
use App\Model\Response\Error\ValidationErrorResponse;
use App\Model\Response\User\UserResponse;
use App\Security\UserProvider;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user')]
#[OA\Tag(name: 'User')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserFactory $userFactory,
        private readonly UserProvider $userProvider,
    ) {
    }

    #[OA\Get(
        description: 'Details of a user',
        responses: [
            new OA\Response(
                response: 200,
                description: 'User details',
                content: new OA\JsonContent(ref: new Model(type: UserResponse::class)),
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: new Model(type: UnauthorizedResponse::class)),
            ),
        ]
    )]
    #[Route('/{user}', methods: ['GET'])]
    #[IsGranted(Role::ROLE_ADMIN)]
    public function get(User $user): JsonResponse
    {
        return new JsonResponse(UserFactory::responseFromEntity($user));
    }

    #[OA\Post(
        description: 'Create a user',
        requestBody: new OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: CreateUserRequest::class))),
        responses: [
            new OA\Response(
                response: 200,
                description: 'User details',
                content: new OA\JsonContent(ref: new Model(type: UserResponse::class)),
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: new Model(type: UnauthorizedResponse::class)),
            ),
            new OA\Response(
                response: 422,
                description: 'Error: Unprocessable Content',
                content: new OA\JsonContent(ref: new Model(type: ValidationErrorResponse::class)),
            ),
        ]
    )]
    #[Route('/', methods: ['POST'])]
    #[IsGranted(Role::ROLE_ADMIN)]
    public function post(#[MapRequestPayload] CreateUserRequest $request): JsonResponse
    {
        $user = $this->userFactory->entityFromRequest($request);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(UserFactory::responseFromEntity($user));
    }

    #[OA\Get(
        description: 'Details of the current user',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Details of the current user',
                content: new OA\JsonContent(ref: new Model(type: UserResponse::class)),
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: new Model(type: UnauthorizedResponse::class)),
            ),
        ]
    )]
    #[Route('/', methods: ['GET'])]
    #[IsGranted(Role::ROLE_USER)]
    public function me(): JsonResponse
    {
        return new JsonResponse(UserFactory::responseFromEntity($this->userProvider->getUser()));
    }
}
