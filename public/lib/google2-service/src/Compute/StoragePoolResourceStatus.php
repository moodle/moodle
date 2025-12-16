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

class StoragePoolResourceStatus extends \Google\Model
{
  /**
   * [Output Only] Number of disks used.
   *
   * @var string
   */
  public $diskCount;
  /**
   * Output only. [Output Only] Maximum allowed read IOPS for this Exapool.
   *
   * @var string
   */
  public $exapoolMaxReadIops;
  /**
   * Output only. [Output Only] Maximum allowed read throughput in MiB/s for
   * this Exapool.
   *
   * @var string
   */
  public $exapoolMaxReadThroughput;
  /**
   * Output only. [Output Only] Maximum allowed write IOPS for this Exapool.
   *
   * @var string
   */
  public $exapoolMaxWriteIops;
  /**
   * Output only. [Output Only] Maximum allowed write throughput in MiB/s for
   * this Exapool.
   *
   * @var string
   */
  public $exapoolMaxWriteThroughput;
  /**
   * Output only. [Output Only] Timestamp of the last successful resize
   * inRFC3339 text format.
   *
   * @var string
   */
  public $lastResizeTimestamp;
  /**
   * [Output Only] Maximum allowed aggregate disk size in GiB.
   *
   * @var string
   */
  public $maxTotalProvisionedDiskCapacityGb;
  /**
   * [Output Only] Space used by data stored in disks within the storage pool
   * (in bytes). This will reflect the total number of bytes written to the
   * disks in the pool, in contrast to the capacity of those disks.
   *
   * @var string
   */
  public $poolUsedCapacityBytes;
  /**
   * [Output Only] Sum of all the disks' provisioned IOPS, minus some amount
   * that is allowed per disk that is not counted towards pool's IOPS capacity.
   * For more information, see
   * https://cloud.google.com/compute/docs/disks/storage-pools.
   *
   * @var string
   */
  public $poolUsedIops;
  /**
   * [Output Only] Sum of all the disks' provisioned throughput in MiB/s.
   *
   * @var string
   */
  public $poolUsedThroughput;
  /**
   * [Output Only] Amount of data written into the pool, before it is compacted.
   *
   * @var string
   */
  public $poolUserWrittenBytes;
  /**
   * [Output Only] Sum of all the disks' provisioned capacity (in GiB) in this
   * storage pool. A disk's provisioned capacity is the same as its total
   * capacity.
   *
   * @var string
   */
  public $totalProvisionedDiskCapacityGb;
  /**
   * [Output Only] Sum of all the disks' provisioned IOPS.
   *
   * @var string
   */
  public $totalProvisionedDiskIops;
  /**
   * [Output Only] Sum of all the disks' provisioned throughput in MiB/s, minus
   * some amount that is allowed per disk that is not counted towards pool's
   * throughput capacity.
   *
   * @var string
   */
  public $totalProvisionedDiskThroughput;

