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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0CommonFinalAppUrl extends \Google\Model
{
  /**
   * Not specified.
   */
  public const OS_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const OS_TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * The Apple IOS operating system.
   */
  public const OS_TYPE_IOS = 'IOS';
  /**
   * The Android operating system.
   */
  public const OS_TYPE_ANDROID = 'ANDROID';
  /**
   * The operating system targeted by this URL. Required.
   *
   * @var string
   */
  public $osType;
  /**
   * The app deep link URL. Deep links specify a location in an app that
   * corresponds to the content you'd like to show, and should be of the form
   * {scheme}://{host_path} The scheme identifies which app to open. For your
   * app, you can use a custom scheme that starts with the app's name. The host
   * and path specify the unique location in the app where your content exists.
   * Example: "exampleapp://productid_1234". Required.
   *
   * @var string
   */
  public $url;

  /**
   * The operating system targeted by this URL. Required.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, IOS, ANDROID
   *
   * @param self::OS_TYPE_* $osType
   */
  public function setOsType($osType)
  {
    $this->osType = $osType;
  }
  /**
   * @return self::OS_TYPE_*
   */
  public function getOsType()
  {
    return $this->osType;
  }
  /**
   * The app deep link URL. Deep links specify a location in an app that
   * corresponds to the content you'd like to show, and should be of the form
   * {scheme}://{host_path} The scheme identifies which app to open. For your
   * app, you can use a custom scheme that starts with the app's name. The host
   * and path specify the unique location in the app where your content exists.
   * Example: "exampleapp://productid_1234". Required.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0CommonFinalAppUrl::class, 'Google_Service_SA360_GoogleAdsSearchads360V0CommonFinalAppUrl');
