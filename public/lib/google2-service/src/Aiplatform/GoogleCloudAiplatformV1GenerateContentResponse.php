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

class GoogleCloudAiplatformV1GenerateContentResponse extends \Google\Collection
{
  protected $collection_key = 'candidates';
  protected $candidatesType = GoogleCloudAiplatformV1Candidate::class;
  protected $candidatesDataType = 'array';
  /**
   * Output only. Timestamp when the request is made to the server.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The model version used to generate the response.
   *
   * @var string
   */
  public $modelVersion;
  protected $promptFeedbackType = GoogleCloudAiplatformV1GenerateContentResponsePromptFeedback::class;
  protected $promptFeedbackDataType = '';
  /**
   * Output only. response_id is used to identify each response. It is the
   * encoding of the event_id.
   *
   * @var string
   */
  public $responseId;
  protected $usageMetadataType = GoogleCloudAiplatformV1GenerateContentResponseUsageMetadata::class;
  protected $usageMetadataDataType = '';

  /**
   * Output only. Generated candidates.
   *
   * @param GoogleCloudAiplatformV1Candidate[] $candidates
   */
  public function setCandidates($candidates)
  {
    $this->candidates = $candidates;
  }
  /**
   * @return GoogleCloudAiplatformV1Candidate[]
   */
  public function getCandidates()
  {
    return $this->candidates;
  }
  /**
   * Output only. Timestamp when the request is made to the server.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. The model version used to generate the response.
   *
   * @param string $modelVersion
   */
  public function setModelVersion($modelVersion)
  {
    $this->modelVersion = $modelVersion;
  }
  /**
   * @return string
   */
  public function getModelVersion()
  {
    return $this->modelVersion;
  }
  /**
   * Output only. Content filter results for a prompt sent in the request. Note:
   * Sent only in the first stream chunk. Only happens when no candidates were
   * generated due to content violations.
   *
   * @param GoogleCloudAiplatformV1GenerateContentResponsePromptFeedback $promptFeedback
   */
  public function setPromptFeedback(GoogleCloudAiplatformV1GenerateContentResponsePromptFeedback $promptFeedback)
  {
    $this->promptFeedback = $promptFeedback;
  }
  /**
   * @return GoogleCloudAiplatformV1GenerateContentResponsePromptFeedback
   */
  public function getPromptFeedback()
  {
    return $this->promptFeedback;
  }
  /**
   * Output only. response_id is used to identify each response. It is the
   * encoding of the event_id.
   *
   * @param string $responseId
   */
  public function setResponseId($responseId)
  {
    $this->responseId = $responseId;
  }
  /**
   * @return string
   */
  public function getResponseId()
  {
    return $this->responseId;
  }
  /**
   * Usage metadata about the response(s).
   *
   * @param GoogleCloudAiplatformV1GenerateContentResponseUsageMetadata $usageMetadata
   */
  public function setUsageMetadata(GoogleCloudAiplatformV1GenerateContentResponseUsageMetadata $usageMetadata)
  {
    $this->usageMetadata = $usageMetadata;
  }
  /**
   * @return GoogleCloudAiplatformV1GenerateContentResponseUsageMetadata
   */
  public function getUsageMetadata()
  {
    return $this->usageMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1GenerateContentResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GenerateContentResponse');
