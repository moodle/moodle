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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1AssistantGenerationConfig extends \Google\Collection
{
  protected $collection_key = 'allowedModelIds';
  /**
   * Optional. The list of models that are allowed to be used for assistant.
   *
   * @var string[]
   */
  public $allowedModelIds;
  /**
   * The default language to use for the generation of the assistant response.
   * Use an ISO 639-1 language code such as `en`. If not specified, the language
   * will be automatically detected.
   *
   * @var string
   */
  public $defaultLanguage;
  /**
   * Optional. The default model to use for assistant.
   *
   * @var string
   */
  public $defaultModelId;
  protected $systemInstructionType = GoogleCloudDiscoveryengineV1AssistantGenerationConfigSystemInstruction::class;
  protected $systemInstructionDataType = '';

  /**
   * Optional. The list of models that are allowed to be used for assistant.
   *
   * @param string[] $allowedModelIds
   */
  public function setAllowedModelIds($allowedModelIds)
  {
    $this->allowedModelIds = $allowedModelIds;
  }
  /**
   * @return string[]
   */
  public function getAllowedModelIds()
  {
    return $this->allowedModelIds;
  }
  /**
   * The default language to use for the generation of the assistant response.
   * Use an ISO 639-1 language code such as `en`. If not specified, the language
   * will be automatically detected.
   *
   * @param string $defaultLanguage
   */
  public function setDefaultLanguage($defaultLanguage)
  {
    $this->defaultLanguage = $defaultLanguage;
  }
  /**
   * @return string
   */
  public function getDefaultLanguage()
  {
    return $this->defaultLanguage;
  }
  /**
   * Optional. The default model to use for assistant.
   *
   * @param string $defaultModelId
   */
  public function setDefaultModelId($defaultModelId)
  {
    $this->defaultModelId = $defaultModelId;
  }
  /**
   * @return string
   */
  public function getDefaultModelId()
  {
    return $this->defaultModelId;
  }
  /**
   * System instruction, also known as the prompt preamble for LLM calls. See
   * also https://cloud.google.com/vertex-ai/generative-
   * ai/docs/learn/prompts/system-instructions
   *
   * @param GoogleCloudDiscoveryengineV1AssistantGenerationConfigSystemInstruction $systemInstruction
   */
  public function setSystemInstruction(GoogleCloudDiscoveryengineV1AssistantGenerationConfigSystemInstruction $systemInstruction)
  {
    $this->systemInstruction = $systemInstruction;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AssistantGenerationConfigSystemInstruction
   */
  public function getSystemInstruction()
  {
    return $this->systemInstruction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1AssistantGenerationConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AssistantGenerationConfig');
