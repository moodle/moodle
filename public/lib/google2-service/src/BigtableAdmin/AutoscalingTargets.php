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

namespace Google\Service\BigtableAdmin;

class AutoscalingTargets extends \Google\Model
{
  /**
   * The cpu utilization that the Autoscaler should be trying to achieve. This
   * number is on a scale from 0 (no utilization) to 100 (total utilization),
   * and is limited between 10 and 80, otherwise it will return INVALID_ARGUMENT
   * error.
   *
   * @var int
   */
  public $cpuUtilizationPercent;
  /**
   * The storage utilization that the Autoscaler should be trying to achieve.
   * This number is limited between 2560 (2.5TiB) and 5120 (5TiB) for a SSD
   * cluster and between 8192 (8TiB) and 16384 (16TiB) for an HDD cluster,
   * otherwise it will return INVALID_ARGUMENT error. If this value is set to 0,
   * it will be treated as if it were set to the default value: 2560 for SSD,
   * 8192 for HDD.
   *
   * @var int
   */
  public $storageUtilizationGibPerNode;

  /**
   * The cpu utilization that the Autoscaler should be trying to achieve. This
   * number is on a scale from 0 (no utilization) to 100 (total utilization),
   * and is limited between 10 and 80, otherwise it will return INVALID_ARGUMENT
   * error.
   *
   * @param int $cpuUtilizationPercent
   */
  public function setCpuUtilizationPercent($cpuUtilizationPercent)
  {
    $this->cpuUtilizationPercent = $cpuUtilizationPercent;
  }
  /**
   * @return int
   */
  public function getCpuUtilizationPercent()
  {
    return $this->cpuUtilizationPercent;
  }
  /**
   * The storage utilization that the Autoscaler should be trying to achieve.
   * This number is limited between 2560 (2.5TiB) and 5120 (5TiB) for a SSD
   * cluster and between 8192 (8TiB) and 16384 (16TiB) for an HDD cluster,
   * otherwise it will return INVALID_ARGUMENT error. If this value is set to 0,
   * it will be treated as if it were set to the default value: 2560 for SSD,
   * 8192 for HDD.
   *
   * @param int $storageUtilizationGibPerNode
   */
  public function setStorageUtilizationGibPerNode($storageUtilizationGibPerNode)
  {
    $this->storageUtilizationGibPerNode = $storageUtilizationGibPerNode;
  }
  /**
   * @return int
   */
  public function getStorageUtilizationGibPerNode()
  {
    return $this->storageUtilizationGibPerNode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutoscalingTargets::class, 'Google_Service_BigtableAdmin_AutoscalingTargets');
