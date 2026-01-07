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

namespace Google\Service\Script;

class Deployment extends \Google\Collection
{
  protected $collection_key = 'entryPoints';
  protected $deploymentConfigType = DeploymentConfig::class;
  protected $deploymentConfigDataType = '';
  /**
   * The deployment ID for this deployment.
   *
   * @var string
   */
  public $deploymentId;
  protected $entryPointsType = EntryPoint::class;
  protected $entryPointsDataType = 'array';
  /**
   * Last modified date time stamp.
   *
   * @var string
   */
  public $updateTime;

  /**
   * The deployment configuration.
   *
   * @param DeploymentConfig $deploymentConfig
   */
  public function setDeploymentConfig(DeploymentConfig $deploymentConfig)
  {
    $this->deploymentConfig = $deploymentConfig;
  }
  /**
   * @return DeploymentConfig
   */
  public function getDeploymentConfig()
  {
    return $this->deploymentConfig;
  }
  /**
   * The deployment ID for this deployment.
   *
   * @param string $deploymentId
   */
  public function setDeploymentId($deploymentId)
  {
    $this->deploymentId = $deploymentId;
  }
  /**
   * @return string
   */
  public function getDeploymentId()
  {
    return $this->deploymentId;
  }
  /**
   * The deployment's entry points.
   *
   * @param EntryPoint[] $entryPoints
   */
  public function setEntryPoints($entryPoints)
  {
    $this->entryPoints = $entryPoints;
  }
  /**
   * @return EntryPoint[]
   */
  public function getEntryPoints()
  {
    return $this->entryPoints;
  }
  /**
   * Last modified date time stamp.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Deployment::class, 'Google_Service_Script_Deployment');
