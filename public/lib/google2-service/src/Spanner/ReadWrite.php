<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Spanner;

class ReadWrite extends \Google\Model
{
  /**
   * Default value. * If isolation level is REPEATABLE_READ, then it is an error
   * to specify `read_lock_mode`. Locking semantics default to `OPTIMISTIC`. No
   * validation checks are done for reads, except to validate that the data that
   * was served at the snapshot time is unchanged at commit time in the
   * following cases: 1. reads done as part of queries that use `SELECT FOR
   * UPDATE` 2. reads done as part of statements with a `LOCK_SCANNED_RANGES`
   * hint 3. reads done as part of DML statements * At all other isolation
   * levels, if `read_lock_mode` is the default value, then pessimistic read
   * locks are used.
   */
  public const READ_LOCK_MODE_READ_LOCK_MODE_UNSPECIFIED = 'READ_LOCK_MODE_UNSPECIFIED';
  /**
   * Pessimistic lock mode. Read locks are acquired immediately on read.
   * Semantics described only applies to SERIALIZABLE isolation.
   */
  public const READ_LOCK_MODE_PESSIMISTIC = 'PESSIMISTIC';
  /**
   * Optimistic lock mode. Locks for reads within the transaction are not
   * acquired on read. Instead the locks are acquired on a commit to validate
   * that read/queried data has not changed since the transaction started.
   * Semantics described only applies to SERIALIZABLE isolation.
   */
  public const READ_LOCK_MODE_OPTIMISTIC = 'OPTIMISTIC';
  /**
   * Optional. Clients should pass the transaction ID of the previous
   * transaction attempt that was aborted if this transaction is being executed
   * on a multiplexed session.
   *
   * @var string
   */
  public $multiplexedSessionPreviousTransactionId;
  /**
   * Read lock mode for the transaction.
   *
   * @var string
   */
  public $readLockMode;

  /**
   * Optional. Clients should pass the transaction ID of the previous
   * transaction attempt that was aborted if this transaction is being executed
   * on a multiplexed session.
   *
   * @param string $multiplexedSessionPreviousTransactionId
   */
  public function setMultiplexedSessionPreviousTransactionId($multiplexedSessionPreviousTransactionId)
  {
    $this->multiplexedSessionPreviousTransactionId = $multiplexedSessionPreviousTransactionId;
  }
  /**
   * @return string
   */
  public function getMultiplexedSessionPreviousTransactionId()
  {
    return $this->multiplexedSessionPreviousTransactionId;
  }
  /**
   * Read lock mode for the transaction.
   *
   * Accepted values: READ_LOCK_MODE_UNSPECIFIED, PESSIMISTIC, OPTIMISTIC
   *
   * @param self::READ_LOCK_MODE_* $readLockMode
   */
  public function setReadLockMode($readLockMode)
  {
    $this->readLockMode = $readLockMode;
  }
  /**
   * @return self::READ_LOCK_MODE_*
   */
  public function getReadLockMode()
  {
    return $this->readLockMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReadWrite::class, 'Google_Service_Spanner_ReadWrite');
