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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1AnnotatorSelectorSummarizationConfig extends \Google\Model
{
  /**
   * Unspecified summarization model.
   */
  public const SUMMARIZATION_MODEL_SUMMARIZATION_MODEL_UNSPECIFIED = 'SUMMARIZATION_MODEL_UNSPECIFIED';
  /**
   * The CCAI baseline model. This model is deprecated and will be removed in
   * the future. We recommend using `generator` instead.
   *
   * @deprecated
   */
  public const SUMMARIZATION_MODEL_BASELINE_MODEL = 'BASELINE_MODEL';
  /**
   * The CCAI baseline model, V2.0. This model is deprecated and will be removed
   * in the future. We recommend using `generator` instead.
   *
   * @deprecated
   */
  public const SUMMARIZATION_MODEL_BASELINE_MODEL_V2_0 = 'BASELINE_MODEL_V2_0';
  /**
   * Resource name of the Dialogflow conversation profile. Format: projects/{pro
   * ject}/locations/{location}/conversationProfiles/{conversation_profile}
   *
   * @var string
   */
  public $conversationProfile;
  /**
   * The resource name of the existing created generator. Format:
   * projects//locations//generators/
   *
   * @var string
   */
  public $generator;
  /**
   * Default summarization model to be used.
   *
   * @var string
   */
  public $summarizationModel;

  /**
   * Resource name of the Dialogflow conversation profile. Format: projects/{pro
   * ject}/locations/{location}/conversationProfiles/{conversation_profile}
   *
   * @param string $conversationProfile
   */
  public function setConversationProfile($conversationProfile)
  {
    $this->conversationProfile = $conversationProfile;
  }
  /**
   * @return string
   */
  public function getConversationProfile()
  {
    return $this->conversationProfile;
  }
  /**
   * The resource name of the existing created generator. Format:
   * projects//locations//generators/
   *
   * @param string $generator
   */
  public function setGenerator($generator)
  {
    $this->generator = $generator;
  }
  /**
   * @return string
   */
  public function getGenerator()
  {
    return $this->generator;
  }
  /**
   * Default summarization model to be used.
   *
   * Accepted values: SUMMARIZATION_MODEL_UNSPECIFIED, BASELINE_MODEL,
   * BASELINE_MODEL_V2_0
   *
   * @param self::SUMMARIZATION_MODEL_* $summarizationModel
   */
  public function setSummarizationModel($summarizationModel)
  {
    $this->summarizationModel = $summarizationModel;
  }
  /**
   * @return self::SUMMARIZATION_MODEL_*
   */
  public function getSummarizationModel()
  {
    return $this->summarizationModel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1AnnotatorSelectorSummarizationConfig::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1AnnotatorSelectorSummarizationConfig');
