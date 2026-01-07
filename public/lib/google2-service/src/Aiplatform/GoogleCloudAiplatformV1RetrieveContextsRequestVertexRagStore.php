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

class GoogleCloudAiplatformV1RetrieveContextsRequestVertexRagStore extends \Google\Collection
{
  protected $collection_key = 'ragResources';
  protected $ragResourcesType = GoogleCloudAiplatformV1RetrieveContextsRequestVertexRagStoreRagResource::class;
  protected $ragResourcesDataType = 'array';
  /**
   * Optional. Only return contexts with vector distance smaller than the
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
   * @param GoogleCloudAiplatformV1RetrieveContextsRequestVertexRagStoreRagResource[] $ragResources
   */
  public function setRagResources($ragResources)
  {
    $this->ragResources = $ragResources;
  }
  /**
   * @return GoogleCloudAiplatformV1RetrieveContextsRequestVertexRagStoreRagResource[]
   */
  public function getRagResources()
  {
    return $this->ragResources;
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
class_alias(GoogleCloudAiplatformV1RetrieveContextsRequestVertexRagStore::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RetrieveContextsRequestVertexRagStore');
