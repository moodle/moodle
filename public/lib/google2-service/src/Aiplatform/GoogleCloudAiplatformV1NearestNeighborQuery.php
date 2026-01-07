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

class GoogleCloudAiplatformV1NearestNeighborQuery extends \Google\Collection
{
  protected $collection_key = 'stringFilters';
  protected $embeddingType = GoogleCloudAiplatformV1NearestNeighborQueryEmbedding::class;
  protected $embeddingDataType = '';
  /**
   * Optional. The entity id whose similar entities should be searched for. If
   * embedding is set, search will use embedding instead of entity_id.
   *
   * @var string
   */
  public $entityId;
  /**
   * Optional. The number of similar entities to be retrieved from feature view
   * for each query.
   *
   * @var int
   */
  public $neighborCount;
  protected $numericFiltersType = GoogleCloudAiplatformV1NearestNeighborQueryNumericFilter::class;
  protected $numericFiltersDataType = 'array';
  protected $parametersType = GoogleCloudAiplatformV1NearestNeighborQueryParameters::class;
  protected $parametersDataType = '';
  /**
   * Optional. Crowding is a constraint on a neighbor list produced by nearest
   * neighbor search requiring that no more than
   * sper_crowding_attribute_neighbor_count of the k neighbors returned have the
   * same value of crowding_attribute. It's used for improving result diversity.
   *
   * @var int
   */
  public $perCrowdingAttributeNeighborCount;
  protected $stringFiltersType = GoogleCloudAiplatformV1NearestNeighborQueryStringFilter::class;
  protected $stringFiltersDataType = 'array';

  /**
   * Optional. The embedding vector that be used for similar search.
   *
   * @param GoogleCloudAiplatformV1NearestNeighborQueryEmbedding $embedding
   */
  public function setEmbedding(GoogleCloudAiplatformV1NearestNeighborQueryEmbedding $embedding)
  {
    $this->embedding = $embedding;
  }
  /**
   * @return GoogleCloudAiplatformV1NearestNeighborQueryEmbedding
   */
  public function getEmbedding()
  {
    return $this->embedding;
  }
  /**
   * Optional. The entity id whose similar entities should be searched for. If
   * embedding is set, search will use embedding instead of entity_id.
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
   * Optional. The number of similar entities to be retrieved from feature view
   * for each query.
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
   * Optional. The list of numeric filters.
   *
   * @param GoogleCloudAiplatformV1NearestNeighborQueryNumericFilter[] $numericFilters
   */
  public function setNumericFilters($numericFilters)
  {
    $this->numericFilters = $numericFilters;
  }
  /**
   * @return GoogleCloudAiplatformV1NearestNeighborQueryNumericFilter[]
   */
  public function getNumericFilters()
  {
    return $this->numericFilters;
  }
  /**
   * Optional. Parameters that can be set to tune query on the fly.
   *
   * @param GoogleCloudAiplatformV1NearestNeighborQueryParameters $parameters
   */
  public function setParameters(GoogleCloudAiplatformV1NearestNeighborQueryParameters $parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return GoogleCloudAiplatformV1NearestNeighborQueryParameters
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Optional. Crowding is a constraint on a neighbor list produced by nearest
   * neighbor search requiring that no more than
   * sper_crowding_attribute_neighbor_count of the k neighbors returned have the
   * same value of crowding_attribute. It's used for improving result diversity.
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
   * Optional. The list of string filters.
   *
   * @param GoogleCloudAiplatformV1NearestNeighborQueryStringFilter[] $stringFilters
   */
  public function setStringFilters($stringFilters)
  {
    $this->stringFilters = $stringFilters;
  }
  /**
   * @return GoogleCloudAiplatformV1NearestNeighborQueryStringFilter[]
   */
  public function getStringFilters()
  {
    return $this->stringFilters;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1NearestNeighborQuery::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1NearestNeighborQuery');
