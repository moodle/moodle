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

namespace Google\Service\AppHub;

class Workload extends \Google\Model
{
  /**
   * Unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The Workload is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The Workload is ready.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The Workload is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The underlying compute resources have been deleted.
   */
  public const STATE_DETACHED = 'DETACHED';
  protected $attributesType = Attributes::class;
  protected $attributesDataType = '';
  /**
   * Output only. Create time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. User-defined description of a Workload. Can have a maximum length
   * of 2048 characters.
   *
   * @var string
   */
  public $description;
  /**
   * Required. Immutable. The resource name of the original discovered workload.
   *
   * @var string
   */
  public $discoveredWorkload;
  /**
   * Optional. User-defined name for the Workload. Can have a maximum length of
   * 63 characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * Identifier. The resource name of the Workload. Format: `"projects/{host-
   * project-id}/locations/{location}/applications/{application-
   * id}/workloads/{workload-id}"`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Workload state.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. A universally unique identifier (UUID) for the `Workload` in
   * the UUID4 format.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Update time.
   *
   * @var string
   */
  public $updateTime;
  protected $workloadPropertiesType = WorkloadProperties::class;
  protected $workloadPropertiesDataType = '';
  protected $workloadReferenceType = WorkloadReference::class;
  protected $workloadReferenceDataType = '';

  /**
   * Optional. Consumer provided attributes.
   *
   * @param Attributes $attributes
   */
  public function setAttributes(Attributes $attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return Attributes
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * Output only. Create time.
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
   * Optional. User-defined description of a Workload. Can have a maximum length
   * of 2048 characters.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. Immutable. The resource name of the original discovered workload.
   *
   * @param string $discoveredWorkload
   */
  public function setDiscoveredWorkload($discoveredWorkload)
  {
    $this->discoveredWorkload = $discoveredWorkload;
  }
  /**
   * @return string
   */
  public function getDiscoveredWorkload()
  {
    return $this->discoveredWorkload;
  }
  /**
   * Optional. User-defined name for the Workload. Can have a maximum length of
   * 63 characters.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Identifier. The resource name of the Workload. Format: `"projects/{host-
   * project-id}/locations/{location}/applications/{application-
   * id}/workloads/{workload-id}"`
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
   * Output only. Workload state.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING, DETACHED
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
   * Output only. A universally unique identifier (UUID) for the `Workload` in
   * the UUID4 format.
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
   * Output only. Update time.
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
   * Output only. Properties of an underlying compute resource represented by
   * the Workload. These are immutable.
   *
   * @param WorkloadProperties $workloadProperties
   */
  public function setWorkloadProperties(WorkloadProperties $workloadProperties)
  {
    $this->workloadProperties = $workloadProperties;
  }
  /**
   * @return WorkloadProperties
   */
  public function getWorkloadProperties()
  {
    return $this->workloadProperties;
  }
  /**
   * Output only. Reference of an underlying compute resource represented by the
   * Workload. These are immutable.
   *
   * @param WorkloadReference $workloadReference
   */
  public function setWorkloadReference(WorkloadReference $workloadReference)
  {
    $this->workloadReference = $workloadReference;
  }
  /**
   * @return WorkloadReference
   */
  public function getWorkloadReference()
  {
    return $this->workloadReference;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Workload::class, 'Google_Service_AppHub_Workload');
