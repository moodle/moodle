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

class GoogleCloudAiplatformV1ToolConfig extends \Google\Model
{
  protected $functionCallingConfigType = GoogleCloudAiplatformV1FunctionCallingConfig::class;
  protected $functionCallingConfigDataType = '';
  protected $retrievalConfigType = GoogleCloudAiplatformV1RetrievalConfig::class;
  protected $retrievalConfigDataType = '';

  /**
   * Optional. Function calling config.
   *
   * @param GoogleCloudAiplatformV1FunctionCallingConfig $functionCallingConfig
   */
  public function setFunctionCallingConfig(GoogleCloudAiplatformV1FunctionCallingConfig $functionCallingConfig)
  {
    $this->functionCallingConfig = $functionCallingConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1FunctionCallingConfig
   */
  public function getFunctionCallingConfig()
  {
    return $this->functionCallingConfig;
  }
  /**
   * Optional. Retrieval config.
   *
   * @param GoogleCloudAiplatformV1RetrievalConfig $retrievalConfig
   */
  public function setRetrievalConfig(GoogleCloudAiplatformV1RetrievalConfig $retrievalConfig)
  {
    $this->retrievalConfig = $retrievalConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1RetrievalConfig
   */
  public function getRetrievalConfig()
  {
    return $this->retrievalConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ToolConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ToolConfig');
