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

class GoogleCloudAiplatformV1ModelDeploymentMonitoringObjectiveConfig extends \Google\Model
{
  /**
   * The DeployedModel ID of the objective config.
   *
   * @var string
   */
  public $deployedModelId;
  protected $objectiveConfigType = GoogleCloudAiplatformV1ModelMonitoringObjectiveConfig::class;
  protected $objectiveConfigDataType = '';

  /**
   * The DeployedModel ID of the objective config.
   *
   * @param string $deployedModelId
   */
  public function setDeployedModelId($deployedModelId)
  {
    $this->deployedModelId = $deployedModelId;
  }
  /**
   * @return string
   */
  public function getDeployedModelId()
  {
    return $this->deployedModelId;
  }
  /**
   * The objective config of for the modelmonitoring job of this deployed model.
   *
   * @param GoogleCloudAiplatformV1ModelMonitoringObjectiveConfig $objectiveConfig
   */
  public function setObjectiveConfig(GoogleCloudAiplatformV1ModelMonitoringObjectiveConfig $objectiveConfig)
  {
    $this->objectiveConfig = $objectiveConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelMonitoringObjectiveConfig
   */
  public function getObjectiveConfig()
  {
    return $this->objectiveConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ModelDeploymentMonitoringObjectiveConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ModelDeploymentMonitoringObjectiveConfig');
