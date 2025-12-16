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

class ResultSet extends \Google\Collection
{
  protected $collection_key = 'rows';
  protected $metadataType = ResultSetMetadata::class;
  protected $metadataDataType = '';
  protected $precommitTokenType = MultiplexedSessionPrecommitToken::class;
  protected $precommitTokenDataType = '';
  /**
   * Each element in `rows` is a row whose format is defined by
   * metadata.row_type. The ith element in each row matches the ith field in
   * metadata.row_type. Elements are encoded based on type as described here.
   *
   * @var array[]
   */
  public $rows;
  protected $statsType = ResultSetStats::class;
  protected $statsDataType = '';

  /**
   * Metadata about the result set, such as row type information.
   *
   * @param ResultSetMetadata $metadata
   */
  public function setMetadata(ResultSetMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return ResultSetMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Optional. A precommit token is included if the read-write transaction is on
   * a multiplexed session. Pass the precommit token with the highest sequence
   * number from this transaction attempt to the Commit request for this
   * transaction.
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
   * Each element in `rows` is a row whose format is defined by
   * metadata.row_type. The ith element in each row matches the ith field in
   * metadata.row_type. Elements are encoded based on type as described here.
   *
   * @param array[] $rows
   */
  public function setRows($rows)
  {
    $this->rows = $rows;
  }
  /**
   * @return array[]
   */
  public function getRows()
  {
    return $this->rows;
  }
  /**
   * Query plan and execution statistics for the SQL statement that produced
   * this result set. These can be requested by setting
   * ExecuteSqlRequest.query_mode. DML statements always produce stats
   * containing the number of rows modified, unless executed using the
   * ExecuteSqlRequest.QueryMode.PLAN ExecuteSqlRequest.query_mode. Other fields
   * might or might not be populated, based on the ExecuteSqlRequest.query_mode.
   *
   * @param ResultSetStats $stats
   */
  public function setStats(ResultSetStats $stats)
  {
    $this->stats = $stats;
  }
  /**
   * @return ResultSetStats
   */
  public function getStats()
  {
    return $this->stats;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResultSet::class, 'Google_Service_Spanner_ResultSet');
