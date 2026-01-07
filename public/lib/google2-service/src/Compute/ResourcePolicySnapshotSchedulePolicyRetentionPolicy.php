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

class ResourcePolicySnapshotSchedulePolicyRetentionPolicy extends \Google\Model
{
  public const ON_SOURCE_DISK_DELETE_APPLY_RETENTION_POLICY = 'APPLY_RETENTION_POLICY';
  public const ON_SOURCE_DISK_DELETE_KEEP_AUTO_SNAPSHOTS = 'KEEP_AUTO_SNAPSHOTS';
  public const ON_SOURCE_DISK_DELETE_UNSPECIFIED_ON_SOURCE_DISK_DELETE = 'UNSPECIFIED_ON_SOURCE_DISK_DELETE';
  /**
   * Maximum age of the snapshot that is allowed to be kept.
   *
   * @var int
   */
  public $maxRetentionDays;
  /**
   * Specifies the behavior to apply to scheduled snapshots when the source disk
   * is deleted.
   *
   * @var string
   */
  public $onSourceDiskDelete;

  /**
   * Maximum age of the snapshot that is allowed to be kept.
   *
   * @param int $maxRetentionDays
   */
  public function setMaxRetentionDays($maxRetentionDays)
  {
    $this->maxRetentionDays = $maxRetentionDays;
  }
  /**
   * @return int
   */
  public function getMaxRetentionDays()
  {
    return $this->maxRetentionDays;
  }
  /**
   * Specifies the behavior to apply to scheduled snapshots when the source disk
   * is deleted.
   *
   * Accepted values: APPLY_RETENTION_POLICY, KEEP_AUTO_SNAPSHOTS,
   * UNSPECIFIED_ON_SOURCE_DISK_DELETE
   *
   * @param self::ON_SOURCE_DISK_DELETE_* $onSourceDiskDelete
   */
  public function setOnSourceDiskDelete($onSourceDiskDelete)
  {
    $this->onSourceDiskDelete = $onSourceDiskDelete;
  }
  /**
   * @return self::ON_SOURCE_DISK_DELETE_*
   */
  public function getOnSourceDiskDelete()
  {
    return $this->onSourceDiskDelete;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourcePolicySnapshotSchedulePolicyRetentionPolicy::class, 'Google_Service_Compute_ResourcePolicySnapshotSchedulePolicyRetentionPolicy');
