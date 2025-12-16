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

class GoogleCloudAiplatformV1DeployModelOperationMetadata extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const DEPLOYMENT_STAGE_DEPLOYMENT_STAGE_UNSPECIFIED = 'DEPLOYMENT_STAGE_UNSPECIFIED';
  /**
   * The deployment is initializing and setting up the environment.
   */
  public const DEPLOYMENT_STAGE_STARTING_DEPLOYMENT = 'STARTING_DEPLOYMENT';
  /**
   * The deployment is preparing the model assets.
   */
  public const DEPLOYMENT_STAGE_PREPARING_MODEL = 'PREPARING_MODEL';
  /**
   * The deployment is creating the underlying serving cluster.
   */
  public const DEPLOYMENT_STAGE_CREATING_SERVING_CLUSTER = 'CREATING_SERVING_CLUSTER';
  /**
   * The deployment is adding nodes to the serving cluster.
   */
  public const DEPLOYMENT_STAGE_ADDING_NODES_TO_CLUSTER = 'ADDING_NODES_TO_CLUSTER';
  /**
   * The deployment is getting the container image for the model server.
   */
  public const DEPLOYMENT_STAGE_GETTING_CONTAINER_IMAGE = 'GETTING_CONTAINER_IMAGE';
  /**
   * The deployment is starting the model server.
   */
  public const DEPLOYMENT_STAGE_STARTING_MODEL_SERVER = 'STARTING_MODEL_SERVER';
  /**
   * The deployment is performing finalization steps.
   */
  public const DEPLOYMENT_STAGE_FINISHING_UP = 'FINISHING_UP';
  /**
   * The deployment has terminated.
   */
  public const DEPLOYMENT_STAGE_DEPLOYMENT_TERMINATED = 'DEPLOYMENT_TERMINATED';
  /**
   * The deployment has succeeded.
   */
  public const DEPLOYMENT_STAGE_SUCCESSFULLY_DEPLOYED = 'SUCCESSFULLY_DEPLOYED';
  /**
   * The deployment has failed.
   */
  public const DEPLOYMENT_STAGE_FAILED_TO_DEPLOY = 'FAILED_TO_DEPLOY';
  /**
   * Output only. The deployment stage of the model.
   *
   * @var string
   */
  public $deploymentStage;
  protected $genericMetadataType = GoogleCloudAiplatformV1GenericOperationMetadata::class;
  protected $genericMetadataDataType = '';

  /**
   * Output only. The deployment stage of the model.
   *
   * Accepted values: DEPLOYMENT_STAGE_UNSPECIFIED, STARTING_DEPLOYMENT,
   * PREPARING_MODEL, CREATING_SERVING_CLUSTER, ADDING_NODES_TO_CLUSTER,
   * GETTING_CONTAINER_IMAGE, STARTING_MODEL_SERVER, FINISHING_UP,
   * DEPLOYMENT_TERMINATED, SUCCESSFULLY_DEPLOYED, FAILED_TO_DEPLOY
   *
   * @param self::DEPLOYMENT_STAGE_* $deploymentStage
   */
  public function setDeploymentStage($deploymentStage)
  {
    $this->deploymentStage = $deploymentStage;
  }
  /**
   * @return self::DEPLOYMENT_STAGE_*
   */
  public function getDeploymentStage()
  {
    return $this->deploymentStage;
  }
  /**
   * The operation generic information.
   *
   * @param GoogleCloudAiplatformV1GenericOperationMetadata $genericMetadata
   */
  public function setGenericMetadata(GoogleCloudAiplatformV1GenericOperationMetadata $genericMetadata)
  {
    $this->genericMetadata = $genericMetadata;
  }
  /**
   * @return GoogleCloudAiplatformV1GenericOperationMetadata
   */
  public function getGenericMetadata()
  {
    return $this->genericMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1DeployModelOperationMetadata::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DeployModelOperationMetadata');
