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

class GoogleCloudAiplatformV1NearestNeighborsNeighbor extends \Google\Model
{
  /**
   * The distance between the neighbor and the query vector.
   *
   * @var 
   */
  public $distance;
  /**
   * The id of the similar entity.
   *
   * @var string
   */
  public $entityId;
  protected $entityKeyValuesType = GoogleCloudAiplatformV1FetchFeatureValuesResponse::class;
  protected $entityKeyValuesDataType = '';

  public function setDistance($distance)
  {
    $this->distance = $distance;
  }
  public function getDistance()
  {
    return $this->distance;
  }
  /**
   * The id of the similar entity.
   *
   * @param string $entityId
   */
  public function setEntityId($entityId)
  {
    $this->entityId = $entityId;
  }
  /**
   * @return string
   */
  public function getEntityId()
  {
    return $this->entityId;
  }
  /**
   * The attributes of the neighbor, e.g. filters, crowding and metadata Note
   * that full entities are returned only when "return_full_entity" is set to
   * true. Otherwise, only the "entity_id" and "distance" fields are populated.
   *
   * @param GoogleCloudAiplatformV1FetchFeatureValuesResponse $entityKeyValues
   */
  public function setEntityKeyValues(GoogleCloudAiplatformV1FetchFeatureValuesResponse $entityKeyValues)
  {
    $this->entityKeyValues = $entityKeyValues;
  }
  /**
   * @return GoogleCloudAiplatformV1FetchFeatureValuesResponse
   */
  public function getEntityKeyValues()
  {
    return $this->entityKeyValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1NearestNeighborsNeighbor::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1NearestNeighborsNeighbor');
