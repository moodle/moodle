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

namespace Google\Service\CloudDeploy;

class Canary extends \Google\Model
{
  protected $canaryDeploymentType = CanaryDeployment::class;
  protected $canaryDeploymentDataType = '';
  protected $customCanaryDeploymentType = CustomCanaryDeployment::class;
  protected $customCanaryDeploymentDataType = '';
  protected $runtimeConfigType = RuntimeConfig::class;
  protected $runtimeConfigDataType = '';

  /**
   * Optional. Configures the progressive based deployment for a Target.
   *
   * @param CanaryDeployment $canaryDeployment
   */
  public function setCanaryDeployment(CanaryDeployment $canaryDeployment)
  {
    $this->canaryDeployment = $canaryDeployment;
  }
  /**
   * @return CanaryDeployment
   */
  public function getCanaryDeployment()
  {
    return $this->canaryDeployment;
  }
  /**
   * Optional. Configures the progressive based deployment for a Target, but
   * allows customizing at the phase level where a phase represents each of the
   * percentage deployments.
   *
   * @param CustomCanaryDeployment $customCanaryDeployment
   */
  public function setCustomCanaryDeployment(CustomCanaryDeployment $customCanaryDeployment)
  {
    $this->customCanaryDeployment = $customCanaryDeployment;
  }
  /**
   * @return CustomCanaryDeployment
   */
  public function getCustomCanaryDeployment()
  {
    return $this->customCanaryDeployment;
  }
  /**
   * Optional. Runtime specific configurations for the deployment strategy. The
   * runtime configuration is used to determine how Cloud Deploy will split
   * traffic to enable a progressive deployment.
   *
   * @param RuntimeConfig $runtimeConfig
   */
  public function setRuntimeConfig(RuntimeConfig $runtimeConfig)
  {
    $this->runtimeConfig = $runtimeConfig;
  }
  /**
   * @return RuntimeConfig
   */
  public function getRuntimeConfig()
  {
    return $this->runtimeConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Canary::class, 'Google_Service_CloudDeploy_Canary');
