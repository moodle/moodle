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

class GoogleCloudAiplatformV1FeaturestoreOnlineServingConfigScaling extends \Google\Model
{
  /**
   * Optional. The cpu utilization that the Autoscaler should be trying to
   * achieve. This number is on a scale from 0 (no utilization) to 100 (total
   * utilization), and is limited between 10 and 80. When a cluster's CPU
   * utilization exceeds the target that you have set, Bigtable immediately adds
   * nodes to the cluster. When CPU utilization is substantially lower than the
   * target, Bigtable removes nodes. If not set or set to 0, default to 50.
   *
   * @var int
   */
  public $cpuUtilizationTarget;
  /**
   * The maximum number of nodes to scale up to. Must be greater than
   * min_node_count, and less than or equal to 10 times of 'min_node_count'.
   *
   * @var int
   */
  public $maxNodeCount;
  /**
   * Required. The minimum number of nodes to scale down to. Must be greater
   * than or equal to 1.
   *
   * @var int
   */
  public $minNodeCount;

  /**
   * Optional. The cpu utilization that the Autoscaler should be trying to
   * achieve. This number is on a scale from 0 (no utilization) to 100 (total
   * utilization), and is limited between 10 and 80. When a cluster's CPU
   * utilization exceeds the target that you have set, Bigtable immediately adds
   * nodes to the cluster. When CPU utilization is substantially lower than the
   * target, Bigtable removes nodes. If not set or set to 0, default to 50.
   *
   * @param int $cpuUtilizationTarget
   */
  public function setCpuUtilizationTarget($cpuUtilizationTarget)
  {
    $this->cpuUtilizationTarget = $cpuUtilizationTarget;
  }
  /**
   * @return int
   */
  public function getCpuUtilizationTarget()
  {
    return $this->cpuUtilizationTarget;
  }
  /**
   * The maximum number of nodes to scale up to. Must be greater than
   * min_node_count, and less than or equal to 10 times of 'min_node_count'.
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
   * Required. The minimum number of nodes to scale down to. Must be greater
   * than or equal to 1.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FeaturestoreOnlineServingConfigScaling::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FeaturestoreOnlineServingConfigScaling');
