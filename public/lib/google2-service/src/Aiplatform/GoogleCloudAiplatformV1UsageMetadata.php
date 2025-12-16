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

class GoogleCloudAiplatformV1UsageMetadata extends \Google\Collection
{
  /**
   * Unspecified request traffic type.
   */
  public const TRAFFIC_TYPE_TRAFFIC_TYPE_UNSPECIFIED = 'TRAFFIC_TYPE_UNSPECIFIED';
  /**
   * Type for Pay-As-You-Go traffic.
   */
  public const TRAFFIC_TYPE_ON_DEMAND = 'ON_DEMAND';
  /**
   * Type for Provisioned Throughput traffic.
   */
  public const TRAFFIC_TYPE_PROVISIONED_THROUGHPUT = 'PROVISIONED_THROUGHPUT';
  protected $collection_key = 'toolUsePromptTokensDetails';
  protected $cacheTokensDetailsType = GoogleCloudAiplatformV1ModalityTokenCount::class;
  protected $cacheTokensDetailsDataType = 'array';
  /**
   * Output only. The number of tokens in the cached content that was used for
   * this request.
   *
   * @var int
   */
  public $cachedContentTokenCount;
  /**
   * The total number of tokens in the generated candidates.
   *
   * @var int
   */
  public $candidatesTokenCount;
  protected $candidatesTokensDetailsType = GoogleCloudAiplatformV1ModalityTokenCount::class;
  protected $candidatesTokensDetailsDataType = 'array';
  /**
   * The total number of tokens in the prompt. This includes any text, images,
   * or other media provided in the request. When `cached_content` is set, this
   * also includes the number of tokens in the cached content.
   *
   * @var int
   */
  public $promptTokenCount;
  protected $promptTokensDetailsType = GoogleCloudAiplatformV1ModalityTokenCount::class;
  protected $promptTokensDetailsDataType = 'array';
  /**
   * Output only. The number of tokens that were part of the model's generated
   * "thoughts" output, if applicable.
   *
   * @var int
   */
  public $thoughtsTokenCount;
  /**
   * Output only. The number of tokens in the results from tool executions,
   * which are provided back to the model as input, if applicable.
   *
   * @var int
   */
  public $toolUsePromptTokenCount;
  protected $toolUsePromptTokensDetailsType = GoogleCloudAiplatformV1ModalityTokenCount::class;
  protected $toolUsePromptTokensDetailsDataType = 'array';
  /**
   * The total number of tokens for the entire request. This is the sum of
   * `prompt_token_count`, `candidates_token_count`,
   * `tool_use_prompt_token_count`, and `thoughts_token_count`.
   *
   * @var int
   */
  public $totalTokenCount;
  /**
   * Output only. The traffic type for this request.
   *
   * @var string
   */
  public $trafficType;

