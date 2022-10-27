<?php

namespace Packback\Lti1p3;

class LtiDeepLinkResourceIcon
{
    private $url;
    private $width;
    private $height;

    public function __construct(string $url, int $width, int $height)
    {
        $this->url = $url;
        $this->width = $width;
        $this->height = $height;
    }

    public static function new(string $url, int $width, int $height): LtiDeepLinkResourceIcon
    {
        return new LtiDeepLinkResourceIcon($url, $width, $height);
    }

    public function setUrl(string $url): LtiDeepLinkResourceIcon
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setWidth(int $width): LtiDeepLinkResourceIcon
    {
        $this->width = $width;

        return $this;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function setHeight(int $height): LtiDeepLinkResourceIcon
    {
        $this->height = $height;

        return $this;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'width' => $this->width,
            'height' => $this->height,
        ];
    }
}
