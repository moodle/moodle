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

class ManagedInstance extends \Google\Collection
{
  /**
   * The managed instance group is abandoning this instance. The instance will
   * be removed from the instance group and from any target pools that are
   * associated with this group.
   */
  public const CURRENT_ACTION_ABANDONING = 'ABANDONING';
  /**
   * The managed instance group is creating this instance. If the group fails to
   * create this instance, it will try again until it is successful.
   */
  public const CURRENT_ACTION_CREATING = 'CREATING';
  /**
   * The managed instance group is attempting to create this instance only once.
   * If the group fails to create this instance, it does not try again and the
   * group's targetSize value is decreased.
   */
  public const CURRENT_ACTION_CREATING_WITHOUT_RETRIES = 'CREATING_WITHOUT_RETRIES';
  /**
   * The managed instance group is permanently deleting this instance.
   */
  public const CURRENT_ACTION_DELETING = 'DELETING';
  /**
   * The managed instance group has not scheduled any actions for this instance.
   */
  public const CURRENT_ACTION_NONE = 'NONE';
  /**
   * The managed instance group is recreating this instance.
   */
  public const CURRENT_ACTION_RECREATING = 'RECREATING';
  /**
   * The managed instance group is applying configuration changes to the
   * instance without stopping it. For example, the group can update the target
   * pool list for an instance without stopping that instance.
   */
  public const CURRENT_ACTION_REFRESHING = 'REFRESHING';
  /**
   * The managed instance group is restarting this instance.
   */
  public const CURRENT_ACTION_RESTARTING = 'RESTARTING';
  /**
   * The managed instance group is resuming this instance.
   */
  public const CURRENT_ACTION_RESUMING = 'RESUMING';
  /**
   * The managed instance group is starting this instance.
   */
  public const CURRENT_ACTION_STARTING = 'STARTING';
  /**
   * The managed instance group is stopping this instance.
   */
  public const CURRENT_ACTION_STOPPING = 'STOPPING';
  /**
   * The managed instance group is suspending this instance.
   */
  public const CURRENT_ACTION_SUSPENDING = 'SUSPENDING';
  /**
   * The managed instance group is verifying this already created instance.
   * Verification happens every time the instance is (re)created or restarted
   * and consists of:  1. Waiting until health check specified as part of this
   * managed instance     group's autohealing policy reports HEALTHY.     Note:
   * Applies only if autohealing policy has a health check specified  2. Waiting
   * for addition verification steps performed as post-instance     creation
   * (subject to future extensions).
   */
  public const CURRENT_ACTION_VERIFYING = 'VERIFYING';
  /**
   * The instance is halted and we are performing tear down tasks like network
   * deprogramming, releasing quota, IP, tearing down disks etc.
   */
  public const INSTANCE_STATUS_DEPROVISIONING = 'DEPROVISIONING';
  /**
   * For Flex Start provisioning instance is waiting for available capacity from
   * Dynamic Workload Scheduler (DWS).
   */
  public const INSTANCE_STATUS_PENDING = 'PENDING';
  /**
   * Resources are being allocated for the instance.
   */
  public const INSTANCE_STATUS_PROVISIONING = 'PROVISIONING';
  /**
   * The instance is in repair.
   */
  public const INSTANCE_STATUS_REPAIRING = 'REPAIRING';
  /**
   * The instance is running.
   */
  public const INSTANCE_STATUS_RUNNING = 'RUNNING';
  /**
   * All required resources have been allocated and the instance is being
   * started.
   */
  public const INSTANCE_STATUS_STAGING = 'STAGING';
  /**
   * The instance has stopped successfully.
   */
  public const INSTANCE_STATUS_STOPPED = 'STOPPED';
  /**
   * The instance is currently stopping (either being deleted or killed).
   */
  public const INSTANCE_STATUS_STOPPING = 'STOPPING';
  /**
   * The instance has suspended.
   */
  public const INSTANCE_STATUS_SUSPENDED = 'SUSPENDED';
  /**
   * The instance is suspending.
   */
  public const INSTANCE_STATUS_SUSPENDING = 'SUSPENDING';
  /**
   * The instance has stopped (either by explicit action or underlying failure).
   */
  public const INSTANCE_STATUS_TERMINATED = 'TERMINATED';
  protected $collection_key = 'instanceHealth';
  /**
   * Output only. [Output Only] The current action that the managed instance
   * group has scheduled for the instance. Possible values:        - NONE The
   * instance is running, and the managed    instance group does not have any
   * scheduled actions for this instance.    - CREATING The managed instance
   * group is creating this    instance. If the group fails to create this
   * instance, it will try again    until it is successful.    -
   * CREATING_WITHOUT_RETRIES The managed instance group    is attempting to
   * create this instance only once. If the group fails    to create this
   * instance, it does not try again and the group'stargetSize value is
   * decreased instead.    - RECREATING The managed instance group is recreating
   * this instance.    - DELETING The managed instance group is permanently
   * deleting this instance.    - ABANDONING The managed instance group is
   * abandoning    this instance. The instance will be removed from the instance
   * group    and from any target pools that are associated with this group.
   * - RESTARTING The managed instance group is restarting    the instance.    -
   * REFRESHING The managed instance group is applying    configuration changes
   * to the instance without stopping it. For example,    the group can update
   * the target pool list for an instance without    stopping that instance.
   * - VERIFYING The managed instance group has created the    instance and it
   * is in the process of being verified.
   *
   * @var string
   */
  public $currentAction;
  /**
   * Output only. [Output only] The unique identifier for this resource. This
   * field is empty when instance does not exist.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] The URL of the instance. The URL can exist even
   * if the instance has not yet been created.
   *
   * @var string
   */
  public $instance;
  protected $instanceHealthType = ManagedInstanceInstanceHealth::class;
  protected $instanceHealthDataType = 'array';
  /**
   * Output only. [Output Only] The status of the instance. This field is empty
   * when the instance does not exist.
   *
   * @var string
   */
  public $instanceStatus;
  protected $lastAttemptType = ManagedInstanceLastAttempt::class;
  protected $lastAttemptDataType = '';
  /**
   * Output only. [Output Only] The name of the instance. The name always exists
   * even if the instance has not yet been created.
   *
   * @var string
   */
  public $name;
  protected $preservedStateFromConfigType = PreservedState::class;
  protected $preservedStateFromConfigDataType = '';
  protected $preservedStateFromPolicyType = PreservedState::class;
  protected $preservedStateFromPolicyDataType = '';
  protected $propertiesFromFlexibilityPolicyType = ManagedInstancePropertiesFromFlexibilityPolicy::class;
  protected $propertiesFromFlexibilityPolicyDataType = '';
  protected $versionType = ManagedInstanceVersion::class;
  protected $versionDataType = '';

