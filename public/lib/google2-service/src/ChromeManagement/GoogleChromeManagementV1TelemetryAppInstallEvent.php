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

class GoogleChromeManagementV1TelemetryAppInstallEvent extends \Google\Model
{
  /**
   * Application install reason is unknown.
   */
  public const APP_INSTALL_REASON_APPLICATION_INSTALL_REASON_UNSPECIFIED = 'APPLICATION_INSTALL_REASON_UNSPECIFIED';
  /**
   * Application installed with the system and is considered part of the OS.
   */
  public const APP_INSTALL_REASON_APPLICATION_INSTALL_REASON_SYSTEM = 'APPLICATION_INSTALL_REASON_SYSTEM';
  /**
   * Application installed by policy.
   */
  public const APP_INSTALL_REASON_APPLICATION_INSTALL_REASON_POLICY = 'APPLICATION_INSTALL_REASON_POLICY';
  /**
   * Application installed by an original equipment manufacturer (OEM).
   */
  public const APP_INSTALL_REASON_APPLICATION_INSTALL_REASON_OEM = 'APPLICATION_INSTALL_REASON_OEM';
  /**
   * Application installed by default, but is not considered a system app.
   */
  public const APP_INSTALL_REASON_APPLICATION_INSTALL_REASON_DEFAULT = 'APPLICATION_INSTALL_REASON_DEFAULT';
  /**
   * Application installed by sync.
   */
  public const APP_INSTALL_REASON_APPLICATION_INSTALL_REASON_SYNC = 'APPLICATION_INSTALL_REASON_SYNC';
  /**
   * Application installed by user action.
   */
  public const APP_INSTALL_REASON_APPLICATION_INSTALL_REASON_USER = 'APPLICATION_INSTALL_REASON_USER';
  /**
   * Application installed bt SubApp API call.
   */
  public const APP_INSTALL_REASON_APPLICATION_INSTALL_REASON_SUB_APP = 'APPLICATION_INSTALL_REASON_SUB_APP';
  /**
   * Application installed by Kiosk on Chrome OS.
   */
  public const APP_INSTALL_REASON_APPLICATION_INSTALL_REASON_KIOSK = 'APPLICATION_INSTALL_REASON_KIOSK';
  /**
   * Application installed by command line argument.
   */
  public const APP_INSTALL_REASON_APPLICATION_INSTALL_REASON_COMMAND_LINE = 'APPLICATION_INSTALL_REASON_COMMAND_LINE';
  /**
   * Application install source is unknown.
   */
  public const APP_INSTALL_SOURCE_APPLICATION_INSTALL_SOURCE_UNSPECIFIED = 'APPLICATION_INSTALL_SOURCE_UNSPECIFIED';
  /**
   * Application installed as part of Chrome OS.
   */
  public const APP_INSTALL_SOURCE_APPLICATION_INSTALL_SOURCE_SYSTEM = 'APPLICATION_INSTALL_SOURCE_SYSTEM';
  /**
   * Application install source is a sync.
   */
  public const APP_INSTALL_SOURCE_APPLICATION_INSTALL_SOURCE_SYNC = 'APPLICATION_INSTALL_SOURCE_SYNC';
  /**
   * Application install source is the Play store.
   */
  public const APP_INSTALL_SOURCE_APPLICATION_INSTALL_SOURCE_PLAY_STORE = 'APPLICATION_INSTALL_SOURCE_PLAY_STORE';
  /**
   * Application install source is the Chrome web store.
   */
  public const APP_INSTALL_SOURCE_APPLICATION_INSTALL_SOURCE_CHROME_WEB_STORE = 'APPLICATION_INSTALL_SOURCE_CHROME_WEB_STORE';
  /**
   * Application install source is a browser.
   */
  public const APP_INSTALL_SOURCE_APPLICATION_INSTALL_SOURCE_BROWSER = 'APPLICATION_INSTALL_SOURCE_BROWSER';
  /**
   * Application install time unknown.
   */
  public const APP_INSTALL_TIME_APPLICATION_INSTALL_TIME_UNSPECIFIED = 'APPLICATION_INSTALL_TIME_UNSPECIFIED';
  /**
   * Application install is initialized.
   */
  public const APP_INSTALL_TIME_APPLICATION_INSTALL_TIME_INIT = 'APPLICATION_INSTALL_TIME_INIT';
  /**
   * Application install is currently running.
   */
  public const APP_INSTALL_TIME_APPLICATION_INSTALL_TIME_RUNNING = 'APPLICATION_INSTALL_TIME_RUNNING';
  /**
   * Application type unknown.
   */
  public const APP_TYPE_TELEMETRY_APPLICATION_TYPE_UNSPECIFIED = 'TELEMETRY_APPLICATION_TYPE_UNSPECIFIED';
  /**
   * Application type arc (Android app).
   */
  public const APP_TYPE_APPLICATION_TYPE_ARC = 'APPLICATION_TYPE_ARC';
  /**
   * Deprecated. This value is no longer used. Application type built-in.
   *
   * @deprecated
   */
  public const APP_TYPE_APPLICATION_TYPE_BUILT_IN = 'APPLICATION_TYPE_BUILT_IN';
  /**
   * Application type Linux (via Crostini).
   */
  public const APP_TYPE_APPLICATION_TYPE_CROSTINI = 'APPLICATION_TYPE_CROSTINI';
  /**
   * Application type Chrome app.
   */
  public const APP_TYPE_APPLICATION_TYPE_CHROME_APP = 'APPLICATION_TYPE_CHROME_APP';
  /**
   * Application type web.
   */
  public const APP_TYPE_APPLICATION_TYPE_WEB = 'APPLICATION_TYPE_WEB';
  /**
   * Application type Mac OS.
   */
  public const APP_TYPE_APPLICATION_TYPE_MAC_OS = 'APPLICATION_TYPE_MAC_OS';
  /**
   * Application type Plugin VM.
   */
  public const APP_TYPE_APPLICATION_TYPE_PLUGIN_VM = 'APPLICATION_TYPE_PLUGIN_VM';
  /**
   * Deprecated. This value is no longer used. Application type standalone
   * browser (Lacros browser app).
   *
   * @deprecated
   */
  public const APP_TYPE_APPLICATION_TYPE_STANDALONE_BROWSER = 'APPLICATION_TYPE_STANDALONE_BROWSER';
  /**
   * Application type remote.
   */
  public const APP_TYPE_APPLICATION_TYPE_REMOTE = 'APPLICATION_TYPE_REMOTE';
  /**
   * Application type borealis.
   */
  public const APP_TYPE_APPLICATION_TYPE_BOREALIS = 'APPLICATION_TYPE_BOREALIS';
  /**
   * Application type system web.
   */
  public const APP_TYPE_APPLICATION_TYPE_SYSTEM_WEB = 'APPLICATION_TYPE_SYSTEM_WEB';
  /**
   * Deprecated. This value is no longer used. Application type standalone
   * browser chrome app.
   *
   * @deprecated
   */
  public const APP_TYPE_APPLICATION_TYPE_STANDALONE_BROWSER_CHROME_APP = 'APPLICATION_TYPE_STANDALONE_BROWSER_CHROME_APP';
  /**
   * Application type extension.
   */
  public const APP_TYPE_APPLICATION_TYPE_EXTENSION = 'APPLICATION_TYPE_EXTENSION';
  /**
   * Deprecated. This value is no longer used. Application type standalone
   * browser extension.
   *
   * @deprecated
   */
  public const APP_TYPE_APPLICATION_TYPE_STANDALONE_BROWSER_EXTENSION = 'APPLICATION_TYPE_STANDALONE_BROWSER_EXTENSION';
  /**
   * Application type bruschetta.
   */
  public const APP_TYPE_APPLICATION_TYPE_BRUSCHETTA = 'APPLICATION_TYPE_BRUSCHETTA';
  /**
   * App id. For PWAs this is the start URL, and for extensions this is the
   * extension id.
   *
   * @var string
   */
  public $appId;
  /**
   * App installation reason.
   *
   * @var string
   */
  public $appInstallReason;
  /**
   * App installation source.
   *
   * @var string
   */
  public $appInstallSource;
  /**
   * App installation time depending on the app lifecycle.
   *
   * @var string
   */
  public $appInstallTime;
  /**
   * Type of app.
   *
   * @var string
   */
  public $appType;

