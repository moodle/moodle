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

class ExecuteBatchDmlRequest extends \Google\Collection
{
  protected $collection_key = 'statements';
  /**
   * Optional. If set to `true`, this request marks the end of the transaction.
   * After these statements execute, you must commit or abort the transaction.
   * Attempts to execute any other requests against this transaction (including
   * reads and queries) are rejected. Setting this option might cause some error
   * reporting to be deferred until commit time (for example, validation of
   * unique constraints). Given this, successful execution of statements
   * shouldn't be assumed until a subsequent `Commit` call completes
   * successfully.
   *
   * @var bool
   */
  public $lastStatements;
  protected $requestOptionsType = RequestOptions::class;
  protected $requestOptionsDataType = '';
  /**
   * Required. A per-transaction sequence number used to identify this request.
   * This field makes each request idempotent such that if the request is
   * received multiple times, at most one succeeds. The sequence number must be
   * monotonically increasing within the transaction. If a request arrives for
   * the first time with an out-of-order sequence number, the transaction might
   * be aborted. Replays of previously handled requests yield the same response
   * as the first execution.
   *
   * @var string
   */
  public $seqno;
  protected $statementsType = Statement::class;
  protected $statementsDataType = 'array';
  protected $transactionType = TransactionSelector::class;
  protected $transactionDataType = '';

  /**
   * Optional. If set to `true`, this request marks the end of the transaction.
   * After these statements execute, you must commit or abort the transaction.
   * Attempts to execute any other requests against this transaction (including
   * reads and queries) are rejected. Setting this option might cause some error
   * reporting to be deferred until commit time (for example, validation of
   * unique constraints). Given this, successful execution of statements
   * shouldn't be assumed until a subsequent `Commit` call completes
   * successfully.
   *
   * @param bool $lastStatements
   */
  public function setLastStatements($lastStatements)
  {
    $this->lastStatements = $lastStatements;
  }
  /**
   * @return bool
   */
  public function getLastStatements()
  {
    return $this->lastStatements;
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
   * Required. A per-transaction sequence number used to identify this request.
   * This field makes each request idempotent such that if the request is
   * received multiple times, at most one succeeds. The sequence number must be
   * monotonically increasing within the transaction. If a request arrives for
   * the first time with an out-of-order sequence number, the transaction might
   * be aborted. Replays of previously handled requests yield the same response
   * as the first execution.
   *
   * @param string $seqno
   */
  public function setSeqno($seqno)
  {
    $this->seqno = $seqno;
  }
  /**
   * @return string
   */
  public function getSeqno()
  {
    return $this->seqno;
  }
  /**
   * Required. The list of statements to execute in this batch. Statements are
   * executed serially, such that the effects of statement `i` are visible to
   * statement `i+1`. Each statement must be a DML statement. Execution stops at
   * the first failed statement; the remaining statements are not executed.
   * Callers must provide at least one statement.
   *
   * @param Statement[] $statements
   */
  public function setStatements($statements)
  {
    $this->statements = $statements;
  }
  /**
   * @return Statement[]
   */
  public function getStatements()
  {
    return $this->statements;
  }
  /**
   * Required. The transaction to use. Must be a read-write transaction. To
   * protect against replays, single-use transactions are not supported. The
   * caller must either supply an existing transaction ID or begin a new
   * transaction.
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
class_alias(ExecuteBatchDmlRequest::class, 'Google_Service_Spanner_ExecuteBatchDmlRequest');
