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

class GoogleCloudAiplatformV1RetrieveMemoriesRequest extends \Google\Model
{
  /**
   * Optional. The standard list filter that will be applied to the retrieved
   * memories. More detail in [AIP-160](https://google.aip.dev/160). Supported
   * fields: * `fact` * `create_time` * `update_time` * `topics` (i.e.
   * `topics.custom_memory_topic_label: "example topic" OR
   * topics.managed_memory_topic: USER_PREFERENCES`)
   *
   * @var string
   */
  public $filter;
  /**
   * Required. The scope of the memories to retrieve. A memory must have exactly
   * the same scope (`Memory.scope`) as the scope provided here to be retrieved
   * (same keys and values). Order does not matter, but it is case-sensitive.
   *
   * @var string[]
   */
  public $scope;
  protected $similaritySearchParamsType = GoogleCloudAiplatformV1RetrieveMemoriesRequestSimilaritySearchParams::class;
  protected $similaritySearchParamsDataType = '';
  protected $simpleRetrievalParamsType = GoogleCloudAiplatformV1RetrieveMemoriesRequestSimpleRetrievalParams::class;
  protected $simpleRetrievalParamsDataType = '';

  /**
   * Optional. The standard list filter that will be applied to the retrieved
   * memories. More detail in [AIP-160](https://google.aip.dev/160). Supported
   * fields: * `fact` * `create_time` * `update_time` * `topics` (i.e.
   * `topics.custom_memory_topic_label: "example topic" OR
   * topics.managed_memory_topic: USER_PREFERENCES`)
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Required. The scope of the memories to retrieve. A memory must have exactly
   * the same scope (`Memory.scope`) as the scope provided here to be retrieved
   * (same keys and values). Order does not matter, but it is case-sensitive.
   *
   * @param string[] $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return string[]
   */
  public function getScope()
  {
    return $this->scope;
  }
  /**
   * Parameters for semantic similarity search based retrieval.
   *
   * @param GoogleCloudAiplatformV1RetrieveMemoriesRequestSimilaritySearchParams $similaritySearchParams
   */
  public function setSimilaritySearchParams(GoogleCloudAiplatformV1RetrieveMemoriesRequestSimilaritySearchParams $similaritySearchParams)
  {
    $this->similaritySearchParams = $similaritySearchParams;
  }
  /**
   * @return GoogleCloudAiplatformV1RetrieveMemoriesRequestSimilaritySearchParams
   */
  public function getSimilaritySearchParams()
  {
    return $this->similaritySearchParams;
  }
  /**
   * Parameters for simple (non-similarity search) retrieval.
   *
   * @param GoogleCloudAiplatformV1RetrieveMemoriesRequestSimpleRetrievalParams $simpleRetrievalParams
   */
  public function setSimpleRetrievalParams(GoogleCloudAiplatformV1RetrieveMemoriesRequestSimpleRetrievalParams $simpleRetrievalParams)
  {
    $this->simpleRetrievalParams = $simpleRetrievalParams;
  }
  /**
   * @return GoogleCloudAiplatformV1RetrieveMemoriesRequestSimpleRetrievalParams
   */
  public function getSimpleRetrievalParams()
  {
    return $this->simpleRetrievalParams;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RetrieveMemoriesRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RetrieveMemoriesRequest');
