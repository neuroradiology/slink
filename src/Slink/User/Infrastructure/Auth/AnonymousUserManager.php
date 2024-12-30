<?php

declare(strict_types=1);

namespace Slink\User\Infrastructure\Auth;

use Slink\Shared\Domain\ValueObject\ID;
use Slink\User\Application\Service\AnonymousUserManagerInterface;
use Slink\User\Domain\Contracts\UserInterface;
use Slink\User\Domain\Enum\UserType;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class AnonymousUserManager implements AnonymousUserManagerInterface {
  public function __construct(
    private RequestStack $requestStack,
  ) {
  }
  
  /**
   * @return string
   */
  public function generateUserId(): string {
    return ID::generateRaw();
  }
  
  /**
   * @return ?string
   */
  public function getTempUserIdFromRequest(): ?string {
    $request = $this->requestStack->getCurrentRequest();
    $cookie = $request?->cookies->get(
      UserType::ANONYMOUS->getCookieName(),
    );
    
    if (!$cookie) {
      return null;
    }
    
    return (string) $cookie;
  }
  
  /**
   * @param ?UserInterface $user
   * @return ?string
   */
  public function getTempUserIdFromAuth(?UserInterface $user): ?string {
    if ($user) {
      return null;
    }
    
    return $this->getTempUserIdFromRequest() ?? $this->generateUserId();
  }
}