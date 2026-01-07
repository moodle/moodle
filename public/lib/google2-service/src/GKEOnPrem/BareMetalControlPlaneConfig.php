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

namespace Google\Service\GKEOnPrem;

class BareMetalControlPlaneConfig extends \Google\Collection
{
  protected $collection_key = 'apiServerArgs';
  protected $apiServerArgsType = BareMetalApiServerArgument::class;
  protected $apiServerArgsDataType = 'array';
  protected $controlPlaneNodePoolConfigType = BareMetalControlPlaneNodePoolConfig::class;
  protected $controlPlaneNodePoolConfigDataType = '';

  /**
   * Customizes the default API server args. Only a subset of customized flags
   * are supported. For the exact format, refer to the [API server
   * documentation](https://kubernetes.io/docs/reference/command-line-tools-
   * reference/kube-apiserver/).
   *
   * @param BareMetalApiServerArgument[] $apiServerArgs
   */
  public function setApiServerArgs($apiServerArgs)
  {
    $this->apiServerArgs = $apiServerArgs;
  }
  /**
   * @return BareMetalApiServerArgument[]
   */
  public function getApiServerArgs()
  {
    return $this->apiServerArgs;
  }
  /**
   * Required. Configures the node pool running the control plane.
   *
   * @param BareMetalControlPlaneNodePoolConfig $controlPlaneNodePoolConfig
   */
  public function setControlPlaneNodePoolConfig(BareMetalControlPlaneNodePoolConfig $controlPlaneNodePoolConfig)
  {
    $this->controlPlaneNodePoolConfig = $controlPlaneNodePoolConfig;
  }
  /**
   * @return BareMetalControlPlaneNodePoolConfig
   */
  public function getControlPlaneNodePoolConfig()
  {
    return $this->controlPlaneNodePoolConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BareMetalControlPlaneConfig::class, 'Google_Service_GKEOnPrem_BareMetalControlPlaneConfig');
