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

namespace Google\Service\Directory;

class MobileDeviceApplications extends \Google\Collection
{
  protected $collection_key = 'permission';
  /**
   * The application's display name. An example is `Browser`.
   *
   * @var string
   */
  public $displayName;
  /**
   * The application's package name. An example is `com.android.browser`.
   *
   * @var string
   */
  public $packageName;
  /**
   * The list of permissions of this application. These can be either a standard
   * Android permission or one defined by the application, and are found in an
   * application's [Android
   * manifest](https://developer.android.com/guide/topics/manifest/uses-
   * permission-element.html). Examples of a Calendar application's permissions
   * are `READ_CALENDAR`, or `MANAGE_ACCOUNTS`.
   *
   * @var string[]
   */
  public $permission;
  /**
   * The application's version code. An example is `13`.
   *
   * @var int
   */
  public $versionCode;
  /**
   * The application's version name. An example is `3.2-140714`.
   *
   * @var string
   */
  public $versionName;

  /**
   * The application's display name. An example is `Browser`.
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
   * The application's package name. An example is `com.android.browser`.
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
  /**
   * The list of permissions of this application. These can be either a standard
   * Android permission or one defined by the application, and are found in an
   * application's [Android
   * manifest](https://developer.android.com/guide/topics/manifest/uses-
   * permission-element.html). Examples of a Calendar application's permissions
   * are `READ_CALENDAR`, or `MANAGE_ACCOUNTS`.
   *
   * @param string[] $permission
   */
  public function setPermission($permission)
  {
    $this->permission = $permission;
  }
  /**
   * @return string[]
   */
  public function getPermission()
  {
    return $this->permission;
  }
  /**
   * The application's version code. An example is `13`.
   *
   * @param int $versionCode
   */
  public function setVersionCode($versionCode)
  {
    $this->versionCode = $versionCode;
  }
  /**
   * @return int
   */
  public function getVersionCode()
  {
    return $this->versionCode;
  }
  /**
   * The application's version name. An example is `3.2-140714`.
   *
   * @param string $versionName
   */
  public function setVersionName($versionName)
  {
    $this->versionName = $versionName;
  }
  /**
   * @return string
   */
  public function getVersionName()
  {
    return $this->versionName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MobileDeviceApplications::class, 'Google_Service_Directory_MobileDeviceApplications');
