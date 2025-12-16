<?php
/**
 * Copyright 2018 Google Inc. All Rights Reserved.
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

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use SysvSemaphore;
use SysvSharedMemory;

/**
 * SystemV shared memory based CacheItemPool implementation.
 *
 * This CacheItemPool implementation can be used among multiple processes, but
 * it doesn't provide any locking mechanism. If multiple processes write to
 * this ItemPool, you have to avoid race condition manually in your code.
 */
class SysVCacheItemPool implements CacheItemPoolInterface
{
    const VAR_KEY = 1;

    const DEFAULT_PROJ = 'A';

    const DEFAULT_SEM_PROJ = 'B';

    const DEFAULT_MEMSIZE = 10000;

    const DEFAULT_PERM = 0600;

    /**
     * @var int
     */
    private $sysvKey;

    /**
     * @var CacheItemInterface[]
     */
    private $items;

    /**
     * @var CacheItemInterface[]
     */
    private $deferredItems;

    /**
     * @var array<mixed>
     */
    private $options;

    /**
     * @var bool
     */
    private $hasLoadedItems = false;

    /**
     * @var SysvSemaphore|false
     */
    private SysvSemaphore|false $semId = false;

    /**
     * Maintain the process which is currently holding the semaphore to prevent deadlock.
     *
     * @var int|null
     */
    private ?int $lockOwnerPid = null;

    /**
     * Create a SystemV shared memory based CacheItemPool.
     *
     * @param array<mixed> $options {
     *     [optional] Configuration options.
     *
     *     @type int    $variableKey The variable key for getting the data from the shared memory. **Defaults to** 1.
     *     @type string $proj        The project identifier for ftok. This needs to be a one character string.
     *                               **Defaults to** 'A'.
     *     @type string $semProj     The project identifier for ftok to provide to `sem_get`. This needs to be a one
     *                               character string.
     *                               **Defaults to** 'B'.
     *     @type int    $memsize     The memory size in bytes for shm_attach. **Defaults to** 10000.
     *     @type int    $perm        The permission for shm_attach. **Defaults to** 0600.
     * }
     */
    public function __construct($options = [])
    {
        if (!extension_loaded('sysvshm')) {
            throw new \RuntimeException(
                'sysvshm extension is required to use this ItemPool'
            );
        }
        $this->options = $options + [
            'variableKey' => self::VAR_KEY,
            'proj' => self::DEFAULT_PROJ,
            'semProj' => self::DEFAULT_SEM_PROJ,
            'memsize' => self::DEFAULT_MEMSIZE,
            'perm' => self::DEFAULT_PERM
        ];
        $this->items = [];
        $this->deferredItems = [];
        $this->sysvKey = ftok(__FILE__, $this->options['proj']);

        // gracefully handle when `sysvsem` isn't loaded
        // @TODO(v2): throw an exception when the extension isn't loaded
        if (extension_loaded('sysvsem')) {
            $semKey = ftok(__FILE__, $this->options['semProj']);
            $this->semId = sem_get($semKey, 1, $this->options['perm'], true);
        }
    }

    /**
     * @param mixed $key
     * @return CacheItemInterface
     */
    public function getItem($key): CacheItemInterface
    {
        $this->loadItems();
        return current($this->getItems([$key])); // @phpstan-ignore-line
    }

    /**
     * @param array<mixed> $keys
     * @return iterable<CacheItemInterface>
     */
    public function getItems(array $keys = []): iterable
    {
        $this->loadItems();
        $items = [];
        foreach ($keys as $key) {
            $items[$key] = $this->hasItem($key) ?
                clone $this->items[$key] :
                new TypedItem($key);
        }
        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem($key): bool
    {
        $this->loadItems();
        return isset($this->items[$key]) && $this->items[$key]->isHit();
    }

    /**
     * {@inheritdoc}
     */
    public function clear(): bool
    {
        if (!$this->acquireLock()) {
            return false;
        }

        $this->items = [];
        $this->deferredItems = [];
        $ret = $this->saveCurrentItems();

        $this->resetShm();
        $this->releaseLock();
        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($key): bool
    {
        return $this->deleteItems([$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItems(array $keys): bool
    {
        if (!$this->acquireLock()) {
            return false;
        }

        if (!$this->hasLoadedItems) {
            $this->loadItems();
        }

        foreach ($keys as $key) {
            unset($this->items[$key]);
        }
        $ret = $this->saveCurrentItems();

        $this->resetShm();
        $this->releaseLock();
        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CacheItemInterface $item): bool
    {
        if (!$this->acquireLock()) {
            return false;
        }

        if (!$this->hasLoadedItems) {
            $this->loadItems();
        }

        $this->items[$item->getKey()] = $item;
        $ret = $this->saveCurrentItems();
        $this->releaseLock();
        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function saveDeferred(CacheItemInterface $item): bool
    {
        $this->deferredItems[$item->getKey()] = $item;
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function commit(): bool
    {
        if (!$this->acquireLock()) {
            return false;
        }

        foreach ($this->deferredItems as $item) {
            if ($this->save($item) === false) {
                $this->releaseLock();
                return false;
            }
        }
        $this->deferredItems = [];
        $this->releaseLock();
        return true;
    }

    /**
     * Save the current items.
     *
     * @return bool true when success, false upon failure
     */
    private function saveCurrentItems()
    {
        if (!$this->acquireLock()) {
            return false;
        }

        if (false !== $shmid = $this->attachShm()) {
            $success = shm_put_var(
                $shmid,
                $this->options['variableKey'],
                $this->items
            );
            shm_detach($shmid);
            $this->releaseLock();
            return $success;
        }
        $this->releaseLock();
        return false;
    }

    /**
     * Load the items from the shared memory.
     *
     * @return bool true when success, false upon failure
     */
    private function loadItems()
    {
        if (!$this->acquireLock()) {
            return false;
        }

        if (false !== $shmid = $this->attachShm()) {
            $data = @shm_get_var($shmid, $this->options['variableKey']);
            $this->items = $data ?: [];
            shm_detach($shmid);
            $this->hasLoadedItems = true;
            $this->releaseLock();
            return true;
        }
        $this->releaseLock();
        return false;
    }

    private function acquireLock(): bool
    {
        if ($this->semId === false) {
            // if `sysvsem` isn't loaded, or if `sem_get` fails, return true
            // this ensures BC with previous versions of the auth library.
            // @TODO consider better handling when `sem_get` fails.
            return true;
        }

        $currentPid = getmypid();
        if ($this->lockOwnerPid === $currentPid) {
            // We already have the lock
            return true;
        }

        if (sem_acquire($this->semId)) {
            $this->lockOwnerPid = (int) $currentPid;
            return true;
        }
        return false;
    }

    private function releaseLock(): bool
    {
        if ($this->semId === false || $this->lockOwnerPid !== getmypid()) {
            return true;
        }

        $this->lockOwnerPid = null;
        return sem_release($this->semId);
    }

    private function resetShm(): void
    {
        // Remove the shared memory segment and semaphore when clearing the cache
        $shmid = @shm_attach($this->sysvKey);
        if ($shmid !== false) {
            @shm_remove($shmid);
            @shm_detach($shmid);
        }
    }

    private function attachShm(): SysvSharedMemory|false
    {
        return shm_attach(
            $this->sysvKey,
            $this->options['memsize'],
            $this->options['perm']
        );
    }
}
