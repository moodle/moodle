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

class ReadRequest extends \Google\Collection
{
  /**
   * Default value. `LOCK_HINT_UNSPECIFIED` is equivalent to `LOCK_HINT_SHARED`.
   */
  public const LOCK_HINT_LOCK_HINT_UNSPECIFIED = 'LOCK_HINT_UNSPECIFIED';
  /**
   * Acquire shared locks. By default when you perform a read as part of a read-
   * write transaction, Spanner acquires shared read locks, which allows other
   * reads to still access the data until your transaction is ready to commit.
   * When your transaction is committing and writes are being applied, the
   * transaction attempts to upgrade to an exclusive lock for any data you are
   * writing. For more information about locks, see [Lock
   * modes](https://cloud.google.com/spanner/docs/introspection/lock-
   * statistics#explain-lock-modes).
   */
  public const LOCK_HINT_LOCK_HINT_SHARED = 'LOCK_HINT_SHARED';
  /**
   * Acquire exclusive locks. Requesting exclusive locks is beneficial if you
   * observe high write contention, which means you notice that multiple
   * transactions are concurrently trying to read and write to the same data,
   * resulting in a large number of aborts. This problem occurs when two
   * transactions initially acquire shared locks and then both try to upgrade to
   * exclusive locks at the same time. In this situation both transactions are
   * waiting for the other to give up their lock, resulting in a deadlocked
   * situation. Spanner is able to detect this occurring and force one of the
   * transactions to abort. However, this is a slow and expensive operation and
   * results in lower performance. In this case it makes sense to acquire
   * exclusive locks at the start of the transaction because then when multiple
   * transactions try to act on the same data, they automatically get
   * serialized. Each transaction waits its turn to acquire the lock and avoids
   * getting into deadlock situations. Because the exclusive lock hint is just a
   * hint, it shouldn't be considered equivalent to a mutex. In other words, you
   * shouldn't use Spanner exclusive locks as a mutual exclusion mechanism for
   * the execution of code outside of Spanner. **Note:** Request exclusive locks
   * judiciously because they block others from reading that data for the entire
   * transaction, rather than just when the writes are being performed. Unless
   * you observe high write contention, you should use the default of shared
   * read locks so you don't prematurely block other clients from reading the
   * data that you're writing to.
   */
  public const LOCK_HINT_LOCK_HINT_EXCLUSIVE = 'LOCK_HINT_EXCLUSIVE';
  /**
   * Default value. `ORDER_BY_UNSPECIFIED` is equivalent to
   * `ORDER_BY_PRIMARY_KEY`.
   */
  public const ORDER_BY_ORDER_BY_UNSPECIFIED = 'ORDER_BY_UNSPECIFIED';
  /**
   * Read rows are returned in primary key order. In the event that this option
   * is used in conjunction with the `partition_token` field, the API returns an
   * `INVALID_ARGUMENT` error.
   */
  public const ORDER_BY_ORDER_BY_PRIMARY_KEY = 'ORDER_BY_PRIMARY_KEY';
  /**
   * Read rows are returned in any order.
   */
  public const ORDER_BY_ORDER_BY_NO_ORDER = 'ORDER_BY_NO_ORDER';
  protected $collection_key = 'columns';
  /**
   * Required. The columns of table to be returned for each row matching this
   * request.
   *
   * @var string[]
   */
  public $columns;
  /**
   * If this is for a partitioned read and this field is set to `true`, the
   * request is executed with Spanner Data Boost independent compute resources.
   * If the field is set to `true` but the request doesn't set
   * `partition_token`, the API returns an `INVALID_ARGUMENT` error.
   *
   * @var bool
   */
  public $dataBoostEnabled;
  protected $directedReadOptionsType = DirectedReadOptions::class;
  protected $directedReadOptionsDataType = '';
  /**
   * If non-empty, the name of an index on table. This index is used instead of
   * the table primary key when interpreting key_set and sorting result rows.
   * See key_set for further information.
   *
   * @var string
   */
  public $index;
  protected $keySetType = KeySet::class;
  protected $keySetDataType = '';
  /**
   * If greater than zero, only the first `limit` rows are yielded. If `limit`
   * is zero, the default is no limit. A limit can't be specified if
   * `partition_token` is set.
   *
   * @var string
   */
  public $limit;
  /**
   * Optional. Lock Hint for the request, it can only be used with read-write
   * transactions.
   *
   * @var string
   */
  public $lockHint;
  /**
   * Optional. Order for the returned rows. By default, Spanner returns result
   * rows in primary key order except for PartitionRead requests. For
   * applications that don't require rows to be returned in primary key
   * (`ORDER_BY_PRIMARY_KEY`) order, setting `ORDER_BY_NO_ORDER` option allows
   * Spanner to optimize row retrieval, resulting in lower latencies in certain
   * cases (for example, bulk point lookups).
   *
   * @var string
   */
  public $orderBy;
  /**
   * If present, results are restricted to the specified partition previously
   * created using `PartitionRead`. There must be an exact match for the values
   * of fields common to this message and the PartitionReadRequest message used
   * to create this partition_token.
   *
   * @var string
   */
  public $partitionToken;
  protected $requestOptionsType = RequestOptions::class;
  protected $requestOptionsDataType = '';
  /**
   * If this request is resuming a previously interrupted read, `resume_token`
   * should be copied from the last PartialResultSet yielded before the
   * interruption. Doing this enables the new read to resume where the last read
   * left off. The rest of the request parameters must exactly match the request
   * that yielded this token.
   *
   * @var string
   */
  public $resumeToken;
  /**
   * Required. The name of the table in the database to be read.
   *
   * @var string
   */
  public $table;
  protected $transactionType = TransactionSelector::class;
  protected $transactionDataType = '';

