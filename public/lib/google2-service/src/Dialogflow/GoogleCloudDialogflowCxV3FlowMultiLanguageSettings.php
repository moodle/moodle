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

class GoogleCloudDialogflowCxV3FlowMultiLanguageSettings extends \Google\Collection
{
  protected $collection_key = 'supportedResponseLanguageCodes';
  /**
   * Optional. Enable multi-language detection for this flow. This can be set
   * only if agent level multi language setting is enabled.
   *
   * @var bool
   */
  public $enableMultiLanguageDetection;
  /**
   * Optional. Agent will respond in the detected language if the detected
   * language code is in the supported resolved languages for this flow. This
   * will be used only if multi-language training is enabled in the agent and
   * multi-language detection is enabled in the flow. The supported languages
   * must be a subset of the languages supported by the agent.
   *
   * @var string[]
   */
  public $supportedResponseLanguageCodes;

  /**
   * Optional. Enable multi-language detection for this flow. This can be set
   * only if agent level multi language setting is enabled.
   *
   * @param bool $enableMultiLanguageDetection
   */
  public function setEnableMultiLanguageDetection($enableMultiLanguageDetection)
  {
    $this->enableMultiLanguageDetection = $enableMultiLanguageDetection;
  }
  /**
   * @return bool
   */
  public function getEnableMultiLanguageDetection()
  {
    return $this->enableMultiLanguageDetection;
  }
  /**
   * Optional. Agent will respond in the detected language if the detected
   * language code is in the supported resolved languages for this flow. This
   * will be used only if multi-language training is enabled in the agent and
   * multi-language detection is enabled in the flow. The supported languages
   * must be a subset of the languages supported by the agent.
   *
   * @param string[] $supportedResponseLanguageCodes
   */
  public function setSupportedResponseLanguageCodes($supportedResponseLanguageCodes)
  {
    $this->supportedResponseLanguageCodes = $supportedResponseLanguageCodes;
  }
  /**
   * @return string[]
   */
  public function getSupportedResponseLanguageCodes()
  {
    return $this->supportedResponseLanguageCodes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3FlowMultiLanguageSettings::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3FlowMultiLanguageSettings');
