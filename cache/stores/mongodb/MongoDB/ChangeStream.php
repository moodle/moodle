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

use MongoDB\BSON\Serializable;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Exception\ConnectionException;
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Driver\Exception\ServerException;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\ResumeTokenException;
use IteratorIterator;
use Iterator;

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

    private static $errorCodeCappedPositionLost = 136;
    private static $errorCodeInterrupted = 11601;
    private static $errorCodeCursorKilled = 237;

    private $resumeToken;
    private $resumeCallable;
    private $csIt;
    private $key = 0;
    private $hasAdvanced = false;

    /**
     * Constructor.
     *
     * @internal
     * @param Cursor $cursor
     * @param callable $resumeCallable
     */
    public function __construct(Cursor $cursor, callable $resumeCallable)
    {
        $this->resumeCallable = $resumeCallable;
        $this->csIt = new IteratorIterator($cursor);
    }

    /**
     * @see http://php.net/iterator.current
     * @return mixed
     */
    public function current()
    {
        return $this->csIt->current();
    }

    /**
     * @return \MongoDB\Driver\CursorId
     */
    public function getCursorId()
    {
        return $this->csIt->getInnerIterator()->getId();
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
     */
    public function next()
    {
        try {
            $this->csIt->next();
            if ($this->valid()) {
                if ($this->hasAdvanced) {
                    $this->key++;
                }
                $this->hasAdvanced = true;
                $this->resumeToken = $this->extractResumeToken($this->csIt->current());
            }
            /* If the cursorId is 0, the server has invalidated the cursor so we
             * will never perform another getMore. This means that we cannot
             * resume and we can therefore unset the resumeCallable, which will
             * free any reference to Watch. This will also free the only
             * reference to an implicit session, since any such reference
             * belongs to Watch. */
            if ((string) $this->getCursorId() === '0') {
                $this->resumeCallable = null;
            }
        } catch (RuntimeException $e) {
            if ($this->isResumableError($e)) {
                $this->resume();
            }
        }
    }

    /**
     * @see http://php.net/iterator.rewind
     * @return void
     */
    public function rewind()
    {
        try {
            $this->csIt->rewind();
            if ($this->valid()) {
                $this->hasAdvanced = true;
                $this->resumeToken = $this->extractResumeToken($this->csIt->current());
            }
            // As with next(), free the callable once we know it will never be used.
            if ((string) $this->getCursorId() === '0') {
                $this->resumeCallable = null;
            }
        } catch (RuntimeException $e) {
            if ($this->isResumableError($e)) {
                $this->resume();
            }
        }
    }

    /**
     * @see http://php.net/iterator.valid
     * @return boolean
     */
    public function valid()
    {
        return $this->csIt->valid();
    }

    /**
     * Extracts the resume token (i.e. "_id" field) from the change document.
     *
     * @param array|document $document Change document
     * @return mixed
     * @throws InvalidArgumentException
     * @throws ResumeTokenException if the resume token is not found or invalid
     */
    private function extractResumeToken($document)
    {
        if ( ! is_array($document) && ! is_object($document)) {
            throw InvalidArgumentException::invalidType('$document', $document, 'array or object');
        }

        if ($document instanceof Serializable) {
            return $this->extractResumeToken($document->bsonSerialize());
        }

        $resumeToken = is_array($document)
            ? (isset($document['_id']) ? $document['_id'] : null)
            : (isset($document->_id) ? $document->_id : null);

        if ( ! isset($resumeToken)) {
            throw ResumeTokenException::notFound();
        }

        if ( ! is_array($resumeToken) && ! is_object($resumeToken)) {
            throw ResumeTokenException::invalidType($resumeToken);
        }

        return $resumeToken;
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

        if ( ! $exception instanceof ServerException) {
            return false;
        }

        if (in_array($exception->getCode(), [self::$errorCodeCappedPositionLost, self::$errorCodeCursorKilled, self::$errorCodeInterrupted])) {
            return false;
        }

        return true;
    }

    /**
     * Creates a new changeStream after a resumable server error.
     *
     * @return void
     */
    private function resume()
    {
        $newChangeStream = call_user_func($this->resumeCallable, $this->resumeToken);
        $this->csIt = $newChangeStream->csIt;
        $this->csIt->rewind();
    }
}
