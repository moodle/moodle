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

class GoogleChromeManagementV1BrowserVersion extends \Google\Model
{
  /**
   * No release channel specified.
   */
  public const CHANNEL_RELEASE_CHANNEL_UNSPECIFIED = 'RELEASE_CHANNEL_UNSPECIFIED';
  /**
   * Canary release channel.
   */
  public const CHANNEL_CANARY = 'CANARY';
  /**
   * Dev release channel.
   */
  public const CHANNEL_DEV = 'DEV';
  /**
   * Beta release channel.
   */
  public const CHANNEL_BETA = 'BETA';
  /**
   * Stable release channel.
   */
  public const CHANNEL_STABLE = 'STABLE';
  /**
   * No operating system specified.
   */
  public const SYSTEM_DEVICE_SYSTEM_UNSPECIFIED = 'DEVICE_SYSTEM_UNSPECIFIED';
  /**
   * Other operating system.
   */
  public const SYSTEM_SYSTEM_OTHER = 'SYSTEM_OTHER';
  /**
   * Android operating system.
   */
  public const SYSTEM_SYSTEM_ANDROID = 'SYSTEM_ANDROID';
  /**
   * Apple iOS operating system.
   */
  public const SYSTEM_SYSTEM_IOS = 'SYSTEM_IOS';
  /**
   * ChromeOS operating system.
   */
  public const SYSTEM_SYSTEM_CROS = 'SYSTEM_CROS';
  /**
   * Microsoft Windows operating system.
   */
  public const SYSTEM_SYSTEM_WINDOWS = 'SYSTEM_WINDOWS';
  /**
   * Apple macOS operating system.
   */
  public const SYSTEM_SYSTEM_MAC = 'SYSTEM_MAC';
  /**
   * Linux operating system.
   */
  public const SYSTEM_SYSTEM_LINUX = 'SYSTEM_LINUX';
  /**
   * Output only. The release channel of the installed browser.
   *
   * @var string
   */
  public $channel;
  /**
   * Output only. Count grouped by device_system and major version
   *
   * @var string
   */
  public $count;
  /**
   * Output only. Version of the system-specified operating system.
   *
   * @var string
   */
  public $deviceOsVersion;
  /**
   * Output only. The device operating system.
   *
   * @var string
   */
  public $system;
  /**
   * Output only. The full version of the installed browser.
   *
   * @var string
   */
  public $version;

  /**
   * Output only. The release channel of the installed browser.
   *
   * Accepted values: RELEASE_CHANNEL_UNSPECIFIED, CANARY, DEV, BETA, STABLE
   *
   * @param self::CHANNEL_* $channel
   */
  public function setChannel($channel)
  {
    $this->channel = $channel;
  }
  /**
   * @return self::CHANNEL_*
   */
  public function getChannel()
  {
    return $this->channel;
  }
  /**
   * Output only. Count grouped by device_system and major version
   *
   * @param string $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return string
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * Output only. Version of the system-specified operating system.
   *
   * @param string $deviceOsVersion
   */
  public function setDeviceOsVersion($deviceOsVersion)
  {
    $this->deviceOsVersion = $deviceOsVersion;
  }
  /**
   * @return string
   */
  public function getDeviceOsVersion()
  {
    return $this->deviceOsVersion;
  }
  /**
   * Output only. The device operating system.
   *
   * Accepted values: DEVICE_SYSTEM_UNSPECIFIED, SYSTEM_OTHER, SYSTEM_ANDROID,
   * SYSTEM_IOS, SYSTEM_CROS, SYSTEM_WINDOWS, SYSTEM_MAC, SYSTEM_LINUX
   *
   * @param self::SYSTEM_* $system
   */
  public function setSystem($system)
  {
    $this->system = $system;
  }
  /**
   * @return self::SYSTEM_*
   */
  public function getSystem()
  {
    return $this->system;
  }
  /**
   * Output only. The full version of the installed browser.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1BrowserVersion::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1BrowserVersion');
