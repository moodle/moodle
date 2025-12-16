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

class GoogleCloudAiplatformV1RagEngineConfig extends \Google\Model
{
  /**
   * Identifier. The name of the RagEngineConfig. Format:
   * `projects/{project}/locations/{location}/ragEngineConfig`
   *
   * @var string
   */
  public $name;
  protected $ragManagedDbConfigType = GoogleCloudAiplatformV1RagManagedDbConfig::class;
  protected $ragManagedDbConfigDataType = '';

  /**
   * Identifier. The name of the RagEngineConfig. Format:
   * `projects/{project}/locations/{location}/ragEngineConfig`
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
   * The config of the RagManagedDb used by RagEngine.
   *
   * @param GoogleCloudAiplatformV1RagManagedDbConfig $ragManagedDbConfig
   */
  public function setRagManagedDbConfig(GoogleCloudAiplatformV1RagManagedDbConfig $ragManagedDbConfig)
  {
    $this->ragManagedDbConfig = $ragManagedDbConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1RagManagedDbConfig
   */
  public function getRagManagedDbConfig()
  {
    return $this->ragManagedDbConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RagEngineConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RagEngineConfig');
