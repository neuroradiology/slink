<?php

declare(strict_types=1);

namespace Slink\Shared\Domain\ValueObject;

use EventSauce\EventSourcing\AggregateRootId;
use Ramsey\Uuid\Uuid;

final readonly class ID extends AbstractValueObject implements AggregateRootId {
  /**
   * @param string $value
   */
  private function __construct(
    private string $value,
  ) {}
  
  /**
   * @param string $value
   * @return static
   */
  public static function fromString(string $value): static {
    return new self($value);
  }
  
  /**
   * @param mixed $value
   * @return static|null
   */
  public static function fromUnknown(mixed $value): ?static {
    if (\is_null($value)) {
      return null;
    }
    
    return self::fromString((string) $value);
  }
  
  public static function generateRaw(): string {
    return Uuid::uuid4()->toString();
  }
  
  /**
   * @return static
   */
  public static function generate(): static {
    return new self(self::generateRaw());
  }
  
  /**
   * @return string
   */
  public function getValue(): string {
    return $this->value;
  }
  
  /**
   * @return string
   */
  #[\Override]
  public function toString(): string {
    return $this->getValue();
  }
}
