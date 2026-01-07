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

class AutoscalingPolicy extends \Google\Collection
{
  /**
   * Do not automatically scale the MIG in or out. The recommended_size field
   * contains the size of MIG that would be set if the actuation mode was
   * enabled.
   */
  public const MODE_OFF = 'OFF';
  /**
   * Automatically scale the MIG in and out according to the policy.
   */
  public const MODE_ON = 'ON';
  /**
   * Automatically create VMs according to the policy, but do not scale the MIG
   * in.
   */
  public const MODE_ONLY_SCALE_OUT = 'ONLY_SCALE_OUT';
  /**
   * Automatically create VMs according to the policy, but do not scale the MIG
   * in.
   */
  public const MODE_ONLY_UP = 'ONLY_UP';
  protected $collection_key = 'customMetricUtilizations';
  /**
   * The number of seconds that your application takes to initialize on a VM
   * instance. This is referred to as the [initialization
   * period](/compute/docs/autoscaler#cool_down_period). Specifying an accurate
   * initialization period improves autoscaler decisions. For example, when
   * scaling out, the autoscaler ignores data from VMs that are still
   * initializing because those VMs might not yet represent normal usage of your
   * application. The default initialization period is 60 seconds.
   *
   * Initialization periods might vary because of numerous factors. We recommend
   * that you test how long your application takes to initialize. To do this,
   * create a VM and time your application's startup process.
   *
   * @var int
   */
  public $coolDownPeriodSec;
  protected $cpuUtilizationType = AutoscalingPolicyCpuUtilization::class;
  protected $cpuUtilizationDataType = '';
  protected $customMetricUtilizationsType = AutoscalingPolicyCustomMetricUtilization::class;
  protected $customMetricUtilizationsDataType = 'array';
  protected $loadBalancingUtilizationType = AutoscalingPolicyLoadBalancingUtilization::class;
  protected $loadBalancingUtilizationDataType = '';
  /**
   * The maximum number of instances that the autoscaler can scale out to. This
   * is required when creating or updating an autoscaler. The maximum number of
   * replicas must not be lower than minimal number of replicas.
   *
   * @var int
   */
  public $maxNumReplicas;
  /**
   * The minimum number of replicas that the autoscaler can scale in to. This
   * cannot be less than 0. If not provided, autoscaler chooses a default value
   * depending on maximum number of instances allowed.
   *
   * @var int
   */
  public $minNumReplicas;
  /**
   * Defines the operating mode for this policy. The following modes are
   * available:        - OFF: Disables the autoscaler but maintains its
   * configuration.    - ONLY_SCALE_OUT: Restricts the autoscaler to add    VM
   * instances only.    - ON: Enables all autoscaler activities according to its
   * policy.
   *
   * For more information, see  "Turning off or restricting an autoscaler"
   *
   * @var string
   */
  public $mode;
  protected $scaleInControlType = AutoscalingPolicyScaleInControl::class;
  protected $scaleInControlDataType = '';
  protected $scalingSchedulesType = AutoscalingPolicyScalingSchedule::class;
  protected $scalingSchedulesDataType = 'map';

