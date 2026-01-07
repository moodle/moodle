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

class AutoscalingConfigOverrides extends \Google\Model
{
  protected $autoscalingLimitsType = AutoscalingLimits::class;
  protected $autoscalingLimitsDataType = '';
  /**
   * Optional. If specified, overrides the autoscaling target
   * high_priority_cpu_utilization_percent in the top-level autoscaling
   * configuration for the selected replicas.
   *
   * @var int
   */
  public $autoscalingTargetHighPriorityCpuUtilizationPercent;

  /**
   * Optional. If specified, overrides the min/max limit in the top-level
   * autoscaling configuration for the selected replicas.
   *
   * @param AutoscalingLimits $autoscalingLimits
   */
  public function setAutoscalingLimits(AutoscalingLimits $autoscalingLimits)
  {
    $this->autoscalingLimits = $autoscalingLimits;
  }
  /**
   * @return AutoscalingLimits
   */
  public function getAutoscalingLimits()
  {
    return $this->autoscalingLimits;
  }
  /**
   * Optional. If specified, overrides the autoscaling target
   * high_priority_cpu_utilization_percent in the top-level autoscaling
   * configuration for the selected replicas.
   *
   * @param int $autoscalingTargetHighPriorityCpuUtilizationPercent
   */
  public function setAutoscalingTargetHighPriorityCpuUtilizationPercent($autoscalingTargetHighPriorityCpuUtilizationPercent)
  {
    $this->autoscalingTargetHighPriorityCpuUtilizationPercent = $autoscalingTargetHighPriorityCpuUtilizationPercent;
  }
  /**
   * @return int
   */
  public function getAutoscalingTargetHighPriorityCpuUtilizationPercent()
  {
    return $this->autoscalingTargetHighPriorityCpuUtilizationPercent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutoscalingConfigOverrides::class, 'Google_Service_Spanner_AutoscalingConfigOverrides');
