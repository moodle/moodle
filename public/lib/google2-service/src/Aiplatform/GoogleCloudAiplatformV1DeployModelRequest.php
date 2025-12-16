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

class GoogleCloudAiplatformV1DeployModelRequest extends \Google\Model
{
  protected $deployedModelType = GoogleCloudAiplatformV1DeployedModel::class;
  protected $deployedModelDataType = '';
  /**
   * A map from a DeployedModel's ID to the percentage of this Endpoint's
   * traffic that should be forwarded to that DeployedModel. If this field is
   * non-empty, then the Endpoint's traffic_split will be overwritten with it.
   * To refer to the ID of the just being deployed Model, a "0" should be used,
   * and the actual ID of the new DeployedModel will be filled in its place by
   * this method. The traffic percentage values must add up to 100. If this
   * field is empty, then the Endpoint's traffic_split is not updated.
   *
   * @var int[]
   */
  public $trafficSplit;

  /**
   * Required. The DeployedModel to be created within the Endpoint. Note that
   * Endpoint.traffic_split must be updated for the DeployedModel to start
   * receiving traffic, either as part of this call, or via
   * EndpointService.UpdateEndpoint.
   *
   * @param GoogleCloudAiplatformV1DeployedModel $deployedModel
   */
  public function setDeployedModel(GoogleCloudAiplatformV1DeployedModel $deployedModel)
  {
    $this->deployedModel = $deployedModel;
  }
  /**
   * @return GoogleCloudAiplatformV1DeployedModel
   */
  public function getDeployedModel()
  {
    return $this->deployedModel;
  }
  /**
   * A map from a DeployedModel's ID to the percentage of this Endpoint's
   * traffic that should be forwarded to that DeployedModel. If this field is
   * non-empty, then the Endpoint's traffic_split will be overwritten with it.
   * To refer to the ID of the just being deployed Model, a "0" should be used,
   * and the actual ID of the new DeployedModel will be filled in its place by
   * this method. The traffic percentage values must add up to 100. If this
   * field is empty, then the Endpoint's traffic_split is not updated.
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
class_alias(GoogleCloudAiplatformV1DeployModelRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DeployModelRequest');
