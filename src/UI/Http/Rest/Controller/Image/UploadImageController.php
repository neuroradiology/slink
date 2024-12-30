<?php

declare(strict_types=1);

namespace UI\Http\Rest\Controller\Image;

use Slink\Image\Application\Command\UploadImage\UploadImageCommand;
use Slink\Shared\Application\Command\CommandTrait;
use Slink\Shared\Application\Http\RequestValueResolver\FileRequestValueResolver;
use Slink\User\Application\Service\AnonymousUserManagerInterface;
use Slink\User\Domain\Enum\UserType;
use Slink\User\Infrastructure\Auth\JwtUser;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use UI\Http\Rest\Response\ApiResponse;

#[AsController]
#[Route(path: '/public/upload', name: 'upload_image', methods: ['POST', 'PUT'])]
final readonly class UploadImageController {
  use CommandTrait;
  
  public function __construct(
    private AnonymousUserManagerInterface $anonymousUserManager,
  ) {
  }
  
  public function __invoke(
    #[MapRequestPayload(
      resolver: FileRequestValueResolver::class
    )] UploadImageCommand $command,
    #[CurrentUser] ?JWTUser $user = null
  ): ApiResponse {
    $tempUserIdentifier = $this->anonymousUserManager->getTempUserIdFromAuth($user);
    
    if ($tempUserIdentifier) {
      $command->forcePublic();
    }
    
    $this->handle($command->withContext([
      'userId' => $user?->getIdentifier() ?? $tempUserIdentifier,
    ]));
    
    $response = ApiResponse::created(
      $command->id->toString(),
      "image/{$command->id}/detail"
    );
    
    if (!$tempUserIdentifier) {
      return $response;
    }
    
    return $response->withCookie(
      UserType::ANONYMOUS->getCookieName(),
      $tempUserIdentifier,
    );
  }
}