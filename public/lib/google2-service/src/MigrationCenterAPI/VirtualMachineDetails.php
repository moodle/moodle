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

namespace Google\Service\MigrationCenterAPI;

class VirtualMachineDetails extends \Google\Model
{
  /**
   * @var int
   */
  public $coreCount;
  /**
   * @var string
   */
  public $createTime;
  protected $guestOsType = GuestOsDetails::class;
  protected $guestOsDataType = '';
  /**
   * @var int
   */
  public $memoryMb;
  /**
   * @var string
   */
  public $osFamily;
  /**
   * @var string
   */
  public $osName;
  /**
   * @var string
   */
  public $osVersion;
  protected $platformType = PlatformDetails::class;
  protected $platformDataType = '';
  /**
   * @var string
   */
  public $powerState;
  /**
   * @var string
   */
  public $vcenterFolder;
  /**
   * @var string
   */
  public $vcenterUrl;
  /**
   * @var string
   */
  public $vcenterVmId;
  protected $vmArchitectureType = VirtualMachineArchitectureDetails::class;
  protected $vmArchitectureDataType = '';
  protected $vmDisksType = VirtualMachineDiskDetails::class;
  protected $vmDisksDataType = '';
  /**
   * @var string
   */
  public $vmName;
  protected $vmNetworkType = VirtualMachineNetworkDetails::class;
  protected $vmNetworkDataType = '';
  /**
   * @var string
   */
  public $vmUuid;

  /**
   * @param int
   */
  public function setCoreCount($coreCount)
  {
    $this->coreCount = $coreCount;
  }
  /**
   * @return int
   */
  public function getCoreCount()
  {
    return $this->coreCount;
  }
  /**
   * @param string
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
   * @param GuestOsDetails
   */
  public function setGuestOs(GuestOsDetails $guestOs)
  {
    $this->guestOs = $guestOs;
  }
  /**
   * @return GuestOsDetails
   */
  public function getGuestOs()
  {
    return $this->guestOs;
  }
  /**
   * @param int
   */
  public function setMemoryMb($memoryMb)
  {
    $this->memoryMb = $memoryMb;
  }
  /**
   * @return int
   */
  public function getMemoryMb()
  {
    return $this->memoryMb;
  }
  /**
   * @param string
   */
  public function setOsFamily($osFamily)
  {
    $this->osFamily = $osFamily;
  }
  /**
   * @return string
   */
  public function getOsFamily()
  {
    return $this->osFamily;
  }
  /**
   * @param string
   */
  public function setOsName($osName)
  {
    $this->osName = $osName;
  }
  /**
   * @return string
   */
  public function getOsName()
  {
    return $this->osName;
  }
  /**
   * @param string
   */
  public function setOsVersion($osVersion)
  {
    $this->osVersion = $osVersion;
  }
  /**
   * @return string
   */
  public function getOsVersion()
  {
    return $this->osVersion;
  }
  /**
   * @param PlatformDetails
   */
  public function setPlatform(PlatformDetails $platform)
  {
    $this->platform = $platform;
  }
  /**
   * @return PlatformDetails
   */
  public function getPlatform()
  {
    return $this->platform;
  }
  /**
   * @param string
   */
  public function setPowerState($powerState)
  {
    $this->powerState = $powerState;
  }
  /**
   * @return string
   */
  public function getPowerState()
  {
    return $this->powerState;
  }
  /**
   * @param string
   */
  public function setVcenterFolder($vcenterFolder)
  {
    $this->vcenterFolder = $vcenterFolder;
  }
  /**
   * @return string
   */
  public function getVcenterFolder()
  {
    return $this->vcenterFolder;
  }
  /**
   * @param string
   */
  public function setVcenterUrl($vcenterUrl)
  {
    $this->vcenterUrl = $vcenterUrl;
  }
  /**
   * @return string
   */
  public function getVcenterUrl()
  {
    return $this->vcenterUrl;
  }
  /**
   * @param string
   */
  public function setVcenterVmId($vcenterVmId)
  {
    $this->vcenterVmId = $vcenterVmId;
  }
  /**
   * @return string
   */
  public function getVcenterVmId()
  {
    return $this->vcenterVmId;
  }
  /**
   * @param VirtualMachineArchitectureDetails
   */
  public function setVmArchitecture(VirtualMachineArchitectureDetails $vmArchitecture)
  {
    $this->vmArchitecture = $vmArchitecture;
  }
  /**
   * @return VirtualMachineArchitectureDetails
   */
  public function getVmArchitecture()
  {
    return $this->vmArchitecture;
  }
  /**
   * @param VirtualMachineDiskDetails
   */
  public function setVmDisks(VirtualMachineDiskDetails $vmDisks)
  {
    $this->vmDisks = $vmDisks;
  }
  /**
   * @return VirtualMachineDiskDetails
   */
  public function getVmDisks()
  {
    return $this->vmDisks;
  }
  /**
   * @param string
   */
  public function setVmName($vmName)
  {
    $this->vmName = $vmName;
  }
  /**
   * @return string
   */
  public function getVmName()
  {
    return $this->vmName;
  }
  /**
   * @param VirtualMachineNetworkDetails
   */
  public function setVmNetwork(VirtualMachineNetworkDetails $vmNetwork)
  {
    $this->vmNetwork = $vmNetwork;
  }
  /**
   * @return VirtualMachineNetworkDetails
   */
  public function getVmNetwork()
  {
    return $this->vmNetwork;
  }
  /**
   * @param string
   */
  public function setVmUuid($vmUuid)
  {
    $this->vmUuid = $vmUuid;
  }
  /**
   * @return string
   */
  public function getVmUuid()
  {
    return $this->vmUuid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VirtualMachineDetails::class, 'Google_Service_MigrationCenterAPI_VirtualMachineDetails');
