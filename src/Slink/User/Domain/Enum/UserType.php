<?php

declare(strict_types=1);

namespace Slink\User\Domain\Enum;

enum UserType: string {
  case ANONYMOUS = 'anonymous_user';
  case REGISTERED = 'registered_user';
  
  public function getCookieName(): string {
    return $this->value;
  }
}