  /**
   * The number of seconds that your application takes to initialize on a VM
   * instance. This is referred to as the [initialization
   * period](/compute/docs/autoscaler#cool_down_period). Specifying an accurate
   * initialization period improves autoscaler decisions. For example, when
   * scaling out, the autoscaler ignores data from VMs that are still
   * initializing because those VMs might not yet represent normal usage of your
   * application. The default initialization period is 60 seconds.
   *
   * Initialization periods might vary because of numerous factors. We recommend
   * that you test how long your application takes to initialize. To do this,
   * create a VM and time your application's startup process.
   *
   * @param int $coolDownPeriodSec
   */
  public function setCoolDownPeriodSec($coolDownPeriodSec)
  {
    $this->coolDownPeriodSec = $coolDownPeriodSec;
  }
  /**
   * @return int
   */
  public function getCoolDownPeriodSec()
  {
    return $this->coolDownPeriodSec;
  }
  /**
   * Defines the CPU utilization policy that allows the autoscaler to scale
   * based on the average CPU utilization of a managed instance group.
   *
   * @param AutoscalingPolicyCpuUtilization $cpuUtilization
   */
  public function setCpuUtilization(AutoscalingPolicyCpuUtilization $cpuUtilization)
  {
    $this->cpuUtilization = $cpuUtilization;
  }
  /**
   * @return AutoscalingPolicyCpuUtilization
   */
  public function getCpuUtilization()
  {
    return $this->cpuUtilization;
  }
  /**
   * Configuration parameters of autoscaling based on a custom metric.
   *
   * @param AutoscalingPolicyCustomMetricUtilization[] $customMetricUtilizations
   */
  public function setCustomMetricUtilizations($customMetricUtilizations)
  {
    $this->customMetricUtilizations = $customMetricUtilizations;
  }
  /**
   * @return AutoscalingPolicyCustomMetricUtilization[]
   */
  public function getCustomMetricUtilizations()
  {
    return $this->customMetricUtilizations;
  }
  /**
   * Configuration parameters of autoscaling based on load balancer.
   *
   * @param AutoscalingPolicyLoadBalancingUtilization $loadBalancingUtilization
   */
  public function setLoadBalancingUtilization(AutoscalingPolicyLoadBalancingUtilization $loadBalancingUtilization)
  {
    $this->loadBalancingUtilization = $loadBalancingUtilization;
  }
  /**
   * @return AutoscalingPolicyLoadBalancingUtilization
   */
  public function getLoadBalancingUtilization()
  {
    return $this->loadBalancingUtilization;
  }
  /**
   * The maximum number of instances that the autoscaler can scale out to. This
   * is required when creating or updating an autoscaler. The maximum number of
   * replicas must not be lower than minimal number of replicas.
   *
   * @param int $maxNumReplicas
   */
  public function setMaxNumReplicas($maxNumReplicas)
  {
    $this->maxNumReplicas = $maxNumReplicas;
  }
  /**
   * @return int
   */
  public function getMaxNumReplicas()
  {
    return $this->maxNumReplicas;
  }
  /**
   * The minimum number of replicas that the autoscaler can scale in to. This
   * cannot be less than 0. If not provided, autoscaler chooses a default value
   * depending on maximum number of instances allowed.
   *
   * @param int $minNumReplicas
   */
  public function setMinNumReplicas($minNumReplicas)
  {
    $this->minNumReplicas = $minNumReplicas;
  }
  /**
   * @return int
   */
  public function getMinNumReplicas()
  {
    return $this->minNumReplicas;
  }
  /**
   * Defines the operating mode for this policy. The following modes are
   * available:        - OFF: Disables the autoscaler but maintains its
   * configuration.    - ONLY_SCALE_OUT: Restricts the autoscaler to add    VM
   * instances only.    - ON: Enables all autoscaler activities according to its
   * policy.
   *
   * For more information, see  "Turning off or restricting an autoscaler"
   *
   * Accepted values: OFF, ON, ONLY_SCALE_OUT, ONLY_UP
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * @param AutoscalingPolicyScaleInControl $scaleInControl
   */
  public function setScaleInControl(AutoscalingPolicyScaleInControl $scaleInControl)
  {
    $this->scaleInControl = $scaleInControl;
  }
  /**
   * @return AutoscalingPolicyScaleInControl
   */
  public function getScaleInControl()
  {
    return $this->scaleInControl;
  }
  /**
   * Scaling schedules defined for an autoscaler. Multiple schedules can be set
   * on an autoscaler, and they can overlap. During overlapping periods the
   * greatest min_required_replicas of all scaling schedules is applied. Up to
   * 128 scaling schedules are allowed.
   *
   * @param AutoscalingPolicyScalingSchedule[] $scalingSchedules
   */
  public function setScalingSchedules($scalingSchedules)
  {
    $this->scalingSchedules = $scalingSchedules;
  }
  /**
   * @return AutoscalingPolicyScalingSchedule[]
   */
  public function getScalingSchedules()
  {
    return $this->scalingSchedules;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutoscalingPolicy::class, 'Google_Service_Compute_AutoscalingPolicy');
