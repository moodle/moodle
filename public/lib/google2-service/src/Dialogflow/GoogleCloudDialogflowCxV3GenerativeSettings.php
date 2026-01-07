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

class GoogleCloudDialogflowCxV3GenerativeSettings extends \Google\Model
{
  protected $fallbackSettingsType = GoogleCloudDialogflowCxV3GenerativeSettingsFallbackSettings::class;
  protected $fallbackSettingsDataType = '';
  protected $generativeSafetySettingsType = GoogleCloudDialogflowCxV3SafetySettings::class;
  protected $generativeSafetySettingsDataType = '';
  protected $knowledgeConnectorSettingsType = GoogleCloudDialogflowCxV3GenerativeSettingsKnowledgeConnectorSettings::class;
  protected $knowledgeConnectorSettingsDataType = '';
  /**
   * Language for this settings.
   *
   * @var string
   */
  public $languageCode;
  protected $llmModelSettingsType = GoogleCloudDialogflowCxV3LlmModelSettings::class;
  protected $llmModelSettingsDataType = '';
  /**
   * Format: `projects//locations//agents//generativeSettings`.
   *
   * @var string
   */
  public $name;

  /**
   * Settings for Generative Fallback.
   *
   * @param GoogleCloudDialogflowCxV3GenerativeSettingsFallbackSettings $fallbackSettings
   */
  public function setFallbackSettings(GoogleCloudDialogflowCxV3GenerativeSettingsFallbackSettings $fallbackSettings)
  {
    $this->fallbackSettings = $fallbackSettings;
  }
  /**
   * @return GoogleCloudDialogflowCxV3GenerativeSettingsFallbackSettings
   */
  public function getFallbackSettings()
  {
    return $this->fallbackSettings;
  }
  /**
   * Settings for Generative Safety.
   *
   * @param GoogleCloudDialogflowCxV3SafetySettings $generativeSafetySettings
   */
  public function setGenerativeSafetySettings(GoogleCloudDialogflowCxV3SafetySettings $generativeSafetySettings)
  {
    $this->generativeSafetySettings = $generativeSafetySettings;
  }
  /**
   * @return GoogleCloudDialogflowCxV3SafetySettings
   */
  public function getGenerativeSafetySettings()
  {
    return $this->generativeSafetySettings;
  }
  /**
   * Settings for knowledge connector.
   *
   * @param GoogleCloudDialogflowCxV3GenerativeSettingsKnowledgeConnectorSettings $knowledgeConnectorSettings
   */
  public function setKnowledgeConnectorSettings(GoogleCloudDialogflowCxV3GenerativeSettingsKnowledgeConnectorSettings $knowledgeConnectorSettings)
  {
    $this->knowledgeConnectorSettings = $knowledgeConnectorSettings;
  }
  /**
   * @return GoogleCloudDialogflowCxV3GenerativeSettingsKnowledgeConnectorSettings
   */
  public function getKnowledgeConnectorSettings()
  {
    return $this->knowledgeConnectorSettings;
  }
  /**
   * Language for this settings.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * LLM model settings.
   *
   * @param GoogleCloudDialogflowCxV3LlmModelSettings $llmModelSettings
   */
  public function setLlmModelSettings(GoogleCloudDialogflowCxV3LlmModelSettings $llmModelSettings)
  {
    $this->llmModelSettings = $llmModelSettings;
  }
  /**
   * @return GoogleCloudDialogflowCxV3LlmModelSettings
   */
  public function getLlmModelSettings()
  {
    return $this->llmModelSettings;
  }
  /**
   * Format: `projects//locations//agents//generativeSettings`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3GenerativeSettings::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3GenerativeSettings');
