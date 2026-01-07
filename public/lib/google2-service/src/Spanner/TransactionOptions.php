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

class TransactionOptions extends \Google\Model
{
  /**
   * Default value. If the value is not specified, the `SERIALIZABLE` isolation
   * level is used.
   */
  public const ISOLATION_LEVEL_ISOLATION_LEVEL_UNSPECIFIED = 'ISOLATION_LEVEL_UNSPECIFIED';
  /**
   * All transactions appear as if they executed in a serial order, even if some
   * of the reads, writes, and other operations of distinct transactions
   * actually occurred in parallel. Spanner assigns commit timestamps that
   * reflect the order of committed transactions to implement this property.
   * Spanner offers a stronger guarantee than serializability called external
   * consistency. For more information, see [TrueTime and external
   * consistency](https://cloud.google.com/spanner/docs/true-time-external-
   * consistency#serializability).
   */
  public const ISOLATION_LEVEL_SERIALIZABLE = 'SERIALIZABLE';
  /**
   * All reads performed during the transaction observe a consistent snapshot of
   * the database, and the transaction is only successfully committed in the
   * absence of conflicts between its updates and any concurrent updates that
   * have occurred since that snapshot. Consequently, in contrast to
   * `SERIALIZABLE` transactions, only write-write conflicts are detected in
   * snapshot transactions. This isolation level does not support read-only and
   * partitioned DML transactions. When `REPEATABLE_READ` is specified on a
   * read-write transaction, the locking semantics default to `OPTIMISTIC`.
   */
  public const ISOLATION_LEVEL_REPEATABLE_READ = 'REPEATABLE_READ';
  /**
   * When `exclude_txn_from_change_streams` is set to `true`, it prevents read
   * or write transactions from being tracked in change streams. * If the DDL
   * option `allow_txn_exclusion` is set to `true`, then the updates made within
   * this transaction aren't recorded in the change stream. * If you don't set
   * the DDL option `allow_txn_exclusion` or if it's set to `false`, then the
   * updates made within this transaction are recorded in the change stream.
   * When `exclude_txn_from_change_streams` is set to `false` or not set,
   * modifications from this transaction are recorded in all change streams that
   * are tracking columns modified by these transactions. The
   * `exclude_txn_from_change_streams` option can only be specified for read-
   * write or partitioned DML transactions, otherwise the API returns an
   * `INVALID_ARGUMENT` error.
   *
   * @var bool
   */
  public $excludeTxnFromChangeStreams;
  /**
   * Isolation level for the transaction.
   *
   * @var string
   */
  public $isolationLevel;
  protected $partitionedDmlType = PartitionedDml::class;
  protected $partitionedDmlDataType = '';
  protected $readOnlyType = SpannerReadOnly::class;
  protected $readOnlyDataType = '';
  protected $readWriteType = ReadWrite::class;
  protected $readWriteDataType = '';

  /**
   * When `exclude_txn_from_change_streams` is set to `true`, it prevents read
   * or write transactions from being tracked in change streams. * If the DDL
   * option `allow_txn_exclusion` is set to `true`, then the updates made within
   * this transaction aren't recorded in the change stream. * If you don't set
   * the DDL option `allow_txn_exclusion` or if it's set to `false`, then the
   * updates made within this transaction are recorded in the change stream.
   * When `exclude_txn_from_change_streams` is set to `false` or not set,
   * modifications from this transaction are recorded in all change streams that
   * are tracking columns modified by these transactions. The
   * `exclude_txn_from_change_streams` option can only be specified for read-
   * write or partitioned DML transactions, otherwise the API returns an
   * `INVALID_ARGUMENT` error.
   *
   * @param bool $excludeTxnFromChangeStreams
   */
  public function setExcludeTxnFromChangeStreams($excludeTxnFromChangeStreams)
  {
    $this->excludeTxnFromChangeStreams = $excludeTxnFromChangeStreams;
  }
  /**
   * @return bool
   */
  public function getExcludeTxnFromChangeStreams()
  {
    return $this->excludeTxnFromChangeStreams;
  }
  /**
   * Isolation level for the transaction.
   *
   * Accepted values: ISOLATION_LEVEL_UNSPECIFIED, SERIALIZABLE, REPEATABLE_READ
   *
   * @param self::ISOLATION_LEVEL_* $isolationLevel
   */
  public function setIsolationLevel($isolationLevel)
  {
    $this->isolationLevel = $isolationLevel;
  }
  /**
   * @return self::ISOLATION_LEVEL_*
   */
  public function getIsolationLevel()
  {
    return $this->isolationLevel;
  }
  /**
   * Partitioned DML transaction. Authorization to begin a Partitioned DML
   * transaction requires `spanner.databases.beginPartitionedDmlTransaction`
   * permission on the `session` resource.
   *
   * @param PartitionedDml $partitionedDml
   */
  public function setPartitionedDml(PartitionedDml $partitionedDml)
  {
    $this->partitionedDml = $partitionedDml;
  }
  /**
   * @return PartitionedDml
   */
  public function getPartitionedDml()
  {
    return $this->partitionedDml;
  }
  /**
   * Transaction does not write. Authorization to begin a read-only transaction
   * requires `spanner.databases.beginReadOnlyTransaction` permission on the
   * `session` resource.
   *
   * @param SpannerReadOnly $readOnly
   */
  public function setReadOnly(SpannerReadOnly $readOnly)
  {
    $this->readOnly = $readOnly;
  }
  /**
   * @return SpannerReadOnly
   */
  public function getReadOnly()
  {
    return $this->readOnly;
  }
  /**
   * Transaction may write. Authorization to begin a read-write transaction
   * requires `spanner.databases.beginOrRollbackReadWriteTransaction` permission
   * on the `session` resource.
   *
   * @param ReadWrite $readWrite
   */
  public function setReadWrite(ReadWrite $readWrite)
  {
    $this->readWrite = $readWrite;
  }
  /**
   * @return ReadWrite
   */
  public function getReadWrite()
  {
    return $this->readWrite;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TransactionOptions::class, 'Google_Service_Spanner_TransactionOptions');
