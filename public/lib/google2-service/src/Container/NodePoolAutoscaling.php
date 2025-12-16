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

class NodePoolAutoscaling extends \Google\Model
{
  /**
   * Not set.
   */
  public const LOCATION_POLICY_LOCATION_POLICY_UNSPECIFIED = 'LOCATION_POLICY_UNSPECIFIED';
  /**
   * BALANCED is a best effort policy that aims to balance the sizes of
   * different zones.
   */
  public const LOCATION_POLICY_BALANCED = 'BALANCED';
  /**
   * ANY policy picks zones that have the highest capacity available.
   */
  public const LOCATION_POLICY_ANY = 'ANY';
  /**
   * Can this node pool be deleted automatically.
   *
   * @var bool
   */
  public $autoprovisioned;
  /**
   * Is autoscaling enabled for this node pool.
   *
   * @var bool
   */
  public $enabled;
  /**
   * Location policy used when scaling up a nodepool.
   *
   * @var string
   */
  public $locationPolicy;
  /**
   * Maximum number of nodes for one location in the node pool. Must be >=
   * min_node_count. There has to be enough quota to scale up the cluster.
   *
   * @var int
   */
  public $maxNodeCount;
  /**
   * Minimum number of nodes for one location in the node pool. Must be greater
   * than or equal to 0 and less than or equal to max_node_count.
   *
   * @var int
   */
  public $minNodeCount;
  /**
   * Maximum number of nodes in the node pool. Must be greater than or equal to
   * total_min_node_count. There has to be enough quota to scale up the cluster.
   * The total_*_node_count fields are mutually exclusive with the *_node_count
   * fields.
   *
   * @var int
   */
  public $totalMaxNodeCount;
  /**
   * Minimum number of nodes in the node pool. Must be greater than or equal to
   * 0 and less than or equal to total_max_node_count. The total_*_node_count
   * fields are mutually exclusive with the *_node_count fields.
   *
   * @var int
   */
  public $totalMinNodeCount;

  /**
   * Can this node pool be deleted automatically.
   *
   * @param bool $autoprovisioned
   */
  public function setAutoprovisioned($autoprovisioned)
  {
    $this->autoprovisioned = $autoprovisioned;
  }
  /**
   * @return bool
   */
  public function getAutoprovisioned()
  {
    return $this->autoprovisioned;
  }
  /**
   * Is autoscaling enabled for this node pool.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Location policy used when scaling up a nodepool.
   *
   * Accepted values: LOCATION_POLICY_UNSPECIFIED, BALANCED, ANY
   *
   * @param self::LOCATION_POLICY_* $locationPolicy
   */
  public function setLocationPolicy($locationPolicy)
  {
    $this->locationPolicy = $locationPolicy;
  }
  /**
   * @return self::LOCATION_POLICY_*
   */
  public function getLocationPolicy()
  {
    return $this->locationPolicy;
  }
  /**
   * Maximum number of nodes for one location in the node pool. Must be >=
   * min_node_count. There has to be enough quota to scale up the cluster.
   *
   * @param int $maxNodeCount
   */
  public function setMaxNodeCount($maxNodeCount)
  {
    $this->maxNodeCount = $maxNodeCount;
  }
  /**
   * @return int
   */
  public function getMaxNodeCount()
  {
    return $this->maxNodeCount;
  }
  /**
   * Minimum number of nodes for one location in the node pool. Must be greater
   * than or equal to 0 and less than or equal to max_node_count.
   *
   * @param int $minNodeCount
   */
  public function setMinNodeCount($minNodeCount)
  {
    $this->minNodeCount = $minNodeCount;
  }
  /**
   * @return int
   */
  public function getMinNodeCount()
  {
    return $this->minNodeCount;
  }
  /**
   * Maximum number of nodes in the node pool. Must be greater than or equal to
   * total_min_node_count. There has to be enough quota to scale up the cluster.
   * The total_*_node_count fields are mutually exclusive with the *_node_count
   * fields.
   *
   * @param int $totalMaxNodeCount
   */
  public function setTotalMaxNodeCount($totalMaxNodeCount)
  {
    $this->totalMaxNodeCount = $totalMaxNodeCount;
  }
  /**
   * @return int
   */
  public function getTotalMaxNodeCount()
  {
    return $this->totalMaxNodeCount;
  }
  /**
   * Minimum number of nodes in the node pool. Must be greater than or equal to
   * 0 and less than or equal to total_max_node_count. The total_*_node_count
   * fields are mutually exclusive with the *_node_count fields.
   *
   * @param int $totalMinNodeCount
   */
  public function setTotalMinNodeCount($totalMinNodeCount)
  {
    $this->totalMinNodeCount = $totalMinNodeCount;
  }
  /**
   * @return int
   */
  public function getTotalMinNodeCount()
  {
    return $this->totalMinNodeCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NodePoolAutoscaling::class, 'Google_Service_Container_NodePoolAutoscaling');
