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

class DeviceInfo extends \Google\Model
{
  /**
   * Device model name.
   *
   * @var string
   */
  public $deviceModelName;
  /**
   * Device language code setting.
   *
   * @deprecated
   * @var string
   */
  public $languageCode;
  /**
   * Device language code setting obtained by executing JavaScript code in
   * WebView.
   *
   * @var string
   */
  public $languageCodeFromWebview;
  /**
   * Device language code raw setting. iOS does returns language code in
   * different format than iOS WebView. For example WebView returns en_US, but
   * iOS returns en-US. Field below will return raw value returned by iOS.
   *
   * @deprecated
   * @var string
   */
  public $languageCodeRaw;
  /**
   * Device display resolution height.
   *
   * @var string
   */
  public $screenResolutionHeight;
  /**
   * Device display resolution width.
   *
   * @var string
   */
  public $screenResolutionWidth;
  /**
   * Device timezone setting.
   *
   * @var string
   */
  public $timezone;

  /**
   * Device model name.
   *
   * @param string $deviceModelName
   */
  public function setDeviceModelName($deviceModelName)
  {
    $this->deviceModelName = $deviceModelName;
  }
  /**
   * @return string
   */
  public function getDeviceModelName()
  {
    return $this->deviceModelName;
  }
  /**
   * Device language code setting.
   *
   * @deprecated
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Device language code setting obtained by executing JavaScript code in
   * WebView.
   *
   * @param string $languageCodeFromWebview
   */
  public function setLanguageCodeFromWebview($languageCodeFromWebview)
  {
    $this->languageCodeFromWebview = $languageCodeFromWebview;
  }
  /**
   * @return string
   */
  public function getLanguageCodeFromWebview()
  {
    return $this->languageCodeFromWebview;
  }
  /**
   * Device language code raw setting. iOS does returns language code in
   * different format than iOS WebView. For example WebView returns en_US, but
   * iOS returns en-US. Field below will return raw value returned by iOS.
   *
   * @deprecated
   * @param string $languageCodeRaw
   */
  public function setLanguageCodeRaw($languageCodeRaw)
  {
    $this->languageCodeRaw = $languageCodeRaw;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getLanguageCodeRaw()
  {
    return $this->languageCodeRaw;
  }
  /**
   * Device display resolution height.
   *
   * @param string $screenResolutionHeight
   */
  public function setScreenResolutionHeight($screenResolutionHeight)
  {
    $this->screenResolutionHeight = $screenResolutionHeight;
  }
  /**
   * @return string
   */
  public function getScreenResolutionHeight()
  {
    return $this->screenResolutionHeight;
  }
  /**
   * Device display resolution width.
   *
   * @param string $screenResolutionWidth
   */
  public function setScreenResolutionWidth($screenResolutionWidth)
  {
    $this->screenResolutionWidth = $screenResolutionWidth;
  }
  /**
   * @return string
   */
  public function getScreenResolutionWidth()
  {
    return $this->screenResolutionWidth;
  }
  /**
   * Device timezone setting.
   *
   * @param string $timezone
   */
  public function setTimezone($timezone)
  {
    $this->timezone = $timezone;
  }
  /**
   * @return string
   */
  public function getTimezone()
  {
    return $this->timezone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceInfo::class, 'Google_Service_FirebaseDynamicLinks_DeviceInfo');
