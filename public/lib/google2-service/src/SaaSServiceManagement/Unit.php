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

namespace Google\Service\SaaSServiceManagement;

class Unit extends \Google\Collection
{
  public const MANAGEMENT_MODE_MANAGEMENT_MODE_UNSPECIFIED = 'MANAGEMENT_MODE_UNSPECIFIED';
  /**
   * Unit's lifecycle is managed by the user.
   */
  public const MANAGEMENT_MODE_MANAGEMENT_MODE_USER = 'MANAGEMENT_MODE_USER';
  /**
   * The system will decide when to deprovision and delete the unit. User still
   * can deprovision or delete the unit manually.
   */
  public const MANAGEMENT_MODE_MANAGEMENT_MODE_SYSTEM = 'MANAGEMENT_MODE_SYSTEM';
  /**
   * Unspecified state.
   */
  public const STATE_UNIT_STATE_UNSPECIFIED = 'UNIT_STATE_UNSPECIFIED';
  /**
   * Unit is not provisioned.
   */
  public const STATE_UNIT_STATE_NOT_PROVISIONED = 'UNIT_STATE_NOT_PROVISIONED';
  /**
   * Unit is being provisioned.
   */
  public const STATE_UNIT_STATE_PROVISIONING = 'UNIT_STATE_PROVISIONING';
  /**
   * Unit is being updated. This is typically when a unit is being upgraded to a
   * new release or some of the input variables on the Unit is being changed.
   * Certain kinds of updates may cause the Unit to become unusable while the
   * update is in progress.
   */
  public const STATE_UNIT_STATE_UPDATING = 'UNIT_STATE_UPDATING';
  /**
   * Unit is being deleted.
   */
  public const STATE_UNIT_STATE_DEPROVISIONING = 'UNIT_STATE_DEPROVISIONING';
  /**
   * Unit has been provisioned and is ready for use
   */
  public const STATE_UNIT_STATE_READY = 'UNIT_STATE_READY';
  /**
   * Unit has error, when it is not ready and some error operation
   */
  public const STATE_UNIT_STATE_ERROR = 'UNIT_STATE_ERROR';
  public const SYSTEM_MANAGED_STATE_SYSTEM_MANAGED_STATE_UNSPECIFIED = 'SYSTEM_MANAGED_STATE_UNSPECIFIED';
  /**
   * Unit has dependents attached.
   */
  public const SYSTEM_MANAGED_STATE_SYSTEM_MANAGED_STATE_ACTIVE = 'SYSTEM_MANAGED_STATE_ACTIVE';
  /**
   * Unit has no dependencies attached, but attachment is allowed.
   */
  public const SYSTEM_MANAGED_STATE_SYSTEM_MANAGED_STATE_INACTIVE = 'SYSTEM_MANAGED_STATE_INACTIVE';
  /**
   * Unit has no dependencies attached, and attachment is not allowed.
   */
  public const SYSTEM_MANAGED_STATE_SYSTEM_MANAGED_STATE_DECOMMISSIONED = 'SYSTEM_MANAGED_STATE_DECOMMISSIONED';
  protected $collection_key = 'scheduledOperations';
  /**
   * Optional. Annotations is an unstructured key-value map stored with a
   * resource that may be set by external tools to store and retrieve arbitrary
   * metadata. They are not queryable and should be preserved when modifying
   * objects. More info: https://kubernetes.io/docs/user-guide/annotations
   *
   * @var string[]
   */
  public $annotations;
  protected $conditionsType = UnitCondition::class;
  protected $conditionsDataType = 'array';
  /**
   * Output only. The timestamp when the resource was created.
   *
   * @var string
   */
  public $createTime;
  protected $dependenciesType = UnitDependency::class;
  protected $dependenciesDataType = 'array';
  protected $dependentsType = UnitDependency::class;
  protected $dependentsDataType = 'array';
  /**
   * Output only. An opaque value that uniquely identifies a version or
   * generation of a resource. It can be used to confirm that the client and
   * server agree on the ordering of a resource being written.
   *
   * @var string
   */
  public $etag;
  protected $inputVariablesType = UnitVariable::class;
  protected $inputVariablesDataType = 'array';
  /**
   * Optional. The labels on the resource, which can be used for categorization.
   * similar to Kubernetes resource labels.
   *
   * @var string[]
   */
  public $labels;
  protected $maintenanceType = MaintenanceSettings::class;
  protected $maintenanceDataType = '';
  /**
   * Optional. Immutable. Indicates whether the Unit life cycle is controlled by
   * the user or by the system. Immutable once created.
   *
   * @var string
   */
  public $managementMode;
  /**
   * Identifier. The resource name (full URI of the resource) following the
   * standard naming scheme:
   * "projects/{project}/locations/{location}/units/{unit}"
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Output only. List of concurrent UnitOperations that are operating
   * on this Unit.
   *
   * @var string[]
   */
  public $ongoingOperations;
  protected $outputVariablesType = UnitVariable::class;
  protected $outputVariablesDataType = 'array';
  /**
   * Optional. Output only. List of pending (wait to be executed) UnitOperations
   * for this unit.
   *
   * @var string[]
   */
  public $pendingOperations;
  /**
   * Optional. Output only. The current Release object for this Unit.
   *
   * @var string
   */
  public $release;
  /**
   * Optional. Output only. List of scheduled UnitOperations for this unit.
   *
   * @var string[]
   */
  public $scheduledOperations;
  /**
   * Optional. Output only. Current lifecycle state of the resource (e.g. if
   * it's being created or ready to use).
   *
   * @var string
   */
  public $state;
  /**
   * Optional. Output only. If set, indicates the time when the system will
   * start removing the unit.
   *
   * @var string
   */
  public $systemCleanupAt;
  /**
   * Optional. Output only. Indicates the system managed state of the unit.
   *
   * @var string
   */
  public $systemManagedState;
  /**
   * Optional. Reference to the Saas Tenant resource this unit belongs to. This
   * for example informs the maintenance policies to use for scheduling future
   * updates on a unit. (optional and immutable once created)
   *
   * @var string
   */
  public $tenant;
  /**
   * Output only. The unique identifier of the resource. UID is unique in the
   * time and space for this resource within the scope of the service. It is
   * typically generated by the server on successful creation of a resource and
   * must not be changed. UID is used to uniquely identify resources with
   * resource name reuses. This should be a UUID4.
   *
   * @var string
   */
  public $uid;
  /**
   * Optional. Reference to the UnitKind this Unit belongs to. Immutable once
   * set.
   *
   * @var string
   */
  public $unitKind;
  /**
   * Output only. The timestamp when the resource was last updated. Any change
   * to the resource made by users must refresh this value. Changes to a
   * resource made by the service should refresh this value.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Annotations is an unstructured key-value map stored with a
   * resource that may be set by external tools to store and retrieve arbitrary
   * metadata. They are not queryable and should be preserved when modifying
   * objects. More info: https://kubernetes.io/docs/user-guide/annotations
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Optional. Output only. A set of conditions which indicate the various
   * conditions this resource can have.
   *
   * @param UnitCondition[] $conditions
   */
  public function setConditions($conditions)
  {
    $this->conditions = $conditions;
  }
  /**
   * @return UnitCondition[]
   */
  public function getConditions()
  {
    return $this->conditions;
  }
  /**
   * Output only. The timestamp when the resource was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. Output only. Set of dependencies for this unit. Maximum 10.
   *
   * @param UnitDependency[] $dependencies
   */
  public function setDependencies($dependencies)
  {
    $this->dependencies = $dependencies;
  }
  /**
   * @return UnitDependency[]
   */
  public function getDependencies()
  {
    return $this->dependencies;
  }
  /**
   * Optional. Output only. List of Units that depend on this unit. Unit can
   * only be deprovisioned if this list is empty. Maximum 1000.
   *
   * @param UnitDependency[] $dependents
   */
  public function setDependents($dependents)
  {
    $this->dependents = $dependents;
  }
  /**
   * @return UnitDependency[]
   */
  public function getDependents()
  {
    return $this->dependents;
  }
  /**
   * Output only. An opaque value that uniquely identifies a version or
   * generation of a resource. It can be used to confirm that the client and
   * server agree on the ordering of a resource being written.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. Output only. Indicates the current input variables deployed by
   * the unit
   *
   * @param UnitVariable[] $inputVariables
   */
  public function setInputVariables($inputVariables)
  {
    $this->inputVariables = $inputVariables;
  }
  /**
   * @return UnitVariable[]
   */
  public function getInputVariables()
  {
    return $this->inputVariables;
  }
  /**
   * Optional. The labels on the resource, which can be used for categorization.
   * similar to Kubernetes resource labels.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Optional. Captures requested directives for performing future maintenance
   * on the unit. This includes a request for the unit to skip maintenance for a
   * period of time and remain pinned to its current release as well as controls
   * for postponing maintenance scheduled in future.
   *
   * @param MaintenanceSettings $maintenance
   */
  public function setMaintenance(MaintenanceSettings $maintenance)
  {
    $this->maintenance = $maintenance;
  }
  /**
   * @return MaintenanceSettings
   */
  public function getMaintenance()
  {
    return $this->maintenance;
  }
  /**
   * Optional. Immutable. Indicates whether the Unit life cycle is controlled by
   * the user or by the system. Immutable once created.
   *
   * Accepted values: MANAGEMENT_MODE_UNSPECIFIED, MANAGEMENT_MODE_USER,
   * MANAGEMENT_MODE_SYSTEM
   *
   * @param self::MANAGEMENT_MODE_* $managementMode
   */
  public function setManagementMode($managementMode)
  {
    $this->managementMode = $managementMode;
  }
  /**
   * @return self::MANAGEMENT_MODE_*
   */
  public function getManagementMode()
  {
    return $this->managementMode;
  }
  /**
   * Identifier. The resource name (full URI of the resource) following the
   * standard naming scheme:
   * "projects/{project}/locations/{location}/units/{unit}"
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
   * Optional. Output only. List of concurrent UnitOperations that are operating
   * on this Unit.
   *
   * @param string[] $ongoingOperations
   */
  public function setOngoingOperations($ongoingOperations)
  {
    $this->ongoingOperations = $ongoingOperations;
  }
  /**
   * @return string[]
   */
  public function getOngoingOperations()
  {
    return $this->ongoingOperations;
  }
  /**
   * Optional. Output only. Set of key/value pairs corresponding to output
   * variables from execution of actuation templates. The variables are declared
   * in actuation configs (e.g in helm chart or terraform) and the values are
   * fetched and returned by the actuation engine upon completion of execution.
   *
   * @param UnitVariable[] $outputVariables
   */
  public function setOutputVariables($outputVariables)
  {
    $this->outputVariables = $outputVariables;
  }
  /**
   * @return UnitVariable[]
   */
  public function getOutputVariables()
  {
    return $this->outputVariables;
  }
  /**
   * Optional. Output only. List of pending (wait to be executed) UnitOperations
   * for this unit.
   *
   * @param string[] $pendingOperations
   */
  public function setPendingOperations($pendingOperations)
  {
    $this->pendingOperations = $pendingOperations;
  }
  /**
   * @return string[]
   */
  public function getPendingOperations()
  {
    return $this->pendingOperations;
  }
  /**
   * Optional. Output only. The current Release object for this Unit.
   *
   * @param string $release
   */
  public function setRelease($release)
  {
    $this->release = $release;
  }
  /**
   * @return string
   */
  public function getRelease()
  {
    return $this->release;
  }
  /**
   * Optional. Output only. List of scheduled UnitOperations for this unit.
   *
   * @param string[] $scheduledOperations
   */
  public function setScheduledOperations($scheduledOperations)
  {
    $this->scheduledOperations = $scheduledOperations;
  }
  /**
   * @return string[]
   */
  public function getScheduledOperations()
  {
    return $this->scheduledOperations;
  }
  /**
   * Optional. Output only. Current lifecycle state of the resource (e.g. if
   * it's being created or ready to use).
   *
   * Accepted values: UNIT_STATE_UNSPECIFIED, UNIT_STATE_NOT_PROVISIONED,
   * UNIT_STATE_PROVISIONING, UNIT_STATE_UPDATING, UNIT_STATE_DEPROVISIONING,
   * UNIT_STATE_READY, UNIT_STATE_ERROR
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Optional. Output only. If set, indicates the time when the system will
   * start removing the unit.
   *
   * @param string $systemCleanupAt
   */
  public function setSystemCleanupAt($systemCleanupAt)
  {
    $this->systemCleanupAt = $systemCleanupAt;
  }
  /**
   * @return string
   */
  public function getSystemCleanupAt()
  {
    return $this->systemCleanupAt;
  }
  /**
   * Optional. Output only. Indicates the system managed state of the unit.
   *
   * Accepted values: SYSTEM_MANAGED_STATE_UNSPECIFIED,
   * SYSTEM_MANAGED_STATE_ACTIVE, SYSTEM_MANAGED_STATE_INACTIVE,
   * SYSTEM_MANAGED_STATE_DECOMMISSIONED
   *
   * @param self::SYSTEM_MANAGED_STATE_* $systemManagedState
   */
  public function setSystemManagedState($systemManagedState)
  {
    $this->systemManagedState = $systemManagedState;
  }
  /**
   * @return self::SYSTEM_MANAGED_STATE_*
   */
  public function getSystemManagedState()
  {
    return $this->systemManagedState;
  }
  /**
   * Optional. Reference to the Saas Tenant resource this unit belongs to. This
   * for example informs the maintenance policies to use for scheduling future
   * updates on a unit. (optional and immutable once created)
   *
   * @param string $tenant
   */
  public function setTenant($tenant)
  {
    $this->tenant = $tenant;
  }
  /**
   * @return string
   */
  public function getTenant()
  {
    return $this->tenant;
  }
  /**
   * Output only. The unique identifier of the resource. UID is unique in the
   * time and space for this resource within the scope of the service. It is
   * typically generated by the server on successful creation of a resource and
   * must not be changed. UID is used to uniquely identify resources with
   * resource name reuses. This should be a UUID4.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Optional. Reference to the UnitKind this Unit belongs to. Immutable once
   * set.
   *
   * @param string $unitKind
   */
  public function setUnitKind($unitKind)
  {
    $this->unitKind = $unitKind;
  }
  /**
   * @return string
   */
  public function getUnitKind()
  {
    return $this->unitKind;
  }
  /**
   * Output only. The timestamp when the resource was last updated. Any change
   * to the resource made by users must refresh this value. Changes to a
   * resource made by the service should refresh this value.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Unit::class, 'Google_Service_SaaSServiceManagement_Unit');
