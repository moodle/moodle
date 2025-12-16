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

class GoogleCloudAiplatformV1ExamplesOverride extends \Google\Collection
{
  /**
   * Unspecified format. Must not be used.
   */
  public const DATA_FORMAT_DATA_FORMAT_UNSPECIFIED = 'DATA_FORMAT_UNSPECIFIED';
  /**
   * Provided data is a set of model inputs.
   */
  public const DATA_FORMAT_INSTANCES = 'INSTANCES';
  /**
   * Provided data is a set of embeddings.
   */
  public const DATA_FORMAT_EMBEDDINGS = 'EMBEDDINGS';
  protected $collection_key = 'restrictions';
  /**
   * The number of neighbors to return that have the same crowding tag.
   *
   * @var int
   */
  public $crowdingCount;
  /**
   * The format of the data being provided with each call.
   *
   * @var string
   */
  public $dataFormat;
  /**
   * The number of neighbors to return.
   *
   * @var int
   */
  public $neighborCount;
  protected $restrictionsType = GoogleCloudAiplatformV1ExamplesRestrictionsNamespace::class;
  protected $restrictionsDataType = 'array';
  /**
   * If true, return the embeddings instead of neighbors.
   *
   * @var bool
   */
  public $returnEmbeddings;

  /**
   * The number of neighbors to return that have the same crowding tag.
   *
   * @param int $crowdingCount
   */
  public function setCrowdingCount($crowdingCount)
  {
    $this->crowdingCount = $crowdingCount;
  }
  /**
   * @return int
   */
  public function getCrowdingCount()
  {
    return $this->crowdingCount;
  }
  /**
   * The format of the data being provided with each call.
   *
   * Accepted values: DATA_FORMAT_UNSPECIFIED, INSTANCES, EMBEDDINGS
   *
   * @param self::DATA_FORMAT_* $dataFormat
   */
  public function setDataFormat($dataFormat)
  {
    $this->dataFormat = $dataFormat;
  }
  /**
   * @return self::DATA_FORMAT_*
   */
  public function getDataFormat()
  {
    return $this->dataFormat;
  }
  /**
   * The number of neighbors to return.
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
   * Restrict the resulting nearest neighbors to respect these constraints.
   *
   * @param GoogleCloudAiplatformV1ExamplesRestrictionsNamespace[] $restrictions
   */
  public function setRestrictions($restrictions)
  {
    $this->restrictions = $restrictions;
  }
  /**
   * @return GoogleCloudAiplatformV1ExamplesRestrictionsNamespace[]
   */
  public function getRestrictions()
  {
    return $this->restrictions;
  }
  /**
   * If true, return the embeddings instead of neighbors.
   *
   * @param bool $returnEmbeddings
   */
  public function setReturnEmbeddings($returnEmbeddings)
  {
    $this->returnEmbeddings = $returnEmbeddings;
  }
  /**
   * @return bool
   */
  public function getReturnEmbeddings()
  {
    return $this->returnEmbeddings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ExamplesOverride::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ExamplesOverride');
