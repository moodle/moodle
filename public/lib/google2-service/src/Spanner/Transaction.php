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

class Transaction extends \Google\Model
{
  /**
   * `id` may be used to identify the transaction in subsequent Read,
   * ExecuteSql, Commit, or Rollback calls. Single-use read-only transactions do
   * not have IDs, because single-use transactions do not support multiple
   * requests.
   *
   * @var string
   */
  public $id;
  protected $precommitTokenType = MultiplexedSessionPrecommitToken::class;
  protected $precommitTokenDataType = '';
  /**
   * For snapshot read-only transactions, the read timestamp chosen for the
   * transaction. Not returned by default: see
   * TransactionOptions.ReadOnly.return_read_timestamp. A timestamp in RFC3339
   * UTC \"Zulu\" format, accurate to nanoseconds. Example:
   * `"2014-10-02T15:01:23.045123456Z"`.
   *
   * @var string
   */
  public $readTimestamp;

  /**
   * `id` may be used to identify the transaction in subsequent Read,
   * ExecuteSql, Commit, or Rollback calls. Single-use read-only transactions do
   * not have IDs, because single-use transactions do not support multiple
   * requests.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * A precommit token is included in the response of a BeginTransaction request
   * if the read-write transaction is on a multiplexed session and a
   * mutation_key was specified in the BeginTransaction. The precommit token
   * with the highest sequence number from this transaction attempt should be
   * passed to the Commit request for this transaction.
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
   * For snapshot read-only transactions, the read timestamp chosen for the
   * transaction. Not returned by default: see
   * TransactionOptions.ReadOnly.return_read_timestamp. A timestamp in RFC3339
   * UTC \"Zulu\" format, accurate to nanoseconds. Example:
   * `"2014-10-02T15:01:23.045123456Z"`.
   *
   * @param string $readTimestamp
   */
  public function setReadTimestamp($readTimestamp)
  {
    $this->readTimestamp = $readTimestamp;
  }
  /**
   * @return string
   */
  public function getReadTimestamp()
  {
    return $this->readTimestamp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Transaction::class, 'Google_Service_Spanner_Transaction');
