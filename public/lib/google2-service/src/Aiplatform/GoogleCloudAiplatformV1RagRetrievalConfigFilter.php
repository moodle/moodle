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

class GoogleCloudAiplatformV1RagRetrievalConfigFilter extends \Google\Model
{
  /**
   * Optional. String for metadata filtering.
   *
   * @var string
   */
  public $metadataFilter;
  /**
   * Optional. Only returns contexts with vector distance smaller than the
   * threshold.
   *
   * @var 
   */
  public $vectorDistanceThreshold;
  /**
   * Optional. Only returns contexts with vector similarity larger than the
   * threshold.
   *
   * @var 
   */
  public $vectorSimilarityThreshold;

  /**
   * Optional. String for metadata filtering.
   *
   * @param string $metadataFilter
   */
  public function setMetadataFilter($metadataFilter)
  {
    $this->metadataFilter = $metadataFilter;
  }
  /**
   * @return string
   */
  public function getMetadataFilter()
  {
    return $this->metadataFilter;
  }
  public function setVectorDistanceThreshold($vectorDistanceThreshold)
  {
    $this->vectorDistanceThreshold = $vectorDistanceThreshold;
  }
  public function getVectorDistanceThreshold()
  {
    return $this->vectorDistanceThreshold;
  }
  public function setVectorSimilarityThreshold($vectorSimilarityThreshold)
  {
    $this->vectorSimilarityThreshold = $vectorSimilarityThreshold;
  }
  public function getVectorSimilarityThreshold()
  {
    return $this->vectorSimilarityThreshold;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RagRetrievalConfigFilter::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RagRetrievalConfigFilter');
