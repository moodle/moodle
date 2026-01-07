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

class GoogleChromeManagementV1InstalledApp extends \Google\Collection
{
  /**
   * Application install type not specified.
   */
  public const APP_INSTALL_TYPE_APP_INSTALL_TYPE_UNSPECIFIED = 'APP_INSTALL_TYPE_UNSPECIFIED';
  /**
   * Multiple app install types.
   */
  public const APP_INSTALL_TYPE_MULTIPLE = 'MULTIPLE';
  /**
   * Normal app install type.
   */
  public const APP_INSTALL_TYPE_NORMAL = 'NORMAL';
  /**
   * Administrator app install type.
   */
  public const APP_INSTALL_TYPE_ADMIN = 'ADMIN';
  /**
   * Development app install type.
   */
  public const APP_INSTALL_TYPE_DEVELOPMENT = 'DEVELOPMENT';
  /**
   * Sideloaded app install type.
   */
  public const APP_INSTALL_TYPE_SIDELOAD = 'SIDELOAD';
  /**
   * Other app install type.
   */
  public const APP_INSTALL_TYPE_OTHER = 'OTHER';
  /**
   * Application source not specified.
   */
  public const APP_SOURCE_APP_SOURCE_UNSPECIFIED = 'APP_SOURCE_UNSPECIFIED';
  /**
   * Generally for extensions and Chrome apps.
   */
  public const APP_SOURCE_CHROME_WEBSTORE = 'CHROME_WEBSTORE';
  /**
   * Play Store app.
   */
  public const APP_SOURCE_PLAY_STORE = 'PLAY_STORE';
  /**
   * App type not specified.
   */
  public const APP_TYPE_APP_TYPE_UNSPECIFIED = 'APP_TYPE_UNSPECIFIED';
  /**
   * Chrome extension.
   */
  public const APP_TYPE_EXTENSION = 'EXTENSION';
  /**
   * Chrome app.
   */
  public const APP_TYPE_APP = 'APP';
  /**
   * Chrome theme.
   */
  public const APP_TYPE_THEME = 'THEME';
  /**
   * Chrome hosted app.
   */
  public const APP_TYPE_HOSTED_APP = 'HOSTED_APP';
  /**
   * ARC++ app.
   */
  public const APP_TYPE_ANDROID_APP = 'ANDROID_APP';
  protected $collection_key = 'permissions';
  /**
   * Output only. Unique identifier of the app. For Chrome apps and extensions,
   * the 32-character id (e.g. ehoadneljpdggcbbknedodolkkjodefl). For Android
   * apps, the package name (e.g. com.evernote).
   *
   * @var string
   */
  public $appId;
  /**
   * Output only. How the app was installed.
   *
   * @var string
   */
  public $appInstallType;
  /**
   * Output only. Source of the installed app.
   *
   * @var string
   */
  public $appSource;
  /**
   * Output only. Type of the app.
   *
   * @var string
   */
  public $appType;
  /**
   * Output only. Count of browser devices with this app installed.
   *
   * @var string
   */
  public $browserDeviceCount;
  /**
   * Output only. Description of the installed app.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. Whether the app is disabled.
   *
   * @var bool
   */
  public $disabled;
  /**
   * Output only. Name of the installed app.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Homepage uri of the installed app.
   *
   * @var string
   */
  public $homepageUri;
  /**
   * Output only. Count of ChromeOS users with this app installed.
   *
   * @var string
   */
  public $osUserCount;
  /**
   * Output only. Permissions of the installed app.
   *
   * @var string[]
   */
  public $permissions;
  protected $riskAssessmentType = GoogleChromeManagementV1RiskAssessmentData::class;
  protected $riskAssessmentDataType = '';

  /**
   * Output only. Unique identifier of the app. For Chrome apps and extensions,
   * the 32-character id (e.g. ehoadneljpdggcbbknedodolkkjodefl). For Android
   * apps, the package name (e.g. com.evernote).
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
   * Output only. How the app was installed.
   *
   * Accepted values: APP_INSTALL_TYPE_UNSPECIFIED, MULTIPLE, NORMAL, ADMIN,
   * DEVELOPMENT, SIDELOAD, OTHER
   *
   * @param self::APP_INSTALL_TYPE_* $appInstallType
   */
  public function setAppInstallType($appInstallType)
  {
    $this->appInstallType = $appInstallType;
  }
  /**
   * @return self::APP_INSTALL_TYPE_*
   */
  public function getAppInstallType()
  {
    return $this->appInstallType;
  }
  /**
   * Output only. Source of the installed app.
   *
   * Accepted values: APP_SOURCE_UNSPECIFIED, CHROME_WEBSTORE, PLAY_STORE
   *
   * @param self::APP_SOURCE_* $appSource
   */
  public function setAppSource($appSource)
  {
    $this->appSource = $appSource;
  }
  /**
   * @return self::APP_SOURCE_*
   */
  public function getAppSource()
  {
    return $this->appSource;
  }
  /**
   * Output only. Type of the app.
   *
   * Accepted values: APP_TYPE_UNSPECIFIED, EXTENSION, APP, THEME, HOSTED_APP,
   * ANDROID_APP
   *
   * @param self::APP_TYPE_* $appType
   */
  public function setAppType($appType)
  {
    $this->appType = $appType;
  }
  /**
   * @return self::APP_TYPE_*
   */
  public function getAppType()
  {
    return $this->appType;
  }
  /**
   * Output only. Count of browser devices with this app installed.
   *
   * @param string $browserDeviceCount
   */
  public function setBrowserDeviceCount($browserDeviceCount)
  {
    $this->browserDeviceCount = $browserDeviceCount;
  }
  /**
   * @return string
   */
  public function getBrowserDeviceCount()
  {
    return $this->browserDeviceCount;
  }
  /**
   * Output only. Description of the installed app.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. Whether the app is disabled.
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * Output only. Name of the installed app.
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
   * Output only. Homepage uri of the installed app.
   *
   * @param string $homepageUri
   */
  public function setHomepageUri($homepageUri)
  {
    $this->homepageUri = $homepageUri;
  }
  /**
   * @return string
   */
  public function getHomepageUri()
  {
    return $this->homepageUri;
  }
  /**
   * Output only. Count of ChromeOS users with this app installed.
   *
   * @param string $osUserCount
   */
  public function setOsUserCount($osUserCount)
  {
    $this->osUserCount = $osUserCount;
  }
  /**
   * @return string
   */
  public function getOsUserCount()
  {
    return $this->osUserCount;
  }
  /**
   * Output only. Permissions of the installed app.
   *
   * @param string[] $permissions
   */
  public function setPermissions($permissions)
  {
    $this->permissions = $permissions;
  }
  /**
   * @return string[]
   */
  public function getPermissions()
  {
    return $this->permissions;
  }
  /**
   * Output only. If available, the risk assessment data about this extension.
   *
   * @param GoogleChromeManagementV1RiskAssessmentData $riskAssessment
   */
  public function setRiskAssessment(GoogleChromeManagementV1RiskAssessmentData $riskAssessment)
  {
    $this->riskAssessment = $riskAssessment;
  }
  /**
   * @return GoogleChromeManagementV1RiskAssessmentData
   */
  public function getRiskAssessment()
  {
    return $this->riskAssessment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1InstalledApp::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1InstalledApp');
