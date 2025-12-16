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

class GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfigTtlConfigGranularTtlConfig extends \Google\Model
{
  /**
   * Optional. The TTL duration for memories uploaded via CreateMemory.
   *
   * @var string
   */
  public $createTtl;
  /**
   * Optional. The TTL duration for memories newly generated via
   * GenerateMemories (GenerateMemoriesResponse.GeneratedMemory.Action.CREATED).
   *
   * @var string
   */
  public $generateCreatedTtl;
  /**
   * Optional. The TTL duration for memories updated via GenerateMemories
   * (GenerateMemoriesResponse.GeneratedMemory.Action.UPDATED). In the case of
   * an UPDATE action, the `expire_time` of the existing memory will be updated
   * to the new value (now + TTL).
   *
   * @var string
   */
  public $generateUpdatedTtl;

  /**
   * Optional. The TTL duration for memories uploaded via CreateMemory.
   *
   * @param string $createTtl
   */
  public function setCreateTtl($createTtl)
  {
    $this->createTtl = $createTtl;
  }
  /**
   * @return string
   */
  public function getCreateTtl()
  {
    return $this->createTtl;
  }
  /**
   * Optional. The TTL duration for memories newly generated via
   * GenerateMemories (GenerateMemoriesResponse.GeneratedMemory.Action.CREATED).
   *
   * @param string $generateCreatedTtl
   */
  public function setGenerateCreatedTtl($generateCreatedTtl)
  {
    $this->generateCreatedTtl = $generateCreatedTtl;
  }
  /**
   * @return string
   */
  public function getGenerateCreatedTtl()
  {
    return $this->generateCreatedTtl;
  }
  /**
   * Optional. The TTL duration for memories updated via GenerateMemories
   * (GenerateMemoriesResponse.GeneratedMemory.Action.UPDATED). In the case of
   * an UPDATE action, the `expire_time` of the existing memory will be updated
   * to the new value (now + TTL).
   *
   * @param string $generateUpdatedTtl
   */
  public function setGenerateUpdatedTtl($generateUpdatedTtl)
  {
    $this->generateUpdatedTtl = $generateUpdatedTtl;
  }
  /**
   * @return string
   */
  public function getGenerateUpdatedTtl()
  {
    return $this->generateUpdatedTtl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfigTtlConfigGranularTtlConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ReasoningEngineContextSpecMemoryBankConfigTtlConfigGranularTtlConfig');
