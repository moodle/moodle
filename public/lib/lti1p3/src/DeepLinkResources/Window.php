<?php

namespace Packback\Lti1p3\DeepLinkResources;

use Packback\Lti1p3\Concerns\Arrayable;

class Window
{
    use Arrayable, HasDimensions;

    public function __construct(
        private ?string $target_name = null,
        private ?int $width = null,
        private ?int $height = null,
        private ?string $window_features = null
    ) {}

    public static function new(): self
    {
        return new Window;
    }

    public function getArray(): array
    {
        return [
            'targetName' => $this->target_name,
            'width' => $this->width,
            'height' => $this->height,
            'windowFeatures' => $this->window_features,
        ];
    }

    public function setTargetName(?string $targetName): self
    {
        $this->target_name = $targetName;

        return $this;
    }

    public function getTargetName(): ?string
    {
        return $this->target_name;
    }

    public function setWindowFeatures(?string $windowFeatures): self
    {
        $this->window_features = $windowFeatures;

        return $this;
    }

    public function getWindowFeatures(): ?string
    {
        return $this->window_features;
    }
}
