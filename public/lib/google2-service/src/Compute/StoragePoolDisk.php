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

class StoragePoolDisk extends \Google\Collection
{
  /**
   * Disk is provisioning
   */
  public const STATUS_CREATING = 'CREATING';
  /**
   * Disk is deleting.
   */
  public const STATUS_DELETING = 'DELETING';
  /**
   * Disk creation failed.
   */
  public const STATUS_FAILED = 'FAILED';
  /**
   * Disk is ready for use.
   */
  public const STATUS_READY = 'READY';
  /**
   * Source data is being copied into the disk.
   */
  public const STATUS_RESTORING = 'RESTORING';
  /**
   * Disk is currently unavailable and cannot be accessed, attached or detached.
   */
  public const STATUS_UNAVAILABLE = 'UNAVAILABLE';
  protected $collection_key = 'resourcePolicies';
  /**
   * Output only. [Output Only] Instances this disk is attached to.
   *
   * @var string[]
   */
  public $attachedInstances;
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * Output only. [Output Only] The URL of the disk.
   *
   * @var string
   */
  public $disk;
  /**
   * Output only. [Output Only] The name of the disk.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. [Output Only] The number of IOPS provisioned for the disk.
   *
   * @var string
   */
  public $provisionedIops;
  /**
   * Output only. [Output Only] The throughput provisioned for the disk.
   *
   * @var string
   */
  public $provisionedThroughput;
  /**
   * Output only. [Output Only] Resource policies applied to disk for automatic
   * snapshot creations.
   *
   * @var string[]
   */
  public $resourcePolicies;
  /**
   * Output only. [Output Only] The disk size, in GB.
   *
   * @var string
   */
  public $sizeGb;
  /**
   * Output only. [Output Only] The disk status.
   *
   * @var string
   */
  public $status;
  /**
   * Output only. [Output Only] The disk type.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. [Output Only] Amount of disk space used.
   *
   * @var string
   */
  public $usedBytes;

  /**
   * Output only. [Output Only] Instances this disk is attached to.
   *
   * @param string[] $attachedInstances
   */
  public function setAttachedInstances($attachedInstances)
  {
    $this->attachedInstances = $attachedInstances;
  }
  /**
   * @return string[]
   */
  public function getAttachedInstances()
  {
    return $this->attachedInstances;
  }
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
   * Output only. [Output Only] The URL of the disk.
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
   * Output only. [Output Only] The name of the disk.
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
   * Output only. [Output Only] The number of IOPS provisioned for the disk.
   *
   * @param string $provisionedIops
   */
  public function setProvisionedIops($provisionedIops)
  {
    $this->provisionedIops = $provisionedIops;
  }
  /**
   * @return string
   */
  public function getProvisionedIops()
  {
    return $this->provisionedIops;
  }
  /**
   * Output only. [Output Only] The throughput provisioned for the disk.
   *
   * @param string $provisionedThroughput
   */
  public function setProvisionedThroughput($provisionedThroughput)
  {
    $this->provisionedThroughput = $provisionedThroughput;
  }
  /**
   * @return string
   */
  public function getProvisionedThroughput()
  {
    return $this->provisionedThroughput;
  }
  /**
   * Output only. [Output Only] Resource policies applied to disk for automatic
   * snapshot creations.
   *
   * @param string[] $resourcePolicies
   */
  public function setResourcePolicies($resourcePolicies)
  {
    $this->resourcePolicies = $resourcePolicies;
  }
  /**
   * @return string[]
   */
  public function getResourcePolicies()
  {
    return $this->resourcePolicies;
  }
  /**
   * Output only. [Output Only] The disk size, in GB.
   *
   * @param string $sizeGb
   */
  public function setSizeGb($sizeGb)
  {
    $this->sizeGb = $sizeGb;
  }
  /**
   * @return string
   */
  public function getSizeGb()
  {
    return $this->sizeGb;
  }
  /**
   * Output only. [Output Only] The disk status.
   *
   * Accepted values: CREATING, DELETING, FAILED, READY, RESTORING, UNAVAILABLE
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
   * Output only. [Output Only] The disk type.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. [Output Only] Amount of disk space used.
   *
   * @param string $usedBytes
   */
  public function setUsedBytes($usedBytes)
  {
    $this->usedBytes = $usedBytes;
  }
  /**
   * @return string
   */
  public function getUsedBytes()
  {
    return $this->usedBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StoragePoolDisk::class, 'Google_Service_Compute_StoragePoolDisk');
