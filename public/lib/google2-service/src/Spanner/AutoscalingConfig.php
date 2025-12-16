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

class AutoscalingConfig extends \Google\Collection
{
  protected $collection_key = 'asymmetricAutoscalingOptions';
  protected $asymmetricAutoscalingOptionsType = AsymmetricAutoscalingOption::class;
  protected $asymmetricAutoscalingOptionsDataType = 'array';
  protected $autoscalingLimitsType = AutoscalingLimits::class;
  protected $autoscalingLimitsDataType = '';
  protected $autoscalingTargetsType = AutoscalingTargets::class;
  protected $autoscalingTargetsDataType = '';

  /**
   * Optional. Optional asymmetric autoscaling options. Replicas matching the
   * replica selection criteria will be autoscaled independently from other
   * replicas. The autoscaler will scale the replicas based on the utilization
   * of replicas identified by the replica selection. Replica selections should
   * not overlap with each other. Other replicas (those do not match any replica
   * selection) will be autoscaled together and will have the same compute
   * capacity allocated to them.
   *
   * @param AsymmetricAutoscalingOption[] $asymmetricAutoscalingOptions
   */
  public function setAsymmetricAutoscalingOptions($asymmetricAutoscalingOptions)
  {
    $this->asymmetricAutoscalingOptions = $asymmetricAutoscalingOptions;
  }
  /**
   * @return AsymmetricAutoscalingOption[]
   */
  public function getAsymmetricAutoscalingOptions()
  {
    return $this->asymmetricAutoscalingOptions;
  }
  /**
   * Required. Autoscaling limits for an instance.
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
   * Required. The autoscaling targets for an instance.
   *
   * @param AutoscalingTargets $autoscalingTargets
   */
  public function setAutoscalingTargets(AutoscalingTargets $autoscalingTargets)
  {
    $this->autoscalingTargets = $autoscalingTargets;
  }
  /**
   * @return AutoscalingTargets
   */
  public function getAutoscalingTargets()
  {
    return $this->autoscalingTargets;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutoscalingConfig::class, 'Google_Service_Spanner_AutoscalingConfig');
