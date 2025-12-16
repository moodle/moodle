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

namespace Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1betaDataStreamAndroidAppStreamData extends \Google\Model
{
  /**
   * Output only. ID of the corresponding Android app in Firebase, if any. This
   * ID can change if the Android app is deleted and recreated.
   *
   * @var string
   */
  public $firebaseAppId;
  /**
   * Immutable. The package name for the app being measured. Example:
   * "com.example.myandroidapp"
   *
   * @var string
   */
  public $packageName;

  /**
   * Output only. ID of the corresponding Android app in Firebase, if any. This
   * ID can change if the Android app is deleted and recreated.
   *
   * @param string $firebaseAppId
   */
  public function setFirebaseAppId($firebaseAppId)
  {
    $this->firebaseAppId = $firebaseAppId;
  }
  /**
   * @return string
   */
  public function getFirebaseAppId()
  {
    return $this->firebaseAppId;
  }
  /**
   * Immutable. The package name for the app being measured. Example:
   * "com.example.myandroidapp"
   *
   * @param string $packageName
   */
  public function setPackageName($packageName)
  {
    $this->packageName = $packageName;
  }
  /**
   * @return string
   */
  public function getPackageName()
  {
    return $this->packageName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAnalyticsAdminV1betaDataStreamAndroidAppStreamData::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaDataStreamAndroidAppStreamData');
