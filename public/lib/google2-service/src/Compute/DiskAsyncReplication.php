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

class DiskAsyncReplication extends \Google\Model
{
  /**
   * Output only. [Output Only] URL of the DiskConsistencyGroupPolicy if
   * replication was started on the disk as a member of a group.
   *
   * @var string
   */
  public $consistencyGroupPolicy;
  /**
   * Output only. [Output Only] ID of the DiskConsistencyGroupPolicy if
   * replication was started on the disk as a member of a group.
   *
   * @var string
   */
  public $consistencyGroupPolicyId;
  /**
   * The other disk asynchronously replicated to or from the current disk. You
   * can provide this as a partial or full URL to the resource. For example, the
   * following are valid values:              - https://www.googleapis.com/compu
   * te/v1/projects/project/zones/zone/disks/disk     -
   * projects/project/zones/zone/disks/disk     - zones/zone/disks/disk
   *
   * @var string
   */
  public $disk;
  /**
   * Output only. [Output Only] The unique ID of the other disk asynchronously
   * replicated to or from the current disk. This value identifies the exact
   * disk that was used to create this replication. For example, if you started
   * replicating the persistent disk from a disk that was later deleted and
   * recreated under the same name, the disk ID would identify the exact version
   * of the disk that was used.
   *
   * @var string
   */
  public $diskId;

  /**
   * Output only. [Output Only] URL of the DiskConsistencyGroupPolicy if
   * replication was started on the disk as a member of a group.
   *
   * @param string $consistencyGroupPolicy
   */
  public function setConsistencyGroupPolicy($consistencyGroupPolicy)
  {
    $this->consistencyGroupPolicy = $consistencyGroupPolicy;
  }
  /**
   * @return string
   */
  public function getConsistencyGroupPolicy()
  {
    return $this->consistencyGroupPolicy;
  }
  /**
   * Output only. [Output Only] ID of the DiskConsistencyGroupPolicy if
   * replication was started on the disk as a member of a group.
   *
   * @param string $consistencyGroupPolicyId
   */
  public function setConsistencyGroupPolicyId($consistencyGroupPolicyId)
  {
    $this->consistencyGroupPolicyId = $consistencyGroupPolicyId;
  }
  /**
   * @return string
   */
  public function getConsistencyGroupPolicyId()
  {
    return $this->consistencyGroupPolicyId;
  }
  /**
   * The other disk asynchronously replicated to or from the current disk. You
   * can provide this as a partial or full URL to the resource. For example, the
   * following are valid values:              - https://www.googleapis.com/compu
   * te/v1/projects/project/zones/zone/disks/disk     -
   * projects/project/zones/zone/disks/disk     - zones/zone/disks/disk
   *
   * @param string $disk
   */
  public function setDisk($disk)
  {
    $this->disk = $disk;
  }
  /**
   * @return string
   */
  public function getDisk()
  {
    return $this->disk;
  }
  /**
   * Output only. [Output Only] The unique ID of the other disk asynchronously
   * replicated to or from the current disk. This value identifies the exact
   * disk that was used to create this replication. For example, if you started
   * replicating the persistent disk from a disk that was later deleted and
   * recreated under the same name, the disk ID would identify the exact version
   * of the disk that was used.
   *
   * @param string $diskId
   */
  public function setDiskId($diskId)
  {
    $this->diskId = $diskId;
  }
  /**
   * @return string
   */
  public function getDiskId()
  {
    return $this->diskId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiskAsyncReplication::class, 'Google_Service_Compute_DiskAsyncReplication');
