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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1alphaSearchRequestEmbeddingSpec extends \Google\Collection
{
  protected $collection_key = 'embeddingVectors';
  protected $embeddingVectorsType = GoogleCloudDiscoveryengineV1alphaSearchRequestEmbeddingSpecEmbeddingVector::class;
  protected $embeddingVectorsDataType = 'array';

  /**
   * The embedding vector used for retrieval. Limit to 1.
   *
   * @param GoogleCloudDiscoveryengineV1alphaSearchRequestEmbeddingSpecEmbeddingVector[] $embeddingVectors
   */
  public function setEmbeddingVectors($embeddingVectors)
  {
    $this->embeddingVectors = $embeddingVectors;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaSearchRequestEmbeddingSpecEmbeddingVector[]
   */
  public function getEmbeddingVectors()
  {
    return $this->embeddingVectors;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaSearchRequestEmbeddingSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaSearchRequestEmbeddingSpec');
