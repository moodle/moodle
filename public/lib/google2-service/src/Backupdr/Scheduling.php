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

namespace Google\Service\Backupdr;

class Scheduling extends \Google\Collection
{
  /**
   * Default value. This value is unused.
   */
  public const INSTANCE_TERMINATION_ACTION_INSTANCE_TERMINATION_ACTION_UNSPECIFIED = 'INSTANCE_TERMINATION_ACTION_UNSPECIFIED';
  /**
   * Delete the VM.
   */
  public const INSTANCE_TERMINATION_ACTION_DELETE = 'DELETE';
  /**
   * Stop the VM without storing in-memory content. default action.
   */
  public const INSTANCE_TERMINATION_ACTION_STOP = 'STOP';
  /**
   * Default value. This value is unused.
   */
  public const ON_HOST_MAINTENANCE_ON_HOST_MAINTENANCE_UNSPECIFIED = 'ON_HOST_MAINTENANCE_UNSPECIFIED';
  /**
   * Tells Compute Engine to terminate and (optionally) restart the instance
   * away from the maintenance activity.
   */
  public const ON_HOST_MAINTENANCE_TERMINATE = 'TERMINATE';
  /**
   * Default, Allows Compute Engine to automatically migrate instances out of
   * the way of maintenance events.
   */
  public const ON_HOST_MAINTENANCE_MIGRATE = 'MIGRATE';
  /**
   * Default value. This value is not used.
   */
  public const PROVISIONING_MODEL_PROVISIONING_MODEL_UNSPECIFIED = 'PROVISIONING_MODEL_UNSPECIFIED';
  /**
   * Standard provisioning with user controlled runtime, no discounts.
   */
  public const PROVISIONING_MODEL_STANDARD = 'STANDARD';
  /**
   * Heavily discounted, no guaranteed runtime.
   */
  public const PROVISIONING_MODEL_SPOT = 'SPOT';
  protected $collection_key = 'nodeAffinities';
  /**
   * Optional. Specifies whether the instance should be automatically restarted
   * if it is terminated by Compute Engine (not terminated by a user).
   *
   * @var bool
   */
  public $automaticRestart;
  /**
   * Optional. Specifies the termination action for the instance.
   *
   * @var string
   */
  public $instanceTerminationAction;
  protected $localSsdRecoveryTimeoutType = SchedulingDuration::class;
  protected $localSsdRecoveryTimeoutDataType = '';
  /**
   * Optional. The minimum number of virtual CPUs this instance will consume
   * when running on a sole-tenant node.
   *
   * @var int
   */
  public $minNodeCpus;
  protected $nodeAffinitiesType = NodeAffinity::class;
  protected $nodeAffinitiesDataType = 'array';
  /**
   * Optional. Defines the maintenance behavior for this instance.
   *
   * @var string
   */
  public $onHostMaintenance;
  /**
   * Optional. Defines whether the instance is preemptible.
   *
   * @var bool
   */
  public $preemptible;
  /**
   * Optional. Specifies the provisioning model of the instance.
   *
   * @var string
   */
  public $provisioningModel;

  /**
   * Optional. Specifies whether the instance should be automatically restarted
   * if it is terminated by Compute Engine (not terminated by a user).
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
   * Optional. Specifies the termination action for the instance.
   *
   * Accepted values: INSTANCE_TERMINATION_ACTION_UNSPECIFIED, DELETE, STOP
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
   * Optional. Specifies the maximum amount of time a Local Ssd Vm should wait
   * while recovery of the Local Ssd state is attempted. Its value should be in
   * between 0 and 168 hours with hour granularity and the default value being 1
   * hour.
   *
   * @param SchedulingDuration $localSsdRecoveryTimeout
   */
  public function setLocalSsdRecoveryTimeout(SchedulingDuration $localSsdRecoveryTimeout)
  {
    $this->localSsdRecoveryTimeout = $localSsdRecoveryTimeout;
  }
  /**
   * @return SchedulingDuration
   */
  public function getLocalSsdRecoveryTimeout()
  {
    return $this->localSsdRecoveryTimeout;
  }
  /**
   * Optional. The minimum number of virtual CPUs this instance will consume
   * when running on a sole-tenant node.
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
   * Optional. A set of node affinity and anti-affinity configurations.
   * Overrides reservationAffinity.
   *
   * @param NodeAffinity[] $nodeAffinities
   */
  public function setNodeAffinities($nodeAffinities)
  {
    $this->nodeAffinities = $nodeAffinities;
  }
  /**
   * @return NodeAffinity[]
   */
  public function getNodeAffinities()
  {
    return $this->nodeAffinities;
  }
  /**
   * Optional. Defines the maintenance behavior for this instance.
   *
   * Accepted values: ON_HOST_MAINTENANCE_UNSPECIFIED, TERMINATE, MIGRATE
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
   * Optional. Defines whether the instance is preemptible.
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
   * Optional. Specifies the provisioning model of the instance.
   *
   * Accepted values: PROVISIONING_MODEL_UNSPECIFIED, STANDARD, SPOT
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Scheduling::class, 'Google_Service_Backupdr_Scheduling');
