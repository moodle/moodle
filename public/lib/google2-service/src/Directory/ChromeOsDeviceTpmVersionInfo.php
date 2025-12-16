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

namespace Google\Service\Directory;

class ChromeOsDeviceTpmVersionInfo extends \Google\Model
{
  /**
   * TPM family. We use the TPM 2.0 style encoding, e.g.: TPM 1.2: "1.2" ->
   * 312e3200 TPM 2.0: "2.0" -> 322e3000
   *
   * @var string
   */
  public $family;
  /**
   * TPM firmware version.
   *
   * @var string
   */
  public $firmwareVersion;
  /**
   * TPM manufacturer code.
   *
   * @var string
   */
  public $manufacturer;
  /**
   * TPM specification level. See Library Specification for TPM 2.0 and Main
   * Specification for TPM 1.2.
   *
   * @var string
   */
  public $specLevel;
  /**
   * TPM model number.
   *
   * @var string
   */
  public $tpmModel;
  /**
   * Vendor-specific information such as Vendor ID.
   *
   * @var string
   */
  public $vendorSpecific;

  /**
   * TPM family. We use the TPM 2.0 style encoding, e.g.: TPM 1.2: "1.2" ->
   * 312e3200 TPM 2.0: "2.0" -> 322e3000
   *
   * @param string $family
   */
  public function setFamily($family)
  {
    $this->family = $family;
  }
  /**
   * @return string
   */
  public function getFamily()
  {
    return $this->family;
  }
  /**
   * TPM firmware version.
   *
   * @param string $firmwareVersion
   */
  public function setFirmwareVersion($firmwareVersion)
  {
    $this->firmwareVersion = $firmwareVersion;
  }
  /**
   * @return string
   */
  public function getFirmwareVersion()
  {
    return $this->firmwareVersion;
  }
  /**
   * TPM manufacturer code.
   *
   * @param string $manufacturer
   */
  public function setManufacturer($manufacturer)
  {
    $this->manufacturer = $manufacturer;
  }
  /**
   * @return string
   */
  public function getManufacturer()
  {
    return $this->manufacturer;
  }
  /**
   * TPM specification level. See Library Specification for TPM 2.0 and Main
   * Specification for TPM 1.2.
   *
   * @param string $specLevel
   */
  public function setSpecLevel($specLevel)
  {
    $this->specLevel = $specLevel;
  }
  /**
   * @return string
   */
  public function getSpecLevel()
  {
    return $this->specLevel;
  }
  /**
   * TPM model number.
   *
   * @param string $tpmModel
   */
  public function setTpmModel($tpmModel)
  {
    $this->tpmModel = $tpmModel;
  }
  /**
   * @return string
   */
  public function getTpmModel()
  {
    return $this->tpmModel;
  }
  /**
   * Vendor-specific information such as Vendor ID.
   *
   * @param string $vendorSpecific
   */
  public function setVendorSpecific($vendorSpecific)
  {
    $this->vendorSpecific = $vendorSpecific;
  }
  /**
   * @return string
   */
  public function getVendorSpecific()
  {
    return $this->vendorSpecific;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChromeOsDeviceTpmVersionInfo::class, 'Google_Service_Directory_ChromeOsDeviceTpmVersionInfo');
