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

class BareMetalWorkloadNodeConfig extends \Google\Model
{
  /**
   * No container runtime selected.
   */
  public const CONTAINER_RUNTIME_CONTAINER_RUNTIME_UNSPECIFIED = 'CONTAINER_RUNTIME_UNSPECIFIED';
  /**
   * Containerd runtime.
   */
  public const CONTAINER_RUNTIME_CONTAINERD = 'CONTAINERD';
  /**
   * Specifies which container runtime will be used.
   *
   * @var string
   */
  public $containerRuntime;
  /**
   * The maximum number of pods a node can run. The size of the CIDR range
   * assigned to the node will be derived from this parameter.
   *
   * @var string
   */
  public $maxPodsPerNode;

  /**
   * Specifies which container runtime will be used.
   *
   * Accepted values: CONTAINER_RUNTIME_UNSPECIFIED, CONTAINERD
   *
   * @param self::CONTAINER_RUNTIME_* $containerRuntime
   */
  public function setContainerRuntime($containerRuntime)
  {
    $this->containerRuntime = $containerRuntime;
  }
  /**
   * @return self::CONTAINER_RUNTIME_*
   */
  public function getContainerRuntime()
  {
    return $this->containerRuntime;
  }
  /**
   * The maximum number of pods a node can run. The size of the CIDR range
   * assigned to the node will be derived from this parameter.
   *
   * @param string $maxPodsPerNode
   */
  public function setMaxPodsPerNode($maxPodsPerNode)
  {
    $this->maxPodsPerNode = $maxPodsPerNode;
  }
  /**
   * @return string
   */
  public function getMaxPodsPerNode()
  {
    return $this->maxPodsPerNode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BareMetalWorkloadNodeConfig::class, 'Google_Service_GKEOnPrem_BareMetalWorkloadNodeConfig');
