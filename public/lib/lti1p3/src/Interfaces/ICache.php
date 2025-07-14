<?php

namespace Packback\Lti1p3\Interfaces;

interface ICache
{
    public function getLaunchData(string $key): ?array;

    public function cacheLaunchData(string $key, array $jwtBody): void;

    public function cacheNonce(string $nonce, string $state): void;

    public function checkNonceIsValid(string $nonce, string $state): bool;

    public function cacheAccessToken(string $key, string $accessToken): void;

    public function getAccessToken(string $key): ?string;

    public function clearAccessToken(string $key): void;
}
