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

class CommitRequest extends \Google\Collection
{
  protected $collection_key = 'mutations';
  /**
   * Optional. The amount of latency this request is configured to incur in
   * order to improve throughput. If this field isn't set, Spanner assumes
   * requests are relatively latency sensitive and automatically determines an
   * appropriate delay time. You can specify a commit delay value between 0 and
   * 500 ms.
   *
   * @var string
   */
  public $maxCommitDelay;
  protected $mutationsType = Mutation::class;
  protected $mutationsDataType = 'array';
  protected $precommitTokenType = MultiplexedSessionPrecommitToken::class;
  protected $precommitTokenDataType = '';
  protected $requestOptionsType = RequestOptions::class;
  protected $requestOptionsDataType = '';
  /**
   * If `true`, then statistics related to the transaction is included in the
   * CommitResponse. Default value is `false`.
   *
   * @var bool
   */
  public $returnCommitStats;
  protected $singleUseTransactionType = TransactionOptions::class;
  protected $singleUseTransactionDataType = '';
  /**
   * Commit a previously-started transaction.
   *
   * @var string
   */
  public $transactionId;

  /**
   * Optional. The amount of latency this request is configured to incur in
   * order to improve throughput. If this field isn't set, Spanner assumes
   * requests are relatively latency sensitive and automatically determines an
   * appropriate delay time. You can specify a commit delay value between 0 and
   * 500 ms.
   *
   * @param string $maxCommitDelay
   */
  public function setMaxCommitDelay($maxCommitDelay)
  {
    $this->maxCommitDelay = $maxCommitDelay;
  }
  /**
   * @return string
   */
  public function getMaxCommitDelay()
  {
    return $this->maxCommitDelay;
  }
  /**
   * The mutations to be executed when this transaction commits. All mutations
   * are applied atomically, in the order they appear in this list.
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
   * Optional. If the read-write transaction was executed on a multiplexed
   * session, then you must include the precommit token with the highest
   * sequence number received in this transaction attempt. Failing to do so
   * results in a `FailedPrecondition` error.
   *
   * @param MultiplexedSessionPrecommitToken $precommitToken
   */
  public function setPrecommitToken(MultiplexedSessionPrecommitToken $precommitToken)
  {
    $this->precommitToken = $precommitToken;
  }
  /**
   * @return MultiplexedSessionPrecommitToken
   */
  public function getPrecommitToken()
  {
    return $this->precommitToken;
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
   * If `true`, then statistics related to the transaction is included in the
   * CommitResponse. Default value is `false`.
   *
   * @param bool $returnCommitStats
   */
  public function setReturnCommitStats($returnCommitStats)
  {
    $this->returnCommitStats = $returnCommitStats;
  }
  /**
   * @return bool
   */
  public function getReturnCommitStats()
  {
    return $this->returnCommitStats;
  }
  /**
   * Execute mutations in a temporary transaction. Note that unlike commit of a
   * previously-started transaction, commit with a temporary transaction is non-
   * idempotent. That is, if the `CommitRequest` is sent to Cloud Spanner more
   * than once (for instance, due to retries in the application, or in the
   * transport library), it's possible that the mutations are executed more than
   * once. If this is undesirable, use BeginTransaction and Commit instead.
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
   * Commit a previously-started transaction.
   *
   * @param string $transactionId
   */
  public function setTransactionId($transactionId)
  {
    $this->transactionId = $transactionId;
  }
  /**
   * @return string
   */
  public function getTransactionId()
  {
    return $this->transactionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CommitRequest::class, 'Google_Service_Spanner_CommitRequest');
