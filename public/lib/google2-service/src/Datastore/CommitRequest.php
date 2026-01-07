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

namespace Google\Service\Datastore;

class CommitRequest extends \Google\Collection
{
  /**
   * Unspecified. This value must not be used.
   */
  public const MODE_MODE_UNSPECIFIED = 'MODE_UNSPECIFIED';
  /**
   * Transactional: The mutations are either all applied, or none are applied.
   * Learn about transactions
   * [here](https://cloud.google.com/datastore/docs/concepts/transactions).
   */
  public const MODE_TRANSACTIONAL = 'TRANSACTIONAL';
  /**
   * Non-transactional: The mutations may not apply as all or none.
   */
  public const MODE_NON_TRANSACTIONAL = 'NON_TRANSACTIONAL';
  protected $collection_key = 'mutations';
  /**
   * The ID of the database against which to make the request. '(default)' is
   * not allowed; please use empty string '' to refer the default database.
   *
   * @var string
   */
  public $databaseId;
  /**
   * The type of commit to perform. Defaults to `TRANSACTIONAL`.
   *
   * @var string
   */
  public $mode;
  protected $mutationsType = Mutation::class;
  protected $mutationsDataType = 'array';
  protected $singleUseTransactionType = TransactionOptions::class;
  protected $singleUseTransactionDataType = '';
  /**
   * The identifier of the transaction associated with the commit. A transaction
   * identifier is returned by a call to Datastore.BeginTransaction.
   *
   * @var string
   */
  public $transaction;

  /**
   * The ID of the database against which to make the request. '(default)' is
   * not allowed; please use empty string '' to refer the default database.
   *
   * @param string $databaseId
   */
  public function setDatabaseId($databaseId)
  {
    $this->databaseId = $databaseId;
  }
  /**
   * @return string
   */
  public function getDatabaseId()
  {
    return $this->databaseId;
  }
  /**
   * The type of commit to perform. Defaults to `TRANSACTIONAL`.
   *
   * Accepted values: MODE_UNSPECIFIED, TRANSACTIONAL, NON_TRANSACTIONAL
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * The mutations to perform. When mode is `TRANSACTIONAL`, mutations affecting
   * a single entity are applied in order. The following sequences of mutations
   * affecting a single entity are not permitted in a single `Commit` request: -
   * `insert` followed by `insert` - `update` followed by `insert` - `upsert`
   * followed by `insert` - `delete` followed by `update` When mode is
   * `NON_TRANSACTIONAL`, no two mutations may affect a single entity.
   *
   * @param Mutation[] $mutations
   */
  public function setMutations($mutations)
  {
    $this->mutations = $mutations;
  }
  /**
   * @return Mutation[]
   */
  public function getMutations()
  {
    return $this->mutations;
  }
  /**
   * Options for beginning a new transaction for this request. The transaction
   * is committed when the request completes. If specified,
   * TransactionOptions.mode must be TransactionOptions.ReadWrite.
   *
   * @param TransactionOptions $singleUseTransaction
   */
  public function setSingleUseTransaction(TransactionOptions $singleUseTransaction)
  {
    $this->singleUseTransaction = $singleUseTransaction;
  }
  /**
   * @return TransactionOptions
   */
  public function getSingleUseTransaction()
  {
    return $this->singleUseTransaction;
  }
  /**
   * The identifier of the transaction associated with the commit. A transaction
   * identifier is returned by a call to Datastore.BeginTransaction.
   *
   * @param string $transaction
   */
  public function setTransaction($transaction)
  {
    $this->transaction = $transaction;
  }
  /**
   * @return string
   */
  public function getTransaction()
  {
    return $this->transaction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CommitRequest::class, 'Google_Service_Datastore_CommitRequest');