  /**
   * Required. The columns of table to be returned for each row matching this
   * request.
   *
   * @param string[] $columns
   */
  public function setColumns($columns)
  {
    $this->columns = $columns;
  }
  /**
   * @return string[]
   */
  public function getColumns()
  {
    return $this->columns;
  }
  /**
   * If this is for a partitioned read and this field is set to `true`, the
   * request is executed with Spanner Data Boost independent compute resources.
   * If the field is set to `true` but the request doesn't set
   * `partition_token`, the API returns an `INVALID_ARGUMENT` error.
   *
   * @param bool $dataBoostEnabled
   */
  public function setDataBoostEnabled($dataBoostEnabled)
  {
    $this->dataBoostEnabled = $dataBoostEnabled;
  }
  /**
   * @return bool
   */
  public function getDataBoostEnabled()
  {
    return $this->dataBoostEnabled;
  }
  /**
   * Directed read options for this request.
   *
   * @param DirectedReadOptions $directedReadOptions
   */
  public function setDirectedReadOptions(DirectedReadOptions $directedReadOptions)
  {
    $this->directedReadOptions = $directedReadOptions;
  }
  /**
   * @return DirectedReadOptions
   */
  public function getDirectedReadOptions()
  {
    return $this->directedReadOptions;
  }
  /**
   * If non-empty, the name of an index on table. This index is used instead of
   * the table primary key when interpreting key_set and sorting result rows.
   * See key_set for further information.
   *
   * @param string $index
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }
  /**
   * @return string
   */
  public function getIndex()
  {
    return $this->index;
  }
  /**
   * Required. `key_set` identifies the rows to be yielded. `key_set` names the
   * primary keys of the rows in table to be yielded, unless index is present.
   * If index is present, then key_set instead names index keys in index. If the
   * partition_token field is empty, rows are yielded in table primary key order
   * (if index is empty) or index key order (if index is non-empty). If the
   * partition_token field isn't empty, rows are yielded in an unspecified
   * order. It isn't an error for the `key_set` to name rows that don't exist in
   * the database. Read yields nothing for nonexistent rows.
   *
   * @param KeySet $keySet
   */
  public function setKeySet(KeySet $keySet)
  {
    $this->keySet = $keySet;
  }
  /**
   * @return KeySet
   */
  public function getKeySet()
  {
    return $this->keySet;
  }
  /**
   * If greater than zero, only the first `limit` rows are yielded. If `limit`
   * is zero, the default is no limit. A limit can't be specified if
   * `partition_token` is set.
   *
   * @param string $limit
   */
  public function setLimit($limit)
  {
    $this->limit = $limit;
  }
  /**
   * @return string
   */
  public function getLimit()
  {
    return $this->limit;
  }
  /**
   * Optional. Lock Hint for the request, it can only be used with read-write
   * transactions.
   *
   * Accepted values: LOCK_HINT_UNSPECIFIED, LOCK_HINT_SHARED,
   * LOCK_HINT_EXCLUSIVE
   *
   * @param self::LOCK_HINT_* $lockHint
   */
  public function setLockHint($lockHint)
  {
    $this->lockHint = $lockHint;
  }
  /**
   * @return self::LOCK_HINT_*
   */
  public function getLockHint()
  {
    return $this->lockHint;
  }
  /**
   * Optional. Order for the returned rows. By default, Spanner returns result
   * rows in primary key order except for PartitionRead requests. For
   * applications that don't require rows to be returned in primary key
   * (`ORDER_BY_PRIMARY_KEY`) order, setting `ORDER_BY_NO_ORDER` option allows
   * Spanner to optimize row retrieval, resulting in lower latencies in certain
   * cases (for example, bulk point lookups).
   *
   * Accepted values: ORDER_BY_UNSPECIFIED, ORDER_BY_PRIMARY_KEY,
   * ORDER_BY_NO_ORDER
   *
   * @param self::ORDER_BY_* $orderBy
   */
  public function setOrderBy($orderBy)
  {
    $this->orderBy = $orderBy;
  }
  /**
   * @return self::ORDER_BY_*
   */
  public function getOrderBy()
  {
    return $this->orderBy;
  }
  /**
   * If present, results are restricted to the specified partition previously
   * created using `PartitionRead`. There must be an exact match for the values
   * of fields common to this message and the PartitionReadRequest message used
   * to create this partition_token.
   *
   * @param string $partitionToken
   */
  public function setPartitionToken($partitionToken)
  {
    $this->partitionToken = $partitionToken;
  }
  /**
   * @return string
   */
  public function getPartitionToken()
  {
    return $this->partitionToken;
  }
  /**
   * Common options for this request.
   *
   * @param RequestOptions $requestOptions
   */
  public function setRequestOptions(RequestOptions $requestOptions)
  {
    $this->requestOptions = $requestOptions;
  }
  /**
   * @return RequestOptions
   */
  public function getRequestOptions()
  {
    return $this->requestOptions;
  }
  /**
   * If this request is resuming a previously interrupted read, `resume_token`
   * should be copied from the last PartialResultSet yielded before the
   * interruption. Doing this enables the new read to resume where the last read
   * left off. The rest of the request parameters must exactly match the request
   * that yielded this token.
   *
   * @param string $resumeToken
   */
  public function setResumeToken($resumeToken)
  {
    $this->resumeToken = $resumeToken;
  }
  /**
   * @return string
   */
  public function getResumeToken()
  {
    return $this->resumeToken;
  }
  /**
   * Required. The name of the table in the database to be read.
   *
   * @param string $table
   */
  public function setTable($table)
  {
    $this->table = $table;
  }
  /**
   * @return string
   */
  public function getTable()
  {
    return $this->table;
  }
  /**
   * The transaction to use. If none is provided, the default is a temporary
   * read-only transaction with strong concurrency.
   *
   * @param TransactionSelector $transaction
   */
  public function setTransaction(TransactionSelector $transaction)
  {
    $this->transaction = $transaction;
  }
  /**
   * @return TransactionSelector
   */
  public function getTransaction()
  {
    return $this->transaction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReadRequest::class, 'Google_Service_Spanner_ReadRequest');
