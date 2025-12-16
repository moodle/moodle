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

class GoogleCloudAiplatformV1Retrieval extends \Google\Model
{
  /**
   * Optional. Deprecated. This option is no longer supported.
   *
   * @deprecated
   * @var bool
   */
  public $disableAttribution;
  protected $externalApiType = GoogleCloudAiplatformV1ExternalApi::class;
  protected $externalApiDataType = '';
  protected $vertexAiSearchType = GoogleCloudAiplatformV1VertexAISearch::class;
  protected $vertexAiSearchDataType = '';
  protected $vertexRagStoreType = GoogleCloudAiplatformV1VertexRagStore::class;
  protected $vertexRagStoreDataType = '';

  /**
   * Optional. Deprecated. This option is no longer supported.
   *
   * @deprecated
   * @param bool $disableAttribution
   */
  public function setDisableAttribution($disableAttribution)
  {
    $this->disableAttribution = $disableAttribution;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getDisableAttribution()
  {
    return $this->disableAttribution;
  }
  /**
   * Use data source powered by external API for grounding.
   *
   * @param GoogleCloudAiplatformV1ExternalApi $externalApi
   */
  public function setExternalApi(GoogleCloudAiplatformV1ExternalApi $externalApi)
  {
    $this->externalApi = $externalApi;
  }
  /**
   * @return GoogleCloudAiplatformV1ExternalApi
   */
  public function getExternalApi()
  {
    return $this->externalApi;
  }
  /**
   * Set to use data source powered by Vertex AI Search.
   *
   * @param GoogleCloudAiplatformV1VertexAISearch $vertexAiSearch
   */
  public function setVertexAiSearch(GoogleCloudAiplatformV1VertexAISearch $vertexAiSearch)
  {
    $this->vertexAiSearch = $vertexAiSearch;
  }
  /**
   * @return GoogleCloudAiplatformV1VertexAISearch
   */
  public function getVertexAiSearch()
  {
    return $this->vertexAiSearch;
  }
  /**
   * Set to use data source powered by Vertex RAG store. User data is uploaded
   * via the VertexRagDataService.
   *
   * @param GoogleCloudAiplatformV1VertexRagStore $vertexRagStore
   */
  public function setVertexRagStore(GoogleCloudAiplatformV1VertexRagStore $vertexRagStore)
  {
    $this->vertexRagStore = $vertexRagStore;
  }
  /**
   * @return GoogleCloudAiplatformV1VertexRagStore
   */
  public function getVertexRagStore()
  {
    return $this->vertexRagStore;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Retrieval::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Retrieval');
