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

class GoogleChromeManagementV1CpuInfo extends \Google\Model
{
  /**
   * Architecture unknown.
   */
  public const ARCHITECTURE_ARCHITECTURE_UNSPECIFIED = 'ARCHITECTURE_UNSPECIFIED';
  /**
   * x64 architecture
   */
  public const ARCHITECTURE_X64 = 'X64';
  /**
   * Output only. Architecture type for the CPU. * This field provides device
   * information, which is static and will not change over time. * Data for this
   * field is controlled via policy: [ReportDeviceCpuInfo](https://chromeenterpr
   * ise.google/policies/#ReportDeviceCpuInfo) * Data Collection Frequency: Only
   * at Upload * Default Data Reporting Frequency: 3 hours - Policy Controlled:
   * Yes * Cache: If the device is offline, the collected data is stored
   * locally, and will be reported when the device is next online: No * Reported
   * for affiliated users only: N/A
   *
   * @var string
   */
  public $architecture;
  /**
   * Output only. Whether keylocker is configured.`TRUE` = Enabled; `FALSE` =
   * disabled. Only reported if keylockerSupported = `TRUE`.
   *
   * @var bool
   */
  public $keylockerConfigured;
  /**
   * Output only. Whether keylocker is supported.
   *
   * @var bool
   */
  public $keylockerSupported;
  /**
   * Output only. The max CPU clock speed in kHz.
   *
   * @var int
   */
  public $maxClockSpeed;
  /**
   * Output only. The CPU model name. Example: Intel(R) Core(TM) i5-8250U CPU @
   * 1.60GHz
   *
   * @var string
   */
  public $model;

  /**
   * Output only. Architecture type for the CPU. * This field provides device
   * information, which is static and will not change over time. * Data for this
   * field is controlled via policy: [ReportDeviceCpuInfo](https://chromeenterpr
   * ise.google/policies/#ReportDeviceCpuInfo) * Data Collection Frequency: Only
   * at Upload * Default Data Reporting Frequency: 3 hours - Policy Controlled:
   * Yes * Cache: If the device is offline, the collected data is stored
   * locally, and will be reported when the device is next online: No * Reported
   * for affiliated users only: N/A
   *
   * Accepted values: ARCHITECTURE_UNSPECIFIED, X64
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
   * Output only. Whether keylocker is configured.`TRUE` = Enabled; `FALSE` =
   * disabled. Only reported if keylockerSupported = `TRUE`.
   *
   * @param bool $keylockerConfigured
   */
  public function setKeylockerConfigured($keylockerConfigured)
  {
    $this->keylockerConfigured = $keylockerConfigured;
  }
  /**
   * @return bool
   */
  public function getKeylockerConfigured()
  {
    return $this->keylockerConfigured;
  }
  /**
   * Output only. Whether keylocker is supported.
   *
   * @param bool $keylockerSupported
   */
  public function setKeylockerSupported($keylockerSupported)
  {
    $this->keylockerSupported = $keylockerSupported;
  }
  /**
   * @return bool
   */
  public function getKeylockerSupported()
  {
    return $this->keylockerSupported;
  }
  /**
   * Output only. The max CPU clock speed in kHz.
   *
   * @param int $maxClockSpeed
   */
  public function setMaxClockSpeed($maxClockSpeed)
  {
    $this->maxClockSpeed = $maxClockSpeed;
  }
  /**
   * @return int
   */
  public function getMaxClockSpeed()
  {
    return $this->maxClockSpeed;
  }
  /**
   * Output only. The CPU model name. Example: Intel(R) Core(TM) i5-8250U CPU @
   * 1.60GHz
   *
   * @param string $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1CpuInfo::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1CpuInfo');
