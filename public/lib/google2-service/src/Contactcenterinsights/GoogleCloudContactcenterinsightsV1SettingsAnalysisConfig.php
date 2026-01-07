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

class GoogleCloudContactcenterinsightsV1SettingsAnalysisConfig extends \Google\Model
{
  protected $annotatorSelectorType = GoogleCloudContactcenterinsightsV1AnnotatorSelector::class;
  protected $annotatorSelectorDataType = '';
  /**
   * Percentage of conversations created using Dialogflow runtime integration to
   * analyze automatically, between [0, 100].
   *
   * @var 
   */
  public $runtimeIntegrationAnalysisPercentage;
  /**
   * Percentage of conversations created using the UploadConversation endpoint
   * to analyze automatically, between [0, 100].
   *
   * @var 
   */
  public $uploadConversationAnalysisPercentage;

  /**
   * To select the annotators to run and the phrase matchers to use (if any). If
   * not specified, all annotators will be run.
   *
   * @param GoogleCloudContactcenterinsightsV1AnnotatorSelector $annotatorSelector
   */
  public function setAnnotatorSelector(GoogleCloudContactcenterinsightsV1AnnotatorSelector $annotatorSelector)
  {
    $this->annotatorSelector = $annotatorSelector;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1AnnotatorSelector
   */
  public function getAnnotatorSelector()
  {
    return $this->annotatorSelector;
  }
  public function setRuntimeIntegrationAnalysisPercentage($runtimeIntegrationAnalysisPercentage)
  {
    $this->runtimeIntegrationAnalysisPercentage = $runtimeIntegrationAnalysisPercentage;
  }
  public function getRuntimeIntegrationAnalysisPercentage()
  {
    return $this->runtimeIntegrationAnalysisPercentage;
  }
  public function setUploadConversationAnalysisPercentage($uploadConversationAnalysisPercentage)
  {
    $this->uploadConversationAnalysisPercentage = $uploadConversationAnalysisPercentage;
  }
  public function getUploadConversationAnalysisPercentage()
  {
    return $this->uploadConversationAnalysisPercentage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1SettingsAnalysisConfig::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1SettingsAnalysisConfig');
