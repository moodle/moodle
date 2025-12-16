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

namespace Google\Service\Firestore;

class RunQueryRequest extends \Google\Model
{
  protected $explainOptionsType = ExplainOptions::class;
  protected $explainOptionsDataType = '';
  protected $newTransactionType = TransactionOptions::class;
  protected $newTransactionDataType = '';
  /**
   * Reads documents as they were at the given time. This must be a microsecond
   * precision timestamp within the past one hour, or if Point-in-Time Recovery
   * is enabled, can additionally be a whole minute timestamp within the past 7
   * days.
   *
   * @var string
   */
  public $readTime;
  protected $structuredQueryType = StructuredQuery::class;
  protected $structuredQueryDataType = '';
  /**
   * Run the query within an already active transaction. The value here is the
   * opaque transaction ID to execute the query in.
   *
   * @var string
   */
  public $transaction;

  /**
   * Optional. Explain options for the query. If set, additional query
   * statistics will be returned. If not, only query results will be returned.
   *
   * @param ExplainOptions $explainOptions
   */
  public function setExplainOptions(ExplainOptions $explainOptions)
  {
    $this->explainOptions = $explainOptions;
  }
  /**
   * @return ExplainOptions
   */
  public function getExplainOptions()
  {
    return $this->explainOptions;
  }
  /**
   * Starts a new transaction and reads the documents. Defaults to a read-only
   * transaction. The new transaction ID will be returned as the first response
   * in the stream.
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
   * Reads documents as they were at the given time. This must be a microsecond
   * precision timestamp within the past one hour, or if Point-in-Time Recovery
   * is enabled, can additionally be a whole minute timestamp within the past 7
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
   * A structured query.
   *
   * @param StructuredQuery $structuredQuery
   */
  public function setStructuredQuery(StructuredQuery $structuredQuery)
  {
    $this->structuredQuery = $structuredQuery;
  }
  /**
   * @return StructuredQuery
   */
  public function getStructuredQuery()
  {
    return $this->structuredQuery;
  }
  /**
   * Run the query within an already active transaction. The value here is the
   * opaque transaction ID to execute the query in.
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
class_alias(RunQueryRequest::class, 'Google_Service_Firestore_RunQueryRequest');
