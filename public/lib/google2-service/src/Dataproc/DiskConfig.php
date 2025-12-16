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

namespace Google\Service\Dataproc;

class DiskConfig extends \Google\Model
{
  /**
   * Optional. Indicates how many IOPS to provision for the disk. This sets the
   * number of I/O operations per second that the disk can handle. This field is
   * supported only if boot_disk_type is hyperdisk-balanced.
   *
   * @var string
   */
  public $bootDiskProvisionedIops;
  /**
   * Optional. Indicates how much throughput to provision for the disk. This
   * sets the number of throughput mb per second that the disk can handle.
   * Values must be greater than or equal to 1. This field is supported only if
   * boot_disk_type is hyperdisk-balanced.
   *
   * @var string
   */
  public $bootDiskProvisionedThroughput;
  /**
   * Optional. Size in GB of the boot disk (default is 500GB).
   *
   * @var int
   */
  public $bootDiskSizeGb;
  /**
   * Optional. Type of the boot disk (default is "pd-standard"). Valid values:
   * "pd-balanced" (Persistent Disk Balanced Solid State Drive), "pd-ssd"
   * (Persistent Disk Solid State Drive), or "pd-standard" (Persistent Disk Hard
   * Disk Drive). See Disk types
   * (https://cloud.google.com/compute/docs/disks#disk-types).
   *
   * @var string
   */
  public $bootDiskType;
  /**
   * Optional. Interface type of local SSDs (default is "scsi"). Valid values:
   * "scsi" (Small Computer System Interface), "nvme" (Non-Volatile Memory
   * Express). See local SSD performance
   * (https://cloud.google.com/compute/docs/disks/local-ssd#performance).
   *
   * @var string
   */
  public $localSsdInterface;
  /**
   * Optional. Number of attached SSDs, from 0 to 8 (default is 0). If SSDs are
   * not attached, the boot disk is used to store runtime logs and HDFS
   * (https://hadoop.apache.org/docs/r1.2.1/hdfs_user_guide.html) data. If one
   * or more SSDs are attached, this runtime bulk data is spread across them,
   * and the boot disk contains only basic config and installed binaries.Note:
   * Local SSD options may vary by machine type and number of vCPUs selected.
   *
   * @var int
   */
  public $numLocalSsds;

  /**
   * Optional. Indicates how many IOPS to provision for the disk. This sets the
   * number of I/O operations per second that the disk can handle. This field is
   * supported only if boot_disk_type is hyperdisk-balanced.
   *
   * @param string $bootDiskProvisionedIops
   */
  public function setBootDiskProvisionedIops($bootDiskProvisionedIops)
  {
    $this->bootDiskProvisionedIops = $bootDiskProvisionedIops;
  }
  /**
   * @return string
   */
  public function getBootDiskProvisionedIops()
  {
    return $this->bootDiskProvisionedIops;
  }
  /**
   * Optional. Indicates how much throughput to provision for the disk. This
   * sets the number of throughput mb per second that the disk can handle.
   * Values must be greater than or equal to 1. This field is supported only if
   * boot_disk_type is hyperdisk-balanced.
   *
   * @param string $bootDiskProvisionedThroughput
   */
  public function setBootDiskProvisionedThroughput($bootDiskProvisionedThroughput)
  {
    $this->bootDiskProvisionedThroughput = $bootDiskProvisionedThroughput;
  }
  /**
   * @return string
   */
  public function getBootDiskProvisionedThroughput()
  {
    return $this->bootDiskProvisionedThroughput;
  }
  /**
   * Optional. Size in GB of the boot disk (default is 500GB).
   *
   * @param int $bootDiskSizeGb
   */
  public function setBootDiskSizeGb($bootDiskSizeGb)
  {
    $this->bootDiskSizeGb = $bootDiskSizeGb;
  }
  /**
   * @return int
   */
  public function getBootDiskSizeGb()
  {
    return $this->bootDiskSizeGb;
  }
  /**
   * Optional. Type of the boot disk (default is "pd-standard"). Valid values:
   * "pd-balanced" (Persistent Disk Balanced Solid State Drive), "pd-ssd"
   * (Persistent Disk Solid State Drive), or "pd-standard" (Persistent Disk Hard
   * Disk Drive). See Disk types
   * (https://cloud.google.com/compute/docs/disks#disk-types).
   *
   * @param string $bootDiskType
   */
  public function setBootDiskType($bootDiskType)
  {
    $this->bootDiskType = $bootDiskType;
  }
  /**
   * @return string
   */
  public function getBootDiskType()
  {
    return $this->bootDiskType;
  }
  /**
   * Optional. Interface type of local SSDs (default is "scsi"). Valid values:
   * "scsi" (Small Computer System Interface), "nvme" (Non-Volatile Memory
   * Express). See local SSD performance
   * (https://cloud.google.com/compute/docs/disks/local-ssd#performance).
   *
   * @param string $localSsdInterface
   */
  public function setLocalSsdInterface($localSsdInterface)
  {
    $this->localSsdInterface = $localSsdInterface;
  }
  /**
   * @return string
   */
  public function getLocalSsdInterface()
  {
    return $this->localSsdInterface;
  }
  /**
   * Optional. Number of attached SSDs, from 0 to 8 (default is 0). If SSDs are
   * not attached, the boot disk is used to store runtime logs and HDFS
   * (https://hadoop.apache.org/docs/r1.2.1/hdfs_user_guide.html) data. If one
   * or more SSDs are attached, this runtime bulk data is spread across them,
   * and the boot disk contains only basic config and installed binaries.Note:
   * Local SSD options may vary by machine type and number of vCPUs selected.
   *
   * @param int $numLocalSsds
   */
  public function setNumLocalSsds($numLocalSsds)
  {
    $this->numLocalSsds = $numLocalSsds;
  }
  /**
   * @return int
   */
  public function getNumLocalSsds()
  {
    return $this->numLocalSsds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiskConfig::class, 'Google_Service_Dataproc_DiskConfig');
