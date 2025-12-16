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

class GoogleCloudAiplatformV1GenerationConfigRoutingConfig extends \Google\Model
{
  protected $autoModeType = GoogleCloudAiplatformV1GenerationConfigRoutingConfigAutoRoutingMode::class;
  protected $autoModeDataType = '';
  protected $manualModeType = GoogleCloudAiplatformV1GenerationConfigRoutingConfigManualRoutingMode::class;
  protected $manualModeDataType = '';

  /**
   * In this mode, the model is selected automatically based on the content of
   * the request.
   *
   * @param GoogleCloudAiplatformV1GenerationConfigRoutingConfigAutoRoutingMode $autoMode
   */
  public function setAutoMode(GoogleCloudAiplatformV1GenerationConfigRoutingConfigAutoRoutingMode $autoMode)
  {
    $this->autoMode = $autoMode;
  }
  /**
   * @return GoogleCloudAiplatformV1GenerationConfigRoutingConfigAutoRoutingMode
   */
  public function getAutoMode()
  {
    return $this->autoMode;
  }
  /**
   * In this mode, the model is specified manually.
   *
   * @param GoogleCloudAiplatformV1GenerationConfigRoutingConfigManualRoutingMode $manualMode
   */
  public function setManualMode(GoogleCloudAiplatformV1GenerationConfigRoutingConfigManualRoutingMode $manualMode)
  {
    $this->manualMode = $manualMode;
  }
  /**
   * @return GoogleCloudAiplatformV1GenerationConfigRoutingConfigManualRoutingMode
   */
  public function getManualMode()
  {
    return $this->manualMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1GenerationConfigRoutingConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GenerationConfigRoutingConfig');
