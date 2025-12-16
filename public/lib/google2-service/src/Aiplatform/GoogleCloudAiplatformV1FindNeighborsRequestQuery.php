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

class GoogleCloudAiplatformV1FindNeighborsRequestQuery extends \Google\Model
{
  /**
   * The number of neighbors to find via approximate search before exact
   * reordering is performed. If not set, the default value from scam config is
   * used; if set, this value must be > 0.
   *
   * @var int
   */
  public $approximateNeighborCount;
  protected $datapointType = GoogleCloudAiplatformV1IndexDatapoint::class;
  protected $datapointDataType = '';
  /**
   * The fraction of the number of leaves to search, set at query time allows
   * user to tune search performance. This value increase result in both search
   * accuracy and latency increase. The value should be between 0.0 and 1.0. If
   * not set or set to 0.0, query uses the default value specified in
   * NearestNeighborSearchConfig.TreeAHConfig.fraction_leaf_nodes_to_search.
   *
   * @var 
   */
  public $fractionLeafNodesToSearchOverride;
  /**
   * The number of nearest neighbors to be retrieved from database for each
   * query. If not set, will use the default from the service configuration
   * (https://cloud.google.com/vertex-ai/docs/matching-engine/configuring-
   * indexes#nearest-neighbor-search-config).
   *
   * @var int
   */
  public $neighborCount;
  /**
   * Crowding is a constraint on a neighbor list produced by nearest neighbor
   * search requiring that no more than some value k' of the k neighbors
   * returned have the same value of crowding_attribute. It's used for improving
   * result diversity. This field is the maximum number of matches with the same
   * crowding tag.
   *
   * @var int
   */
  public $perCrowdingAttributeNeighborCount;
  protected $rrfType = GoogleCloudAiplatformV1FindNeighborsRequestQueryRRF::class;
  protected $rrfDataType = '';

  /**
   * The number of neighbors to find via approximate search before exact
   * reordering is performed. If not set, the default value from scam config is
   * used; if set, this value must be > 0.
   *
   * @param int $approximateNeighborCount
   */
  public function setApproximateNeighborCount($approximateNeighborCount)
  {
    $this->approximateNeighborCount = $approximateNeighborCount;
  }
  /**
   * @return int
   */
  public function getApproximateNeighborCount()
  {
    return $this->approximateNeighborCount;
  }
  /**
   * Required. The datapoint/vector whose nearest neighbors should be searched
   * for.
   *
   * @param GoogleCloudAiplatformV1IndexDatapoint $datapoint
   */
  public function setDatapoint(GoogleCloudAiplatformV1IndexDatapoint $datapoint)
  {
    $this->datapoint = $datapoint;
  }
  /**
   * @return GoogleCloudAiplatformV1IndexDatapoint
   */
  public function getDatapoint()
  {
    return $this->datapoint;
  }
  public function setFractionLeafNodesToSearchOverride($fractionLeafNodesToSearchOverride)
  {
    $this->fractionLeafNodesToSearchOverride = $fractionLeafNodesToSearchOverride;
  }
  public function getFractionLeafNodesToSearchOverride()
  {
    return $this->fractionLeafNodesToSearchOverride;
  }
  /**
   * The number of nearest neighbors to be retrieved from database for each
   * query. If not set, will use the default from the service configuration
   * (https://cloud.google.com/vertex-ai/docs/matching-engine/configuring-
   * indexes#nearest-neighbor-search-config).
   *
   * @param int $neighborCount
   */
  public function setNeighborCount($neighborCount)
  {
    $this->neighborCount = $neighborCount;
  }
  /**
   * @return int
   */
  public function getNeighborCount()
  {
    return $this->neighborCount;
  }
  /**
   * Crowding is a constraint on a neighbor list produced by nearest neighbor
   * search requiring that no more than some value k' of the k neighbors
   * returned have the same value of crowding_attribute. It's used for improving
   * result diversity. This field is the maximum number of matches with the same
   * crowding tag.
   *
   * @param int $perCrowdingAttributeNeighborCount
   */
  public function setPerCrowdingAttributeNeighborCount($perCrowdingAttributeNeighborCount)
  {
    $this->perCrowdingAttributeNeighborCount = $perCrowdingAttributeNeighborCount;
  }
  /**
   * @return int
   */
  public function getPerCrowdingAttributeNeighborCount()
  {
    return $this->perCrowdingAttributeNeighborCount;
  }
  /**
   * Optional. Represents RRF algorithm that combines search results.
   *
   * @param GoogleCloudAiplatformV1FindNeighborsRequestQueryRRF $rrf
   */
  public function setRrf(GoogleCloudAiplatformV1FindNeighborsRequestQueryRRF $rrf)
  {
    $this->rrf = $rrf;
  }
  /**
   * @return GoogleCloudAiplatformV1FindNeighborsRequestQueryRRF
   */
  public function getRrf()
  {
    return $this->rrf;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FindNeighborsRequestQuery::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FindNeighborsRequestQuery');
