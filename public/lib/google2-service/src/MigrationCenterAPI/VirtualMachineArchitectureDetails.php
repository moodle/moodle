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

class VirtualMachineArchitectureDetails extends \Google\Model
{
  protected $biosType = BiosDetails::class;
  protected $biosDataType = '';
  /**
   * @var string
   */
  public $cpuArchitecture;
  /**
   * @var string
   */
  public $cpuManufacturer;
  /**
   * @var string
   */
  public $cpuName;
  /**
   * @var int
   */
  public $cpuSocketCount;
  /**
   * @var int
   */
  public $cpuThreadCount;
  /**
   * @var string
   */
  public $firmware;
  /**
   * @var string
   */
  public $hyperthreading;
  /**
   * @var string
   */
  public $vendor;

  /**
   * @param BiosDetails
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
   * @param string
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
   * @param string
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
   * @param string
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
   * @param int
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
   * @param int
   */
  public function setCpuThreadCount($cpuThreadCount)
  {
    $this->cpuThreadCount = $cpuThreadCount;
  }
  /**
   * @return int
   */
  public function getCpuThreadCount()
  {
    return $this->cpuThreadCount;
  }
  /**
   * @param string
   */
  public function setFirmware($firmware)
  {
    $this->firmware = $firmware;
  }
  /**
   * @return string
   */
  public function getFirmware()
  {
    return $this->firmware;
  }
  /**
   * @param string
   */
  public function setHyperthreading($hyperthreading)
  {
    $this->hyperthreading = $hyperthreading;
  }
  /**
   * @return string
   */
  public function getHyperthreading()
  {
    return $this->hyperthreading;
  }
  /**
   * @param string
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
class_alias(VirtualMachineArchitectureDetails::class, 'Google_Service_MigrationCenterAPI_VirtualMachineArchitectureDetails');
