<?php

namespace Packback\Lti1p3\Interfaces;

Interface IHttpClient
{
    public function request(string $method, string $url, array $options) : IHttpResponse;
}
