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

class MachineArchitectureDetails extends \Google\Model
{
  /**
   * Unspecified or unknown.
   */
  public const FIRMWARE_TYPE_FIRMWARE_TYPE_UNSPECIFIED = 'FIRMWARE_TYPE_UNSPECIFIED';
  /**
   * BIOS firmware.
   */
  public const FIRMWARE_TYPE_BIOS = 'BIOS';
  /**
   * EFI firmware.
   */
  public const FIRMWARE_TYPE_EFI = 'EFI';
  /**
   * Unspecified or unknown.
   */
  public const HYPERTHREADING_CPU_HYPER_THREADING_UNSPECIFIED = 'CPU_HYPER_THREADING_UNSPECIFIED';
  /**
   * Hyper-threading is disabled.
   */
  public const HYPERTHREADING_DISABLED = 'DISABLED';
  /**
   * Hyper-threading is enabled.
   */
  public const HYPERTHREADING_ENABLED = 'ENABLED';
  protected $biosType = BiosDetails::class;
  protected $biosDataType = '';
  /**
   * CPU architecture, e.g., "x64-based PC", "x86_64", "i686" etc.
   *
   * @var string
   */
  public $cpuArchitecture;
  /**
   * Optional. CPU manufacturer, e.g., "Intel", "AMD".
   *
   * @var string
   */
  public $cpuManufacturer;
  /**
   * CPU name, e.g., "Intel Xeon E5-2690", "AMD EPYC 7571" etc.
   *
   * @var string
   */
  public $cpuName;
  /**
   * Number of processor sockets allocated to the machine.
   *
   * @var int
   */
  public $cpuSocketCount;
  /**
   * Deprecated: use MachineDetails.core_count instead. Number of CPU threads
   * allocated to the machine.
   *
   * @deprecated
   * @var int
   */
  public $cpuThreadCount;
  /**
   * Firmware type.
   *
   * @var string
   */
  public $firmwareType;
  /**
   * CPU hyper-threading support.
   *
   * @var string
   */
  public $hyperthreading;
  /**
   * Hardware vendor.
   *
   * @var string
   */
  public $vendor;

  /**
   * BIOS Details.
   *
   * @param BiosDetails $bios
   */
  public function setBios(BiosDetails $bios)
  {
    $this->bios = $bios;
  }
  /**
   * @return BiosDetails
   */
  public function getBios()
  {
    return $this->bios;
  }
  /**
   * CPU architecture, e.g., "x64-based PC", "x86_64", "i686" etc.
   *
   * @param string $cpuArchitecture
   */
  public function setCpuArchitecture($cpuArchitecture)
  {
    $this->cpuArchitecture = $cpuArchitecture;
  }
  /**
   * @return string
   */
  public function getCpuArchitecture()
  {
    return $this->cpuArchitecture;
  }
  /**
   * Optional. CPU manufacturer, e.g., "Intel", "AMD".
   *
   * @param string $cpuManufacturer
   */
  public function setCpuManufacturer($cpuManufacturer)
  {
    $this->cpuManufacturer = $cpuManufacturer;
  }
  /**
   * @return string
   */
  public function getCpuManufacturer()
  {
    return $this->cpuManufacturer;
  }
  /**
   * CPU name, e.g., "Intel Xeon E5-2690", "AMD EPYC 7571" etc.
   *
   * @param string $cpuName
   */
  public function setCpuName($cpuName)
  {
    $this->cpuName = $cpuName;
  }
  /**
   * @return string
   */
  public function getCpuName()
  {
    return $this->cpuName;
  }
  /**
   * Number of processor sockets allocated to the machine.
   *
   * @param int $cpuSocketCount
   */
  public function setCpuSocketCount($cpuSocketCount)
  {
    $this->cpuSocketCount = $cpuSocketCount;
  }
  /**
   * @return int
   */
  public function getCpuSocketCount()
  {
    return $this->cpuSocketCount;
  }
  /**
   * Deprecated: use MachineDetails.core_count instead. Number of CPU threads
   * allocated to the machine.
   *
   * @deprecated
   * @param int $cpuThreadCount
   */
  public function setCpuThreadCount($cpuThreadCount)
  {
    $this->cpuThreadCount = $cpuThreadCount;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getCpuThreadCount()
  {
    return $this->cpuThreadCount;
  }
  /**
   * Firmware type.
   *
   * Accepted values: FIRMWARE_TYPE_UNSPECIFIED, BIOS, EFI
   *
   * @param self::FIRMWARE_TYPE_* $firmwareType
   */
  public function setFirmwareType($firmwareType)
  {
    $this->firmwareType = $firmwareType;
  }
  /**
   * @return self::FIRMWARE_TYPE_*
   */
  public function getFirmwareType()
  {
    return $this->firmwareType;
  }
  /**
   * CPU hyper-threading support.
   *
   * Accepted values: CPU_HYPER_THREADING_UNSPECIFIED, DISABLED, ENABLED
   *
   * @param self::HYPERTHREADING_* $hyperthreading
   */
  public function setHyperthreading($hyperthreading)
  {
    $this->hyperthreading = $hyperthreading;
  }
  /**
   * @return self::HYPERTHREADING_*
   */
  public function getHyperthreading()
  {
    return $this->hyperthreading;
  }
  /**
   * Hardware vendor.
   *
   * @param string $vendor
   */
  public function setVendor($vendor)
  {
    $this->vendor = $vendor;
  }
  /**
   * @return string
   */
  public function getVendor()
  {
    return $this->vendor;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MachineArchitectureDetails::class, 'Google_Service_MigrationCenterAPI_MachineArchitectureDetails');
