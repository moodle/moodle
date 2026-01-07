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

class GoogleCloudAiplatformV1DeployedModelRef extends \Google\Model
{
  /**
   * Immutable. The ID of the Checkpoint deployed in the DeployedModel.
   *
   * @var string
   */
  public $checkpointId;
  /**
   * Immutable. An ID of a DeployedModel in the above Endpoint.
   *
   * @var string
   */
  public $deployedModelId;
  /**
   * Immutable. A resource name of an Endpoint.
   *
   * @var string
   */
  public $endpoint;

  /**
   * Immutable. The ID of the Checkpoint deployed in the DeployedModel.
   *
   * @param string $checkpointId
   */
  public function setCheckpointId($checkpointId)
  {
    $this->checkpointId = $checkpointId;
  }
  /**
   * @return string
   */
  public function getCheckpointId()
  {
    return $this->checkpointId;
  }
  /**
   * Immutable. An ID of a DeployedModel in the above Endpoint.
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
   * Immutable. A resource name of an Endpoint.
   *
   * @param string $endpoint
   */
  public function setEndpoint($endpoint)
  {
    $this->endpoint = $endpoint;
  }
  /**
   * @return string
   */
  public function getEndpoint()
  {
    return $this->endpoint;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1DeployedModelRef::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DeployedModelRef');
