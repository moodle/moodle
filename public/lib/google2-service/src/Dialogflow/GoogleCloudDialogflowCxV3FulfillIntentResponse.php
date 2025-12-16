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

class GoogleCloudDialogflowCxV3FulfillIntentResponse extends \Google\Model
{
  /**
   * The audio data bytes encoded as specified in the request. Note: The output
   * audio is generated based on the values of default platform text responses
   * found in the `query_result.response_messages` field. If multiple default
   * text responses exist, they will be concatenated when generating audio. If
   * no default platform text responses exist, the generated audio content will
   * be empty. In some scenarios, multiple output audio fields may be present in
   * the response structure. In these cases, only the top-most-level audio
   * output has content.
   *
   * @var string
   */
  public $outputAudio;
  protected $outputAudioConfigType = GoogleCloudDialogflowCxV3OutputAudioConfig::class;
  protected $outputAudioConfigDataType = '';
  protected $queryResultType = GoogleCloudDialogflowCxV3QueryResult::class;
  protected $queryResultDataType = '';
  /**
   * Output only. The unique identifier of the response. It can be used to
   * locate a response in the training example set or for reporting issues.
   *
   * @var string
   */
  public $responseId;

  /**
   * The audio data bytes encoded as specified in the request. Note: The output
   * audio is generated based on the values of default platform text responses
   * found in the `query_result.response_messages` field. If multiple default
   * text responses exist, they will be concatenated when generating audio. If
   * no default platform text responses exist, the generated audio content will
   * be empty. In some scenarios, multiple output audio fields may be present in
   * the response structure. In these cases, only the top-most-level audio
   * output has content.
   *
   * @param string $outputAudio
   */
  public function setOutputAudio($outputAudio)
  {
    $this->outputAudio = $outputAudio;
  }
  /**
   * @return string
   */
  public function getOutputAudio()
  {
    return $this->outputAudio;
  }
  /**
   * The config used by the speech synthesizer to generate the output audio.
   *
   * @param GoogleCloudDialogflowCxV3OutputAudioConfig $outputAudioConfig
   */
  public function setOutputAudioConfig(GoogleCloudDialogflowCxV3OutputAudioConfig $outputAudioConfig)
  {
    $this->outputAudioConfig = $outputAudioConfig;
  }
  /**
   * @return GoogleCloudDialogflowCxV3OutputAudioConfig
   */
  public function getOutputAudioConfig()
  {
    return $this->outputAudioConfig;
  }
  /**
   * The result of the conversational query.
   *
   * @param GoogleCloudDialogflowCxV3QueryResult $queryResult
   */
  public function setQueryResult(GoogleCloudDialogflowCxV3QueryResult $queryResult)
  {
    $this->queryResult = $queryResult;
  }
  /**
   * @return GoogleCloudDialogflowCxV3QueryResult
   */
  public function getQueryResult()
  {
    return $this->queryResult;
  }
  /**
   * Output only. The unique identifier of the response. It can be used to
   * locate a response in the training example set or for reporting issues.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3FulfillIntentResponse::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3FulfillIntentResponse');
