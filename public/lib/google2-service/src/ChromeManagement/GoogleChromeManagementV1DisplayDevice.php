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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1DisplayDevice extends \Google\Model
{
  /**
   * Output only. Display height in millimeters.
   *
   * @var int
   */
  public $displayHeightMm;
  /**
   * Output only. Display device name.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Display width in millimeters.
   *
   * @var int
   */
  public $displayWidthMm;
  /**
   * Output only. EDID version.
   *
   * @var string
   */
  public $edidVersion;
  /**
   * Output only. Is display internal or not.
   *
   * @var bool
   */
  public $internal;
  /**
   * Output only. Year of manufacture.
   *
   * @var int
   */
  public $manufactureYear;
  /**
   * Output only. Three letter manufacturer ID.
   *
   * @var string
   */
  public $manufacturerId;
  /**
   * Output only. Manufacturer product code.
   *
   * @var int
   */
  public $modelId;
  /**
   * Output only. Serial number.
   *
   * @var int
   */
  public $serialNumber;

  /**
   * Output only. Display height in millimeters.
   *
   * @param int $displayHeightMm
   */
  public function setDisplayHeightMm($displayHeightMm)
  {
    $this->displayHeightMm = $displayHeightMm;
  }
  /**
   * @return int
   */
  public function getDisplayHeightMm()
  {
    return $this->displayHeightMm;
  }
  /**
   * Output only. Display device name.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. Display width in millimeters.
   *
   * @param int $displayWidthMm
   */
  public function setDisplayWidthMm($displayWidthMm)
  {
    $this->displayWidthMm = $displayWidthMm;
  }
  /**
   * @return int
   */
  public function getDisplayWidthMm()
  {
    return $this->displayWidthMm;
  }
  /**
   * Output only. EDID version.
   *
   * @param string $edidVersion
   */
  public function setEdidVersion($edidVersion)
  {
    $this->edidVersion = $edidVersion;
  }
  /**
   * @return string
   */
  public function getEdidVersion()
  {
    return $this->edidVersion;
  }
  /**
   * Output only. Is display internal or not.
   *
   * @param bool $internal
   */
  public function setInternal($internal)
  {
    $this->internal = $internal;
  }
  /**
   * @return bool
   */
  public function getInternal()
  {
    return $this->internal;
  }
  /**
   * Output only. Year of manufacture.
   *
   * @param int $manufactureYear
   */
  public function setManufactureYear($manufactureYear)
  {
    $this->manufactureYear = $manufactureYear;
  }
  /**
   * @return int
   */
  public function getManufactureYear()
  {
    return $this->manufactureYear;
  }
  /**
   * Output only. Three letter manufacturer ID.
   *
   * @param string $manufacturerId
   */
  public function setManufacturerId($manufacturerId)
  {
    $this->manufacturerId = $manufacturerId;
  }
  /**
   * @return string
   */
  public function getManufacturerId()
  {
    return $this->manufacturerId;
  }
  /**
   * Output only. Manufacturer product code.
   *
   * @param int $modelId
   */
  public function setModelId($modelId)
  {
    $this->modelId = $modelId;
  }
  /**
   * @return int
   */
  public function getModelId()
  {
    return $this->modelId;
  }
  /**
   * Output only. Serial number.
   *
   * @param int $serialNumber
   */
  public function setSerialNumber($serialNumber)
  {
    $this->serialNumber = $serialNumber;
  }
  /**
   * @return int
   */
  public function getSerialNumber()
  {
    return $this->serialNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1DisplayDevice::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1DisplayDevice');
