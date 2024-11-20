<?php

namespace Packback\Lti1p3\DeepLinkResources;

use Packback\Lti1p3\Concerns\Arrayable;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiLineitem;

class Resource
{
    use Arrayable;
    private string $type = LtiConstants::DL_RESOURCE_LINK_TYPE;
    private ?string $title = null;
    private ?string $text = null;
    private ?string $url = null;
    private ?LtiLineitem $line_item = null;
    private ?Icon $icon = null;
    private ?Icon $thumbnail = null;
    private array $custom_params = [];
    private string $target = 'iframe';
    private ?Iframe $iframe = null;
    private ?Window $window = null;
    private ?DateTimeInterval $availability_interval = null;
    private ?DateTimeInterval $submission_interval = null;

    public static function new(): self
    {
        return new Resource();
    }

    public function getArray(): array
    {
        $resource = [
            'type' => $this->type,
            'title' => $this->title,
            'text' => $this->text,
            'url' => $this->url,
            'icon' => $this->icon?->toArray(),
            'thumbnail' => $this->thumbnail?->toArray(),
            'iframe' => $this->iframe?->toArray(),
            'window' => $this->window?->toArray(),
            'available' => $this->availability_interval?->toArray(),
            'submission' => $this->submission_interval?->toArray(),
        ];

        if (!empty($this->custom_params)) {
            $resource['custom'] = $this->custom_params;
        }

        if (isset($this->line_item)) {
            $resource['lineItem'] = [
                'scoreMaximum' => $this->line_item->getScoreMaximum(),
                'label' => $this->line_item->getLabel(),
            ];
        }

        // Kept for backwards compatibility
        if (!isset($this->iframe) && !isset($this->window)) {
            $resource['presentation'] = [
                'documentTarget' => $this->target,
            ];
        }

        return $resource;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $value): self
    {
        $this->type = $value;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $value): self
    {
        $this->title = $value;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $value): self
    {
        $this->text = $value;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $value): self
    {
        $this->url = $value;

        return $this;
    }

    public function getLineItem(): ?LtiLineitem
    {
        return $this->line_item;
    }

    public function setLineItem(?LtiLineitem $value): self
    {
        $this->line_item = $value;

        return $this;
    }

    public function setIcon(?Icon $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): ?Icon
    {
        return $this->icon;
    }

    public function setThumbnail(?Icon $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    public function getThumbnail(): ?Icon
    {
        return $this->thumbnail;
    }

    public function getCustomParams(): array
    {
        return $this->custom_params;
    }

    public function setCustomParams(array $value): self
    {
        $this->custom_params = $value;

        return $this;
    }

    public function getIframe(): ?Iframe
    {
        return $this->iframe;
    }

    public function setIframe(?Iframe $iframe): self
    {
        $this->iframe = $iframe;

        return $this;
    }

    public function getWindow(): ?Window
    {
        return $this->window;
    }

    public function setWindow(?Window $window): self
    {
        $this->window = $window;

        return $this;
    }

    public function getAvailabilityInterval(): ?DateTimeInterval
    {
        return $this->availability_interval;
    }

    public function setAvailabilityInterval(?DateTimeInterval $availabilityInterval): self
    {
        $this->availability_interval = $availabilityInterval;

        return $this;
    }

    public function getSubmissionInterval(): ?DateTimeInterval
    {
        return $this->submission_interval;
    }

    public function setSubmissionInterval(?DateTimeInterval $submissionInterval): self
    {
        $this->submission_interval = $submissionInterval;

        return $this;
    }
}
