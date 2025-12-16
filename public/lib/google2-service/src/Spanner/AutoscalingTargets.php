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

namespace Google\Service\Spanner;

class AutoscalingTargets extends \Google\Model
{
  /**
   * Required. The target high priority cpu utilization percentage that the
   * autoscaler should be trying to achieve for the instance. This number is on
   * a scale from 0 (no utilization) to 100 (full utilization). The valid range
   * is [10, 90] inclusive.
   *
   * @var int
   */
  public $highPriorityCpuUtilizationPercent;
  /**
   * Required. The target storage utilization percentage that the autoscaler
   * should be trying to achieve for the instance. This number is on a scale
   * from 0 (no utilization) to 100 (full utilization). The valid range is [10,
   * 99] inclusive.
   *
   * @var int
   */
  public $storageUtilizationPercent;

  /**
   * Required. The target high priority cpu utilization percentage that the
   * autoscaler should be trying to achieve for the instance. This number is on
   * a scale from 0 (no utilization) to 100 (full utilization). The valid range
   * is [10, 90] inclusive.
   *
   * @param int $highPriorityCpuUtilizationPercent
   */
  public function setHighPriorityCpuUtilizationPercent($highPriorityCpuUtilizationPercent)
  {
    $this->highPriorityCpuUtilizationPercent = $highPriorityCpuUtilizationPercent;
  }
  /**
   * @return int
   */
  public function getHighPriorityCpuUtilizationPercent()
  {
    return $this->highPriorityCpuUtilizationPercent;
  }
  /**
   * Required. The target storage utilization percentage that the autoscaler
   * should be trying to achieve for the instance. This number is on a scale
   * from 0 (no utilization) to 100 (full utilization). The valid range is [10,
   * 99] inclusive.
   *
   * @param int $storageUtilizationPercent
   */
  public function setStorageUtilizationPercent($storageUtilizationPercent)
  {
    $this->storageUtilizationPercent = $storageUtilizationPercent;
  }
  /**
   * @return int
   */
  public function getStorageUtilizationPercent()
  {
    return $this->storageUtilizationPercent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutoscalingTargets::class, 'Google_Service_Spanner_AutoscalingTargets');
