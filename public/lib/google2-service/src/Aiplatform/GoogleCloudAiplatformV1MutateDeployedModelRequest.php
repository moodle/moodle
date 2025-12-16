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

class GoogleCloudAiplatformV1MutateDeployedModelRequest extends \Google\Model
{
  protected $deployedModelType = GoogleCloudAiplatformV1DeployedModel::class;
  protected $deployedModelDataType = '';
  /**
   * Required. The update mask applies to the resource. See
   * google.protobuf.FieldMask.
   *
   * @var string
   */
  public $updateMask;

  /**
   * Required. The DeployedModel to be mutated within the Endpoint. Only the
   * following fields can be mutated: * `min_replica_count` in either
   * DedicatedResources or AutomaticResources * `max_replica_count` in either
   * DedicatedResources or AutomaticResources * `required_replica_count` in
   * DedicatedResources * autoscaling_metric_specs * `disable_container_logging`
   * (v1 only) * `enable_container_logging` (v1beta1 only) *
   * `scale_to_zero_spec` in DedicatedResources (v1beta1 only) *
   * `initial_replica_count` in DedicatedResources (v1beta1 only)
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
   * Required. The update mask applies to the resource. See
   * google.protobuf.FieldMask.
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1MutateDeployedModelRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1MutateDeployedModelRequest');
