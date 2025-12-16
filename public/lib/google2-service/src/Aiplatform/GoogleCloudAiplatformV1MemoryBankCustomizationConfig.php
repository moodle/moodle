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

class GoogleCloudAiplatformV1MemoryBankCustomizationConfig extends \Google\Collection
{
  protected $collection_key = 'scopeKeys';
  /**
   * Optional. If true, then the memories will be generated in the third person
   * (i.e. "The user generates memories with Memory Bank."). By default, the
   * memories will be generated in the first person (i.e. "I generate memories
   * with Memory Bank.")
   *
   * @var bool
   */
  public $enableThirdPersonMemories;
  protected $generateMemoriesExamplesType = GoogleCloudAiplatformV1MemoryBankCustomizationConfigGenerateMemoriesExample::class;
  protected $generateMemoriesExamplesDataType = 'array';
  protected $memoryTopicsType = GoogleCloudAiplatformV1MemoryBankCustomizationConfigMemoryTopic::class;
  protected $memoryTopicsDataType = 'array';
  /**
   * Optional. The scope keys (i.e. 'user_id') for which to use this config. A
   * request's scope must include all of the provided keys for the config to be
   * used (order does not matter). If empty, then the config will be used for
   * all requests that do not have a more specific config. Only one default
   * config is allowed per Memory Bank.
   *
   * @var string[]
   */
  public $scopeKeys;

  /**
   * Optional. If true, then the memories will be generated in the third person
   * (i.e. "The user generates memories with Memory Bank."). By default, the
   * memories will be generated in the first person (i.e. "I generate memories
   * with Memory Bank.")
   *
   * @param bool $enableThirdPersonMemories
   */
  public function setEnableThirdPersonMemories($enableThirdPersonMemories)
  {
    $this->enableThirdPersonMemories = $enableThirdPersonMemories;
  }
  /**
   * @return bool
   */
  public function getEnableThirdPersonMemories()
  {
    return $this->enableThirdPersonMemories;
  }
  /**
   * Optional. Examples of how to generate memories for a particular scope.
   *
   * @param GoogleCloudAiplatformV1MemoryBankCustomizationConfigGenerateMemoriesExample[] $generateMemoriesExamples
   */
  public function setGenerateMemoriesExamples($generateMemoriesExamples)
  {
    $this->generateMemoriesExamples = $generateMemoriesExamples;
  }
  /**
   * @return GoogleCloudAiplatformV1MemoryBankCustomizationConfigGenerateMemoriesExample[]
   */
  public function getGenerateMemoriesExamples()
  {
    return $this->generateMemoriesExamples;
  }
  /**
   * Optional. Topics of information that should be extracted from conversations
   * and stored as memories. If not set, then Memory Bank's default topics will
   * be used.
   *
   * @param GoogleCloudAiplatformV1MemoryBankCustomizationConfigMemoryTopic[] $memoryTopics
   */
  public function setMemoryTopics($memoryTopics)
  {
    $this->memoryTopics = $memoryTopics;
  }
  /**
   * @return GoogleCloudAiplatformV1MemoryBankCustomizationConfigMemoryTopic[]
   */
  public function getMemoryTopics()
  {
    return $this->memoryTopics;
  }
  /**
   * Optional. The scope keys (i.e. 'user_id') for which to use this config. A
   * request's scope must include all of the provided keys for the config to be
   * used (order does not matter). If empty, then the config will be used for
   * all requests that do not have a more specific config. Only one default
   * config is allowed per Memory Bank.
   *
   * @param string[] $scopeKeys
   */
  public function setScopeKeys($scopeKeys)
  {
    $this->scopeKeys = $scopeKeys;
  }
  /**
   * @return string[]
   */
  public function getScopeKeys()
  {
    return $this->scopeKeys;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1MemoryBankCustomizationConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1MemoryBankCustomizationConfig');
