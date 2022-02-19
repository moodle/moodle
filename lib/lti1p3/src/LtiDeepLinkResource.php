<?php

namespace Packback\Lti1p3;

class LtiDeepLinkResource
{
    private $type = 'ltiResourceLink';
    private $title;
    private $text;
    private $url;
    private $lineitem;
    private $custom_params = [];
    private $target = 'iframe';

    public static function new()
    {
        return new LtiDeepLinkResource();
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($value)
    {
        $this->type = $value;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($value)
    {
        $this->title = $value;

        return $this;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($value)
    {
        $this->text = $value;

        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($value)
    {
        $this->url = $value;

        return $this;
    }

    public function getLineitem()
    {
        return $this->lineitem;
    }

    public function setLineitem(LtiLineitem $value)
    {
        $this->lineitem = $value;

        return $this;
    }

    public function getCustomParams()
    {
        return $this->custom_params;
    }

    public function setCustomParams($value)
    {
        $this->custom_params = $value;

        return $this;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function setTarget($value)
    {
        $this->target = $value;

        return $this;
    }

    public function toArray()
    {
        $resource = [
            'type' => $this->type,
            'title' => $this->title,
            'text' => $this->text,
            'url' => $this->url,
            'presentation' => [
                'documentTarget' => $this->target,
            ],
            'custom' => $this->custom_params,
        ];
        if ($this->lineitem !== null) {
            $resource['lineItem'] = [
                'scoreMaximum' => $this->lineitem->getScoreMaximum(),
                'label' => $this->lineitem->getLabel(),
            ];
        }

        return $resource;
    }
}
