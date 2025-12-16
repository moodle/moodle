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

namespace Google\Service\Datastore;

class FindNearest extends \Google\Model
{
  /**
   * Should not be set.
   */
  public const DISTANCE_MEASURE_DISTANCE_MEASURE_UNSPECIFIED = 'DISTANCE_MEASURE_UNSPECIFIED';
  /**
   * Measures the EUCLIDEAN distance between the vectors. See
   * [Euclidean](https://en.wikipedia.org/wiki/Euclidean_distance) to learn
   * more. The resulting distance decreases the more similar two vectors are.
   */
  public const DISTANCE_MEASURE_EUCLIDEAN = 'EUCLIDEAN';
  /**
   * COSINE distance compares vectors based on the angle between them, which
   * allows you to measure similarity that isn't based on the vectors magnitude.
   * We recommend using DOT_PRODUCT with unit normalized vectors instead of
   * COSINE distance, which is mathematically equivalent with better
   * performance. See [Cosine
   * Similarity](https://en.wikipedia.org/wiki/Cosine_similarity) to learn more
   * about COSINE similarity and COSINE distance. The resulting COSINE distance
   * decreases the more similar two vectors are.
   */
  public const DISTANCE_MEASURE_COSINE = 'COSINE';
  /**
   * Similar to cosine but is affected by the magnitude of the vectors. See [Dot
   * Product](https://en.wikipedia.org/wiki/Dot_product) to learn more. The
   * resulting distance increases the more similar two vectors are.
   */
  public const DISTANCE_MEASURE_DOT_PRODUCT = 'DOT_PRODUCT';
  /**
   * Required. The Distance Measure to use, required.
   *
   * @var string
   */
  public $distanceMeasure;
  /**
   * Optional. Optional name of the field to output the result of the vector
   * distance calculation. Must conform to entity property limitations.
   *
   * @var string
   */
  public $distanceResultProperty;
  /**
   * Optional. Option to specify a threshold for which no less similar documents
   * will be returned. The behavior of the specified `distance_measure` will
   * affect the meaning of the distance threshold. Since DOT_PRODUCT distances
   * increase when the vectors are more similar, the comparison is inverted. *
   * For EUCLIDEAN, COSINE: WHERE distance <= distance_threshold * For
   * DOT_PRODUCT: WHERE distance >= distance_threshold
   *
   * @var 
   */
  public $distanceThreshold;
  /**
   * Required. The number of nearest neighbors to return. Must be a positive
   * integer of no more than 100.
   *
   * @var int
   */
  public $limit;
  protected $queryVectorType = Value::class;
  protected $queryVectorDataType = '';
  protected $vectorPropertyType = PropertyReference::class;
  protected $vectorPropertyDataType = '';

  /**
   * Required. The Distance Measure to use, required.
   *
   * Accepted values: DISTANCE_MEASURE_UNSPECIFIED, EUCLIDEAN, COSINE,
   * DOT_PRODUCT
   *
   * @param self::DISTANCE_MEASURE_* $distanceMeasure
   */
  public function setDistanceMeasure($distanceMeasure)
  {
    $this->distanceMeasure = $distanceMeasure;
  }
  /**
   * @return self::DISTANCE_MEASURE_*
   */
  public function getDistanceMeasure()
  {
    return $this->distanceMeasure;
  }
  /**
   * Optional. Optional name of the field to output the result of the vector
   * distance calculation. Must conform to entity property limitations.
   *
   * @param string $distanceResultProperty
   */
  public function setDistanceResultProperty($distanceResultProperty)
  {
    $this->distanceResultProperty = $distanceResultProperty;
  }
  /**
   * @return string
   */
  public function getDistanceResultProperty()
  {
    return $this->distanceResultProperty;
  }
  public function setDistanceThreshold($distanceThreshold)
  {
    $this->distanceThreshold = $distanceThreshold;
  }
  public function getDistanceThreshold()
  {
    return $this->distanceThreshold;
  }
  /**
   * Required. The number of nearest neighbors to return. Must be a positive
   * integer of no more than 100.
   *
   * @param int $limit
   */
  public function setLimit($limit)
  {
    $this->limit = $limit;
  }
  /**
   * @return int
   */
  public function getLimit()
  {
    return $this->limit;
  }
  /**
   * Required. The query vector that we are searching on. Must be a vector of no
   * more than 2048 dimensions.
   *
   * @param Value $queryVector
   */
  public function setQueryVector(Value $queryVector)
  {
    $this->queryVector = $queryVector;
  }
  /**
   * @return Value
   */
  public function getQueryVector()
  {
    return $this->queryVector;
  }
  /**
   * Required. An indexed vector property to search upon. Only documents which
   * contain vectors whose dimensionality match the query_vector can be
   * returned.
   *
   * @param PropertyReference $vectorProperty
   */
  public function setVectorProperty(PropertyReference $vectorProperty)
  {
    $this->vectorProperty = $vectorProperty;
  }
  /**
   * @return PropertyReference
   */
  public function getVectorProperty()
  {
    return $this->vectorProperty;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FindNearest::class, 'Google_Service_Datastore_FindNearest');
