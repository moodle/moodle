<?php

namespace Packback\Lti1p3;

class LtiDeepLinkResource
{
    private $type = LtiConstants::DL_RESOURCE_LINK_TYPE;
    private $title;
    private $text;
    private $url;
    private $line_item;
    private $icon;
    private $thumbnail;
    private $custom_params = [];
    private $target = 'iframe';
    private $iframe;
    private $window;
    private $availability_interval;
    private $submission_interval;

    public static function new(): LtiDeepLinkResource
    {
        return new LtiDeepLinkResource();
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $value): LtiDeepLinkResource
    {
        $this->type = $value;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $value): LtiDeepLinkResource
    {
        $this->title = $value;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $value): LtiDeepLinkResource
    {
        $this->text = $value;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $value): LtiDeepLinkResource
    {
        $this->url = $value;

        return $this;
    }

    public function getLineItem(): ?LtiLineitem
    {
        return $this->line_item;
    }

    public function setLineItem(?LtiLineitem $value): LtiDeepLinkResource
    {
        $this->line_item = $value;

        return $this;
    }

    public function setIcon(?LtiDeepLinkResourceIcon $icon): LtiDeepLinkResource
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): ?LtiDeepLinkResourceIcon
    {
        return $this->icon;
    }

    public function setThumbnail(?LtiDeepLinkResourceIcon $thumbnail): LtiDeepLinkResource
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    public function getThumbnail(): ?LtiDeepLinkResourceIcon
    {
        return $this->thumbnail;
    }

    public function getCustomParams(): array
    {
        return $this->custom_params;
    }

    public function setCustomParams(array $value): LtiDeepLinkResource
    {
        $this->custom_params = $value;

        return $this;
    }

    /**
     * @deprecated This field maps the "presentation" resource property, which is non-standard.
     * Consider using "iframe" and/or "window" instead.
     */
    public function getTarget(): string
    {
        return $this->target;
    }

    /**
     * @deprecated This field maps the "presentation" resource property, which is non-standard.
     * Consider using "iframe" and/or "window" instead.
     */
    public function setTarget(string $value): LtiDeepLinkResource
    {
        $this->target = $value;

        return $this;
    }

    public function getIframe(): ?LtiDeepLinkResourceIframe
    {
        return $this->iframe;
    }

    public function setIframe(?LtiDeepLinkResourceIframe $iframe): LtiDeepLinkResource
    {
        $this->iframe = $iframe;

        return $this;
    }

    public function getWindow(): ?LtiDeepLinkResourceWindow
    {
        return $this->window;
    }

    public function setWindow(?LtiDeepLinkResourceWindow $window): LtiDeepLinkResource
    {
        $this->window = $window;

        return $this;
    }

    public function getAvailabilityInterval(): ?LtiDeepLinkDateTimeInterval
    {
        return $this->availability_interval;
    }

    public function setAvailabilityInterval(?LtiDeepLinkDateTimeInterval $availabilityInterval): LtiDeepLinkResource
    {
        $this->availability_interval = $availabilityInterval;

        return $this;
    }

    public function getSubmissionInterval(): ?LtiDeepLinkDateTimeInterval
    {
        return $this->submission_interval;
    }

    public function setSubmissionInterval(?LtiDeepLinkDateTimeInterval $submissionInterval): LtiDeepLinkResource
    {
        $this->submission_interval = $submissionInterval;

        return $this;
    }

    public function toArray(): array
    {
        $resource = [
            'type' => $this->type,
        ];

        if (isset($this->title)) {
            $resource['title'] = $this->title;
        }
        if (isset($this->text)) {
            $resource['text'] = $this->text;
        }
        if (isset($this->url)) {
            $resource['url'] = $this->url;
        }
        if (!empty($this->custom_params)) {
            $resource['custom'] = $this->custom_params;
        }
        if (isset($this->icon)) {
            $resource['icon'] = $this->icon->toArray();
        }
        if (isset($this->thumbnail)) {
            $resource['thumbnail'] = $this->thumbnail->toArray();
        }
        if ($this->line_item !== null) {
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

        if (isset($this->iframe)) {
            $resource['iframe'] = $this->iframe->toArray();
        }
        if (isset($this->window)) {
            $resource['window'] = $this->window->toArray();
        }
        if (isset($this->availability_interval)) {
            $resource['available'] = $this->availability_interval->toArray();
        }
        if (isset($this->submission_interval)) {
            $resource['submission'] = $this->submission_interval->toArray();
        }

        return $resource;
    }
}
