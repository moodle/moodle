<?php

namespace Packback\Lti1p3\DeepLinkResources;

use Packback\Lti1p3\Concerns\Arrayable;

class Iframe
{
    use Arrayable, HasDimensions;

    public function __construct(
        private ?string $src = null,
        private ?int $width = null,
        private ?int $height = null
    ) {}

    public static function new(): self
    {
        return new Iframe;
    }

    public function getArray(): array
    {
        return [
            'width' => $this->width,
            'height' => $this->height,
            'src' => $this->src,
        ];
    }

    public function setSrc(?string $src): self
    {
        $this->src = $src;

        return $this;
    }

    public function getSrc(): ?string
    {
        return $this->src;
    }
}
