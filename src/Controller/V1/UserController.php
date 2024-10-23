<?php
declare(strict_types=1);

namespace App\Controller\V1;

use App\Model\Response\UserResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

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
    public function get(string $user): JsonResponse
    {
        return new JsonResponse(new UserResponse($user));
    }
}
