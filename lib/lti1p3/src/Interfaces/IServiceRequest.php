<?php

namespace Packback\Lti1p3\Interfaces;

interface IServiceRequest
{
    public function getMethod(): string;

    public function getUrl(): string;

    public function getPayload(): array;

    public function setUrl(string $url): self;

    public function setAccessToken(string $accessToken): self;

    public function setBody(string $body): self;

    public function setAccept(string $accept): self;

    public function setContentType(string $contentType): self;

    public function getErrorPrefix(): string;
}
