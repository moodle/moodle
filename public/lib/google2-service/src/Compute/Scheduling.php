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

class Scheduling extends \Google\Collection
{
  /**
   * Delete the VM.
   */
  public const INSTANCE_TERMINATION_ACTION_DELETE = 'DELETE';
  /**
   * Default value. This value is unused.
   */
  public const INSTANCE_TERMINATION_ACTION_INSTANCE_TERMINATION_ACTION_UNSPECIFIED = 'INSTANCE_TERMINATION_ACTION_UNSPECIFIED';
  /**
   * Stop the VM without storing in-memory content. default action.
   */
  public const INSTANCE_TERMINATION_ACTION_STOP = 'STOP';
  /**
   * *[Default]* Allows Compute Engine to automatically migrate instances out of
   * the way of maintenance events.
   */
  public const ON_HOST_MAINTENANCE_MIGRATE = 'MIGRATE';
  /**
   * Tells Compute Engine to terminate and (optionally) restart the instance
   * away from the maintenance activity. If you would like your instance to be
   * restarted, set the automaticRestart flag to true. Your instance may be
   * restarted more than once, and it may be restarted outside the window of
   * maintenance events.
   */
  public const ON_HOST_MAINTENANCE_TERMINATE = 'TERMINATE';
  /**
   * Instance is provisioned using the Flex Start provisioning model and has a
   * limited runtime.
   */
  public const PROVISIONING_MODEL_FLEX_START = 'FLEX_START';
  /**
   * Bound to the lifecycle of the reservation in which it is provisioned.
   */
  public const PROVISIONING_MODEL_RESERVATION_BOUND = 'RESERVATION_BOUND';
  /**
   * Heavily discounted, no guaranteed runtime.
   */
  public const PROVISIONING_MODEL_SPOT = 'SPOT';
  /**
   * Standard provisioning with user controlled runtime, no discounts.
   */
  public const PROVISIONING_MODEL_STANDARD = 'STANDARD';
  protected $collection_key = 'nodeAffinities';
  /**
   * Specifies whether the instance should be automatically restarted if it is
   * terminated by Compute Engine (not terminated by a user). You can only set
   * the automatic restart option for standard instances.Preemptible instances
   * cannot be automatically restarted.
   *
   * By default, this is set to true so an instance is automatically restarted
   * if it is terminated by Compute Engine.
   *
   * @var bool
   */
  public $automaticRestart;
  /**
   * Specifies the availability domain to place the instance in. The value must
   * be a number between 1 and the number of availability domains specified in
   * the spread placement policy attached to the instance.
   *
   * @var int
   */
  public $availabilityDomain;
  /**
   * Specify the time in seconds for host error detection, the value must be
   * within the range of [90, 330] with the increment of 30, if unset, the
   * default behavior of host error recovery will be used.
   *
   * @var int
   */
  public $hostErrorTimeoutSeconds;
  /**
   * Specifies the termination action for the instance.
   *
   * @var string
   */
  public $instanceTerminationAction;
  protected $localSsdRecoveryTimeoutType = Duration::class;
  protected $localSsdRecoveryTimeoutDataType = '';
  /**
   * An opaque location hint used to place the instance close to other
   * resources. This field is for use by internal tools that use the public API.
   *
   * @var string
   */
  public $locationHint;
  protected $maxRunDurationType = Duration::class;
  protected $maxRunDurationDataType = '';
  /**
   * The minimum number of virtual CPUs this instance will consume when running
   * on a sole-tenant node.
   *
   * @var int
   */
  public $minNodeCpus;
  protected $nodeAffinitiesType = SchedulingNodeAffinity::class;
  protected $nodeAffinitiesDataType = 'array';
  /**
   * Defines the maintenance behavior for this instance. For standard instances,
   * the default behavior is MIGRATE. Forpreemptible instances, the default and
   * only possible behavior is TERMINATE. For more information, see  Set  VM
   * host maintenance policy.
   *
   * @var string
   */
  public $onHostMaintenance;
  protected $onInstanceStopActionType = SchedulingOnInstanceStopAction::class;
  protected $onInstanceStopActionDataType = '';
  /**
   * Defines whether the instance is preemptible. This can only be set during
   * instance creation or while the instance isstopped and therefore, in a
   * `TERMINATED` state. SeeInstance Life Cycle for more information on the
   * possible instance states.
   *
   * @var bool
   */
  public $preemptible;
  /**
   * Specifies the provisioning model of the instance.
   *
   * @var string
   */
  public $provisioningModel;
  /**
   * Default is false and there will be 120 seconds between GCE ACPI G2 Soft Off
   * and ACPI G3 Mechanical Off for Standard VMs and 30 seconds for Spot VMs.
   *
   * @var bool
   */
  public $skipGuestOsShutdown;
  /**
   * Specifies the timestamp, when the instance will be terminated, inRFC3339
   * text format. If specified, the instance termination action will be
   * performed at the termination time.
   *
   * @var string
   */
  public $terminationTime;

