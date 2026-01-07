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

class MobileApp extends \Google\Model
{
  /**
   * Platform is not specified.
   */
  public const PLATFORM_PLATFORM_UNSPECIFIED = 'PLATFORM_UNSPECIFIED';
  /**
   * iOS platform.
   */
  public const PLATFORM_IOS = 'IOS';
  /**
   * Android platform.
   */
  public const PLATFORM_ANDROID = 'ANDROID';
  /**
   * Required. The ID of the app provided by the platform store. Android apps
   * are identified by the bundle ID used by Android's Play store, such as
   * `com.google.android.gm`. iOS apps are identified by a nine-digit app ID
   * used by Apple's App store, such as `422689480`.
   *
   * @var string
   */
  public $appId;
  /**
   * Output only. The app name.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The app platform.
   *
   * @var string
   */
  public $platform;
  /**
   * Output only. The app publisher.
   *
   * @var string
   */
  public $publisher;

  /**
   * Required. The ID of the app provided by the platform store. Android apps
   * are identified by the bundle ID used by Android's Play store, such as
   * `com.google.android.gm`. iOS apps are identified by a nine-digit app ID
   * used by Apple's App store, such as `422689480`.
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
   * Output only. The app name.
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
   * Output only. The app platform.
   *
   * Accepted values: PLATFORM_UNSPECIFIED, IOS, ANDROID
   *
   * @param self::PLATFORM_* $platform
   */
  public function setPlatform($platform)
  {
    $this->platform = $platform;
  }
  /**
   * @return self::PLATFORM_*
   */
  public function getPlatform()
  {
    return $this->platform;
  }
  /**
   * Output only. The app publisher.
   *
   * @param string $publisher
   */
  public function setPublisher($publisher)
  {
    $this->publisher = $publisher;
  }
  /**
   * @return string
   */
  public function getPublisher()
  {
    return $this->publisher;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MobileApp::class, 'Google_Service_DisplayVideo_MobileApp');
