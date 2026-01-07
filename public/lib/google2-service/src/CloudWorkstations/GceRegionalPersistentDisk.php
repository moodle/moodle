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

namespace Google\Service\CloudWorkstations;

class GceRegionalPersistentDisk extends \Google\Model
{
  /**
   * Do not use.
   */
  public const RECLAIM_POLICY_RECLAIM_POLICY_UNSPECIFIED = 'RECLAIM_POLICY_UNSPECIFIED';
  /**
   * Delete the persistent disk when deleting the workstation.
   */
  public const RECLAIM_POLICY_DELETE = 'DELETE';
  /**
   * Keep the persistent disk when deleting the workstation. An administrator
   * must manually delete the disk.
   */
  public const RECLAIM_POLICY_RETAIN = 'RETAIN';
  /**
   * Optional. The [type of the persistent
   * disk](https://cloud.google.com/compute/docs/disks#disk-types) for the home
   * directory. Defaults to `"pd-standard"`.
   *
   * @var string
   */
  public $diskType;
  /**
   * Optional. Type of file system that the disk should be formatted with. The
   * workstation image must support this file system type. Must be empty if
   * source_snapshot is set. Defaults to `"ext4"`.
   *
   * @var string
   */
  public $fsType;
  /**
   * Optional. Whether the persistent disk should be deleted when the
   * workstation is deleted. Valid values are `DELETE` and `RETAIN`. Defaults to
   * `DELETE`.
   *
   * @var string
   */
  public $reclaimPolicy;
  /**
   * Optional. The GB capacity of a persistent home directory for each
   * workstation created with this configuration. Must be empty if
   * source_snapshot is set. Valid values are `10`, `50`, `100`, `200`, `500`,
   * or `1000`. Defaults to `200`. If less than `200` GB, the disk_type must be
   * `"pd-balanced"` or `"pd-ssd"`.
   *
   * @var int
   */
  public $sizeGb;
  /**
   * Optional. Name of the snapshot to use as the source for the disk. If set,
   * size_gb and fs_type must be empty. Must be formatted as ext4 file system
   * with no partitions.
   *
   * @var string
   */
  public $sourceSnapshot;

  /**
   * Optional. The [type of the persistent
   * disk](https://cloud.google.com/compute/docs/disks#disk-types) for the home
   * directory. Defaults to `"pd-standard"`.
   *
   * @param string $diskType
   */
  public function setDiskType($diskType)
  {
    $this->diskType = $diskType;
  }
  /**
   * @return string
   */
  public function getDiskType()
  {
    return $this->diskType;
  }
  /**
   * Optional. Type of file system that the disk should be formatted with. The
   * workstation image must support this file system type. Must be empty if
   * source_snapshot is set. Defaults to `"ext4"`.
   *
   * @param string $fsType
   */
  public function setFsType($fsType)
  {
    $this->fsType = $fsType;
  }
  /**
   * @return string
   */
  public function getFsType()
  {
    return $this->fsType;
  }
  /**
   * Optional. Whether the persistent disk should be deleted when the
   * workstation is deleted. Valid values are `DELETE` and `RETAIN`. Defaults to
   * `DELETE`.
   *
   * Accepted values: RECLAIM_POLICY_UNSPECIFIED, DELETE, RETAIN
   *
   * @param self::RECLAIM_POLICY_* $reclaimPolicy
   */
  public function setReclaimPolicy($reclaimPolicy)
  {
    $this->reclaimPolicy = $reclaimPolicy;
  }
  /**
   * @return self::RECLAIM_POLICY_*
   */
  public function getReclaimPolicy()
  {
    return $this->reclaimPolicy;
  }
  /**
   * Optional. The GB capacity of a persistent home directory for each
   * workstation created with this configuration. Must be empty if
   * source_snapshot is set. Valid values are `10`, `50`, `100`, `200`, `500`,
   * or `1000`. Defaults to `200`. If less than `200` GB, the disk_type must be
   * `"pd-balanced"` or `"pd-ssd"`.
   *
   * @param int $sizeGb
   */
  public function setSizeGb($sizeGb)
  {
    $this->sizeGb = $sizeGb;
  }
  /**
   * @return int
   */
  public function getSizeGb()
  {
    return $this->sizeGb;
  }
  /**
   * Optional. Name of the snapshot to use as the source for the disk. If set,
   * size_gb and fs_type must be empty. Must be formatted as ext4 file system
   * with no partitions.
   *
   * @param string $sourceSnapshot
   */
  public function setSourceSnapshot($sourceSnapshot)
  {
    $this->sourceSnapshot = $sourceSnapshot;
  }
  /**
   * @return string
   */
  public function getSourceSnapshot()
  {
    return $this->sourceSnapshot;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GceRegionalPersistentDisk::class, 'Google_Service_CloudWorkstations_GceRegionalPersistentDisk');