  /**
   * Specifies whether the instance should be automatically restarted if it is
   * terminated by Compute Engine (not terminated by a user). You can only set
   * the automatic restart option for standard instances.Preemptible instances
   * cannot be automatically restarted.
   *
   * By default, this is set to true so an instance is automatically restarted
   * if it is terminated by Compute Engine.
   *
   * @param bool $automaticRestart
   */
  public function setAutomaticRestart($automaticRestart)
  {
    $this->automaticRestart = $automaticRestart;
  }
  /**
   * @return bool
   */
  public function getAutomaticRestart()
  {
    return $this->automaticRestart;
  }
  /**
   * Specifies the availability domain to place the instance in. The value must
   * be a number between 1 and the number of availability domains specified in
   * the spread placement policy attached to the instance.
   *
   * @param int $availabilityDomain
   */
  public function setAvailabilityDomain($availabilityDomain)
  {
    $this->availabilityDomain = $availabilityDomain;
  }
  /**
   * @return int
   */
  public function getAvailabilityDomain()
  {
    return $this->availabilityDomain;
  }
  /**
   * Specify the time in seconds for host error detection, the value must be
   * within the range of [90, 330] with the increment of 30, if unset, the
   * default behavior of host error recovery will be used.
   *
   * @param int $hostErrorTimeoutSeconds
   */
  public function setHostErrorTimeoutSeconds($hostErrorTimeoutSeconds)
  {
    $this->hostErrorTimeoutSeconds = $hostErrorTimeoutSeconds;
  }
  /**
   * @return int
   */
  public function getHostErrorTimeoutSeconds()
  {
    return $this->hostErrorTimeoutSeconds;
  }
  /**
   * Specifies the termination action for the instance.
   *
   * Accepted values: DELETE, INSTANCE_TERMINATION_ACTION_UNSPECIFIED, STOP
   *
   * @param self::INSTANCE_TERMINATION_ACTION_* $instanceTerminationAction
   */
  public function setInstanceTerminationAction($instanceTerminationAction)
  {
    $this->instanceTerminationAction = $instanceTerminationAction;
  }
  /**
   * @return self::INSTANCE_TERMINATION_ACTION_*
   */
  public function getInstanceTerminationAction()
  {
    return $this->instanceTerminationAction;
  }
  /**
   * Specifies the maximum amount of time a Local Ssd Vm should wait while
   * recovery of the Local Ssd state is attempted. Its value should be in
   * between 0 and 168 hours with hour granularity and the default value being 1
   * hour.
   *
   * @param Duration $localSsdRecoveryTimeout
   */
  public function setLocalSsdRecoveryTimeout(Duration $localSsdRecoveryTimeout)
  {
    $this->localSsdRecoveryTimeout = $localSsdRecoveryTimeout;
  }
  /**
   * @return Duration
   */
  public function getLocalSsdRecoveryTimeout()
  {
    return $this->localSsdRecoveryTimeout;
  }
  /**
   * An opaque location hint used to place the instance close to other
   * resources. This field is for use by internal tools that use the public API.
   *
   * @param string $locationHint
   */
  public function setLocationHint($locationHint)
  {
    $this->locationHint = $locationHint;
  }
  /**
   * @return string
   */
  public function getLocationHint()
  {
    return $this->locationHint;
  }
  /**
   * Specifies the max run duration for the given instance. If specified, the
   * instance termination action will be performed at the end of the run
   * duration.
   *
   * @param Duration $maxRunDuration
   */
  public function setMaxRunDuration(Duration $maxRunDuration)
  {
    $this->maxRunDuration = $maxRunDuration;
  }
  /**
   * @return Duration
   */
  public function getMaxRunDuration()
  {
    return $this->maxRunDuration;
  }
  /**
   * The minimum number of virtual CPUs this instance will consume when running
   * on a sole-tenant node.
   *
   * @param int $minNodeCpus
   */
  public function setMinNodeCpus($minNodeCpus)
  {
    $this->minNodeCpus = $minNodeCpus;
  }
  /**
   * @return int
   */
  public function getMinNodeCpus()
  {
    return $this->minNodeCpus;
  }
  /**
   * A set of node affinity and anti-affinity configurations. Refer
   * toConfiguring node affinity for more information. Overrides
   * reservationAffinity.
   *
   * @param SchedulingNodeAffinity[] $nodeAffinities
   */
  public function setNodeAffinities($nodeAffinities)
  {
    $this->nodeAffinities = $nodeAffinities;
  }
  /**
   * @return SchedulingNodeAffinity[]
   */
  public function getNodeAffinities()
  {
    return $this->nodeAffinities;
  }
  /**
   * Defines the maintenance behavior for this instance. For standard instances,
   * the default behavior is MIGRATE. Forpreemptible instances, the default and
   * only possible behavior is TERMINATE. For more information, see  Set  VM
   * host maintenance policy.
   *
   * Accepted values: MIGRATE, TERMINATE
   *
   * @param self::ON_HOST_MAINTENANCE_* $onHostMaintenance
   */
  public function setOnHostMaintenance($onHostMaintenance)
  {
    $this->onHostMaintenance = $onHostMaintenance;
  }
  /**
   * @return self::ON_HOST_MAINTENANCE_*
   */
  public function getOnHostMaintenance()
  {
    return $this->onHostMaintenance;
  }
  /**
   * @param SchedulingOnInstanceStopAction $onInstanceStopAction
   */
  public function setOnInstanceStopAction(SchedulingOnInstanceStopAction $onInstanceStopAction)
  {
    $this->onInstanceStopAction = $onInstanceStopAction;
  }
  /**
   * @return SchedulingOnInstanceStopAction
   */
  public function getOnInstanceStopAction()
  {
    return $this->onInstanceStopAction;
  }
  /**
   * Defines whether the instance is preemptible. This can only be set during
   * instance creation or while the instance isstopped and therefore, in a
   * `TERMINATED` state. SeeInstance Life Cycle for more information on the
   * possible instance states.
   *
   * @param bool $preemptible
   */
  public function setPreemptible($preemptible)
  {
    $this->preemptible = $preemptible;
  }
  /**
   * @return bool
   */
  public function getPreemptible()
  {
    return $this->preemptible;
  }
  /**
   * Specifies the provisioning model of the instance.
   *
   * Accepted values: FLEX_START, RESERVATION_BOUND, SPOT, STANDARD
   *
   * @param self::PROVISIONING_MODEL_* $provisioningModel
   */
  public function setProvisioningModel($provisioningModel)
  {
    $this->provisioningModel = $provisioningModel;
  }
  /**
   * @return self::PROVISIONING_MODEL_*
   */
  public function getProvisioningModel()
  {
    return $this->provisioningModel;
  }
  /**
   * Default is false and there will be 120 seconds between GCE ACPI G2 Soft Off
   * and ACPI G3 Mechanical Off for Standard VMs and 30 seconds for Spot VMs.
   *
   * @param bool $skipGuestOsShutdown
   */
  public function setSkipGuestOsShutdown($skipGuestOsShutdown)
  {
    $this->skipGuestOsShutdown = $skipGuestOsShutdown;
  }
  /**
   * @return bool
   */
  public function getSkipGuestOsShutdown()
  {
    return $this->skipGuestOsShutdown;
  }
  /**
   * Specifies the timestamp, when the instance will be terminated, inRFC3339
   * text format. If specified, the instance termination action will be
   * performed at the termination time.
   *
   * @param string $terminationTime
   */
  public function setTerminationTime($terminationTime)
  {
    $this->terminationTime = $terminationTime;
  }
  /**
   * @return string
   */
  public function getTerminationTime()
  {
    return $this->terminationTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Scheduling::class, 'Google_Service_Compute_Scheduling');
