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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowV2AgentCoachingSuggestionSampleResponse extends \Google\Model
{
  protected $duplicateCheckResultType = GoogleCloudDialogflowV2AgentCoachingSuggestionDuplicateCheckResult::class;
  protected $duplicateCheckResultDataType = '';
  /**
   * Optional. Sample response for Agent in text.
   *
   * @var string
   */
  public $responseText;
  protected $sourcesType = GoogleCloudDialogflowV2AgentCoachingSuggestionSources::class;
  protected $sourcesDataType = '';

  /**
   * Output only. Duplicate check result for the sample response.
   *
   * @param GoogleCloudDialogflowV2AgentCoachingSuggestionDuplicateCheckResult $duplicateCheckResult
   */
  public function setDuplicateCheckResult(GoogleCloudDialogflowV2AgentCoachingSuggestionDuplicateCheckResult $duplicateCheckResult)
  {
    $this->duplicateCheckResult = $duplicateCheckResult;
  }
  /**
   * @return GoogleCloudDialogflowV2AgentCoachingSuggestionDuplicateCheckResult
   */
  public function getDuplicateCheckResult()
  {
    return $this->duplicateCheckResult;
  }
  /**
   * Optional. Sample response for Agent in text.
   *
   * @param string $responseText
   */
  public function setResponseText($responseText)
  {
    $this->responseText = $responseText;
  }
  /**
   * @return string
   */
  public function getResponseText()
  {
    return $this->responseText;
  }
  /**
   * Output only. Sources for the Sample Response.
   *
   * @param GoogleCloudDialogflowV2AgentCoachingSuggestionSources $sources
   */
  public function setSources(GoogleCloudDialogflowV2AgentCoachingSuggestionSources $sources)
  {
    $this->sources = $sources;
  }
  /**
   * @return GoogleCloudDialogflowV2AgentCoachingSuggestionSources
   */
  public function getSources()
  {
    return $this->sources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2AgentCoachingSuggestionSampleResponse::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2AgentCoachingSuggestionSampleResponse');
