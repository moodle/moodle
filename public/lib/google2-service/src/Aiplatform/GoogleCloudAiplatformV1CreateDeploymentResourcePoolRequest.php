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

class GoogleCloudAiplatformV1CreateDeploymentResourcePoolRequest extends \Google\Model
{
  protected $deploymentResourcePoolType = GoogleCloudAiplatformV1DeploymentResourcePool::class;
  protected $deploymentResourcePoolDataType = '';
  /**
   * Required. The ID to use for the DeploymentResourcePool, which will become
   * the final component of the DeploymentResourcePool's resource name. The
   * maximum length is 63 characters, and valid characters are
   * `/^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$/`.
   *
   * @var string
   */
  public $deploymentResourcePoolId;

  /**
   * Required. The DeploymentResourcePool to create.
   *
   * @param GoogleCloudAiplatformV1DeploymentResourcePool $deploymentResourcePool
   */
  public function setDeploymentResourcePool(GoogleCloudAiplatformV1DeploymentResourcePool $deploymentResourcePool)
  {
    $this->deploymentResourcePool = $deploymentResourcePool;
  }
  /**
   * @return GoogleCloudAiplatformV1DeploymentResourcePool
   */
  public function getDeploymentResourcePool()
  {
    return $this->deploymentResourcePool;
  }
  /**
   * Required. The ID to use for the DeploymentResourcePool, which will become
   * the final component of the DeploymentResourcePool's resource name. The
   * maximum length is 63 characters, and valid characters are
   * `/^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$/`.
   *
   * @param string $deploymentResourcePoolId
   */
  public function setDeploymentResourcePoolId($deploymentResourcePoolId)
  {
    $this->deploymentResourcePoolId = $deploymentResourcePoolId;
  }
  /**
   * @return string
   */
  public function getDeploymentResourcePoolId()
  {
    return $this->deploymentResourcePoolId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1CreateDeploymentResourcePoolRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1CreateDeploymentResourcePoolRequest');