  /**
   * [Output Only] Number of disks used.
   *
   * @param string $diskCount
   */
  public function setDiskCount($diskCount)
  {
    $this->diskCount = $diskCount;
  }
  /**
   * @return string
   */
  public function getDiskCount()
  {
    return $this->diskCount;
  }
  /**
   * Output only. [Output Only] Maximum allowed read IOPS for this Exapool.
   *
   * @param string $exapoolMaxReadIops
   */
  public function setExapoolMaxReadIops($exapoolMaxReadIops)
  {
    $this->exapoolMaxReadIops = $exapoolMaxReadIops;
  }
  /**
   * @return string
   */
  public function getExapoolMaxReadIops()
  {
    return $this->exapoolMaxReadIops;
  }
  /**
   * Output only. [Output Only] Maximum allowed read throughput in MiB/s for
   * this Exapool.
   *
   * @param string $exapoolMaxReadThroughput
   */
  public function setExapoolMaxReadThroughput($exapoolMaxReadThroughput)
  {
    $this->exapoolMaxReadThroughput = $exapoolMaxReadThroughput;
  }
  /**
   * @return string
   */
  public function getExapoolMaxReadThroughput()
  {
    return $this->exapoolMaxReadThroughput;
  }
  /**
   * Output only. [Output Only] Maximum allowed write IOPS for this Exapool.
   *
   * @param string $exapoolMaxWriteIops
   */
  public function setExapoolMaxWriteIops($exapoolMaxWriteIops)
  {
    $this->exapoolMaxWriteIops = $exapoolMaxWriteIops;
  }
  /**
   * @return string
   */
  public function getExapoolMaxWriteIops()
  {
    return $this->exapoolMaxWriteIops;
  }
  /**
   * Output only. [Output Only] Maximum allowed write throughput in MiB/s for
   * this Exapool.
   *
   * @param string $exapoolMaxWriteThroughput
   */
  public function setExapoolMaxWriteThroughput($exapoolMaxWriteThroughput)
  {
    $this->exapoolMaxWriteThroughput = $exapoolMaxWriteThroughput;
  }
  /**
   * @return string
   */
  public function getExapoolMaxWriteThroughput()
  {
    return $this->exapoolMaxWriteThroughput;
  }
  /**
   * Output only. [Output Only] Timestamp of the last successful resize
   * inRFC3339 text format.
   *
   * @param string $lastResizeTimestamp
   */
  public function setLastResizeTimestamp($lastResizeTimestamp)
  {
    $this->lastResizeTimestamp = $lastResizeTimestamp;
  }
  /**
   * @return string
   */
  public function getLastResizeTimestamp()
  {
    return $this->lastResizeTimestamp;
  }
  /**
   * [Output Only] Maximum allowed aggregate disk size in GiB.
   *
   * @param string $maxTotalProvisionedDiskCapacityGb
   */
  public function setMaxTotalProvisionedDiskCapacityGb($maxTotalProvisionedDiskCapacityGb)
  {
    $this->maxTotalProvisionedDiskCapacityGb = $maxTotalProvisionedDiskCapacityGb;
  }
  /**
   * @return string
   */
  public function getMaxTotalProvisionedDiskCapacityGb()
  {
    return $this->maxTotalProvisionedDiskCapacityGb;
  }
  /**
   * [Output Only] Space used by data stored in disks within the storage pool
   * (in bytes). This will reflect the total number of bytes written to the
   * disks in the pool, in contrast to the capacity of those disks.
   *
   * @param string $poolUsedCapacityBytes
   */
  public function setPoolUsedCapacityBytes($poolUsedCapacityBytes)
  {
    $this->poolUsedCapacityBytes = $poolUsedCapacityBytes;
  }
  /**
   * @return string
   */
  public function getPoolUsedCapacityBytes()
  {
    return $this->poolUsedCapacityBytes;
  }
  /**
   * [Output Only] Sum of all the disks' provisioned IOPS, minus some amount
   * that is allowed per disk that is not counted towards pool's IOPS capacity.
   * For more information, see
   * https://cloud.google.com/compute/docs/disks/storage-pools.
   *
   * @param string $poolUsedIops
   */
  public function setPoolUsedIops($poolUsedIops)
  {
    $this->poolUsedIops = $poolUsedIops;
  }
  /**
   * @return string
   */
  public function getPoolUsedIops()
  {
    return $this->poolUsedIops;
  }
  /**
   * [Output Only] Sum of all the disks' provisioned throughput in MiB/s.
   *
   * @param string $poolUsedThroughput
   */
  public function setPoolUsedThroughput($poolUsedThroughput)
  {
    $this->poolUsedThroughput = $poolUsedThroughput;
  }
  /**
   * @return string
   */
  public function getPoolUsedThroughput()
  {
    return $this->poolUsedThroughput;
  }
  /**
   * [Output Only] Amount of data written into the pool, before it is compacted.
   *
   * @param string $poolUserWrittenBytes
   */
  public function setPoolUserWrittenBytes($poolUserWrittenBytes)
  {
    $this->poolUserWrittenBytes = $poolUserWrittenBytes;
  }
  /**
   * @return string
   */
  public function getPoolUserWrittenBytes()
  {
    return $this->poolUserWrittenBytes;
  }
  /**
   * [Output Only] Sum of all the disks' provisioned capacity (in GiB) in this
   * storage pool. A disk's provisioned capacity is the same as its total
   * capacity.
   *
   * @param string $totalProvisionedDiskCapacityGb
   */
  public function setTotalProvisionedDiskCapacityGb($totalProvisionedDiskCapacityGb)
  {
    $this->totalProvisionedDiskCapacityGb = $totalProvisionedDiskCapacityGb;
  }
  /**
   * @return string
   */
  public function getTotalProvisionedDiskCapacityGb()
  {
    return $this->totalProvisionedDiskCapacityGb;
  }
  /**
   * [Output Only] Sum of all the disks' provisioned IOPS.
   *
   * @param string $totalProvisionedDiskIops
   */
  public function setTotalProvisionedDiskIops($totalProvisionedDiskIops)
  {
    $this->totalProvisionedDiskIops = $totalProvisionedDiskIops;
  }
  /**
   * @return string
   */
  public function getTotalProvisionedDiskIops()
  {
    return $this->totalProvisionedDiskIops;
  }
  /**
   * [Output Only] Sum of all the disks' provisioned throughput in MiB/s, minus
   * some amount that is allowed per disk that is not counted towards pool's
   * throughput capacity.
   *
   * @param string $totalProvisionedDiskThroughput
   */
  public function setTotalProvisionedDiskThroughput($totalProvisionedDiskThroughput)
  {
    $this->totalProvisionedDiskThroughput = $totalProvisionedDiskThroughput;
  }
  /**
   * @return string
   */
  public function getTotalProvisionedDiskThroughput()
  {
    return $this->totalProvisionedDiskThroughput;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StoragePoolResourceStatus::class, 'Google_Service_Compute_StoragePoolResourceStatus');
