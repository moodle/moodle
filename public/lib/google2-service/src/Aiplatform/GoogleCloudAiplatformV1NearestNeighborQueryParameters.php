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

class GoogleCloudAiplatformV1NearestNeighborQueryParameters extends \Google\Model
{
  /**
   * Optional. The number of neighbors to find via approximate search before
   * exact reordering is performed; if set, this value must be > neighbor_count.
   *
   * @var int
   */
  public $approximateNeighborCandidates;
  /**
   * Optional. The fraction of the number of leaves to search, set at query time
   * allows user to tune search performance. This value increase result in both
   * search accuracy and latency increase. The value should be between 0.0 and
   * 1.0.
   *
   * @var 
   */
  public $leafNodesSearchFraction;

  /**
   * Optional. The number of neighbors to find via approximate search before
   * exact reordering is performed; if set, this value must be > neighbor_count.
   *
   * @param int $approximateNeighborCandidates
   */
  public function setApproximateNeighborCandidates($approximateNeighborCandidates)
  {
    $this->approximateNeighborCandidates = $approximateNeighborCandidates;
  }
  /**
   * @return int
   */
  public function getApproximateNeighborCandidates()
  {
    return $this->approximateNeighborCandidates;
  }
  public function setLeafNodesSearchFraction($leafNodesSearchFraction)
  {
    $this->leafNodesSearchFraction = $leafNodesSearchFraction;
  }
  public function getLeafNodesSearchFraction()
  {
    return $this->leafNodesSearchFraction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1NearestNeighborQueryParameters::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1NearestNeighborQueryParameters');
