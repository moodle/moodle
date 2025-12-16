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

class GoogleCloudAiplatformV1FeatureOnlineStoreBigtableAutoScaling extends \Google\Model
{
  /**
   * Optional. A percentage of the cluster's CPU capacity. Can be from 10% to
   * 80%. When a cluster's CPU utilization exceeds the target that you have set,
   * Bigtable immediately adds nodes to the cluster. When CPU utilization is
   * substantially lower than the target, Bigtable removes nodes. If not set
   * will default to 50%.
   *
   * @var int
   */
  public $cpuUtilizationTarget;
  /**
   * Required. The maximum number of nodes to scale up to. Must be greater than
   * or equal to min_node_count, and less than or equal to 10 times of
   * 'min_node_count'.
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
   * Optional. A percentage of the cluster's CPU capacity. Can be from 10% to
   * 80%. When a cluster's CPU utilization exceeds the target that you have set,
   * Bigtable immediately adds nodes to the cluster. When CPU utilization is
   * substantially lower than the target, Bigtable removes nodes. If not set
   * will default to 50%.
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
   * Required. The maximum number of nodes to scale up to. Must be greater than
   * or equal to min_node_count, and less than or equal to 10 times of
   * 'min_node_count'.
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
class_alias(GoogleCloudAiplatformV1FeatureOnlineStoreBigtableAutoScaling::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FeatureOnlineStoreBigtableAutoScaling');
