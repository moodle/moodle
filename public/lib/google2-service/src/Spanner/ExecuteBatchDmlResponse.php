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

class ExecuteBatchDmlResponse extends \Google\Collection
{
  protected $collection_key = 'resultSets';
  protected $precommitTokenType = MultiplexedSessionPrecommitToken::class;
  protected $precommitTokenDataType = '';
  protected $resultSetsType = ResultSet::class;
  protected $resultSetsDataType = 'array';
  protected $statusType = Status::class;
  protected $statusDataType = '';

  /**
   * Optional. A precommit token is included if the read-write transaction is on
   * a multiplexed session. Pass the precommit token with the highest sequence
   * number from this transaction attempt should be passed to the Commit request
   * for this transaction.
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
   * One ResultSet for each statement in the request that ran successfully, in
   * the same order as the statements in the request. Each ResultSet does not
   * contain any rows. The ResultSetStats in each ResultSet contain the number
   * of rows modified by the statement. Only the first ResultSet in the response
   * contains valid ResultSetMetadata.
   *
   * @param ResultSet[] $resultSets
   */
  public function setResultSets($resultSets)
  {
    $this->resultSets = $resultSets;
  }
  /**
   * @return ResultSet[]
   */
  public function getResultSets()
  {
    return $this->resultSets;
  }
  /**
   * If all DML statements are executed successfully, the status is `OK`.
   * Otherwise, the error status of the first failed statement.
   *
   * @param Status $status
   */
  public function setStatus(Status $status)
  {
    $this->status = $status;
  }
  /**
   * @return Status
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExecuteBatchDmlResponse::class, 'Google_Service_Spanner_ExecuteBatchDmlResponse');
