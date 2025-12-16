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

namespace Google\Service\FirebaseDynamicLinks;

class AndroidInfo extends \Google\Model
{
  /**
   * Link to open on Android if the app is not installed.
   *
   * @var string
   */
  public $androidFallbackLink;
  /**
   * If specified, this overrides the ‘link’ parameter on Android.
   *
   * @var string
   */
  public $androidLink;
  /**
   * Minimum version code for the Android app. If the installed app’s version
   * code is lower, then the user is taken to the Play Store.
   *
   * @var string
   */
  public $androidMinPackageVersionCode;
  /**
   * Android package name of the app.
   *
   * @var string
   */
  public $androidPackageName;

  /**
   * Link to open on Android if the app is not installed.
   *
   * @param string $androidFallbackLink
   */
  public function setAndroidFallbackLink($androidFallbackLink)
  {
    $this->androidFallbackLink = $androidFallbackLink;
  }
  /**
   * @return string
   */
  public function getAndroidFallbackLink()
  {
    return $this->androidFallbackLink;
  }
  /**
   * If specified, this overrides the ‘link’ parameter on Android.
   *
   * @param string $androidLink
   */
  public function setAndroidLink($androidLink)
  {
    $this->androidLink = $androidLink;
  }
  /**
   * @return string
   */
  public function getAndroidLink()
  {
    return $this->androidLink;
  }
  /**
   * Minimum version code for the Android app. If the installed app’s version
   * code is lower, then the user is taken to the Play Store.
   *
   * @param string $androidMinPackageVersionCode
   */
  public function setAndroidMinPackageVersionCode($androidMinPackageVersionCode)
  {
    $this->androidMinPackageVersionCode = $androidMinPackageVersionCode;
  }
  /**
   * @return string
   */
  public function getAndroidMinPackageVersionCode()
  {
    return $this->androidMinPackageVersionCode;
  }
  /**
   * Android package name of the app.
   *
   * @param string $androidPackageName
   */
  public function setAndroidPackageName($androidPackageName)
  {
    $this->androidPackageName = $androidPackageName;
  }
  /**
   * @return string
   */
  public function getAndroidPackageName()
  {
    return $this->androidPackageName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AndroidInfo::class, 'Google_Service_FirebaseDynamicLinks_AndroidInfo');
