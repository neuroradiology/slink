<?php

declare(strict_types=1);

namespace Slink\Image\Application\Command\UploadImage;

use Slink\Shared\Application\Command\CommandInterface;
use Slink\Shared\Domain\ValueObject\ID;
use Slink\Shared\Infrastructure\MessageBus\EnvelopedMessage;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

final class UploadImageCommand implements CommandInterface {
  use EnvelopedMessage;
  
  public ID $id {
    get {
      return $this->id;
    }
  }
  
  /**
   * @param File $image
   * @param bool $isPublic
   * @param string $description
   */
  public function __construct(
    #[Assert\Image(
      mimeTypesMessage: <<<'MESSAGE'
        The mime type {{ type }} is not supported.
        <a href="/help/faq#supported-image-formats"
            class="text-indigo-500 hover:text-indigo-700 mt-2 block"
        >See supported formats</a>
        MESSAGE,
    )]
    private readonly File $image,
    
    private bool $isPublic = false,
    
    #[Assert\Length(max: 255)]
    private readonly string $description = '',
  ) {
    $this->id = ID::generate();
  }
  
  /**
   * @return File
   */
  public function getImageFile(): File {
    return $this->image;
  }
  
  /**
   * @return bool
   */
  public function isPublic(): bool {
    return $this->isPublic;
  }
  
  /**
   * @return string
   */
  public function getDescription(): string {
    return $this->description;
  }
  
  public function forcePublic(): void {
    $this->isPublic = true;
  }
}