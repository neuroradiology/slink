<?php

declare(strict_types=1);

namespace Slink\User\Application\Service;

use Slink\User\Domain\Contracts\UserInterface;

interface AnonymousUserManagerInterface {
  public function generateUserId(): string;
  public function getTempUserIdFromRequest(): ?string;
  public function getTempUserIdFromAuth(?UserInterface $user): ?string;
}