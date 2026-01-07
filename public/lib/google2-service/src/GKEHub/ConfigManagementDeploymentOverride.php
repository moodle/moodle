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

namespace Google\Service\GKEHub;

class ConfigManagementDeploymentOverride extends \Google\Collection
{
  protected $collection_key = 'containers';
  protected $containersType = ConfigManagementContainerOverride::class;
  protected $containersDataType = 'array';
  /**
   * Required. The name of the deployment resource to be overridden.
   *
   * @var string
   */
  public $deploymentName;
  /**
   * Required. The namespace of the deployment resource to be overridden.
   *
   * @var string
   */
  public $deploymentNamespace;

  /**
   * Optional. The containers of the deployment resource to be overridden.
   *
   * @param ConfigManagementContainerOverride[] $containers
   */
  public function setContainers($containers)
  {
    $this->containers = $containers;
  }
  /**
   * @return ConfigManagementContainerOverride[]
   */
  public function getContainers()
  {
    return $this->containers;
  }
  /**
   * Required. The name of the deployment resource to be overridden.
   *
   * @param string $deploymentName
   */
  public function setDeploymentName($deploymentName)
  {
    $this->deploymentName = $deploymentName;
  }
  /**
   * @return string
   */
  public function getDeploymentName()
  {
    return $this->deploymentName;
  }
  /**
   * Required. The namespace of the deployment resource to be overridden.
   *
   * @param string $deploymentNamespace
   */
  public function setDeploymentNamespace($deploymentNamespace)
  {
    $this->deploymentNamespace = $deploymentNamespace;
  }
  /**
   * @return string
   */
  public function getDeploymentNamespace()
  {
    return $this->deploymentNamespace;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfigManagementDeploymentOverride::class, 'Google_Service_GKEHub_ConfigManagementDeploymentOverride');
