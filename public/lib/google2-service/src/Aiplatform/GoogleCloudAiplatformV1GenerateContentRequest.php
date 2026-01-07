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

class GoogleCloudAiplatformV1GenerateContentRequest extends \Google\Collection
{
  protected $collection_key = 'tools';
  /**
   * Optional. The name of the cached content used as context to serve the
   * prediction. Note: only used in explicit caching, where users can have
   * control over caching (e.g. what content to cache) and enjoy guaranteed cost
   * savings. Format:
   * `projects/{project}/locations/{location}/cachedContents/{cachedContent}`
   *
   * @var string
   */
  public $cachedContent;
  protected $contentsType = GoogleCloudAiplatformV1Content::class;
  protected $contentsDataType = 'array';
  protected $generationConfigType = GoogleCloudAiplatformV1GenerationConfig::class;
  protected $generationConfigDataType = '';
  /**
   * Optional. The labels with user-defined metadata for the request. It is used
   * for billing and reporting only. Label keys and values can be no longer than
   * 63 characters (Unicode codepoints) and can only contain lowercase letters,
   * numeric characters, underscores, and dashes. International characters are
   * allowed. Label values are optional. Label keys must start with a letter.
   *
   * @var string[]
   */
  public $labels;
  protected $modelArmorConfigType = GoogleCloudAiplatformV1ModelArmorConfig::class;
  protected $modelArmorConfigDataType = '';
  protected $safetySettingsType = GoogleCloudAiplatformV1SafetySetting::class;
  protected $safetySettingsDataType = 'array';
  protected $systemInstructionType = GoogleCloudAiplatformV1Content::class;
  protected $systemInstructionDataType = '';
  protected $toolConfigType = GoogleCloudAiplatformV1ToolConfig::class;
  protected $toolConfigDataType = '';
  protected $toolsType = GoogleCloudAiplatformV1Tool::class;
  protected $toolsDataType = 'array';

  /**
   * Optional. The name of the cached content used as context to serve the
   * prediction. Note: only used in explicit caching, where users can have
   * control over caching (e.g. what content to cache) and enjoy guaranteed cost
   * savings. Format:
   * `projects/{project}/locations/{location}/cachedContents/{cachedContent}`
   *
   * @param string $cachedContent
   */
  public function setCachedContent($cachedContent)
  {
    $this->cachedContent = $cachedContent;
  }
  /**
   * @return string
   */
  public function getCachedContent()
  {
    return $this->cachedContent;
  }
  /**
   * Required. The content of the current conversation with the model. For
   * single-turn queries, this is a single instance. For multi-turn queries,
   * this is a repeated field that contains conversation history + latest
   * request.
   *
   * @param GoogleCloudAiplatformV1Content[] $contents
   */
  public function setContents($contents)
  {
    $this->contents = $contents;
  }
  /**
   * @return GoogleCloudAiplatformV1Content[]
   */
  public function getContents()
  {
    return $this->contents;
  }
  /**
   * Optional. Generation config.
   *
   * @param GoogleCloudAiplatformV1GenerationConfig $generationConfig
   */
  public function setGenerationConfig(GoogleCloudAiplatformV1GenerationConfig $generationConfig)
  {
    $this->generationConfig = $generationConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1GenerationConfig
   */
  public function getGenerationConfig()
  {
    return $this->generationConfig;
  }
  /**
   * Optional. The labels with user-defined metadata for the request. It is used
   * for billing and reporting only. Label keys and values can be no longer than
   * 63 characters (Unicode codepoints) and can only contain lowercase letters,
   * numeric characters, underscores, and dashes. International characters are
   * allowed. Label values are optional. Label keys must start with a letter.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Optional. Settings for prompt and response sanitization using the Model
   * Armor service. If supplied, safety_settings must not be supplied.
   *
   * @param GoogleCloudAiplatformV1ModelArmorConfig $modelArmorConfig
   */
  public function setModelArmorConfig(GoogleCloudAiplatformV1ModelArmorConfig $modelArmorConfig)
  {
    $this->modelArmorConfig = $modelArmorConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelArmorConfig
   */
  public function getModelArmorConfig()
  {
    return $this->modelArmorConfig;
  }
  /**
   * Optional. Per request settings for blocking unsafe content. Enforced on
   * GenerateContentResponse.candidates.
   *
   * @param GoogleCloudAiplatformV1SafetySetting[] $safetySettings
   */
  public function setSafetySettings($safetySettings)
  {
    $this->safetySettings = $safetySettings;
  }
  /**
   * @return GoogleCloudAiplatformV1SafetySetting[]
   */
  public function getSafetySettings()
  {
    return $this->safetySettings;
  }
  /**
   * Optional. The user provided system instructions for the model. Note: only
   * text should be used in parts and content in each part will be in a separate
   * paragraph.
   *
   * @param GoogleCloudAiplatformV1Content $systemInstruction
   */
  public function setSystemInstruction(GoogleCloudAiplatformV1Content $systemInstruction)
  {
    $this->systemInstruction = $systemInstruction;
  }
  /**
   * @return GoogleCloudAiplatformV1Content
   */
  public function getSystemInstruction()
  {
    return $this->systemInstruction;
  }
  /**
   * Optional. Tool config. This config is shared for all tools provided in the
   * request.
   *
   * @param GoogleCloudAiplatformV1ToolConfig $toolConfig
   */
  public function setToolConfig(GoogleCloudAiplatformV1ToolConfig $toolConfig)
  {
    $this->toolConfig = $toolConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1ToolConfig
   */
  public function getToolConfig()
  {
    return $this->toolConfig;
  }
  /**
   * Optional. A list of `Tools` the model may use to generate the next
   * response. A `Tool` is a piece of code that enables the system to interact
   * with external systems to perform an action, or set of actions, outside of
   * knowledge and scope of the model.
   *
   * @param GoogleCloudAiplatformV1Tool[] $tools
   */
  public function setTools($tools)
  {
    $this->tools = $tools;
  }
  /**
   * @return GoogleCloudAiplatformV1Tool[]
   */
  public function getTools()
  {
    return $this->tools;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1GenerateContentRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GenerateContentRequest');
