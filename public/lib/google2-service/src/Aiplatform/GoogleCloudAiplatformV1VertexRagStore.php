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

class GoogleCloudAiplatformV1VertexRagStore extends \Google\Collection
{
  protected $collection_key = 'ragResources';
  protected $ragResourcesType = GoogleCloudAiplatformV1VertexRagStoreRagResource::class;
  protected $ragResourcesDataType = 'array';
  protected $ragRetrievalConfigType = GoogleCloudAiplatformV1RagRetrievalConfig::class;
  protected $ragRetrievalConfigDataType = '';
  /**
   * Optional. Number of top k results to return from the selected corpora.
   *
   * @deprecated
   * @var int
   */
  public $similarityTopK;
  /**
   * Optional. Only return results with vector distance smaller than the
   * threshold.
   *
   * @deprecated
   * @var 
   */
  public $vectorDistanceThreshold;

  /**
   * Optional. The representation of the rag source. It can be used to specify
   * corpus only or ragfiles. Currently only support one corpus or multiple
   * files from one corpus. In the future we may open up multiple corpora
   * support.
   *
   * @param GoogleCloudAiplatformV1VertexRagStoreRagResource[] $ragResources
   */
  public function setRagResources($ragResources)
  {
    $this->ragResources = $ragResources;
  }
  /**
   * @return GoogleCloudAiplatformV1VertexRagStoreRagResource[]
   */
  public function getRagResources()
  {
    return $this->ragResources;
  }
  /**
   * Optional. The retrieval config for the Rag query.
   *
   * @param GoogleCloudAiplatformV1RagRetrievalConfig $ragRetrievalConfig
   */
  public function setRagRetrievalConfig(GoogleCloudAiplatformV1RagRetrievalConfig $ragRetrievalConfig)
  {
    $this->ragRetrievalConfig = $ragRetrievalConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1RagRetrievalConfig
   */
  public function getRagRetrievalConfig()
  {
    return $this->ragRetrievalConfig;
  }
  /**
   * Optional. Number of top k results to return from the selected corpora.
   *
   * @deprecated
   * @param int $similarityTopK
   */
  public function setSimilarityTopK($similarityTopK)
  {
    $this->similarityTopK = $similarityTopK;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getSimilarityTopK()
  {
    return $this->similarityTopK;
  }
  public function setVectorDistanceThreshold($vectorDistanceThreshold)
  {
    $this->vectorDistanceThreshold = $vectorDistanceThreshold;
  }
  public function getVectorDistanceThreshold()
  {
    return $this->vectorDistanceThreshold;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1VertexRagStore::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1VertexRagStore');
