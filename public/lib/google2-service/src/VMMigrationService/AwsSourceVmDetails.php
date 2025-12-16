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

class AwsSourceVmDetails extends \Google\Collection
{
  /**
   * The architecture is unknown.
   */
  public const ARCHITECTURE_VM_ARCHITECTURE_UNSPECIFIED = 'VM_ARCHITECTURE_UNSPECIFIED';
  /**
   * The architecture is one of the x86 architectures.
   */
  public const ARCHITECTURE_VM_ARCHITECTURE_X86_FAMILY = 'VM_ARCHITECTURE_X86_FAMILY';
  /**
   * The architecture is ARM64.
   */
  public const ARCHITECTURE_VM_ARCHITECTURE_ARM64 = 'VM_ARCHITECTURE_ARM64';
  /**
   * The firmware is unknown.
   */
  public const FIRMWARE_FIRMWARE_UNSPECIFIED = 'FIRMWARE_UNSPECIFIED';
  /**
   * The firmware is EFI.
   */
  public const FIRMWARE_EFI = 'EFI';
  /**
   * The firmware is BIOS.
   */
  public const FIRMWARE_BIOS = 'BIOS';
  protected $collection_key = 'disks';
  /**
   * Output only. The VM architecture.
   *
   * @var string
   */
  public $architecture;
  /**
   * Output only. The total size of the disks being migrated in bytes.
   *
   * @var string
   */
  public $committedStorageBytes;
  protected $disksType = AwsDiskDetails::class;
  protected $disksDataType = 'array';
  /**
   * Output only. The firmware type of the source VM.
   *
   * @var string
   */
  public $firmware;
  protected $vmCapabilitiesInfoType = VmCapabilities::class;
  protected $vmCapabilitiesInfoDataType = '';

  /**
   * Output only. The VM architecture.
   *
   * Accepted values: VM_ARCHITECTURE_UNSPECIFIED, VM_ARCHITECTURE_X86_FAMILY,
   * VM_ARCHITECTURE_ARM64
   *
   * @param self::ARCHITECTURE_* $architecture
   */
  public function setArchitecture($architecture)
  {
    $this->architecture = $architecture;
  }
  /**
   * @return self::ARCHITECTURE_*
   */
  public function getArchitecture()
  {
    return $this->architecture;
  }
  /**
   * Output only. The total size of the disks being migrated in bytes.
   *
   * @param string $committedStorageBytes
   */
  public function setCommittedStorageBytes($committedStorageBytes)
  {
    $this->committedStorageBytes = $committedStorageBytes;
  }
  /**
   * @return string
   */
  public function getCommittedStorageBytes()
  {
    return $this->committedStorageBytes;
  }
  /**
   * Output only. The disks attached to the source VM.
   *
   * @param AwsDiskDetails[] $disks
   */
  public function setDisks($disks)
  {
    $this->disks = $disks;
  }
  /**
   * @return AwsDiskDetails[]
   */
  public function getDisks()
  {
    return $this->disks;
  }
  /**
   * Output only. The firmware type of the source VM.
   *
   * Accepted values: FIRMWARE_UNSPECIFIED, EFI, BIOS
   *
   * @param self::FIRMWARE_* $firmware
   */
  public function setFirmware($firmware)
  {
    $this->firmware = $firmware;
  }
  /**
   * @return self::FIRMWARE_*
   */
  public function getFirmware()
  {
    return $this->firmware;
  }
  /**
   * Output only. Information about VM capabilities needed for some Compute
   * Engine features.
   *
   * @param VmCapabilities $vmCapabilitiesInfo
   */
  public function setVmCapabilitiesInfo(VmCapabilities $vmCapabilitiesInfo)
  {
    $this->vmCapabilitiesInfo = $vmCapabilitiesInfo;
  }
  /**
   * @return VmCapabilities
   */
  public function getVmCapabilitiesInfo()
  {
    return $this->vmCapabilitiesInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AwsSourceVmDetails::class, 'Google_Service_VMMigrationService_AwsSourceVmDetails');