  /**
   * Output only. A detailed breakdown of the token count for each modality in
   * the cached content.
   *
   * @param GoogleCloudAiplatformV1ModalityTokenCount[] $cacheTokensDetails
   */
  public function setCacheTokensDetails($cacheTokensDetails)
  {
    $this->cacheTokensDetails = $cacheTokensDetails;
  }
  /**
   * @return GoogleCloudAiplatformV1ModalityTokenCount[]
   */
  public function getCacheTokensDetails()
  {
    return $this->cacheTokensDetails;
  }
  /**
   * Output only. The number of tokens in the cached content that was used for
   * this request.
   *
   * @param int $cachedContentTokenCount
   */
  public function setCachedContentTokenCount($cachedContentTokenCount)
  {
    $this->cachedContentTokenCount = $cachedContentTokenCount;
  }
  /**
   * @return int
   */
  public function getCachedContentTokenCount()
  {
    return $this->cachedContentTokenCount;
  }
  /**
   * The total number of tokens in the generated candidates.
   *
   * @param int $candidatesTokenCount
   */
  public function setCandidatesTokenCount($candidatesTokenCount)
  {
    $this->candidatesTokenCount = $candidatesTokenCount;
  }
  /**
   * @return int
   */
  public function getCandidatesTokenCount()
  {
    return $this->candidatesTokenCount;
  }
  /**
   * Output only. A detailed breakdown of the token count for each modality in
   * the generated candidates.
   *
   * @param GoogleCloudAiplatformV1ModalityTokenCount[] $candidatesTokensDetails
   */
  public function setCandidatesTokensDetails($candidatesTokensDetails)
  {
    $this->candidatesTokensDetails = $candidatesTokensDetails;
  }
  /**
   * @return GoogleCloudAiplatformV1ModalityTokenCount[]
   */
  public function getCandidatesTokensDetails()
  {
    return $this->candidatesTokensDetails;
  }
  /**
   * The total number of tokens in the prompt. This includes any text, images,
   * or other media provided in the request. When `cached_content` is set, this
   * also includes the number of tokens in the cached content.
   *
   * @param int $promptTokenCount
   */
  public function setPromptTokenCount($promptTokenCount)
  {
    $this->promptTokenCount = $promptTokenCount;
  }
  /**
   * @return int
   */
  public function getPromptTokenCount()
  {
    return $this->promptTokenCount;
  }
  /**
   * Output only. A detailed breakdown of the token count for each modality in
   * the prompt.
   *
   * @param GoogleCloudAiplatformV1ModalityTokenCount[] $promptTokensDetails
   */
  public function setPromptTokensDetails($promptTokensDetails)
  {
    $this->promptTokensDetails = $promptTokensDetails;
  }
  /**
   * @return GoogleCloudAiplatformV1ModalityTokenCount[]
   */
  public function getPromptTokensDetails()
  {
    return $this->promptTokensDetails;
  }
  /**
   * Output only. The number of tokens that were part of the model's generated
   * "thoughts" output, if applicable.
   *
   * @param int $thoughtsTokenCount
   */
  public function setThoughtsTokenCount($thoughtsTokenCount)
  {
    $this->thoughtsTokenCount = $thoughtsTokenCount;
  }
  /**
   * @return int
   */
  public function getThoughtsTokenCount()
  {
    return $this->thoughtsTokenCount;
  }
  /**
   * Output only. The number of tokens in the results from tool executions,
   * which are provided back to the model as input, if applicable.
   *
   * @param int $toolUsePromptTokenCount
   */
  public function setToolUsePromptTokenCount($toolUsePromptTokenCount)
  {
    $this->toolUsePromptTokenCount = $toolUsePromptTokenCount;
  }
  /**
   * @return int
   */
  public function getToolUsePromptTokenCount()
  {
    return $this->toolUsePromptTokenCount;
  }
  /**
   * Output only. A detailed breakdown by modality of the token counts from the
   * results of tool executions, which are provided back to the model as input.
   *
   * @param GoogleCloudAiplatformV1ModalityTokenCount[] $toolUsePromptTokensDetails
   */
  public function setToolUsePromptTokensDetails($toolUsePromptTokensDetails)
  {
    $this->toolUsePromptTokensDetails = $toolUsePromptTokensDetails;
  }
  /**
   * @return GoogleCloudAiplatformV1ModalityTokenCount[]
   */
  public function getToolUsePromptTokensDetails()
  {
    return $this->toolUsePromptTokensDetails;
  }
  /**
   * The total number of tokens for the entire request. This is the sum of
   * `prompt_token_count`, `candidates_token_count`,
   * `tool_use_prompt_token_count`, and `thoughts_token_count`.
   *
   * @param int $totalTokenCount
   */
  public function setTotalTokenCount($totalTokenCount)
  {
    $this->totalTokenCount = $totalTokenCount;
  }
  /**
   * @return int
   */
  public function getTotalTokenCount()
  {
    return $this->totalTokenCount;
  }
  /**
   * Output only. The traffic type for this request.
   *
   * Accepted values: TRAFFIC_TYPE_UNSPECIFIED, ON_DEMAND,
   * PROVISIONED_THROUGHPUT
   *
   * @param self::TRAFFIC_TYPE_* $trafficType
   */
  public function setTrafficType($trafficType)
  {
    $this->trafficType = $trafficType;
  }
  /**
   * @return self::TRAFFIC_TYPE_*
   */
  public function getTrafficType()
  {
    return $this->trafficType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1UsageMetadata::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1UsageMetadata');
