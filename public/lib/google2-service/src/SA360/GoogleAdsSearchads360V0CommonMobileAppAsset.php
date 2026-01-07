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

class GoogleAdsSearchads360V0CommonMobileAppAsset extends \Google\Model
{
  /**
   * Not specified.
   */
  public const APP_STORE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const APP_STORE_UNKNOWN = 'UNKNOWN';
  /**
   * Mobile app vendor for Apple app store.
   */
  public const APP_STORE_APPLE_APP_STORE = 'APPLE_APP_STORE';
  /**
   * Mobile app vendor for Google app store.
   */
  public const APP_STORE_GOOGLE_APP_STORE = 'GOOGLE_APP_STORE';
  /**
   * Required. A string that uniquely identifies a mobile application. It should
   * just contain the platform native id, like "com.android.ebay" for Android or
   * "12345689" for iOS.
   *
   * @var string
   */
  public $appId;
  /**
   * Required. The application store that distributes this specific app.
   *
   * @var string
   */
  public $appStore;

  /**
   * Required. A string that uniquely identifies a mobile application. It should
   * just contain the platform native id, like "com.android.ebay" for Android or
   * "12345689" for iOS.
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
   * Required. The application store that distributes this specific app.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, APPLE_APP_STORE, GOOGLE_APP_STORE
   *
   * @param self::APP_STORE_* $appStore
   */
  public function setAppStore($appStore)
  {
    $this->appStore = $appStore;
  }
  /**
   * @return self::APP_STORE_*
   */
  public function getAppStore()
  {
    return $this->appStore;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0CommonMobileAppAsset::class, 'Google_Service_SA360_GoogleAdsSearchads360V0CommonMobileAppAsset');
