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

class UnitOperation extends \Google\Collection
{
  /**
   * Unit operation error category is unspecified
   */
  public const ERROR_CATEGORY_UNIT_OPERATION_ERROR_CATEGORY_UNSPECIFIED = 'UNIT_OPERATION_ERROR_CATEGORY_UNSPECIFIED';
  /**
   * Unit operation error category is not applicable, or it is not an error
   */
  public const ERROR_CATEGORY_NOT_APPLICABLE = 'NOT_APPLICABLE';
  /**
   * Unit operation error category is fatal
   */
  public const ERROR_CATEGORY_FATAL = 'FATAL';
  /**
   * Unit operation error category is retriable
   */
  public const ERROR_CATEGORY_RETRIABLE = 'RETRIABLE';
  /**
   * Unit operation error category is ignorable
   */
  public const ERROR_CATEGORY_IGNORABLE = 'IGNORABLE';
  /**
   * Unit operation error category is standard, counts towards Rollout error
   * budget
   */
  public const ERROR_CATEGORY_STANDARD = 'STANDARD';
  public const STATE_UNIT_OPERATION_STATE_UNKNOWN = 'UNIT_OPERATION_STATE_UNKNOWN';
  /**
   * Unit operation is accepted but not ready to run.
   */
  public const STATE_UNIT_OPERATION_STATE_PENDING = 'UNIT_OPERATION_STATE_PENDING';
  /**
   * Unit operation is accepted and scheduled.
   */
  public const STATE_UNIT_OPERATION_STATE_SCHEDULED = 'UNIT_OPERATION_STATE_SCHEDULED';
  /**
   * Unit operation is running.
   */
  public const STATE_UNIT_OPERATION_STATE_RUNNING = 'UNIT_OPERATION_STATE_RUNNING';
  /**
   * Unit operation has completed successfully.
   */
  public const STATE_UNIT_OPERATION_STATE_SUCCEEDED = 'UNIT_OPERATION_STATE_SUCCEEDED';
  /**
   * Unit operation has failed.
   */
  public const STATE_UNIT_OPERATION_STATE_FAILED = 'UNIT_OPERATION_STATE_FAILED';
  /**
   * Unit operation was cancelled.
   */
  public const STATE_UNIT_OPERATION_STATE_CANCELLED = 'UNIT_OPERATION_STATE_CANCELLED';
  protected $collection_key = 'conditions';
  /**
   * Optional. Annotations is an unstructured key-value map stored with a
   * resource that may be set by external tools to store and retrieve arbitrary
   * metadata. They are not queryable and should be preserved when modifying
   * objects. More info: https://kubernetes.io/docs/user-guide/annotations
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Optional. When true, attempt to cancel the operation. Cancellation may fail
   * if the operation is already executing. (Optional)
   *
   * @var bool
   */
  public $cancel;
  protected $conditionsType = UnitOperationCondition::class;
  protected $conditionsDataType = 'array';
  /**
   * Output only. The timestamp when the resource was created.
   *
   * @var string
   */
  public $createTime;
  protected $deprovisionType = Deprovision::class;
  protected $deprovisionDataType = '';
  /**
   * Optional. Output only. The engine state for on-going deployment engine
   * operation(s). This field is opaque for external usage.
   *
   * @var string
   */
  public $engineState;
  /**
   * Optional. Output only. UnitOperationErrorCategory describe the error
   * category.
   *
   * @var string
   */
  public $errorCategory;
  /**
   * Output only. An opaque value that uniquely identifies a version or
   * generation of a resource. It can be used to confirm that the client and
   * server agree on the ordering of a resource being written.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. The labels on the resource, which can be used for categorization.
   * similar to Kubernetes resource labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The resource name (full URI of the resource) following the
   * standard naming scheme:
   * "projects/{project}/locations/{location}/unitOperations/{unitOperation}"
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Reference to parent resource: UnitOperation. If an operation
   * needs to create other operations as part of its workflow, each of the child
   * operations should have this field set to the parent. This can be used for
   * tracing. (Optional)
   *
   * @var string
   */
  public $parentUnitOperation;
  protected $provisionType = Provision::class;
  protected $provisionDataType = '';
  /**
   * Optional. Specifies which rollout created this Unit Operation. This cannot
   * be modified and is used for filtering purposes only. If a dependent unit
   * and unit operation are created as part of another unit operation, they will
   * use the same rolloutId.
   *
   * @var string
   */
  public $rollout;
  protected $scheduleType = Schedule::class;
  protected $scheduleDataType = '';
  /**
   * Optional. Output only. UnitOperationState describes the current state of
   * the unit operation.
   *
   * @var string
   */
  public $state;
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
   * Required. Immutable. The Unit a given UnitOperation will act upon.
   *
   * @var string
   */
  public $unit;
  /**
   * Output only. The timestamp when the resource was last updated. Any change
   * to the resource made by users must refresh this value. Changes to a
   * resource made by the service should refresh this value.
   *
   * @var string
   */
  public $updateTime;
  protected $upgradeType = Upgrade::class;
  protected $upgradeDataType = '';

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
   * Optional. When true, attempt to cancel the operation. Cancellation may fail
   * if the operation is already executing. (Optional)
   *
   * @param bool $cancel
   */
  public function setCancel($cancel)
  {
    $this->cancel = $cancel;
  }
  /**
   * @return bool
   */
  public function getCancel()
  {
    return $this->cancel;
  }
  /**
   * Optional. Output only. A set of conditions which indicate the various
   * conditions this resource can have.
   *
   * @param UnitOperationCondition[] $conditions
   */
  public function setConditions($conditions)
  {
    $this->conditions = $conditions;
  }
  /**
   * @return UnitOperationCondition[]
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
   * @param Deprovision $deprovision
   */
  public function setDeprovision(Deprovision $deprovision)
  {
    $this->deprovision = $deprovision;
  }
  /**
   * @return Deprovision
   */
  public function getDeprovision()
  {
    return $this->deprovision;
  }
  /**
   * Optional. Output only. The engine state for on-going deployment engine
   * operation(s). This field is opaque for external usage.
   *
   * @param string $engineState
   */
  public function setEngineState($engineState)
  {
    $this->engineState = $engineState;
  }
  /**
   * @return string
   */
  public function getEngineState()
  {
    return $this->engineState;
  }
  /**
   * Optional. Output only. UnitOperationErrorCategory describe the error
   * category.
   *
   * Accepted values: UNIT_OPERATION_ERROR_CATEGORY_UNSPECIFIED, NOT_APPLICABLE,
   * FATAL, RETRIABLE, IGNORABLE, STANDARD
   *
   * @param self::ERROR_CATEGORY_* $errorCategory
   */
  public function setErrorCategory($errorCategory)
  {
    $this->errorCategory = $errorCategory;
  }
  /**
   * @return self::ERROR_CATEGORY_*
   */
  public function getErrorCategory()
  {
    return $this->errorCategory;
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
   * Identifier. The resource name (full URI of the resource) following the
   * standard naming scheme:
   * "projects/{project}/locations/{location}/unitOperations/{unitOperation}"
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
   * Optional. Reference to parent resource: UnitOperation. If an operation
   * needs to create other operations as part of its workflow, each of the child
   * operations should have this field set to the parent. This can be used for
   * tracing. (Optional)
   *
   * @param string $parentUnitOperation
   */
  public function setParentUnitOperation($parentUnitOperation)
  {
    $this->parentUnitOperation = $parentUnitOperation;
  }
  /**
   * @return string
   */
  public function getParentUnitOperation()
  {
    return $this->parentUnitOperation;
  }
  /**
   * @param Provision $provision
   */
  public function setProvision(Provision $provision)
  {
    $this->provision = $provision;
  }
  /**
   * @return Provision
   */
  public function getProvision()
  {
    return $this->provision;
  }
  /**
   * Optional. Specifies which rollout created this Unit Operation. This cannot
   * be modified and is used for filtering purposes only. If a dependent unit
   * and unit operation are created as part of another unit operation, they will
   * use the same rolloutId.
   *
   * @param string $rollout
   */
  public function setRollout($rollout)
  {
    $this->rollout = $rollout;
  }
  /**
   * @return string
   */
  public function getRollout()
  {
    return $this->rollout;
  }
  /**
   * Optional. When to schedule this operation.
   *
   * @param Schedule $schedule
   */
  public function setSchedule(Schedule $schedule)
  {
    $this->schedule = $schedule;
  }
  /**
   * @return Schedule
   */
  public function getSchedule()
  {
    return $this->schedule;
  }
  /**
   * Optional. Output only. UnitOperationState describes the current state of
   * the unit operation.
   *
   * Accepted values: UNIT_OPERATION_STATE_UNKNOWN,
   * UNIT_OPERATION_STATE_PENDING, UNIT_OPERATION_STATE_SCHEDULED,
   * UNIT_OPERATION_STATE_RUNNING, UNIT_OPERATION_STATE_SUCCEEDED,
   * UNIT_OPERATION_STATE_FAILED, UNIT_OPERATION_STATE_CANCELLED
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
   * Required. Immutable. The Unit a given UnitOperation will act upon.
   *
   * @param string $unit
   */
  public function setUnit($unit)
  {
    $this->unit = $unit;
  }
  /**
   * @return string
   */
  public function getUnit()
  {
    return $this->unit;
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
  /**
   * @param Upgrade $upgrade
   */
  public function setUpgrade(Upgrade $upgrade)
  {
    $this->upgrade = $upgrade;
  }
  /**
   * @return Upgrade
   */
  public function getUpgrade()
  {
    return $this->upgrade;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UnitOperation::class, 'Google_Service_SaaSServiceManagement_UnitOperation');
