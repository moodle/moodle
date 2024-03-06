<?php

namespace Packback\Lti1p3\DeepLinkResources;

use Packback\Lti1p3\Concerns\Arrayable;

class Icon
{
    use Arrayable, HasDimensions;

    public function __construct(
        private string $url,
        private int $width,
        private int $height
    ) {
    }

    public static function new(string $url, int $width, int $height): self
    {
        return new Icon($url, $width, $height);
    }

    public function getArray(): array
    {
        return [
            'url' => $this->url,
            'width' => $this->width,
            'height' => $this->height,
        ];
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