  /**
   * Output only. [Output Only] The current action that the managed instance
   * group has scheduled for the instance. Possible values:        - NONE The
   * instance is running, and the managed    instance group does not have any
   * scheduled actions for this instance.    - CREATING The managed instance
   * group is creating this    instance. If the group fails to create this
   * instance, it will try again    until it is successful.    -
   * CREATING_WITHOUT_RETRIES The managed instance group    is attempting to
   * create this instance only once. If the group fails    to create this
   * instance, it does not try again and the group'stargetSize value is
   * decreased instead.    - RECREATING The managed instance group is recreating
   * this instance.    - DELETING The managed instance group is permanently
   * deleting this instance.    - ABANDONING The managed instance group is
   * abandoning    this instance. The instance will be removed from the instance
   * group    and from any target pools that are associated with this group.
   * - RESTARTING The managed instance group is restarting    the instance.    -
   * REFRESHING The managed instance group is applying    configuration changes
   * to the instance without stopping it. For example,    the group can update
   * the target pool list for an instance without    stopping that instance.
   * - VERIFYING The managed instance group has created the    instance and it
   * is in the process of being verified.
   *
   * Accepted values: ABANDONING, CREATING, CREATING_WITHOUT_RETRIES, DELETING,
   * NONE, RECREATING, REFRESHING, RESTARTING, RESUMING, STARTING, STOPPING,
   * SUSPENDING, VERIFYING
   *
   * @param self::CURRENT_ACTION_* $currentAction
   */
  public function setCurrentAction($currentAction)
  {
    $this->currentAction = $currentAction;
  }
  /**
   * @return self::CURRENT_ACTION_*
   */
  public function getCurrentAction()
  {
    return $this->currentAction;
  }
  /**
   * Output only. [Output only] The unique identifier for this resource. This
   * field is empty when instance does not exist.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. [Output Only] The URL of the instance. The URL can exist even
   * if the instance has not yet been created.
   *
   * @param string $instance
   */
  public function setInstance($instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return string
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * Output only. [Output Only] Health state of the instance per health-check.
   *
   * @param ManagedInstanceInstanceHealth[] $instanceHealth
   */
  public function setInstanceHealth($instanceHealth)
  {
    $this->instanceHealth = $instanceHealth;
  }
  /**
   * @return ManagedInstanceInstanceHealth[]
   */
  public function getInstanceHealth()
  {
    return $this->instanceHealth;
  }
  /**
   * Output only. [Output Only] The status of the instance. This field is empty
   * when the instance does not exist.
   *
   * Accepted values: DEPROVISIONING, PENDING, PROVISIONING, REPAIRING, RUNNING,
   * STAGING, STOPPED, STOPPING, SUSPENDED, SUSPENDING, TERMINATED
   *
   * @param self::INSTANCE_STATUS_* $instanceStatus
   */
  public function setInstanceStatus($instanceStatus)
  {
    $this->instanceStatus = $instanceStatus;
  }
  /**
   * @return self::INSTANCE_STATUS_*
   */
  public function getInstanceStatus()
  {
    return $this->instanceStatus;
  }
  /**
   * Output only. [Output Only] Information about the last attempt to create or
   * delete the instance.
   *
   * @param ManagedInstanceLastAttempt $lastAttempt
   */
  public function setLastAttempt(ManagedInstanceLastAttempt $lastAttempt)
  {
    $this->lastAttempt = $lastAttempt;
  }
  /**
   * @return ManagedInstanceLastAttempt
   */
  public function getLastAttempt()
  {
    return $this->lastAttempt;
  }
  /**
   * Output only. [Output Only] The name of the instance. The name always exists
   * even if the instance has not yet been created.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. [Output Only] Preserved state applied from per-instance config
   * for this instance.
   *
   * @param PreservedState $preservedStateFromConfig
   */
  public function setPreservedStateFromConfig(PreservedState $preservedStateFromConfig)
  {
    $this->preservedStateFromConfig = $preservedStateFromConfig;
  }
  /**
   * @return PreservedState
   */
  public function getPreservedStateFromConfig()
  {
    return $this->preservedStateFromConfig;
  }
  /**
   * Output only. [Output Only] Preserved state generated based on stateful
   * policy for this instance.
   *
   * @param PreservedState $preservedStateFromPolicy
   */
  public function setPreservedStateFromPolicy(PreservedState $preservedStateFromPolicy)
  {
    $this->preservedStateFromPolicy = $preservedStateFromPolicy;
  }
  /**
   * @return PreservedState
   */
  public function getPreservedStateFromPolicy()
  {
    return $this->preservedStateFromPolicy;
  }
  /**
   * Output only. [Output Only] Instance properties selected for this instance
   * resulting from InstanceFlexibilityPolicy.
   *
   * @param ManagedInstancePropertiesFromFlexibilityPolicy $propertiesFromFlexibilityPolicy
   */
  public function setPropertiesFromFlexibilityPolicy(ManagedInstancePropertiesFromFlexibilityPolicy $propertiesFromFlexibilityPolicy)
  {
    $this->propertiesFromFlexibilityPolicy = $propertiesFromFlexibilityPolicy;
  }
  /**
   * @return ManagedInstancePropertiesFromFlexibilityPolicy
   */
  public function getPropertiesFromFlexibilityPolicy()
  {
    return $this->propertiesFromFlexibilityPolicy;
  }
  /**
   * Output only. [Output Only] Intended version of this instance.
   *
   * @param ManagedInstanceVersion $version
   */
  public function setVersion(ManagedInstanceVersion $version)
  {
    $this->version = $version;
  }
  /**
   * @return ManagedInstanceVersion
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ManagedInstance::class, 'Google_Service_Compute_ManagedInstance');
