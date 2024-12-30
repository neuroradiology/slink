<?php

declare(strict_types=1);

namespace Slink\Image\Application\Query\GetImageById;

use Doctrine\ORM\NonUniqueResultException;
use Ramsey\Uuid\Uuid;
use Slink\Image\Domain\Repository\ImageRepositoryInterface;
use Slink\Image\Domain\Service\ImageAnalyzerInterface;
use Slink\Image\Infrastructure\ReadModel\View\ImageView;
use Slink\Shared\Application\Http\Item;
use Slink\Shared\Application\Query\QueryHandlerInterface;
use Slink\Shared\Infrastructure\Exception\NotFoundException;
use Slink\User\Application\Service\AnonymousUserManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final readonly class GetImageByIdHandler implements QueryHandlerInterface {
  
  public function __construct(
    private ImageRepositoryInterface $repository,
    private ImageAnalyzerInterface $imageAnalyzer,
    private AnonymousUserManagerInterface $anonymousUserManager,
  ) {
  }
  
  /**
   * @throws NonUniqueResultException
   * @throws NotFoundException
   */
  public function __invoke(GetImageByIdQuery $query, ?string $userId): Item {
    if(!Uuid::isValid($query->getId())) {
      throw new NotFoundException();
    }
    
    $imageView = $this->repository->oneById($query->getId());
    
    $tempUserIdentifier = $this->anonymousUserManager->getTempUserIdFromRequest();
    $userId = $userId ?? $tempUserIdentifier;
    
    if($imageView->getUser()?->getUuid() !== $userId) {
      throw new AccessDeniedException();
    }
    
    return Item::fromPayload(ImageView::class, [
      ...$imageView->toPayload(),
      'supportsResize' => $this->imageAnalyzer->supportsResize($imageView->getMimeType()),
      'isAnonymousUser' => !is_null($tempUserIdentifier),
      'url' => implode('/',
        [
          '/image',
          $imageView->getFileName()
        ]),
    ]);
  }
}