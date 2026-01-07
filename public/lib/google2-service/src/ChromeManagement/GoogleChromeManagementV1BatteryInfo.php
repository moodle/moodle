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

class GoogleChromeManagementV1BatteryInfo extends \Google\Model
{
  /**
   * Output only. Design capacity (mAmpere-hours).
   *
   * @var string
   */
  public $designCapacity;
  /**
   * Output only. Designed minimum output voltage (mV)
   *
   * @var int
   */
  public $designMinVoltage;
  protected $manufactureDateType = GoogleTypeDate::class;
  protected $manufactureDateDataType = '';
  /**
   * Output only. Battery manufacturer.
   *
   * @var string
   */
  public $manufacturer;
  /**
   * Output only. Battery serial number.
   *
   * @var string
   */
  public $serialNumber;
  /**
   * Output only. Technology of the battery. Example: Li-ion
   *
   * @var string
   */
  public $technology;

  /**
   * Output only. Design capacity (mAmpere-hours).
   *
   * @param string $designCapacity
   */
  public function setDesignCapacity($designCapacity)
  {
    $this->designCapacity = $designCapacity;
  }
  /**
   * @return string
   */
  public function getDesignCapacity()
  {
    return $this->designCapacity;
  }
  /**
   * Output only. Designed minimum output voltage (mV)
   *
   * @param int $designMinVoltage
   */
  public function setDesignMinVoltage($designMinVoltage)
  {
    $this->designMinVoltage = $designMinVoltage;
  }
  /**
   * @return int
   */
  public function getDesignMinVoltage()
  {
    return $this->designMinVoltage;
  }
  /**
   * Output only. The date the battery was manufactured.
   *
   * @param GoogleTypeDate $manufactureDate
   */
  public function setManufactureDate(GoogleTypeDate $manufactureDate)
  {
    $this->manufactureDate = $manufactureDate;
  }
  /**
   * @return GoogleTypeDate
   */
  public function getManufactureDate()
  {
    return $this->manufactureDate;
  }
  /**
   * Output only. Battery manufacturer.
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
   * Output only. Battery serial number.
   *
   * @param string $serialNumber
   */
  public function setSerialNumber($serialNumber)
  {
    $this->serialNumber = $serialNumber;
  }
  /**
   * @return string
   */
  public function getSerialNumber()
  {
    return $this->serialNumber;
  }
  /**
   * Output only. Technology of the battery. Example: Li-ion
   *
   * @param string $technology
   */
  public function setTechnology($technology)
  {
    $this->technology = $technology;
  }
  /**
   * @return string
   */
  public function getTechnology()
  {
    return $this->technology;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1BatteryInfo::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1BatteryInfo');
