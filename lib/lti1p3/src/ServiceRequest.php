<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Interfaces\IServiceRequest;

class ServiceRequest implements IServiceRequest
{
    private $method;
    private $url;
    private $body;
    private $accessToken;
    private $contentType = 'application/json';
    private $accept = 'application/json';

    public function __construct(string $method, string $url)
    {
        $this->method = $method;
        $this->url = $url;
    }

    public function getMethod(): string
    {
        return strtoupper($this->method);
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getPayload(): array
    {
        $payload = [
            'headers' => $this->getHeaders(),
        ];

        $body = $this->getBody();
        if ($body) {
            $payload['body'] = $body;
        }

        return $payload;
    }

    public function setUrl(string $url): IServiceRequest
    {
        $this->url = $url;

        return $this;
    }

    public function setAccessToken(string $accessToken): IServiceRequest
    {
        $this->accessToken = 'Bearer '.$accessToken;

        return $this;
    }

    public function setBody(string $body): IServiceRequest
    {
        $this->body = $body;

        return $this;
    }

    public function setAccept(string $accept): IServiceRequest
    {
        $this->accept = $accept;

        return $this;
    }

    public function setContentType(string $contentType): IServiceRequest
    {
        $this->contentType = $contentType;

        return $this;
    }

    private function getHeaders(): array
    {
        $headers = [
            'Accept' => $this->accept,
        ];

        if (isset($this->accessToken)) {
            $headers['Authorization'] = $this->accessToken;
        }

        if ($this->getMethod() === LtiServiceConnector::METHOD_POST) {
            $headers['Content-Type'] = $this->contentType;
        }

        return $headers;
    }

    private function getBody(): ?string
    {
        return $this->body;
    }
}
