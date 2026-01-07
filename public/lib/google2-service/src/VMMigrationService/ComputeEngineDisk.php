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

namespace Google\Service\VMMigrationService;

class ComputeEngineDisk extends \Google\Collection
{
  /**
   * An unspecified disk type. Will be used as STANDARD.
   */
  public const DISK_TYPE_COMPUTE_ENGINE_DISK_TYPE_UNSPECIFIED = 'COMPUTE_ENGINE_DISK_TYPE_UNSPECIFIED';
  /**
   * A Standard disk type.
   */
  public const DISK_TYPE_COMPUTE_ENGINE_DISK_TYPE_STANDARD = 'COMPUTE_ENGINE_DISK_TYPE_STANDARD';
  /**
   * SSD hard disk type.
   */
  public const DISK_TYPE_COMPUTE_ENGINE_DISK_TYPE_SSD = 'COMPUTE_ENGINE_DISK_TYPE_SSD';
  /**
   * An alternative to SSD persistent disks that balance performance and cost.
   */
  public const DISK_TYPE_COMPUTE_ENGINE_DISK_TYPE_BALANCED = 'COMPUTE_ENGINE_DISK_TYPE_BALANCED';
  /**
   * Hyperdisk balanced disk type.
   */
  public const DISK_TYPE_COMPUTE_ENGINE_DISK_TYPE_HYPERDISK_BALANCED = 'COMPUTE_ENGINE_DISK_TYPE_HYPERDISK_BALANCED';
  protected $collection_key = 'replicaZones';
  /**
   * Optional. Target Compute Engine Disk ID. This is the resource ID segment of
   * the Compute Engine Disk to create. In the resource name
   * compute/v1/projects/{project}/zones/{zone}/disks/disk1 "disk1" is the
   * resource ID for the disk.
   *
   * @var string
   */
  public $diskId;
  /**
   * Required. The disk type to use.
   *
   * @var string
   */
  public $diskType;
  /**
   * Optional. Replication zones of the regional disk. Should be of the form:
   * projects/{target-project}/locations/{replica-zone} Currently only one
   * replica zone is supported.
   *
   * @var string[]
   */
  public $replicaZones;
  /**
   * Required. The Compute Engine zone in which to create the disk. Should be of
   * the form: projects/{target-project}/locations/{zone}
   *
   * @var string
   */
  public $zone;

  /**
   * Optional. Target Compute Engine Disk ID. This is the resource ID segment of
   * the Compute Engine Disk to create. In the resource name
   * compute/v1/projects/{project}/zones/{zone}/disks/disk1 "disk1" is the
   * resource ID for the disk.
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
  /**
   * Required. The disk type to use.
   *
   * Accepted values: COMPUTE_ENGINE_DISK_TYPE_UNSPECIFIED,
   * COMPUTE_ENGINE_DISK_TYPE_STANDARD, COMPUTE_ENGINE_DISK_TYPE_SSD,
   * COMPUTE_ENGINE_DISK_TYPE_BALANCED,
   * COMPUTE_ENGINE_DISK_TYPE_HYPERDISK_BALANCED
   *
   * @param self::DISK_TYPE_* $diskType
   */
  public function setDiskType($diskType)
  {
    $this->diskType = $diskType;
  }
  /**
   * @return self::DISK_TYPE_*
   */
  public function getDiskType()
  {
    return $this->diskType;
  }
  /**
   * Optional. Replication zones of the regional disk. Should be of the form:
   * projects/{target-project}/locations/{replica-zone} Currently only one
   * replica zone is supported.
   *
   * @param string[] $replicaZones
   */
  public function setReplicaZones($replicaZones)
  {
    $this->replicaZones = $replicaZones;
  }
  /**
   * @return string[]
   */
  public function getReplicaZones()
  {
    return $this->replicaZones;
  }
  /**
   * Required. The Compute Engine zone in which to create the disk. Should be of
   * the form: projects/{target-project}/locations/{zone}
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ComputeEngineDisk::class, 'Google_Service_VMMigrationService_ComputeEngineDisk');
