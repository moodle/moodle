<?php
/**
 * Copyright 2024 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Google\Auth\Cache;

use ErrorException;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class FileSystemCacheItemPool implements CacheItemPoolInterface
{
    /**
     * @var string
     */
    private string $cachePath;

    /**
     * @var array<CacheItemInterface>
     */
    private array $buffer = [];

    /**
     * Creates a FileSystemCacheItemPool cache that stores values in local storage
     *
     * @param string $path The string representation of the path where the cache will store the serialized objects.
     */
    public function __construct(string $path)
    {
        $this->cachePath = $path;

        if (is_dir($this->cachePath)) {
            return;
        }

        // Suppress the error for when the directory already exists because of a
        // race condition
        if (!@mkdir($this->cachePath, 0777, true) && !is_dir($this->cachePath)) {
            throw new ErrorException("Cache folder couldn't be created.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getItem(string $key): CacheItemInterface
    {
        if (!$this->validKey($key)) {
            throw new InvalidArgumentException("The key '$key' is not valid. The key should follow the pattern |^[a-zA-Z0-9_\.! ]+$|");
        }

        $item = new TypedItem($key);

        $itemPath = $this->cacheFilePath($key);

        if (!file_exists($itemPath)) {
            return $item;
        }

        $serializedItem = file_get_contents($itemPath);

        if ($serializedItem === false) {
            return $item;
        }

        $item->set(unserialize($serializedItem));

        return $item;
    }

    /**
     * {@inheritdoc}
     *
     * @return iterable<CacheItemInterface> An iterable object containing all the
     *   A traversable collection of Cache Items keyed by the cache keys of
     *   each item. A Cache item will be returned for each key, even if that
     *   key is not found. However, if no keys are specified then an empty
     *   traversable MUST be returned instead.
     */
    public function getItems(array $keys = []): iterable
    {
        $result = [];

        foreach ($keys as $key) {
            $result[$key] = $this->getItem($key);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CacheItemInterface $item): bool
    {
        if (!$this->validKey($item->getKey())) {
            return false;
        }

        $itemPath = $this->cacheFilePath($item->getKey());
        $serializedItem = serialize($item->get());

        $result = file_put_contents($itemPath, $serializedItem, LOCK_EX);

        // 0 bytes write is considered a successful operation
        if ($result === false) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem(string $key): bool
    {
        return $this->getItem($key)->isHit();
    }

    /**
     * {@inheritdoc}
     */
    public function clear(): bool
    {
        $this->buffer = [];

        if (!is_dir($this->cachePath)) {
            return false;
        }

        $files = scandir($this->cachePath);
        if (!$files) {
            return false;
        }

        foreach ($files as $fileName) {
            if ($fileName === '.' || $fileName === '..') {
                continue;
            }

            if (!unlink($this->cachePath . '/' . $fileName)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem(string $key): bool
    {
        if (!$this->validKey($key)) {
            throw new InvalidArgumentException("The key '$key' is not valid. The key should follow the pattern |^[a-zA-Z0-9_\.! ]+$|");
        }

        $itemPath = $this->cacheFilePath($key);

        if (!file_exists($itemPath)) {
            return true;
        }

        return unlink($itemPath);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItems(array $keys): bool
    {
        $result = true;

        foreach ($keys as $key) {
            if (!$this->deleteItem($key)) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function saveDeferred(CacheItemInterface $item): bool
    {
        array_push($this->buffer, $item);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function commit(): bool
    {
        $result = true;

        foreach ($this->buffer as $item) {
            if (!$this->save($item)) {
                $result = false;
            }
        }

        return $result;
    }

    private function cacheFilePath(string $key): string
    {
        return $this->cachePath . '/' . $key;
    }

    private function validKey(string $key): bool
    {
        return (bool) preg_match('|^[a-zA-Z0-9_\.]+$|', $key);
    }
}
