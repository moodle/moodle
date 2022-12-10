<?php
/*
 * Copyright 2017-present MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB\Model;

use Countable;
use Iterator;
use IteratorIterator;
use ReturnTypeWillChange;
use Traversable;

use function count;
use function current;
use function next;
use function reset;

/**
 * Iterator for wrapping a Traversable and caching its results.
 *
 * By caching results, this iterators allows a Traversable to be counted and
 * rewound multiple times, even if the wrapped object does not natively support
 * those operations (e.g. MongoDB\Driver\Cursor).
 *
 * @internal
 */
class CachingIterator implements Countable, Iterator
{
    private const FIELD_KEY = 0;
    private const FIELD_VALUE = 1;

    /** @var array */
    private $items = [];

    /** @var Iterator */
    private $iterator;

    /** @var boolean */
    private $iteratorAdvanced = false;

    /** @var boolean */
    private $iteratorExhausted = false;

    /**
     * Initialize the iterator and stores the first item in the cache. This
     * effectively rewinds the Traversable and the wrapping IteratorIterator.
     * Additionally, this mimics behavior of the SPL iterators and allows users
     * to omit an explicit call to rewind() before using the other methods.
     *
     * @param Traversable $traversable
     */
    public function __construct(Traversable $traversable)
    {
        $this->iterator = $traversable instanceof Iterator ? $traversable : new IteratorIterator($traversable);

        $this->iterator->rewind();
        $this->storeCurrentItem();
    }

    /**
     * @see https://php.net/countable.count
     */
    public function count(): int
    {
        $this->exhaustIterator();

        return count($this->items);
    }

    /**
     * @see https://php.net/iterator.current
     * @return mixed
     */
    #[ReturnTypeWillChange]
    public function current()
    {
        $currentItem = current($this->items);

        return $currentItem !== false ? $currentItem[self::FIELD_VALUE] : false;
    }

    /**
     * @see https://php.net/iterator.key
     * @return mixed
     */
    #[ReturnTypeWillChange]
    public function key()
    {
        $currentItem = current($this->items);

        return $currentItem !== false ? $currentItem[self::FIELD_KEY] : null;
    }

    /**
     * @see https://php.net/iterator.next
     */
    public function next(): void
    {
        if (! $this->iteratorExhausted) {
            $this->iteratorAdvanced = true;
            $this->iterator->next();

            $this->storeCurrentItem();

            $this->iteratorExhausted = ! $this->iterator->valid();
        }

        next($this->items);
    }

    /**
     * @see https://php.net/iterator.rewind
     */
    public function rewind(): void
    {
        /* If the iterator has advanced, exhaust it now so that future iteration
         * can rely on the cache.
         */
        if ($this->iteratorAdvanced) {
            $this->exhaustIterator();
        }

        reset($this->items);
    }

    /**
     * @see https://php.net/iterator.valid
     */
    public function valid(): bool
    {
        return $this->key() !== null;
    }

    /**
     * Ensures that the inner iterator is fully consumed and cached.
     */
    private function exhaustIterator(): void
    {
        while (! $this->iteratorExhausted) {
            $this->next();
        }
    }

    /**
     * Stores the current item in the cache.
     */
    private function storeCurrentItem(): void
    {
        if (! $this->iterator->valid()) {
            return;
        }

        // Storing a new item in the internal cache
        $this->items[] = [
            self::FIELD_KEY => $this->iterator->key(),
            self::FIELD_VALUE => $this->iterator->current(),
        ];
    }
}
