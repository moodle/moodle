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

namespace Google\Service\AdMob;

class AppLinkedAppInfo extends \Google\Model
{
  /**
   * The app store ID of the app; present if and only if the app is linked to an
   * app store. If the app is added to the Google Play store, it will be the
   * application ID of the app. For example: "com.example.myapp". See
   * https://developer.android.com/studio/build/application-id. If the app is
   * added to the Apple App Store, it will be app store ID. For example
   * "105169111". Note that setting the app store id is considered an
   * irreversible action. Once an app is linked, it cannot be unlinked.
   *
   * @var string
   */
  public $appStoreId;
  /**
   * Output only. Display name of the app as it appears in the app store. This
   * is an output-only field, and may be empty if the app cannot be found in the
   * store.
   *
   * @var string
   */
  public $displayName;

  /**
   * The app store ID of the app; present if and only if the app is linked to an
   * app store. If the app is added to the Google Play store, it will be the
   * application ID of the app. For example: "com.example.myapp". See
   * https://developer.android.com/studio/build/application-id. If the app is
   * added to the Apple App Store, it will be app store ID. For example
   * "105169111". Note that setting the app store id is considered an
   * irreversible action. Once an app is linked, it cannot be unlinked.
   *
   * @param string $appStoreId
   */
  public function setAppStoreId($appStoreId)
  {
    $this->appStoreId = $appStoreId;
  }
  /**
   * @return string
   */
  public function getAppStoreId()
  {
    return $this->appStoreId;
  }
  /**
   * Output only. Display name of the app as it appears in the app store. This
   * is an output-only field, and may be empty if the app cannot be found in the
   * store.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppLinkedAppInfo::class, 'Google_Service_AdMob_AppLinkedAppInfo');
