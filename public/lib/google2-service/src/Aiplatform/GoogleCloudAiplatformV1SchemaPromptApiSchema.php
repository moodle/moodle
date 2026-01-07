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

class GoogleCloudAiplatformV1SchemaPromptApiSchema extends \Google\Collection
{
  protected $collection_key = 'executions';
  /**
   * The Schema version that represents changes to the API behavior.
   *
   * @var string
   */
  public $apiSchemaVersion;
  protected $executionsType = GoogleCloudAiplatformV1SchemaPromptInstancePromptExecution::class;
  protected $executionsDataType = 'array';
  protected $multimodalPromptType = GoogleCloudAiplatformV1SchemaPromptSpecMultimodalPrompt::class;
  protected $multimodalPromptDataType = '';
  protected $structuredPromptType = GoogleCloudAiplatformV1SchemaPromptSpecStructuredPrompt::class;
  protected $structuredPromptDataType = '';
  protected $translationPromptType = GoogleCloudAiplatformV1SchemaPromptSpecTranslationPrompt::class;
  protected $translationPromptDataType = '';

  /**
   * The Schema version that represents changes to the API behavior.
   *
   * @param string $apiSchemaVersion
   */
  public function setApiSchemaVersion($apiSchemaVersion)
  {
    $this->apiSchemaVersion = $apiSchemaVersion;
  }
  /**
   * @return string
   */
  public function getApiSchemaVersion()
  {
    return $this->apiSchemaVersion;
  }
  /**
   * A list of execution instances for constructing a ready-to-use prompt.
   *
   * @param GoogleCloudAiplatformV1SchemaPromptInstancePromptExecution[] $executions
   */
  public function setExecutions($executions)
  {
    $this->executions = $executions;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaPromptInstancePromptExecution[]
   */
  public function getExecutions()
  {
    return $this->executions;
  }
  /**
   * Multimodal prompt which embeds preambles to prompt string.
   *
   * @param GoogleCloudAiplatformV1SchemaPromptSpecMultimodalPrompt $multimodalPrompt
   */
  public function setMultimodalPrompt(GoogleCloudAiplatformV1SchemaPromptSpecMultimodalPrompt $multimodalPrompt)
  {
    $this->multimodalPrompt = $multimodalPrompt;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaPromptSpecMultimodalPrompt
   */
  public function getMultimodalPrompt()
  {
    return $this->multimodalPrompt;
  }
  /**
   * The prompt variation that stores preambles in separate fields.
   *
   * @param GoogleCloudAiplatformV1SchemaPromptSpecStructuredPrompt $structuredPrompt
   */
  public function setStructuredPrompt(GoogleCloudAiplatformV1SchemaPromptSpecStructuredPrompt $structuredPrompt)
  {
    $this->structuredPrompt = $structuredPrompt;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaPromptSpecStructuredPrompt
   */
  public function getStructuredPrompt()
  {
    return $this->structuredPrompt;
  }
  /**
   * The prompt variation for Translation use case.
   *
   * @param GoogleCloudAiplatformV1SchemaPromptSpecTranslationPrompt $translationPrompt
   */
  public function setTranslationPrompt(GoogleCloudAiplatformV1SchemaPromptSpecTranslationPrompt $translationPrompt)
  {
    $this->translationPrompt = $translationPrompt;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaPromptSpecTranslationPrompt
   */
  public function getTranslationPrompt()
  {
    return $this->translationPrompt;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaPromptApiSchema::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaPromptApiSchema');
