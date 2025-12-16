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

namespace Google\Service\Container;

class BootDisk extends \Google\Model
{
  /**
   * Disk type of the boot disk. (i.e. Hyperdisk-Balanced, PD-Balanced, etc.)
   *
   * @var string
   */
  public $diskType;
  /**
   * For Hyperdisk-Balanced only, the provisioned IOPS config value.
   *
   * @var string
   */
  public $provisionedIops;
  /**
   * For Hyperdisk-Balanced only, the provisioned throughput config value.
   *
   * @var string
   */
  public $provisionedThroughput;
  /**
   * Disk size in GB. Replaces NodeConfig.disk_size_gb
   *
   * @var string
   */
  public $sizeGb;

  /**
   * Disk type of the boot disk. (i.e. Hyperdisk-Balanced, PD-Balanced, etc.)
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
   * For Hyperdisk-Balanced only, the provisioned IOPS config value.
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
   * For Hyperdisk-Balanced only, the provisioned throughput config value.
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
   * Disk size in GB. Replaces NodeConfig.disk_size_gb
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BootDisk::class, 'Google_Service_Container_BootDisk');
