<?php
/*
 * Copyright 2017 MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB;

use Iterator;
use MongoDB\Driver\CursorId;
use MongoDB\Driver\Exception\ConnectionException;
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Driver\Exception\ServerException;
use MongoDB\Exception\ResumeTokenException;
use MongoDB\Model\ChangeStreamIterator;
use function call_user_func;
use function in_array;

/**
 * Iterator for a change stream.
 *
 * @api
 * @see \MongoDB\Collection::watch()
 * @see http://docs.mongodb.org/manual/reference/command/changeStream/
 */
class ChangeStream implements Iterator
{
    /**
     * @deprecated 1.4
     * @todo Remove this in 2.0 (see: PHPLIB-360)
     */
    const CURSOR_NOT_FOUND = 43;

    /** @var array */
    private static $nonResumableErrorCodes = [
        136, // CappedPositionLost
        237, // CursorKilled
        11601, // Interrupted
    ];

    /** @var callable */
    private $resumeCallable;

    /** @var ChangeStreamIterator */
    private $iterator;

    /** @var integer */
    private $key = 0;

    /**
     * Whether the change stream has advanced to its first result. This is used
     * to determine whether $key should be incremented after an iteration event.
     *
     * @var boolean
     */
    private $hasAdvanced = false;

    /**
     * @internal
     * @param ChangeStreamIterator $iterator
     * @param callable             $resumeCallable
     */
    public function __construct(ChangeStreamIterator $iterator, callable $resumeCallable)
    {
        $this->iterator = $iterator;
        $this->resumeCallable = $resumeCallable;
    }

    /**
     * @see http://php.net/iterator.current
     * @return mixed
     */
    public function current()
    {
        return $this->iterator->current();
    }

    /**
     * @return CursorId
     */
    public function getCursorId()
    {
        return $this->iterator->getInnerIterator()->getId();
    }

    /**
     * Returns the resume token for the iterator's current position.
     *
     * Null may be returned if no change documents have been iterated and the
     * server did not include a postBatchResumeToken in its aggregate or getMore
     * command response.
     *
     * @return array|object|null
     */
    public function getResumeToken()
    {
        return $this->iterator->getResumeToken();
    }

    /**
     * @see http://php.net/iterator.key
     * @return mixed
     */
    public function key()
    {
        if ($this->valid()) {
            return $this->key;
        }

        return null;
    }

    /**
     * @see http://php.net/iterator.next
     * @return void
     * @throws ResumeTokenException
     */
    public function next()
    {
        try {
            $this->iterator->next();
            $this->onIteration($this->hasAdvanced);
        } catch (RuntimeException $e) {
            $this->resumeOrThrow($e);
        }
    }

    /**
     * @see http://php.net/iterator.rewind
     * @return void
     * @throws ResumeTokenException
     */
    public function rewind()
    {
        try {
            $this->iterator->rewind();
            /* Unlike next() and resume(), the decision to increment the key
             * does not depend on whether the change stream has advanced. This
             * ensures that multiple calls to rewind() do not alter state. */
            $this->onIteration(false);
        } catch (RuntimeException $e) {
            $this->resumeOrThrow($e);
        }
    }

    /**
     * @see http://php.net/iterator.valid
     * @return boolean
     */
    public function valid()
    {
        return $this->iterator->valid();
    }

    /**
     * Determines if an exception is a resumable error.
     *
     * @see https://github.com/mongodb/specifications/blob/master/source/change-streams/change-streams.rst#resumable-error
     * @param RuntimeException $exception
     * @return boolean
     */
    private function isResumableError(RuntimeException $exception)
    {
        if ($exception instanceof ConnectionException) {
            return true;
        }

        if (! $exception instanceof ServerException) {
            return false;
        }

        if ($exception->hasErrorLabel('NonResumableChangeStreamError')) {
            return false;
        }

        if (in_array($exception->getCode(), self::$nonResumableErrorCodes)) {
            return false;
        }

        return true;
    }

    /**
     * Perform housekeeping after an iteration event.
     *
     * @param boolean $incrementKey Increment $key if there is a current result
     * @throws ResumeTokenException
     */
    private function onIteration($incrementKey)
    {
        /* If the cursorId is 0, the server has invalidated the cursor and we
         * will never perform another getMore nor need to resume since any
         * remaining results (up to and including the invalidate event) will
         * have been received in the last response. Therefore, we can unset the
         * resumeCallable. This will free any reference to Watch as well as the
         * only reference to any implicit session created therein. */
        if ((string) $this->getCursorId() === '0') {
            $this->resumeCallable = null;
        }

        /* Return early if there is not a current result. Avoid any attempt to
         * increment the iterator's key. */
        if (! $this->valid()) {
            return;
        }

        if ($incrementKey) {
            $this->key++;
        }

        $this->hasAdvanced = true;
    }

    /**
     * Recreates the ChangeStreamIterator after a resumable server error.
     *
     * @return void
     */
    private function resume()
    {
        $this->iterator = call_user_func($this->resumeCallable, $this->getResumeToken(), $this->hasAdvanced);
        $this->iterator->rewind();

        $this->onIteration($this->hasAdvanced);
    }

    /**
     * Either resumes after a resumable error or re-throws the exception.
     *
     * @param RuntimeException $exception
     * @throws RuntimeException
     */
    private function resumeOrThrow(RuntimeException $exception)
    {
        if ($this->isResumableError($exception)) {
            $this->resume();

            return;
        }

        throw $exception;
    }
}
