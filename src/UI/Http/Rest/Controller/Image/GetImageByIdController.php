<?php

declare(strict_types=1);

namespace UI\Http\Rest\Controller\Image;

use Slink\Image\Application\Query\GetImageById\GetImageByIdQuery;
use Slink\Shared\Application\Query\QueryTrait;
use Slink\User\Infrastructure\Auth\JwtUser;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use UI\Http\Rest\Response\ApiResponse;

#[AsController]
#[Route(path: '/public/image/{id}/detail', name: 'get_image_by_id', methods: ['GET'])]
final class GetImageByIdController {
  use QueryTrait;
  
  public function __invoke(
    string $id,
    #[CurrentUser] ?JWTUser $user = null,
  ): ApiResponse {
    $query = new GetImageByIdQuery($id);
    
    $image = $this->ask($query->withContext([
      'userId' => $user?->getIdentifier(),
    ]));
    
    return ApiResponse::one($image);
  }
}