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

class ReadOptions extends \Google\Model
{
  /**
   * Unspecified. This value must not be used.
   */
  public const READ_CONSISTENCY_READ_CONSISTENCY_UNSPECIFIED = 'READ_CONSISTENCY_UNSPECIFIED';
  /**
   * Strong consistency.
   */
  public const READ_CONSISTENCY_STRONG = 'STRONG';
  /**
   * Eventual consistency.
   */
  public const READ_CONSISTENCY_EVENTUAL = 'EVENTUAL';
  protected $newTransactionType = TransactionOptions::class;
  protected $newTransactionDataType = '';
  /**
   * The non-transactional read consistency to use.
   *
   * @var string
   */
  public $readConsistency;
  /**
   * Reads entities as they were at the given time. This value is only supported
   * for Cloud Firestore in Datastore mode. This must be a microsecond precision
   * timestamp within the past one hour, or if Point-in-Time Recovery is
   * enabled, can additionally be a whole minute timestamp within the past 7
   * days.
   *
   * @var string
   */
  public $readTime;
  /**
   * The identifier of the transaction in which to read. A transaction
   * identifier is returned by a call to Datastore.BeginTransaction.
   *
   * @var string
   */
  public $transaction;

  /**
   * Options for beginning a new transaction for this request. The new
   * transaction identifier will be returned in the corresponding response as
   * either LookupResponse.transaction or RunQueryResponse.transaction.
   *
   * @param TransactionOptions $newTransaction
   */
  public function setNewTransaction(TransactionOptions $newTransaction)
  {
    $this->newTransaction = $newTransaction;
  }
  /**
   * @return TransactionOptions
   */
  public function getNewTransaction()
  {
    return $this->newTransaction;
  }
  /**
   * The non-transactional read consistency to use.
   *
   * Accepted values: READ_CONSISTENCY_UNSPECIFIED, STRONG, EVENTUAL
   *
   * @param self::READ_CONSISTENCY_* $readConsistency
   */
  public function setReadConsistency($readConsistency)
  {
    $this->readConsistency = $readConsistency;
  }
  /**
   * @return self::READ_CONSISTENCY_*
   */
  public function getReadConsistency()
  {
    return $this->readConsistency;
  }
  /**
   * Reads entities as they were at the given time. This value is only supported
   * for Cloud Firestore in Datastore mode. This must be a microsecond precision
   * timestamp within the past one hour, or if Point-in-Time Recovery is
   * enabled, can additionally be a whole minute timestamp within the past 7
   * days.
   *
   * @param string $readTime
   */
  public function setReadTime($readTime)
  {
    $this->readTime = $readTime;
  }
  /**
   * @return string
   */
  public function getReadTime()
  {
    return $this->readTime;
  }
  /**
   * The identifier of the transaction in which to read. A transaction
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
class_alias(ReadOptions::class, 'Google_Service_Datastore_ReadOptions');
