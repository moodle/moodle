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

class GoogleCloudDialogflowCxV3Generator extends \Google\Collection
{
  protected $collection_key = 'placeholders';
  /**
   * Required. The human-readable name of the generator, unique within the
   * agent. The prompt contains pre-defined parameters such as $conversation,
   * $last-user-utterance, etc. populated by Dialogflow. It can also contain
   * custom placeholders which will be resolved during fulfillment.
   *
   * @var string
   */
  public $displayName;
  protected $llmModelSettingsType = GoogleCloudDialogflowCxV3LlmModelSettings::class;
  protected $llmModelSettingsDataType = '';
  protected $modelParameterType = GoogleCloudDialogflowCxV3GeneratorModelParameter::class;
  protected $modelParameterDataType = '';
  /**
   * The unique identifier of the generator. Must be set for the
   * Generators.UpdateGenerator method. Generators.CreateGenerate populates the
   * name automatically. Format: `projects//locations//agents//generators/`.
   *
   * @var string
   */
  public $name;
  protected $placeholdersType = GoogleCloudDialogflowCxV3GeneratorPlaceholder::class;
  protected $placeholdersDataType = 'array';
  protected $promptTextType = GoogleCloudDialogflowCxV3Phrase::class;
  protected $promptTextDataType = '';

  /**
   * Required. The human-readable name of the generator, unique within the
   * agent. The prompt contains pre-defined parameters such as $conversation,
   * $last-user-utterance, etc. populated by Dialogflow. It can also contain
   * custom placeholders which will be resolved during fulfillment.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The LLM model settings.
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
   * Parameters passed to the LLM to configure its behavior.
   *
   * @param GoogleCloudDialogflowCxV3GeneratorModelParameter $modelParameter
   */
  public function setModelParameter(GoogleCloudDialogflowCxV3GeneratorModelParameter $modelParameter)
  {
    $this->modelParameter = $modelParameter;
  }
  /**
   * @return GoogleCloudDialogflowCxV3GeneratorModelParameter
   */
  public function getModelParameter()
  {
    return $this->modelParameter;
  }
  /**
   * The unique identifier of the generator. Must be set for the
   * Generators.UpdateGenerator method. Generators.CreateGenerate populates the
   * name automatically. Format: `projects//locations//agents//generators/`.
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
  /**
   * Optional. List of custom placeholders in the prompt text.
   *
   * @param GoogleCloudDialogflowCxV3GeneratorPlaceholder[] $placeholders
   */
  public function setPlaceholders($placeholders)
  {
    $this->placeholders = $placeholders;
  }
  /**
   * @return GoogleCloudDialogflowCxV3GeneratorPlaceholder[]
   */
  public function getPlaceholders()
  {
    return $this->placeholders;
  }
  /**
   * Required. Prompt for the LLM model.
   *
   * @param GoogleCloudDialogflowCxV3Phrase $promptText
   */
  public function setPromptText(GoogleCloudDialogflowCxV3Phrase $promptText)
  {
    $this->promptText = $promptText;
  }
  /**
   * @return GoogleCloudDialogflowCxV3Phrase
   */
  public function getPromptText()
  {
    return $this->promptText;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3Generator::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3Generator');
