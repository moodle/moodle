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

namespace Google\Service\Container;

class AdditionalPodNetworkConfig extends \Google\Model
{
  protected $maxPodsPerNodeType = MaxPodsConstraint::class;
  protected $maxPodsPerNodeDataType = '';
  /**
   * The name of the network attachment for pods to communicate to; cannot be
   * specified along with subnetwork or secondary_pod_range.
   *
   * @var string
   */
  public $networkAttachment;
  /**
   * The name of the secondary range on the subnet which provides IP address for
   * this pod range.
   *
   * @var string
   */
  public $secondaryPodRange;
  /**
   * Name of the subnetwork where the additional pod network belongs.
   *
   * @var string
   */
  public $subnetwork;

  /**
   * The maximum number of pods per node which use this pod network.
   *
   * @param MaxPodsConstraint $maxPodsPerNode
   */
  public function setMaxPodsPerNode(MaxPodsConstraint $maxPodsPerNode)
  {
    $this->maxPodsPerNode = $maxPodsPerNode;
  }
  /**
   * @return MaxPodsConstraint
   */
  public function getMaxPodsPerNode()
  {
    return $this->maxPodsPerNode;
  }
  /**
   * The name of the network attachment for pods to communicate to; cannot be
   * specified along with subnetwork or secondary_pod_range.
   *
   * @param string $networkAttachment
   */
  public function setNetworkAttachment($networkAttachment)
  {
    $this->networkAttachment = $networkAttachment;
  }
  /**
   * @return string
   */
  public function getNetworkAttachment()
  {
    return $this->networkAttachment;
  }
  /**
   * The name of the secondary range on the subnet which provides IP address for
   * this pod range.
   *
   * @param string $secondaryPodRange
   */
  public function setSecondaryPodRange($secondaryPodRange)
  {
    $this->secondaryPodRange = $secondaryPodRange;
  }
  /**
   * @return string
   */
  public function getSecondaryPodRange()
  {
    return $this->secondaryPodRange;
  }
  /**
   * Name of the subnetwork where the additional pod network belongs.
   *
   * @param string $subnetwork
   */
  public function setSubnetwork($subnetwork)
  {
    $this->subnetwork = $subnetwork;
  }
  /**
   * @return string
   */
  public function getSubnetwork()
  {
    return $this->subnetwork;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdditionalPodNetworkConfig::class, 'Google_Service_Container_AdditionalPodNetworkConfig');
