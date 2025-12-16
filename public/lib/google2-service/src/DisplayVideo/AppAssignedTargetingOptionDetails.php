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

namespace Google\Service\DisplayVideo;

class AppAssignedTargetingOptionDetails extends \Google\Model
{
  /**
   * Default value when app platform is not specified in this version. This enum
   * is a placeholder for default value and does not represent a real platform
   * option.
   */
  public const APP_PLATFORM_APP_PLATFORM_UNSPECIFIED = 'APP_PLATFORM_UNSPECIFIED';
  /**
   * The app platform is iOS.
   */
  public const APP_PLATFORM_APP_PLATFORM_IOS = 'APP_PLATFORM_IOS';
  /**
   * The app platform is Android.
   */
  public const APP_PLATFORM_APP_PLATFORM_ANDROID = 'APP_PLATFORM_ANDROID';
  /**
   * The app platform is Roku.
   */
  public const APP_PLATFORM_APP_PLATFORM_ROKU = 'APP_PLATFORM_ROKU';
  /**
   * The app platform is Amazon FireTV.
   */
  public const APP_PLATFORM_APP_PLATFORM_AMAZON_FIRETV = 'APP_PLATFORM_AMAZON_FIRETV';
  /**
   * The app platform is Playstation.
   */
  public const APP_PLATFORM_APP_PLATFORM_PLAYSTATION = 'APP_PLATFORM_PLAYSTATION';
  /**
   * The app platform is Apple TV.
   */
  public const APP_PLATFORM_APP_PLATFORM_APPLE_TV = 'APP_PLATFORM_APPLE_TV';
  /**
   * The app platform is Xbox.
   */
  public const APP_PLATFORM_APP_PLATFORM_XBOX = 'APP_PLATFORM_XBOX';
  /**
   * The app platform is Samsung TV.
   */
  public const APP_PLATFORM_APP_PLATFORM_SAMSUNG_TV = 'APP_PLATFORM_SAMSUNG_TV';
  /**
   * The app platform is Android TV.
   */
  public const APP_PLATFORM_APP_PLATFORM_ANDROID_TV = 'APP_PLATFORM_ANDROID_TV';
  /**
   * The app platform is a CTV platform that is not explicitly listed elsewhere.
   */
  public const APP_PLATFORM_APP_PLATFORM_GENERIC_CTV = 'APP_PLATFORM_GENERIC_CTV';
  /**
   * The app platform is LG TV.
   */
  public const APP_PLATFORM_APP_PLATFORM_LG_TV = 'APP_PLATFORM_LG_TV';
  /**
   * The app platform is VIZIO TV.
   */
  public const APP_PLATFORM_APP_PLATFORM_VIZIO_TV = 'APP_PLATFORM_VIZIO_TV';
  /**
   * The app platform is Vidaa.
   */
  public const APP_PLATFORM_APP_PLATFORM_VIDAA = 'APP_PLATFORM_VIDAA';
  /**
   * Required. The ID of the app. Android's Play store app uses bundle ID, for
   * example `com.google.android.gm`. Apple's App store app ID uses 9 digit
   * string, for example `422689480`.
   *
   * @var string
   */
  public $appId;
  /**
   * Indicates the platform of the targeted app. If this field is not specified,
   * the app platform will be assumed to be mobile (i.e., Android or iOS), and
   * we will derive the appropriate mobile platform from the app ID.
   *
   * @var string
   */
  public $appPlatform;
  /**
   * Output only. The display name of the app.
   *
   * @var string
   */
  public $displayName;
  /**
   * Indicates if this option is being negatively targeted.
   *
   * @var bool
   */
  public $negative;

  /**
   * Required. The ID of the app. Android's Play store app uses bundle ID, for
   * example `com.google.android.gm`. Apple's App store app ID uses 9 digit
   * string, for example `422689480`.
   *
   * @param string $appId
   */
  public function setAppId($appId)
  {
    $this->appId = $appId;
  }
  /**
   * @return string
   */
  public function getAppId()
  {
    return $this->appId;
  }
  /**
   * Indicates the platform of the targeted app. If this field is not specified,
   * the app platform will be assumed to be mobile (i.e., Android or iOS), and
   * we will derive the appropriate mobile platform from the app ID.
   *
   * Accepted values: APP_PLATFORM_UNSPECIFIED, APP_PLATFORM_IOS,
   * APP_PLATFORM_ANDROID, APP_PLATFORM_ROKU, APP_PLATFORM_AMAZON_FIRETV,
   * APP_PLATFORM_PLAYSTATION, APP_PLATFORM_APPLE_TV, APP_PLATFORM_XBOX,
   * APP_PLATFORM_SAMSUNG_TV, APP_PLATFORM_ANDROID_TV, APP_PLATFORM_GENERIC_CTV,
   * APP_PLATFORM_LG_TV, APP_PLATFORM_VIZIO_TV, APP_PLATFORM_VIDAA
   *
   * @param self::APP_PLATFORM_* $appPlatform
   */
  public function setAppPlatform($appPlatform)
  {
    $this->appPlatform = $appPlatform;
  }
  /**
   * @return self::APP_PLATFORM_*
   */
  public function getAppPlatform()
  {
    return $this->appPlatform;
  }
  /**
   * Output only. The display name of the app.
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
   * Indicates if this option is being negatively targeted.
   *
   * @param bool $negative
   */
  public function setNegative($negative)
  {
    $this->negative = $negative;
  }
  /**
   * @return bool
   */
  public function getNegative()
  {
    return $this->negative;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppAssignedTargetingOptionDetails::class, 'Google_Service_DisplayVideo_AppAssignedTargetingOptionDetails');
