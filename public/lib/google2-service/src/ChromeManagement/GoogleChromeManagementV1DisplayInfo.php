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

class GoogleChromeManagementV1DisplayInfo extends \Google\Model
{
  /**
   * Output only. Represents the graphics card device id.
   *
   * @var string
   */
  public $deviceId;
  /**
   * Output only. Display device name.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. EDID version.
   *
   * @var string
   */
  public $edidVersion;
  /**
   * Output only. Indicates if display is internal or not.
   *
   * @var bool
   */
  public $isInternal;
  /**
   * Output only. Refresh rate in Hz.
   *
   * @var int
   */
  public $refreshRate;
  /**
   * Output only. Resolution height in pixels.
   *
   * @var int
   */
  public $resolutionHeight;
  /**
   * Output only. Resolution width in pixels.
   *
   * @var int
   */
  public $resolutionWidth;
  /**
   * Output only. Serial number.
   *
   * @var int
   */
  public $serialNumber;

  /**
   * Output only. Represents the graphics card device id.
   *
   * @param string $deviceId
   */
  public function setDeviceId($deviceId)
  {
    $this->deviceId = $deviceId;
  }
  /**
   * @return string
   */
  public function getDeviceId()
  {
    return $this->deviceId;
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
   * Output only. Indicates if display is internal or not.
   *
   * @param bool $isInternal
   */
  public function setIsInternal($isInternal)
  {
    $this->isInternal = $isInternal;
  }
  /**
   * @return bool
   */
  public function getIsInternal()
  {
    return $this->isInternal;
  }
  /**
   * Output only. Refresh rate in Hz.
   *
   * @param int $refreshRate
   */
  public function setRefreshRate($refreshRate)
  {
    $this->refreshRate = $refreshRate;
  }
  /**
   * @return int
   */
  public function getRefreshRate()
  {
    return $this->refreshRate;
  }
  /**
   * Output only. Resolution height in pixels.
   *
   * @param int $resolutionHeight
   */
  public function setResolutionHeight($resolutionHeight)
  {
    $this->resolutionHeight = $resolutionHeight;
  }
  /**
   * @return int
   */
  public function getResolutionHeight()
  {
    return $this->resolutionHeight;
  }
  /**
   * Output only. Resolution width in pixels.
   *
   * @param int $resolutionWidth
   */
  public function setResolutionWidth($resolutionWidth)
  {
    $this->resolutionWidth = $resolutionWidth;
  }
  /**
   * @return int
   */
  public function getResolutionWidth()
  {
    return $this->resolutionWidth;
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
class_alias(GoogleChromeManagementV1DisplayInfo::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1DisplayInfo');
