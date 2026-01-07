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

class GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfig extends \Google\Collection
{
  protected $collection_key = 'customizationConfigs';
  protected $customizationConfigsType = GoogleCloudAiplatformV1MemoryBankCustomizationConfig::class;
  protected $customizationConfigsDataType = 'array';
  /**
   * If true, no memory revisions will be created for any requests to the Memory
   * Bank.
   *
   * @var bool
   */
  public $disableMemoryRevisions;
  protected $generationConfigType = GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfigGenerationConfig::class;
  protected $generationConfigDataType = '';
  protected $similaritySearchConfigType = GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfigSimilaritySearchConfig::class;
  protected $similaritySearchConfigDataType = '';
  protected $ttlConfigType = GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfigTtlConfig::class;
  protected $ttlConfigDataType = '';

  /**
   * Optional. Configuration for how to customize Memory Bank behavior for a
   * particular scope.
   *
   * @param GoogleCloudAiplatformV1MemoryBankCustomizationConfig[] $customizationConfigs
   */
  public function setCustomizationConfigs($customizationConfigs)
  {
    $this->customizationConfigs = $customizationConfigs;
  }
  /**
   * @return GoogleCloudAiplatformV1MemoryBankCustomizationConfig[]
   */
  public function getCustomizationConfigs()
  {
    return $this->customizationConfigs;
  }
  /**
   * If true, no memory revisions will be created for any requests to the Memory
   * Bank.
   *
   * @param bool $disableMemoryRevisions
   */
  public function setDisableMemoryRevisions($disableMemoryRevisions)
  {
    $this->disableMemoryRevisions = $disableMemoryRevisions;
  }
  /**
   * @return bool
   */
  public function getDisableMemoryRevisions()
  {
    return $this->disableMemoryRevisions;
  }
  /**
   * Optional. Configuration for how to generate memories for the Memory Bank.
   *
   * @param GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfigGenerationConfig $generationConfig
   */
  public function setGenerationConfig(GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfigGenerationConfig $generationConfig)
  {
    $this->generationConfig = $generationConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfigGenerationConfig
   */
  public function getGenerationConfig()
  {
    return $this->generationConfig;
  }
  /**
   * Optional. Configuration for how to perform similarity search on memories.
   * If not set, the Memory Bank will use the default embedding model `text-
   * embedding-005`.
   *
   * @param GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfigSimilaritySearchConfig $similaritySearchConfig
   */
  public function setSimilaritySearchConfig(GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfigSimilaritySearchConfig $similaritySearchConfig)
  {
    $this->similaritySearchConfig = $similaritySearchConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfigSimilaritySearchConfig
   */
  public function getSimilaritySearchConfig()
  {
    return $this->similaritySearchConfig;
  }
  /**
   * Optional. Configuration for automatic TTL ("time-to-live") of the memories
   * in the Memory Bank. If not set, TTL will not be applied automatically. The
   * TTL can be explicitly set by modifying the `expire_time` of each Memory
   * resource.
   *
   * @param GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfigTtlConfig $ttlConfig
   */
  public function setTtlConfig(GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfigTtlConfig $ttlConfig)
  {
    $this->ttlConfig = $ttlConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfigTtlConfig
   */
  public function getTtlConfig()
  {
    return $this->ttlConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfig');
