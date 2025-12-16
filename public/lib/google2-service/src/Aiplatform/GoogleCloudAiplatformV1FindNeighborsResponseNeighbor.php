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

class GoogleCloudAiplatformV1FindNeighborsResponseNeighbor extends \Google\Model
{
  protected $datapointType = GoogleCloudAiplatformV1IndexDatapoint::class;
  protected $datapointDataType = '';
  /**
   * The distance between the neighbor and the dense embedding query.
   *
   * @var 
   */
  public $distance;
  /**
   * The distance between the neighbor and the query sparse_embedding.
   *
   * @var 
   */
  public $sparseDistance;

  /**
   * The datapoint of the neighbor. Note that full datapoints are returned only
   * when "return_full_datapoint" is set to true. Otherwise, only the
   * "datapoint_id" and "crowding_tag" fields are populated.
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
  public function setDistance($distance)
  {
    $this->distance = $distance;
  }
  public function getDistance()
  {
    return $this->distance;
  }
  public function setSparseDistance($sparseDistance)
  {
    $this->sparseDistance = $sparseDistance;
  }
  public function getSparseDistance()
  {
    return $this->sparseDistance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FindNeighborsResponseNeighbor::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FindNeighborsResponseNeighbor');
