<?php

namespace Packback\Lti1p3;

class LtiDeepLinkResourceWindow
{
    private ?string $target_name;
    private ?int $width;
    private ?int $height;
    private ?string $window_features;

    public function __construct(string $targetName = null, int $width = null, int $height = null, string $windowFeatures = null)
    {
        $this->target_name = $targetName ?? null;
        $this->width = $width ?? null;
        $this->height = $height ?? null;
        $this->window_features = $windowFeatures ?? null;
    }

    public static function new(): LtiDeepLinkResourceWindow
    {
        return new LtiDeepLinkResourceWindow();
    }

    public function setTargetName(?string $targetName): LtiDeepLinkResourceWindow
    {
        $this->target_name = $targetName;

        return $this;
    }

    public function getTargetName(): ?string
    {
        return $this->target_name;
    }

    public function setWidth(?int $width): LtiDeepLinkResourceWindow
    {
        $this->width = $width;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setHeight(?int $height): LtiDeepLinkResourceWindow
    {
        $this->height = $height;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setWindowFeatures(?string $windowFeatures): LtiDeepLinkResourceWindow
    {
        $this->window_features = $windowFeatures;

        return $this;
    }

    public function getWindowFeatures(): ?string
    {
        return $this->window_features;
    }

    public function toArray(): array
    {
        $window = [];

        if (isset($this->target_name)) {
            $window['targetName'] = $this->target_name;
        }
        if (isset($this->width)) {
            $window['width'] = $this->width;
        }
        if (isset($this->height)) {
            $window['height'] = $this->height;
        }
        if (isset($this->window_features)) {
            $window['windowFeatures'] = $this->window_features;
        }

        return $window;
    }
}
