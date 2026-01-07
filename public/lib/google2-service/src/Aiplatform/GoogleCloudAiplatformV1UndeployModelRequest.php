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

class GoogleCloudAiplatformV1UndeployModelRequest extends \Google\Model
{
  /**
   * Required. The ID of the DeployedModel to be undeployed from the Endpoint.
   *
   * @var string
   */
  public $deployedModelId;
  /**
   * If this field is provided, then the Endpoint's traffic_split will be
   * overwritten with it. If last DeployedModel is being undeployed from the
   * Endpoint, the [Endpoint.traffic_split] will always end up empty when this
   * call returns. A DeployedModel will be successfully undeployed only if it
   * doesn't have any traffic assigned to it when this method executes, or if
   * this field unassigns any traffic to it.
   *
   * @var int[]
   */
  public $trafficSplit;

  /**
   * Required. The ID of the DeployedModel to be undeployed from the Endpoint.
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
   * If this field is provided, then the Endpoint's traffic_split will be
   * overwritten with it. If last DeployedModel is being undeployed from the
   * Endpoint, the [Endpoint.traffic_split] will always end up empty when this
   * call returns. A DeployedModel will be successfully undeployed only if it
   * doesn't have any traffic assigned to it when this method executes, or if
   * this field unassigns any traffic to it.
   *
   * @param int[] $trafficSplit
   */
  public function setTrafficSplit($trafficSplit)
  {
    $this->trafficSplit = $trafficSplit;
  }
  /**
   * @return int[]
   */
  public function getTrafficSplit()
  {
    return $this->trafficSplit;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1UndeployModelRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1UndeployModelRequest');
