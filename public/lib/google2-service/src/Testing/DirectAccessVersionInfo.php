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

namespace Google\Service\Testing;

class DirectAccessVersionInfo extends \Google\Model
{
  /**
   * Whether direct access is supported at all. Clients are expected to filter
   * down the device list to only android models and versions which support
   * Direct Access when that is the user intent.
   *
   * @var bool
   */
  public $directAccessSupported;
  /**
   * Output only. Indicates client-device compatibility, where a device is known
   * to work only with certain workarounds implemented in the Android Studio
   * client. Expected format "major.minor.micro.patch", e.g.
   * "5921.22.2211.8881706".
   *
   * @var string
   */
  public $minimumAndroidStudioVersion;

  /**
   * Whether direct access is supported at all. Clients are expected to filter
   * down the device list to only android models and versions which support
   * Direct Access when that is the user intent.
   *
   * @param bool $directAccessSupported
   */
  public function setDirectAccessSupported($directAccessSupported)
  {
    $this->directAccessSupported = $directAccessSupported;
  }
  /**
   * @return bool
   */
  public function getDirectAccessSupported()
  {
    return $this->directAccessSupported;
  }
  /**
   * Output only. Indicates client-device compatibility, where a device is known
   * to work only with certain workarounds implemented in the Android Studio
   * client. Expected format "major.minor.micro.patch", e.g.
   * "5921.22.2211.8881706".
   *
   * @param string $minimumAndroidStudioVersion
   */
  public function setMinimumAndroidStudioVersion($minimumAndroidStudioVersion)
  {
    $this->minimumAndroidStudioVersion = $minimumAndroidStudioVersion;
  }
  /**
   * @return string
   */
  public function getMinimumAndroidStudioVersion()
  {
    return $this->minimumAndroidStudioVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DirectAccessVersionInfo::class, 'Google_Service_Testing_DirectAccessVersionInfo');
