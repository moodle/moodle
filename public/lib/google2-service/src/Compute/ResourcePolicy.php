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

class ResourcePolicy extends \Google\Model
{
  /**
   * Resource policy is being created.
   */
  public const STATUS_CREATING = 'CREATING';
  /**
   * Resource policy is being deleted.
   */
  public const STATUS_DELETING = 'DELETING';
  /**
   * Resource policy is expired and will not run again.
   */
  public const STATUS_EXPIRED = 'EXPIRED';
  public const STATUS_INVALID = 'INVALID';
  /**
   * Resource policy is ready to be used.
   */
  public const STATUS_READY = 'READY';
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * @var string
   */
  public $description;
  protected $diskConsistencyGroupPolicyType = ResourcePolicyDiskConsistencyGroupPolicy::class;
  protected $diskConsistencyGroupPolicyDataType = '';
  protected $groupPlacementPolicyType = ResourcePolicyGroupPlacementPolicy::class;
  protected $groupPlacementPolicyDataType = '';
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @var string
   */
  public $id;
  protected $instanceSchedulePolicyType = ResourcePolicyInstanceSchedulePolicy::class;
  protected $instanceSchedulePolicyDataType = '';
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#resource_policies for resource policies.
   *
   * @var string
   */
  public $kind;
  /**
   * The name of the resource, provided by the client when initially creating
   * the resource. The resource name must be 1-63 characters long, and comply
   * withRFC1035. Specifically, the name must be 1-63 characters long and match
   * the regular expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first
   * character must be a lowercase letter, and all following characters must be
   * a dash, lowercase letter, or digit, except the last character, which cannot
   * be a dash.
   *
   * @var string
   */
  public $name;
  /**
   * @var string
   */
  public $region;
  protected $resourceStatusType = ResourcePolicyResourceStatus::class;
  protected $resourceStatusDataType = '';
  /**
   * Output only. [Output Only] Server-defined fully-qualified URL for this
   * resource.
   *
   * @var string
   */
  public $selfLink;
  protected $snapshotSchedulePolicyType = ResourcePolicySnapshotSchedulePolicy::class;
  protected $snapshotSchedulePolicyDataType = '';
  /**
   * Output only. [Output Only] The status of resource policy creation.
   *
   * @var string
   */
  public $status;
  protected $workloadPolicyType = ResourcePolicyWorkloadPolicy::class;
  protected $workloadPolicyDataType = '';

  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @param string $creationTimestamp
   */
  public function setCreationTimestamp($creationTimestamp)
  {
    $this->creationTimestamp = $creationTimestamp;
  }
  /**
   * @return string
   */
  public function getCreationTimestamp()
  {
    return $this->creationTimestamp;
  }
  /**
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
   * Resource policy for disk consistency groups.
   *
   * @param ResourcePolicyDiskConsistencyGroupPolicy $diskConsistencyGroupPolicy
   */
  public function setDiskConsistencyGroupPolicy(ResourcePolicyDiskConsistencyGroupPolicy $diskConsistencyGroupPolicy)
  {
    $this->diskConsistencyGroupPolicy = $diskConsistencyGroupPolicy;
  }
  /**
   * @return ResourcePolicyDiskConsistencyGroupPolicy
   */
  public function getDiskConsistencyGroupPolicy()
  {
    return $this->diskConsistencyGroupPolicy;
  }
  /**
   * Resource policy for instances for placement configuration.
   *
   * @param ResourcePolicyGroupPlacementPolicy $groupPlacementPolicy
   */
  public function setGroupPlacementPolicy(ResourcePolicyGroupPlacementPolicy $groupPlacementPolicy)
  {
    $this->groupPlacementPolicy = $groupPlacementPolicy;
  }
  /**
   * @return ResourcePolicyGroupPlacementPolicy
   */
  public function getGroupPlacementPolicy()
  {
    return $this->groupPlacementPolicy;
  }
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
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
   * Resource policy for scheduling instance operations.
   *
   * @param ResourcePolicyInstanceSchedulePolicy $instanceSchedulePolicy
   */
  public function setInstanceSchedulePolicy(ResourcePolicyInstanceSchedulePolicy $instanceSchedulePolicy)
  {
    $this->instanceSchedulePolicy = $instanceSchedulePolicy;
  }
  /**
   * @return ResourcePolicyInstanceSchedulePolicy
   */
  public function getInstanceSchedulePolicy()
  {
    return $this->instanceSchedulePolicy;
  }
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#resource_policies for resource policies.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The name of the resource, provided by the client when initially creating
   * the resource. The resource name must be 1-63 characters long, and comply
   * withRFC1035. Specifically, the name must be 1-63 characters long and match
   * the regular expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first
   * character must be a lowercase letter, and all following characters must be
   * a dash, lowercase letter, or digit, except the last character, which cannot
   * be a dash.
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
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * Output only. [Output Only] The system status of the resource policy.
   *
   * @param ResourcePolicyResourceStatus $resourceStatus
   */
  public function setResourceStatus(ResourcePolicyResourceStatus $resourceStatus)
  {
    $this->resourceStatus = $resourceStatus;
  }
  /**
   * @return ResourcePolicyResourceStatus
   */
  public function getResourceStatus()
  {
    return $this->resourceStatus;
  }
  /**
   * Output only. [Output Only] Server-defined fully-qualified URL for this
   * resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Resource policy for persistent disks for creating snapshots.
   *
   * @param ResourcePolicySnapshotSchedulePolicy $snapshotSchedulePolicy
   */
  public function setSnapshotSchedulePolicy(ResourcePolicySnapshotSchedulePolicy $snapshotSchedulePolicy)
  {
    $this->snapshotSchedulePolicy = $snapshotSchedulePolicy;
  }
  /**
   * @return ResourcePolicySnapshotSchedulePolicy
   */
  public function getSnapshotSchedulePolicy()
  {
    return $this->snapshotSchedulePolicy;
  }
  /**
   * Output only. [Output Only] The status of resource policy creation.
   *
   * Accepted values: CREATING, DELETING, EXPIRED, INVALID, READY
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Resource policy for defining instance placement for MIGs.
   *
   * @param ResourcePolicyWorkloadPolicy $workloadPolicy
   */
  public function setWorkloadPolicy(ResourcePolicyWorkloadPolicy $workloadPolicy)
  {
    $this->workloadPolicy = $workloadPolicy;
  }
  /**
   * @return ResourcePolicyWorkloadPolicy
   */
  public function getWorkloadPolicy()
  {
    return $this->workloadPolicy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourcePolicy::class, 'Google_Service_Compute_ResourcePolicy');
