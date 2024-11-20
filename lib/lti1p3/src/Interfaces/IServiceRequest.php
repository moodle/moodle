<?php

namespace Packback\Lti1p3\Interfaces;

/** @internal */
interface IServiceRequest
{
    public function getMethod(): string;

    public function getUrl(): string;

    public function getPayload(): array;

    public function setUrl(string $url): IServiceRequest;

    public function setAccessToken(string $accessToken): IServiceRequest;

    public function setBody(string $body): IServiceRequest;

    public function setPayload(array $payload): IServiceRequest;

    public function setAccept(string $accept): IServiceRequest;

    public function setContentType(string $contentType): IServiceRequest;

    public function getErrorPrefix(): string;

    public function getMaskResponseLogs(): bool;

    public function setMaskResponseLogs(bool $maskResponseLogs): self;
}
