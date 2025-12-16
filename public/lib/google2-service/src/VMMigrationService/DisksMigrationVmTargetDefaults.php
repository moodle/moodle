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

class DisksMigrationVmTargetDefaults extends \Google\Collection
{
  protected $collection_key = 'networkTags';
  /**
   * Optional. Additional licenses to assign to the VM.
   *
   * @var string[]
   */
  public $additionalLicenses;
  protected $bootDiskDefaultsType = BootDiskDefaults::class;
  protected $bootDiskDefaultsDataType = '';
  protected $computeSchedulingType = ComputeScheduling::class;
  protected $computeSchedulingDataType = '';
  /**
   * Optional. Defines whether the instance has integrity monitoring enabled.
   *
   * @var bool
   */
  public $enableIntegrityMonitoring;
  /**
   * Optional. Defines whether the instance has vTPM enabled.
   *
   * @var bool
   */
  public $enableVtpm;
  protected $encryptionType = Encryption::class;
  protected $encryptionDataType = '';
  /**
   * Optional. The hostname to assign to the VM.
   *
   * @var string
   */
  public $hostname;
  /**
   * Optional. A map of labels to associate with the VM.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. The machine type to create the VM with.
   *
   * @var string
   */
  public $machineType;
  /**
   * Optional. The machine type series to create the VM with. For presentation
   * only.
   *
   * @var string
   */
  public $machineTypeSeries;
  /**
   * Optional. The metadata key/value pairs to assign to the VM.
   *
   * @var string[]
   */
  public $metadata;
  protected $networkInterfacesType = NetworkInterface::class;
  protected $networkInterfacesDataType = 'array';
  /**
   * Optional. A list of network tags to associate with the VM.
   *
   * @var string[]
   */
  public $networkTags;
  /**
   * Optional. Defines whether the instance has Secure Boot enabled. This can be
   * set to true only if the VM boot option is EFI.
   *
   * @var bool
   */
  public $secureBoot;
  /**
   * Optional. The service account to associate the VM with.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Required. The name of the VM to create.
   *
   * @var string
   */
  public $vmName;

  /**
   * Optional. Additional licenses to assign to the VM.
   *
   * @param string[] $additionalLicenses
   */
  public function setAdditionalLicenses($additionalLicenses)
  {
    $this->additionalLicenses = $additionalLicenses;
  }
  /**
   * @return string[]
   */
  public function getAdditionalLicenses()
  {
    return $this->additionalLicenses;
  }
  /**
   * Optional. Details of the boot disk of the VM.
   *
   * @param BootDiskDefaults $bootDiskDefaults
   */
  public function setBootDiskDefaults(BootDiskDefaults $bootDiskDefaults)
  {
    $this->bootDiskDefaults = $bootDiskDefaults;
  }
  /**
   * @return BootDiskDefaults
   */
  public function getBootDiskDefaults()
  {
    return $this->bootDiskDefaults;
  }
  /**
   * Optional. Compute instance scheduling information (if empty default is
   * used).
   *
   * @param ComputeScheduling $computeScheduling
   */
  public function setComputeScheduling(ComputeScheduling $computeScheduling)
  {
    $this->computeScheduling = $computeScheduling;
  }
  /**
   * @return ComputeScheduling
   */
  public function getComputeScheduling()
  {
    return $this->computeScheduling;
  }
  /**
   * Optional. Defines whether the instance has integrity monitoring enabled.
   *
   * @param bool $enableIntegrityMonitoring
   */
  public function setEnableIntegrityMonitoring($enableIntegrityMonitoring)
  {
    $this->enableIntegrityMonitoring = $enableIntegrityMonitoring;
  }
  /**
   * @return bool
   */
  public function getEnableIntegrityMonitoring()
  {
    return $this->enableIntegrityMonitoring;
  }
  /**
   * Optional. Defines whether the instance has vTPM enabled.
   *
   * @param bool $enableVtpm
   */
  public function setEnableVtpm($enableVtpm)
  {
    $this->enableVtpm = $enableVtpm;
  }
  /**
   * @return bool
   */
  public function getEnableVtpm()
  {
    return $this->enableVtpm;
  }
  /**
   * Optional. The encryption to apply to the VM.
   *
   * @param Encryption $encryption
   */
  public function setEncryption(Encryption $encryption)
  {
    $this->encryption = $encryption;
  }
  /**
   * @return Encryption
   */
  public function getEncryption()
  {
    return $this->encryption;
  }
  /**
   * Optional. The hostname to assign to the VM.
   *
   * @param string $hostname
   */
  public function setHostname($hostname)
  {
    $this->hostname = $hostname;
  }
  /**
   * @return string
   */
  public function getHostname()
  {
    return $this->hostname;
  }
  /**
   * Optional. A map of labels to associate with the VM.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Required. The machine type to create the VM with.
   *
   * @param string $machineType
   */
  public function setMachineType($machineType)
  {
    $this->machineType = $machineType;
  }
  /**
   * @return string
   */
  public function getMachineType()
  {
    return $this->machineType;
  }
  /**
   * Optional. The machine type series to create the VM with. For presentation
   * only.
   *
   * @param string $machineTypeSeries
   */
  public function setMachineTypeSeries($machineTypeSeries)
  {
    $this->machineTypeSeries = $machineTypeSeries;
  }
  /**
   * @return string
   */
  public function getMachineTypeSeries()
  {
    return $this->machineTypeSeries;
  }
  /**
   * Optional. The metadata key/value pairs to assign to the VM.
   *
   * @param string[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return string[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Optional. NICs to attach to the VM.
   *
   * @param NetworkInterface[] $networkInterfaces
   */
  public function setNetworkInterfaces($networkInterfaces)
  {
    $this->networkInterfaces = $networkInterfaces;
  }
  /**
   * @return NetworkInterface[]
   */
  public function getNetworkInterfaces()
  {
    return $this->networkInterfaces;
  }
  /**
   * Optional. A list of network tags to associate with the VM.
   *
   * @param string[] $networkTags
   */
  public function setNetworkTags($networkTags)
  {
    $this->networkTags = $networkTags;
  }
  /**
   * @return string[]
   */
  public function getNetworkTags()
  {
    return $this->networkTags;
  }
  /**
   * Optional. Defines whether the instance has Secure Boot enabled. This can be
   * set to true only if the VM boot option is EFI.
   *
   * @param bool $secureBoot
   */
  public function setSecureBoot($secureBoot)
  {
    $this->secureBoot = $secureBoot;
  }
  /**
   * @return bool
   */
  public function getSecureBoot()
  {
    return $this->secureBoot;
  }
  /**
   * Optional. The service account to associate the VM with.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Required. The name of the VM to create.
   *
   * @param string $vmName
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DisksMigrationVmTargetDefaults::class, 'Google_Service_VMMigrationService_DisksMigrationVmTargetDefaults');
