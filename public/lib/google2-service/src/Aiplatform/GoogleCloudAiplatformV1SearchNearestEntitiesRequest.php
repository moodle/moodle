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

class GoogleCloudAiplatformV1SearchNearestEntitiesRequest extends \Google\Model
{
  protected $queryType = GoogleCloudAiplatformV1NearestNeighborQuery::class;
  protected $queryDataType = '';
  /**
   * Optional. If set to true, the full entities (including all vector values
   * and metadata) of the nearest neighbors are returned; otherwise only entity
   * id of the nearest neighbors will be returned. Note that returning full
   * entities will significantly increase the latency and cost of the query.
   *
   * @var bool
   */
  public $returnFullEntity;

  /**
   * Required. The query.
   *
   * @param GoogleCloudAiplatformV1NearestNeighborQuery $query
   */
  public function setQuery(GoogleCloudAiplatformV1NearestNeighborQuery $query)
  {
    $this->query = $query;
  }
  /**
   * @return GoogleCloudAiplatformV1NearestNeighborQuery
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * Optional. If set to true, the full entities (including all vector values
   * and metadata) of the nearest neighbors are returned; otherwise only entity
   * id of the nearest neighbors will be returned. Note that returning full
   * entities will significantly increase the latency and cost of the query.
   *
   * @param bool $returnFullEntity
   */
  public function setReturnFullEntity($returnFullEntity)
  {
    $this->returnFullEntity = $returnFullEntity;
  }
  /**
   * @return bool
   */
  public function getReturnFullEntity()
  {
    return $this->returnFullEntity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SearchNearestEntitiesRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SearchNearestEntitiesRequest');
