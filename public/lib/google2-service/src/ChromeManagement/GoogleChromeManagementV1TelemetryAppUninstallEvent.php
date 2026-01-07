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

class GoogleChromeManagementV1TelemetryAppUninstallEvent extends \Google\Model
{
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
   * Application uninstall source unknown.
   */
  public const APP_UNINSTALL_SOURCE_APPLICATION_UNINSTALL_SOURCE_UNSPECIFIED = 'APPLICATION_UNINSTALL_SOURCE_UNSPECIFIED';
  /**
   * Application uninstalled from the App List (Launcher).
   */
  public const APP_UNINSTALL_SOURCE_APPLICATION_UNINSTALL_SOURCE_APP_LIST = 'APPLICATION_UNINSTALL_SOURCE_APP_LIST';
  /**
   * Application uninstalled from the App Managedment page.
   */
  public const APP_UNINSTALL_SOURCE_APPLICATION_UNINSTALL_SOURCE_APP_MANAGEMENT = 'APPLICATION_UNINSTALL_SOURCE_APP_MANAGEMENT';
  /**
   * Application uninstalled from the Shelf.
   */
  public const APP_UNINSTALL_SOURCE_APPLICATION_UNINSTALL_SOURCE_SHELF = 'APPLICATION_UNINSTALL_SOURCE_SHELF';
  /**
   * Application uninstalled by app migration.
   */
  public const APP_UNINSTALL_SOURCE_APPLICATION_UNINSTALL_SOURCE_MIGRATION = 'APPLICATION_UNINSTALL_SOURCE_MIGRATION';
  /**
   * App id. For PWAs this is the start URL, and for extensions this is the
   * extension id.
   *
   * @var string
   */
  public $appId;
  /**
   * Type of app.
   *
   * @var string
   */
  public $appType;
  /**
   * App uninstall source.
   *
   * @var string
   */
  public $appUninstallSource;

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
  /**
   * App uninstall source.
   *
   * Accepted values: APPLICATION_UNINSTALL_SOURCE_UNSPECIFIED,
   * APPLICATION_UNINSTALL_SOURCE_APP_LIST,
   * APPLICATION_UNINSTALL_SOURCE_APP_MANAGEMENT,
   * APPLICATION_UNINSTALL_SOURCE_SHELF, APPLICATION_UNINSTALL_SOURCE_MIGRATION
   *
   * @param self::APP_UNINSTALL_SOURCE_* $appUninstallSource
   */
  public function setAppUninstallSource($appUninstallSource)
  {
    $this->appUninstallSource = $appUninstallSource;
  }
  /**
   * @return self::APP_UNINSTALL_SOURCE_*
   */
  public function getAppUninstallSource()
  {
    return $this->appUninstallSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1TelemetryAppUninstallEvent::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1TelemetryAppUninstallEvent');
