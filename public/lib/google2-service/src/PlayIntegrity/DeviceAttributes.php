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

namespace Google\Service\PlayIntegrity;

class DeviceAttributes extends \Google\Model
{
  /**
   * Android SDK version of the device, as defined in the public Android
   * documentation:
   * https://developer.android.com/reference/android/os/Build.VERSION_CODES. It
   * won't be set if a necessary requirement was missed. For example
   * DeviceIntegrity did not meet the minimum bar.
   *
   * @var int
   */
  public $sdkVersion;

  /**
   * Android SDK version of the device, as defined in the public Android
   * documentation:
   * https://developer.android.com/reference/android/os/Build.VERSION_CODES. It
   * won't be set if a necessary requirement was missed. For example
   * DeviceIntegrity did not meet the minimum bar.
   *
   * @param int $sdkVersion
   */
  public function setSdkVersion($sdkVersion)
  {
    $this->sdkVersion = $sdkVersion;
  }
  /**
   * @return int
   */
  public function getSdkVersion()
  {
    return $this->sdkVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceAttributes::class, 'Google_Service_PlayIntegrity_DeviceAttributes');
