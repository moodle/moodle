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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1ChromeAppInfo extends \Google\Collection
{
  /**
   * Unspecified ItemType.
   */
  public const TYPE_ITEM_TYPE_UNSPECIFIED = 'ITEM_TYPE_UNSPECIFIED';
  /**
   * Chrome Extensions.
   */
  public const TYPE_EXTENSION = 'EXTENSION';
  /**
   * Any other type than extension.
   */
  public const TYPE_OTHERS = 'OTHERS';
  protected $collection_key = 'siteAccess';
  /**
   * Output only. Whether the app or extension is built and maintained by
   * Google. Version-specific field that will only be set when the requested app
   * version is found.
   *
   * @var bool
   */
  public $googleOwned;
  /**
   * Output only. Whether the app or extension is in a published state in the
   * Chrome Web Store.
   *
   * @var bool
   */
  public $isCwsHosted;
  /**
   * Output only. Whether an app supports policy for extensions.
   *
   * @var bool
   */
  public $isExtensionPolicySupported;
  /**
   * Output only. Whether the app is only for Kiosk mode on ChromeOS devices
   *
   * @var bool
   */
  public $isKioskOnly;
  /**
   * Output only. Whether the app or extension is a theme.
   *
   * @var bool
   */
  public $isTheme;
  /**
   * Output only. Whether this app is enabled for Kiosk mode on ChromeOS devices
   *
   * @var bool
   */
  public $kioskEnabled;
  /**
   * Output only. The version of this extension's manifest.
   *
   * @var string
   */
  public $manifestVersion;
  /**
   * Output only. The minimum number of users using this app.
   *
   * @var int
   */
  public $minUserCount;
  protected $permissionsType = GoogleChromeManagementV1ChromeAppPermission::class;
  protected $permissionsDataType = 'array';
  protected $siteAccessType = GoogleChromeManagementV1ChromeAppSiteAccess::class;
  protected $siteAccessDataType = 'array';
  /**
   * Output only. The app developer has enabled support for their app. Version-
   * specific field that will only be set when the requested app version is
   * found.
   *
   * @var bool
   */
  public $supportEnabled;
  /**
   * Output only. Types of an item in the Chrome Web Store
   *
   * @var string
   */
  public $type;

  /**
   * Output only. Whether the app or extension is built and maintained by
   * Google. Version-specific field that will only be set when the requested app
   * version is found.
   *
   * @param bool $googleOwned
   */
  public function setGoogleOwned($googleOwned)
  {
    $this->googleOwned = $googleOwned;
  }
  /**
   * @return bool
   */
  public function getGoogleOwned()
  {
    return $this->googleOwned;
  }
  /**
   * Output only. Whether the app or extension is in a published state in the
   * Chrome Web Store.
   *
   * @param bool $isCwsHosted
   */
  public function setIsCwsHosted($isCwsHosted)
  {
    $this->isCwsHosted = $isCwsHosted;
  }
  /**
   * @return bool
   */
  public function getIsCwsHosted()
  {
    return $this->isCwsHosted;
  }
  /**
   * Output only. Whether an app supports policy for extensions.
   *
   * @param bool $isExtensionPolicySupported
   */
  public function setIsExtensionPolicySupported($isExtensionPolicySupported)
  {
    $this->isExtensionPolicySupported = $isExtensionPolicySupported;
  }
  /**
   * @return bool
   */
  public function getIsExtensionPolicySupported()
  {
    return $this->isExtensionPolicySupported;
  }
  /**
   * Output only. Whether the app is only for Kiosk mode on ChromeOS devices
   *
   * @param bool $isKioskOnly
   */
  public function setIsKioskOnly($isKioskOnly)
  {
    $this->isKioskOnly = $isKioskOnly;
  }
  /**
   * @return bool
   */
  public function getIsKioskOnly()
  {
    return $this->isKioskOnly;
  }
  /**
   * Output only. Whether the app or extension is a theme.
   *
   * @param bool $isTheme
   */
  public function setIsTheme($isTheme)
  {
    $this->isTheme = $isTheme;
  }
  /**
   * @return bool
   */
  public function getIsTheme()
  {
    return $this->isTheme;
  }
  /**
   * Output only. Whether this app is enabled for Kiosk mode on ChromeOS devices
   *
   * @param bool $kioskEnabled
   */
  public function setKioskEnabled($kioskEnabled)
  {
    $this->kioskEnabled = $kioskEnabled;
  }
  /**
   * @return bool
   */
  public function getKioskEnabled()
  {
    return $this->kioskEnabled;
  }
  /**
   * Output only. The version of this extension's manifest.
   *
   * @param string $manifestVersion
   */
  public function setManifestVersion($manifestVersion)
  {
    $this->manifestVersion = $manifestVersion;
  }
  /**
   * @return string
   */
  public function getManifestVersion()
  {
    return $this->manifestVersion;
  }
  /**
   * Output only. The minimum number of users using this app.
   *
   * @param int $minUserCount
   */
  public function setMinUserCount($minUserCount)
  {
    $this->minUserCount = $minUserCount;
  }
  /**
   * @return int
   */
  public function getMinUserCount()
  {
    return $this->minUserCount;
  }
  /**
   * Output only. Every custom permission requested by the app. Version-specific
   * field that will only be set when the requested app version is found.
   *
   * @param GoogleChromeManagementV1ChromeAppPermission[] $permissions
   */
  public function setPermissions($permissions)
  {
    $this->permissions = $permissions;
  }
  /**
   * @return GoogleChromeManagementV1ChromeAppPermission[]
   */
  public function getPermissions()
  {
    return $this->permissions;
  }
  /**
   * Output only. Every permission giving access to domains or broad host
   * patterns. ( e.g. www.google.com). This includes the matches from content
   * scripts as well as hosts in the permissions node of the manifest. Version-
   * specific field that will only be set when the requested app version is
   * found.
   *
   * @param GoogleChromeManagementV1ChromeAppSiteAccess[] $siteAccess
   */
  public function setSiteAccess($siteAccess)
  {
    $this->siteAccess = $siteAccess;
  }
  /**
   * @return GoogleChromeManagementV1ChromeAppSiteAccess[]
   */
  public function getSiteAccess()
  {
    return $this->siteAccess;
  }
  /**
   * Output only. The app developer has enabled support for their app. Version-
   * specific field that will only be set when the requested app version is
   * found.
   *
   * @param bool $supportEnabled
   */
  public function setSupportEnabled($supportEnabled)
  {
    $this->supportEnabled = $supportEnabled;
  }
  /**
   * @return bool
   */
  public function getSupportEnabled()
  {
    return $this->supportEnabled;
  }
  /**
   * Output only. Types of an item in the Chrome Web Store
   *
   * Accepted values: ITEM_TYPE_UNSPECIFIED, EXTENSION, OTHERS
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1ChromeAppInfo::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1ChromeAppInfo');
