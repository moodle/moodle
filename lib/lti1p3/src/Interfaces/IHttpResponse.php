<?php

namespace Packback\Lti1p3\Interfaces;

interface IHttpResponse
{
    public function getBody();
    public function getHeaders(): array;
    public function getStatusCode(): int;
}
