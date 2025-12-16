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

class GoogleCloudAiplatformV1RagRetrievalConfigRanking extends \Google\Model
{
  protected $llmRankerType = GoogleCloudAiplatformV1RagRetrievalConfigRankingLlmRanker::class;
  protected $llmRankerDataType = '';
  protected $rankServiceType = GoogleCloudAiplatformV1RagRetrievalConfigRankingRankService::class;
  protected $rankServiceDataType = '';

  /**
   * Optional. Config for LlmRanker.
   *
   * @param GoogleCloudAiplatformV1RagRetrievalConfigRankingLlmRanker $llmRanker
   */
  public function setLlmRanker(GoogleCloudAiplatformV1RagRetrievalConfigRankingLlmRanker $llmRanker)
  {
    $this->llmRanker = $llmRanker;
  }
  /**
   * @return GoogleCloudAiplatformV1RagRetrievalConfigRankingLlmRanker
   */
  public function getLlmRanker()
  {
    return $this->llmRanker;
  }
  /**
   * Optional. Config for Rank Service.
   *
   * @param GoogleCloudAiplatformV1RagRetrievalConfigRankingRankService $rankService
   */
  public function setRankService(GoogleCloudAiplatformV1RagRetrievalConfigRankingRankService $rankService)
  {
    $this->rankService = $rankService;
  }
  /**
   * @return GoogleCloudAiplatformV1RagRetrievalConfigRankingRankService
   */
  public function getRankService()
  {
    return $this->rankService;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RagRetrievalConfigRanking::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RagRetrievalConfigRanking');
