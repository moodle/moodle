<?php

namespace Packback\Lti1p3;

class LtiDeepLinkResourceIframe
{
    private ?int $width;
    private ?int $height;

    public function __construct(int $width = null, int $height = null)
    {
        $this->width = $width ?? null;
        $this->height = $height ?? null;
    }

    public static function new(): LtiDeepLinkResourceIframe
    {
        return new LtiDeepLinkResourceIframe();
    }

    public function setWidth(?int $width): LtiDeepLinkResourceIframe
    {
        $this->width = $width;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setHeight(?int $height): LtiDeepLinkResourceIframe
    {
        $this->height = $height;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function toArray(): array
    {
        $iframe = [];

        if (isset($this->width)) {
            $iframe['width'] = $this->width;
        }
        if (isset($this->height)) {
            $iframe['height'] = $this->height;
        }

        return $iframe;
    }
}
