<?php
declare(strict_types=1);

namespace App\Controller\V1;

use App\Constant\Role;
use App\Entity\User;
use App\Model\Response\Error\UnauthorizedResponse;
use App\Model\Response\User\UserResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user')]
#[OA\Tag(name: 'User')]
class UserController extends AbstractController
{
    #[Route('/{user}', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'User details',
        content: new OA\JsonContent(ref: new Model(type: UserResponse::class)),
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized',
        content: new OA\JsonContent(ref: new Model(type: UnauthorizedResponse::class)),
    )]
    #[IsGranted(Role::ROLE_USER)]
    public function get(User $user): JsonResponse
    {
        return new JsonResponse(UserResponse::fromUser($user));
    }
}
