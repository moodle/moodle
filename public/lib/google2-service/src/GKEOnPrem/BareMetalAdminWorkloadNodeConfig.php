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

class BareMetalAdminWorkloadNodeConfig extends \Google\Model
{
  /**
   * The maximum number of pods a node can run. The size of the CIDR range
   * assigned to the node will be derived from this parameter. By default 110
   * Pods are created per Node. Upper bound is 250 for both HA and non-HA admin
   * cluster. Lower bound is 64 for non-HA admin cluster and 32 for HA admin
   * cluster.
   *
   * @var string
   */
  public $maxPodsPerNode;

  /**
   * The maximum number of pods a node can run. The size of the CIDR range
   * assigned to the node will be derived from this parameter. By default 110
   * Pods are created per Node. Upper bound is 250 for both HA and non-HA admin
   * cluster. Lower bound is 64 for non-HA admin cluster and 32 for HA admin
   * cluster.
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
class_alias(BareMetalAdminWorkloadNodeConfig::class, 'Google_Service_GKEOnPrem_BareMetalAdminWorkloadNodeConfig');
