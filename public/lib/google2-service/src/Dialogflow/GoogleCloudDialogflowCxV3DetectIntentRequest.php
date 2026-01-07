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

class GoogleCloudDialogflowCxV3DetectIntentRequest extends \Google\Model
{
  /**
   * Not specified. `FULL` will be used.
   */
  public const RESPONSE_VIEW_DETECT_INTENT_RESPONSE_VIEW_UNSPECIFIED = 'DETECT_INTENT_RESPONSE_VIEW_UNSPECIFIED';
  /**
   * Full response view includes all fields.
   */
  public const RESPONSE_VIEW_DETECT_INTENT_RESPONSE_VIEW_FULL = 'DETECT_INTENT_RESPONSE_VIEW_FULL';
  /**
   * Basic response view omits the following fields: -
   * QueryResult.diagnostic_info
   */
  public const RESPONSE_VIEW_DETECT_INTENT_RESPONSE_VIEW_BASIC = 'DETECT_INTENT_RESPONSE_VIEW_BASIC';
  protected $outputAudioConfigType = GoogleCloudDialogflowCxV3OutputAudioConfig::class;
  protected $outputAudioConfigDataType = '';
  protected $queryInputType = GoogleCloudDialogflowCxV3QueryInput::class;
  protected $queryInputDataType = '';
  protected $queryParamsType = GoogleCloudDialogflowCxV3QueryParameters::class;
  protected $queryParamsDataType = '';
  /**
   * Optional. Specifies which fields in the QueryResult to return. If not set,
   * the default is DETECT_INTENT_RESPONSE_VIEW_FULL.
   *
   * @var string
   */
  public $responseView;

  /**
   * Instructs the speech synthesizer how to generate the output audio.
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
   * Required. The input specification.
   *
   * @param GoogleCloudDialogflowCxV3QueryInput $queryInput
   */
  public function setQueryInput(GoogleCloudDialogflowCxV3QueryInput $queryInput)
  {
    $this->queryInput = $queryInput;
  }
  /**
   * @return GoogleCloudDialogflowCxV3QueryInput
   */
  public function getQueryInput()
  {
    return $this->queryInput;
  }
  /**
   * The parameters of this query.
   *
   * @param GoogleCloudDialogflowCxV3QueryParameters $queryParams
   */
  public function setQueryParams(GoogleCloudDialogflowCxV3QueryParameters $queryParams)
  {
    $this->queryParams = $queryParams;
  }
  /**
   * @return GoogleCloudDialogflowCxV3QueryParameters
   */
  public function getQueryParams()
  {
    return $this->queryParams;
  }
  /**
   * Optional. Specifies which fields in the QueryResult to return. If not set,
   * the default is DETECT_INTENT_RESPONSE_VIEW_FULL.
   *
   * Accepted values: DETECT_INTENT_RESPONSE_VIEW_UNSPECIFIED,
   * DETECT_INTENT_RESPONSE_VIEW_FULL, DETECT_INTENT_RESPONSE_VIEW_BASIC
   *
   * @param self::RESPONSE_VIEW_* $responseView
   */
  public function setResponseView($responseView)
  {
    $this->responseView = $responseView;
  }
  /**
   * @return self::RESPONSE_VIEW_*
   */
  public function getResponseView()
  {
    return $this->responseView;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3DetectIntentRequest::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3DetectIntentRequest');
