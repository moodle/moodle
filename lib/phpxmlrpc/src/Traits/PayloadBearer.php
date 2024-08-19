<?php

namespace PhpXmlRpc\Traits;

trait PayloadBearer
{
    /** @var string */
    protected $payload;
    /** @var string */
    protected $content_type = 'text/xml';

    /**
     * @internal
     *
     * @param string $payload
     * @param string $contentType
     * @return $this
     */
    public function setPayload($payload, $contentType = '')
    {
        $this->payload = $payload;

        if ($contentType != '') {
            $this->content_type = $contentType;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->content_type;
    }
}
