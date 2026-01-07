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

class GoogleCloudAiplatformV1IndexDatapoint extends \Google\Collection
{
  protected $collection_key = 'restricts';
  protected $crowdingTagType = GoogleCloudAiplatformV1IndexDatapointCrowdingTag::class;
  protected $crowdingTagDataType = '';
  /**
   * Required. Unique identifier of the datapoint.
   *
   * @var string
   */
  public $datapointId;
  /**
   * Optional. The key-value map of additional metadata for the datapoint.
   *
   * @var array[]
   */
  public $embeddingMetadata;
  /**
   * Required. Feature embedding vector for dense index. An array of numbers
   * with the length of [NearestNeighborSearchConfig.dimensions].
   *
   * @var float[]
   */
  public $featureVector;
  protected $numericRestrictsType = GoogleCloudAiplatformV1IndexDatapointNumericRestriction::class;
  protected $numericRestrictsDataType = 'array';
  protected $restrictsType = GoogleCloudAiplatformV1IndexDatapointRestriction::class;
  protected $restrictsDataType = 'array';
  protected $sparseEmbeddingType = GoogleCloudAiplatformV1IndexDatapointSparseEmbedding::class;
  protected $sparseEmbeddingDataType = '';

  /**
   * Optional. CrowdingTag of the datapoint, the number of neighbors to return
   * in each crowding can be configured during query.
   *
   * @param GoogleCloudAiplatformV1IndexDatapointCrowdingTag $crowdingTag
   */
  public function setCrowdingTag(GoogleCloudAiplatformV1IndexDatapointCrowdingTag $crowdingTag)
  {
    $this->crowdingTag = $crowdingTag;
  }
  /**
   * @return GoogleCloudAiplatformV1IndexDatapointCrowdingTag
   */
  public function getCrowdingTag()
  {
    return $this->crowdingTag;
  }
  /**
   * Required. Unique identifier of the datapoint.
   *
   * @param string $datapointId
   */
  public function setDatapointId($datapointId)
  {
    $this->datapointId = $datapointId;
  }
  /**
   * @return string
   */
  public function getDatapointId()
  {
    return $this->datapointId;
  }
  /**
   * Optional. The key-value map of additional metadata for the datapoint.
   *
   * @param array[] $embeddingMetadata
   */
  public function setEmbeddingMetadata($embeddingMetadata)
  {
    $this->embeddingMetadata = $embeddingMetadata;
  }
  /**
   * @return array[]
   */
  public function getEmbeddingMetadata()
  {
    return $this->embeddingMetadata;
  }
  /**
   * Required. Feature embedding vector for dense index. An array of numbers
   * with the length of [NearestNeighborSearchConfig.dimensions].
   *
   * @param float[] $featureVector
   */
  public function setFeatureVector($featureVector)
  {
    $this->featureVector = $featureVector;
  }
  /**
   * @return float[]
   */
  public function getFeatureVector()
  {
    return $this->featureVector;
  }
  /**
   * Optional. List of Restrict of the datapoint, used to perform "restricted
   * searches" where boolean rule are used to filter the subset of the database
   * eligible for matching. This uses numeric comparisons.
   *
   * @param GoogleCloudAiplatformV1IndexDatapointNumericRestriction[] $numericRestricts
   */
  public function setNumericRestricts($numericRestricts)
  {
    $this->numericRestricts = $numericRestricts;
  }
  /**
   * @return GoogleCloudAiplatformV1IndexDatapointNumericRestriction[]
   */
  public function getNumericRestricts()
  {
    return $this->numericRestricts;
  }
  /**
   * Optional. List of Restrict of the datapoint, used to perform "restricted
   * searches" where boolean rule are used to filter the subset of the database
   * eligible for matching. This uses categorical tokens. See:
   * https://cloud.google.com/vertex-ai/docs/matching-engine/filtering
   *
   * @param GoogleCloudAiplatformV1IndexDatapointRestriction[] $restricts
   */
  public function setRestricts($restricts)
  {
    $this->restricts = $restricts;
  }
  /**
   * @return GoogleCloudAiplatformV1IndexDatapointRestriction[]
   */
  public function getRestricts()
  {
    return $this->restricts;
  }
  /**
   * Optional. Feature embedding vector for sparse index.
   *
   * @param GoogleCloudAiplatformV1IndexDatapointSparseEmbedding $sparseEmbedding
   */
  public function setSparseEmbedding(GoogleCloudAiplatformV1IndexDatapointSparseEmbedding $sparseEmbedding)
  {
    $this->sparseEmbedding = $sparseEmbedding;
  }
  /**
   * @return GoogleCloudAiplatformV1IndexDatapointSparseEmbedding
   */
  public function getSparseEmbedding()
  {
    return $this->sparseEmbedding;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1IndexDatapoint::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1IndexDatapoint');
