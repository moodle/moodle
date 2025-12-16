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

namespace Google\Service\Compute;

class AutoscalingPolicyCpuUtilization extends \Google\Model
{
  /**
   * No predictive method is used. The autoscaler scales the group to meet
   * current demand based on real-time metrics
   */
  public const PREDICTIVE_METHOD_NONE = 'NONE';
  /**
   * Predictive autoscaling improves availability by monitoring daily and weekly
   * load patterns and scaling out ahead of anticipated demand.
   */
  public const PREDICTIVE_METHOD_OPTIMIZE_AVAILABILITY = 'OPTIMIZE_AVAILABILITY';
  /**
   * Indicates whether predictive autoscaling based on CPU metric is enabled.
   * Valid values are:
   *
   * * NONE (default). No predictive method is used. The autoscaler scales the
   * group to meet current demand based on real-time metrics. *
   * OPTIMIZE_AVAILABILITY. Predictive autoscaling improves availability by
   * monitoring daily and weekly load patterns and scaling out ahead of
   * anticipated demand.
   *
   * @var string
   */
  public $predictiveMethod;
  /**
   * The target CPU utilization that the autoscaler maintains. Must be a float
   * value in the range (0, 1]. If not specified, the default is0.6.
   *
   * If the CPU level is below the target utilization, the autoscaler scales in
   * the number of instances until it reaches the minimum number of instances
   * you specified or until the average CPU of your instances reaches the target
   * utilization.
   *
   * If the average CPU is above the target utilization, the autoscaler scales
   * out until it reaches the maximum number of instances you specified or until
   * the average utilization reaches the target utilization.
   *
   * @var 
   */
  public $utilizationTarget;

  /**
   * Indicates whether predictive autoscaling based on CPU metric is enabled.
   * Valid values are:
   *
   * * NONE (default). No predictive method is used. The autoscaler scales the
   * group to meet current demand based on real-time metrics. *
   * OPTIMIZE_AVAILABILITY. Predictive autoscaling improves availability by
   * monitoring daily and weekly load patterns and scaling out ahead of
   * anticipated demand.
   *
   * Accepted values: NONE, OPTIMIZE_AVAILABILITY
   *
   * @param self::PREDICTIVE_METHOD_* $predictiveMethod
   */
  public function setPredictiveMethod($predictiveMethod)
  {
    $this->predictiveMethod = $predictiveMethod;
  }
  /**
   * @return self::PREDICTIVE_METHOD_*
   */
  public function getPredictiveMethod()
  {
    return $this->predictiveMethod;
  }
  public function setUtilizationTarget($utilizationTarget)
  {
    $this->utilizationTarget = $utilizationTarget;
  }
  public function getUtilizationTarget()
  {
    return $this->utilizationTarget;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutoscalingPolicyCpuUtilization::class, 'Google_Service_Compute_AutoscalingPolicyCpuUtilization');
