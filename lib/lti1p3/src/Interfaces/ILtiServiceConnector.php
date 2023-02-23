<?php

namespace Packback\Lti1p3\Interfaces;

interface ILtiServiceConnector
{
    public function getAccessToken(ILtiRegistration $registration, array $scopes);

    public function makeRequest(IServiceRequest $request);

    public function getResponseBody(IHttpResponse $response): ?array;

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
        string $key
    ): array;

    public function setDebuggingMode(bool $enable): void;
}
