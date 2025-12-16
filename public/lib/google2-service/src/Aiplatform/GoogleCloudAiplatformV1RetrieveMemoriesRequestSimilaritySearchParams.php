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

class GoogleCloudAiplatformV1RetrieveMemoriesRequestSimilaritySearchParams extends \Google\Model
{
  /**
   * Required. Query to use for similarity search retrieval. If provided, then
   * the parent ReasoningEngine must have
   * ReasoningEngineContextSpec.MemoryBankConfig.SimilaritySearchConfig set.
   *
   * @var string
   */
  public $searchQuery;
  /**
   * Optional. The maximum number of memories to return. The service may return
   * fewer than this value. If unspecified, at most 3 memories will be returned.
   * The maximum value is 100; values above 100 will be coerced to 100.
   *
   * @var int
   */
  public $topK;

  /**
   * Required. Query to use for similarity search retrieval. If provided, then
   * the parent ReasoningEngine must have
   * ReasoningEngineContextSpec.MemoryBankConfig.SimilaritySearchConfig set.
   *
   * @param string $searchQuery
   */
  public function setSearchQuery($searchQuery)
  {
    $this->searchQuery = $searchQuery;
  }
  /**
   * @return string
   */
  public function getSearchQuery()
  {
    return $this->searchQuery;
  }
  /**
   * Optional. The maximum number of memories to return. The service may return
   * fewer than this value. If unspecified, at most 3 memories will be returned.
   * The maximum value is 100; values above 100 will be coerced to 100.
   *
   * @param int $topK
   */
  public function setTopK($topK)
  {
    $this->topK = $topK;
  }
  /**
   * @return int
   */
  public function getTopK()
  {
    return $this->topK;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RetrieveMemoriesRequestSimilaritySearchParams::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RetrieveMemoriesRequestSimilaritySearchParams');
