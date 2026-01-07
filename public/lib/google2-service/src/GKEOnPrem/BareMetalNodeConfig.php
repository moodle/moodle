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

class BareMetalNodeConfig extends \Google\Model
{
  /**
   * The labels assigned to this node. An object containing a list of key/value
   * pairs. The labels here, unioned with the labels set on
   * BareMetalNodePoolConfig are the set of labels that will be applied to the
   * node. If there are any conflicts, the BareMetalNodeConfig labels take
   * precedence. Example: { "name": "wrench", "mass": "1.3kg", "count": "3" }.
   *
   * @var string[]
   */
  public $labels;
  /**
   * The default IPv4 address for SSH access and Kubernetes node. Example:
   * 192.168.0.1
   *
   * @var string
   */
  public $nodeIp;

  /**
   * The labels assigned to this node. An object containing a list of key/value
   * pairs. The labels here, unioned with the labels set on
   * BareMetalNodePoolConfig are the set of labels that will be applied to the
   * node. If there are any conflicts, the BareMetalNodeConfig labels take
   * precedence. Example: { "name": "wrench", "mass": "1.3kg", "count": "3" }.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * The default IPv4 address for SSH access and Kubernetes node. Example:
   * 192.168.0.1
   *
   * @param string $nodeIp
   */
  public function setNodeIp($nodeIp)
  {
    $this->nodeIp = $nodeIp;
  }
  /**
   * @return string
   */
  public function getNodeIp()
  {
    return $this->nodeIp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BareMetalNodeConfig::class, 'Google_Service_GKEOnPrem_BareMetalNodeConfig');
