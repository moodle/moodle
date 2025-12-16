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

class GoogleCloudAiplatformV1ReasoningEngineContextSpec extends \Google\Model
{
  protected $memoryBankConfigType = GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfig::class;
  protected $memoryBankConfigDataType = '';

  /**
   * Optional. Specification for a Memory Bank, which manages memories for the
   * Agent Engine.
   *
   * @param GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfig $memoryBankConfig
   */
  public function setMemoryBankConfig(GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfig $memoryBankConfig)
  {
    $this->memoryBankConfig = $memoryBankConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfig
   */
  public function getMemoryBankConfig()
  {
    return $this->memoryBankConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ReasoningEngineContextSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ReasoningEngineContextSpec');
