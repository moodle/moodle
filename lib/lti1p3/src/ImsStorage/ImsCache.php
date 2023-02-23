<?php

namespace Packback\Lti1p3\ImsStorage;

use Packback\Lti1p3\Interfaces\ICache;

class ImsCache implements ICache
{
    private $cache;

    public function getLaunchData(string $key): ?array
    {
        $this->loadCache();

        return $this->cache[$key] ?? null;
    }

    public function cacheLaunchData(string $key, array $jwtBody): void
    {
        $this->loadCache();

        $this->cache[$key] = $jwtBody;
        $this->saveCache();
    }

    public function cacheNonce(string $nonce, string $state): void
    {
        $this->loadCache();

        $this->cache['nonce'][$nonce] = $state;
        $this->saveCache();
    }

    public function checkNonceIsValid(string $nonce, string $state): bool
    {
        $this->loadCache();

        return isset($this->cache['nonce'][$nonce]) &&
            $this->cache['nonce'][$nonce] === $state;
    }

    public function cacheAccessToken(string $key, string $accessToken): void
    {
        $this->loadCache();

        $this->cache[$key] = $accessToken;
        $this->saveCache();
    }

    public function getAccessToken(string $key): ?string
    {
        $this->loadCache();

        return $this->cache[$key] ?? null;
    }

    public function clearAccessToken(string $key): void
    {
        $this->loadCache();

        unset($this->cache[$key]);
        $this->saveCache();
    }

    private function loadCache()
    {
        $cache = file_get_contents(sys_get_temp_dir().'/lti_cache.txt');
        if (empty($cache)) {
            file_put_contents(sys_get_temp_dir().'/lti_cache.txt', '{}');
            $this->cache = [];
        }
        $this->cache = json_decode($cache, true);
    }

    private function saveCache()
    {
        file_put_contents(sys_get_temp_dir().'/lti_cache.txt', json_encode($this->cache));
    }
}
