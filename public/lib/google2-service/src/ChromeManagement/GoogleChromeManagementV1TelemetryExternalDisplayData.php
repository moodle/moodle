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

class GoogleChromeManagementV1TelemetryExternalDisplayData extends \Google\Model
{
  /**
   * The display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * The EDID version.
   *
   * @var string
   */
  public $edidVersion;
  /**
   * The refresh rate.
   *
   * @var string
   */
  public $refreshRate;
  /**
   * The horizontal resolution.
   *
   * @var int
   */
  public $resolutionHorizontal;
  /**
   * The vertical resolution.
   *
   * @var int
   */
  public $resolutionVertical;
  /**
   * The serial number.
   *
   * @var int
   */
  public $serialNumber;

  /**
   * The display name.
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
   * The EDID version.
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
   * The refresh rate.
   *
   * @param string $refreshRate
   */
  public function setRefreshRate($refreshRate)
  {
    $this->refreshRate = $refreshRate;
  }
  /**
   * @return string
   */
  public function getRefreshRate()
  {
    return $this->refreshRate;
  }
  /**
   * The horizontal resolution.
   *
   * @param int $resolutionHorizontal
   */
  public function setResolutionHorizontal($resolutionHorizontal)
  {
    $this->resolutionHorizontal = $resolutionHorizontal;
  }
  /**
   * @return int
   */
  public function getResolutionHorizontal()
  {
    return $this->resolutionHorizontal;
  }
  /**
   * The vertical resolution.
   *
   * @param int $resolutionVertical
   */
  public function setResolutionVertical($resolutionVertical)
  {
    $this->resolutionVertical = $resolutionVertical;
  }
  /**
   * @return int
   */
  public function getResolutionVertical()
  {
    return $this->resolutionVertical;
  }
  /**
   * The serial number.
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
class_alias(GoogleChromeManagementV1TelemetryExternalDisplayData::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1TelemetryExternalDisplayData');
