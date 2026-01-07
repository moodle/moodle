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

class GoogleCloudAiplatformV1FeatureViewIndexConfig extends \Google\Collection
{
  /**
   * Should not be set.
   */
  public const DISTANCE_MEASURE_TYPE_DISTANCE_MEASURE_TYPE_UNSPECIFIED = 'DISTANCE_MEASURE_TYPE_UNSPECIFIED';
  /**
   * Euclidean (L_2) Distance.
   */
  public const DISTANCE_MEASURE_TYPE_SQUARED_L2_DISTANCE = 'SQUARED_L2_DISTANCE';
  /**
   * Cosine Distance. Defined as 1 - cosine similarity. We strongly suggest
   * using DOT_PRODUCT_DISTANCE + UNIT_L2_NORM instead of COSINE distance. Our
   * algorithms have been more optimized for DOT_PRODUCT distance which, when
   * combined with UNIT_L2_NORM, is mathematically equivalent to COSINE distance
   * and results in the same ranking.
   */
  public const DISTANCE_MEASURE_TYPE_COSINE_DISTANCE = 'COSINE_DISTANCE';
  /**
   * Dot Product Distance. Defined as a negative of the dot product.
   */
  public const DISTANCE_MEASURE_TYPE_DOT_PRODUCT_DISTANCE = 'DOT_PRODUCT_DISTANCE';
  protected $collection_key = 'filterColumns';
  protected $bruteForceConfigType = GoogleCloudAiplatformV1FeatureViewIndexConfigBruteForceConfig::class;
  protected $bruteForceConfigDataType = '';
  /**
   * Optional. Column of crowding. This column contains crowding attribute which
   * is a constraint on a neighbor list produced by
   * FeatureOnlineStoreService.SearchNearestEntities to diversify search
   * results. If NearestNeighborQuery.per_crowding_attribute_neighbor_count is
   * set to K in SearchNearestEntitiesRequest, it's guaranteed that no more than
   * K entities of the same crowding attribute are returned in the response.
   *
   * @var string
   */
  public $crowdingColumn;
  /**
   * Optional. The distance measure used in nearest neighbor search.
   *
   * @var string
   */
  public $distanceMeasureType;
  /**
   * Optional. Column of embedding. This column contains the source data to
   * create index for vector search. embedding_column must be set when using
   * vector search.
   *
   * @var string
   */
  public $embeddingColumn;
  /**
   * Optional. The number of dimensions of the input embedding.
   *
   * @var int
   */
  public $embeddingDimension;
  /**
   * Optional. Columns of features that're used to filter vector search results.
   *
   * @var string[]
   */
  public $filterColumns;
  protected $treeAhConfigType = GoogleCloudAiplatformV1FeatureViewIndexConfigTreeAHConfig::class;
  protected $treeAhConfigDataType = '';

  /**
   * Optional. Configuration options for using brute force search, which simply
   * implements the standard linear search in the database for each query. It is
   * primarily meant for benchmarking and to generate the ground truth for
   * approximate search.
   *
   * @param GoogleCloudAiplatformV1FeatureViewIndexConfigBruteForceConfig $bruteForceConfig
   */
  public function setBruteForceConfig(GoogleCloudAiplatformV1FeatureViewIndexConfigBruteForceConfig $bruteForceConfig)
  {
    $this->bruteForceConfig = $bruteForceConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureViewIndexConfigBruteForceConfig
   */
  public function getBruteForceConfig()
  {
    return $this->bruteForceConfig;
  }
  /**
   * Optional. Column of crowding. This column contains crowding attribute which
   * is a constraint on a neighbor list produced by
   * FeatureOnlineStoreService.SearchNearestEntities to diversify search
   * results. If NearestNeighborQuery.per_crowding_attribute_neighbor_count is
   * set to K in SearchNearestEntitiesRequest, it's guaranteed that no more than
   * K entities of the same crowding attribute are returned in the response.
   *
   * @param string $crowdingColumn
   */
  public function setCrowdingColumn($crowdingColumn)
  {
    $this->crowdingColumn = $crowdingColumn;
  }
  /**
   * @return string
   */
  public function getCrowdingColumn()
  {
    return $this->crowdingColumn;
  }
  /**
   * Optional. The distance measure used in nearest neighbor search.
   *
   * Accepted values: DISTANCE_MEASURE_TYPE_UNSPECIFIED, SQUARED_L2_DISTANCE,
   * COSINE_DISTANCE, DOT_PRODUCT_DISTANCE
   *
   * @param self::DISTANCE_MEASURE_TYPE_* $distanceMeasureType
   */
  public function setDistanceMeasureType($distanceMeasureType)
  {
    $this->distanceMeasureType = $distanceMeasureType;
  }
  /**
   * @return self::DISTANCE_MEASURE_TYPE_*
   */
  public function getDistanceMeasureType()
  {
    return $this->distanceMeasureType;
  }
  /**
   * Optional. Column of embedding. This column contains the source data to
   * create index for vector search. embedding_column must be set when using
   * vector search.
   *
   * @param string $embeddingColumn
   */
  public function setEmbeddingColumn($embeddingColumn)
  {
    $this->embeddingColumn = $embeddingColumn;
  }
  /**
   * @return string
   */
  public function getEmbeddingColumn()
  {
    return $this->embeddingColumn;
  }
  /**
   * Optional. The number of dimensions of the input embedding.
   *
   * @param int $embeddingDimension
   */
  public function setEmbeddingDimension($embeddingDimension)
  {
    $this->embeddingDimension = $embeddingDimension;
  }
  /**
   * @return int
   */
  public function getEmbeddingDimension()
  {
    return $this->embeddingDimension;
  }
  /**
   * Optional. Columns of features that're used to filter vector search results.
   *
   * @param string[] $filterColumns
   */
  public function setFilterColumns($filterColumns)
  {
    $this->filterColumns = $filterColumns;
  }
  /**
   * @return string[]
   */
  public function getFilterColumns()
  {
    return $this->filterColumns;
  }
  /**
   * Optional. Configuration options for the tree-AH algorithm (Shallow tree +
   * Asymmetric Hashing). Please refer to this paper for more details:
   * https://arxiv.org/abs/1908.10396
   *
   * @param GoogleCloudAiplatformV1FeatureViewIndexConfigTreeAHConfig $treeAhConfig
   */
  public function setTreeAhConfig(GoogleCloudAiplatformV1FeatureViewIndexConfigTreeAHConfig $treeAhConfig)
  {
    $this->treeAhConfig = $treeAhConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureViewIndexConfigTreeAHConfig
   */
  public function getTreeAhConfig()
  {
    return $this->treeAhConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FeatureViewIndexConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FeatureViewIndexConfig');
