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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1FindNeighborsRequest extends \Google\Collection
{
  protected $collection_key = 'queries';
  /**
   * The ID of the DeployedIndex that will serve the request. This request is
   * sent to a specific IndexEndpoint, as per the IndexEndpoint.network. That
   * IndexEndpoint also has IndexEndpoint.deployed_indexes, and each such index
   * has a DeployedIndex.id field. The value of the field below must equal one
   * of the DeployedIndex.id fields of the IndexEndpoint that is being called
   * for this request.
   *
   * @var string
   */
  public $deployedIndexId;
  protected $queriesType = GoogleCloudAiplatformV1FindNeighborsRequestQuery::class;
  protected $queriesDataType = 'array';
  /**
   * If set to true, the full datapoints (including all vector values and
   * restricts) of the nearest neighbors are returned. Note that returning full
   * datapoint will significantly increase the latency and cost of the query.
   *
   * @var bool
   */
  public $returnFullDatapoint;

  /**
   * The ID of the DeployedIndex that will serve the request. This request is
   * sent to a specific IndexEndpoint, as per the IndexEndpoint.network. That
   * IndexEndpoint also has IndexEndpoint.deployed_indexes, and each such index
   * has a DeployedIndex.id field. The value of the field below must equal one
   * of the DeployedIndex.id fields of the IndexEndpoint that is being called
   * for this request.
   *
   * @param string $deployedIndexId
   */
  public function setDeployedIndexId($deployedIndexId)
  {
    $this->deployedIndexId = $deployedIndexId;
  }
  /**
   * @return string
   */
  public function getDeployedIndexId()
  {
    return $this->deployedIndexId;
  }
  /**
   * The list of queries.
   *
   * @param GoogleCloudAiplatformV1FindNeighborsRequestQuery[] $queries
   */
  public function setQueries($queries)
  {
    $this->queries = $queries;
  }
  /**
   * @return GoogleCloudAiplatformV1FindNeighborsRequestQuery[]
   */
  public function getQueries()
  {
    return $this->queries;
  }
  /**
   * If set to true, the full datapoints (including all vector values and
   * restricts) of the nearest neighbors are returned. Note that returning full
   * datapoint will significantly increase the latency and cost of the query.
   *
   * @param bool $returnFullDatapoint
   */
  public function setReturnFullDatapoint($returnFullDatapoint)
  {
    $this->returnFullDatapoint = $returnFullDatapoint;
  }
  /**
   * @return bool
   */
  public function getReturnFullDatapoint()
  {
    return $this->returnFullDatapoint;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FindNeighborsRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FindNeighborsRequest');