  /**
   * App id. For PWAs this is the start URL, and for extensions this is the
   * extension id.
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
   * App installation reason.
   *
   * Accepted values: APPLICATION_INSTALL_REASON_UNSPECIFIED,
   * APPLICATION_INSTALL_REASON_SYSTEM, APPLICATION_INSTALL_REASON_POLICY,
   * APPLICATION_INSTALL_REASON_OEM, APPLICATION_INSTALL_REASON_DEFAULT,
   * APPLICATION_INSTALL_REASON_SYNC, APPLICATION_INSTALL_REASON_USER,
   * APPLICATION_INSTALL_REASON_SUB_APP, APPLICATION_INSTALL_REASON_KIOSK,
   * APPLICATION_INSTALL_REASON_COMMAND_LINE
   *
   * @param self::APP_INSTALL_REASON_* $appInstallReason
   */
  public function setAppInstallReason($appInstallReason)
  {
    $this->appInstallReason = $appInstallReason;
  }
  /**
   * @return self::APP_INSTALL_REASON_*
   */
  public function getAppInstallReason()
  {
    return $this->appInstallReason;
  }
  /**
   * App installation source.
   *
   * Accepted values: APPLICATION_INSTALL_SOURCE_UNSPECIFIED,
   * APPLICATION_INSTALL_SOURCE_SYSTEM, APPLICATION_INSTALL_SOURCE_SYNC,
   * APPLICATION_INSTALL_SOURCE_PLAY_STORE,
   * APPLICATION_INSTALL_SOURCE_CHROME_WEB_STORE,
   * APPLICATION_INSTALL_SOURCE_BROWSER
   *
   * @param self::APP_INSTALL_SOURCE_* $appInstallSource
   */
  public function setAppInstallSource($appInstallSource)
  {
    $this->appInstallSource = $appInstallSource;
  }
  /**
   * @return self::APP_INSTALL_SOURCE_*
   */
  public function getAppInstallSource()
  {
    return $this->appInstallSource;
  }
  /**
   * App installation time depending on the app lifecycle.
   *
   * Accepted values: APPLICATION_INSTALL_TIME_UNSPECIFIED,
   * APPLICATION_INSTALL_TIME_INIT, APPLICATION_INSTALL_TIME_RUNNING
   *
   * @param self::APP_INSTALL_TIME_* $appInstallTime
   */
  public function setAppInstallTime($appInstallTime)
  {
    $this->appInstallTime = $appInstallTime;
  }
  /**
   * @return self::APP_INSTALL_TIME_*
   */
  public function getAppInstallTime()
  {
    return $this->appInstallTime;
  }
  /**
   * Type of app.
   *
   * Accepted values: TELEMETRY_APPLICATION_TYPE_UNSPECIFIED,
   * APPLICATION_TYPE_ARC, APPLICATION_TYPE_BUILT_IN, APPLICATION_TYPE_CROSTINI,
   * APPLICATION_TYPE_CHROME_APP, APPLICATION_TYPE_WEB, APPLICATION_TYPE_MAC_OS,
   * APPLICATION_TYPE_PLUGIN_VM, APPLICATION_TYPE_STANDALONE_BROWSER,
   * APPLICATION_TYPE_REMOTE, APPLICATION_TYPE_BOREALIS,
   * APPLICATION_TYPE_SYSTEM_WEB,
   * APPLICATION_TYPE_STANDALONE_BROWSER_CHROME_APP, APPLICATION_TYPE_EXTENSION,
   * APPLICATION_TYPE_STANDALONE_BROWSER_EXTENSION, APPLICATION_TYPE_BRUSCHETTA
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1TelemetryAppInstallEvent::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1TelemetryAppInstallEvent');
