<?php

namespace Packback\Lti1p3\Interfaces;

use Psr\Http\Message\ResponseInterface;

/** @internal */
interface ILtiServiceConnector
{
    public function getAccessToken(ILtiRegistration $registration, array $scopes): string;

    public function makeRequest(IServiceRequest $request): ResponseInterface;

    public function getResponseBody(ResponseInterface $response): ?array;

    public function getResponseHeaders(ResponseInterface $response): ?array;

    public function makeServiceRequest(
        ILtiRegistration $registration,
        array $scopes,
        IServiceRequest $request,
        bool $shouldRetry = true
    ): array;

    public function getAll(
        ILtiRegistration $registration,
        array $scopes,
        IServiceRequest $request,
        ?string $key
    ): array;

    public function setDebuggingMode(bool $enable): ILtiServiceConnector;
}
