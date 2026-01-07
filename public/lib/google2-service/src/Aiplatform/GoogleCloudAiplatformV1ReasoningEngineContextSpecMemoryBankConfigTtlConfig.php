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

class GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfigTtlConfig extends \Google\Model
{
  /**
   * Optional. The default TTL duration of the memories in the Memory Bank. This
   * applies to all operations that create or update a memory.
   *
   * @var string
   */
  public $defaultTtl;
  protected $granularTtlConfigType = GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfigTtlConfigGranularTtlConfig::class;
  protected $granularTtlConfigDataType = '';
  /**
   * Optional. The default TTL duration of the memory revisions in the Memory
   * Bank. This applies to all operations that create a memory revision. If not
   * set, a default TTL of 365 days will be used.
   *
   * @var string
   */
  public $memoryRevisionDefaultTtl;

  /**
   * Optional. The default TTL duration of the memories in the Memory Bank. This
   * applies to all operations that create or update a memory.
   *
   * @param string $defaultTtl
   */
  public function setDefaultTtl($defaultTtl)
  {
    $this->defaultTtl = $defaultTtl;
  }
  /**
   * @return string
   */
  public function getDefaultTtl()
  {
    return $this->defaultTtl;
  }
  /**
   * Optional. The granular TTL configuration of the memories in the Memory
   * Bank.
   *
   * @param GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfigTtlConfigGranularTtlConfig $granularTtlConfig
   */
  public function setGranularTtlConfig(GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfigTtlConfigGranularTtlConfig $granularTtlConfig)
  {
    $this->granularTtlConfig = $granularTtlConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfigTtlConfigGranularTtlConfig
   */
  public function getGranularTtlConfig()
  {
    return $this->granularTtlConfig;
  }
  /**
   * Optional. The default TTL duration of the memory revisions in the Memory
   * Bank. This applies to all operations that create a memory revision. If not
   * set, a default TTL of 365 days will be used.
   *
   * @param string $memoryRevisionDefaultTtl
   */
  public function setMemoryRevisionDefaultTtl($memoryRevisionDefaultTtl)
  {
    $this->memoryRevisionDefaultTtl = $memoryRevisionDefaultTtl;
  }
  /**
   * @return string
   */
  public function getMemoryRevisionDefaultTtl()
  {
    return $this->memoryRevisionDefaultTtl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfigTtlConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfigTtlConfig');
