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

class GoogleChromeManagementV1TelemetryAppLaunchEvent extends \Google\Model
{
  /**
   * Application launch source unknown.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_UNSPECIFIED = 'APPLICATION_LAUNCH_SOURCE_UNSPECIFIED';
  /**
   * Application launched from the grid of apps, not the search box.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_APP_LIST_GRID = 'APPLICATION_LAUNCH_SOURCE_APP_LIST_GRID';
  /**
   * Application launched from the grid of apps, off of the context menu.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_APP_LIST_GRID_CONTEXT_MENU = 'APPLICATION_LAUNCH_SOURCE_APP_LIST_GRID_CONTEXT_MENU';
  /**
   * Application launched from query-dependent results (larger icons).
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_APP_LIST_QUERY = 'APPLICATION_LAUNCH_SOURCE_APP_LIST_QUERY';
  /**
   * Application launched from query-dependent results, off of the context menu.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_APP_LIST_QUERY_CONTEXT_MENU = 'APPLICATION_LAUNCH_SOURCE_APP_LIST_QUERY_CONTEXT_MENU';
  /**
   * Application launched from query-less recommendations (smaller icons).
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_APP_LIST_RECOMMENDATION = 'APPLICATION_LAUNCH_SOURCE_APP_LIST_RECOMMENDATION';
  /**
   * Application launched from the Parental Controls Settings section and Per
   * App time notification.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_PARENTAL_CONTROLS = 'APPLICATION_LAUNCH_SOURCE_PARENTAL_CONTROLS';
  /**
   * Application launched from shelf.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_SHELF = 'APPLICATION_LAUNCH_SOURCE_SHELF';
  /**
   * Application launched from the file manager
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_FILE_MANAGER = 'APPLICATION_LAUNCH_SOURCE_FILE_MANAGER';
  /**
   * Application launched from left click on a link in the browser.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_LINK = 'APPLICATION_LAUNCH_SOURCE_LINK';
  /**
   * Application launched from entering a URL in the Omnibox on the browser.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_OMNIBOX = 'APPLICATION_LAUNCH_SOURCE_OMNIBOX';
  /**
   * Application launched from a Chrome internal call.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_CHROME_INTERNAL = 'APPLICATION_LAUNCH_SOURCE_CHROME_INTERNAL';
  /**
   * Application launched from keyboard shortcut for opening app.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_KEYBOARD = 'APPLICATION_LAUNCH_SOURCE_KEYBOARD';
  /**
   * Application launched from clicking a link in another app or WebUI.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_OTHER_APP = 'APPLICATION_LAUNCH_SOURCE_OTHER_APP';
  /**
   * Application launched from menu.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_MENU = 'APPLICATION_LAUNCH_SOURCE_MENU';
  /**
   * Application launched from the installed notification.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_INSTALLED_NOTIFICATION = 'APPLICATION_LAUNCH_SOURCE_INSTALLED_NOTIFICATION';
  /**
   * Application launched from a test.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_TEST = 'APPLICATION_LAUNCH_SOURCE_TEST';
  /**
   * Application launched from Arc.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_ARC = 'APPLICATION_LAUNCH_SOURCE_ARC';
  /**
   * Application launched from Sharesheet.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_SHARESHEET = 'APPLICATION_LAUNCH_SOURCE_SHARESHEET';
  /**
   * Application launched from the release notes notification.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_RELEASE_NOTES_NOTIFICATION = 'APPLICATION_LAUNCH_SOURCE_RELEASE_NOTES_NOTIFICATION';
  /**
   * Application launched from a full restore.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_FULL_RESTORE = 'APPLICATION_LAUNCH_SOURCE_FULL_RESTORE';
  /**
   * Application launched from a smart text selection context menu.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_SMART_TEXT_CONTEXT_MENU = 'APPLICATION_LAUNCH_SOURCE_SMART_TEXT_CONTEXT_MENU';
  /**
   * Application launched from a discover tab notification.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_DISCOVER_TAB_NOTIFICATION = 'APPLICATION_LAUNCH_SOURCE_DISCOVER_TAB_NOTIFICATION';
  /**
   * Application launched from the Management API.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_MANAGEMENT_API = 'APPLICATION_LAUNCH_SOURCE_MANAGEMENT_API';
  /**
   * Application launched from kiosk.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_KIOSK = 'APPLICATION_LAUNCH_SOURCE_KIOSK';
  /**
   * Application launched from the command line.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_COMMAND_LINE = 'APPLICATION_LAUNCH_SOURCE_COMMAND_LINE';
  /**
   * Application launched from background mode.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_BACKGROUND_MODE = 'APPLICATION_LAUNCH_SOURCE_BACKGROUND_MODE';
  /**
   * Application launched from the new tab page.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_NEW_TAB_PAGE = 'APPLICATION_LAUNCH_SOURCE_NEW_TAB_PAGE';
  /**
   * Application launched from an intent URL.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_INTENT_URL = 'APPLICATION_LAUNCH_SOURCE_INTENT_URL';
  /**
   * Application launched from OS login.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_OS_LOGIN = 'APPLICATION_LAUNCH_SOURCE_OS_LOGIN';
  /**
   * Application launched from protocol handler.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_PROTOCOL_HANDLER = 'APPLICATION_LAUNCH_SOURCE_PROTOCOL_HANDLER';
  /**
   * Application launched from URL handler.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_URL_HANDLER = 'APPLICATION_LAUNCH_SOURCE_URL_HANDLER';
  /**
   * Application launched from lock screen app launcher.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_LOCK_SCREEN = 'APPLICATION_LAUNCH_SOURCE_LOCK_SCREEN';
  /**
   * Application launched from app home (chrome://apps) page.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_APP_HOME_PAGE = 'APPLICATION_LAUNCH_SOURCE_APP_HOME_PAGE';
  /**
   * Application launched from moving content into an app.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_REPARENTING = 'APPLICATION_LAUNCH_SOURCE_REPARENTING';
  /**
   * Application launched from profile menu of installable chrome://password-
   * manager WebUI.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_PROFILE_MENU = 'APPLICATION_LAUNCH_SOURCE_PROFILE_MENU';
  /**
   * Application launched from system tray calendar.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_SYSTEM_TRAY_CALENDAR = 'APPLICATION_LAUNCH_SOURCE_SYSTEM_TRAY_CALENDAR';
  /**
   * Application launched from source installer.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_INSTALLER = 'APPLICATION_LAUNCH_SOURCE_INSTALLER';
  /**
   * Count first-run Help app launches separately so that we can understand the
   * number of user-triggered launches.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_FIRST_RUN = 'APPLICATION_LAUNCH_SOURCE_FIRST_RUN';
  /**
   * Application launched from welcome tour.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_WELCOME_TOUR = 'APPLICATION_LAUNCH_SOURCE_WELCOME_TOUR';
  /**
   * Applicationed launched from focus panel.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_FOCUS_MODE = 'APPLICATION_LAUNCH_SOURCE_FOCUS_MODE';
  /**
   * Application launched from experimental feature Sparky.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_SPARKY = 'APPLICATION_LAUNCH_SOURCE_SPARKY';
  /**
   * Application launched from navigation capturing.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_NAVIGATION_CAPTURING = 'APPLICATION_LAUNCH_SOURCE_NAVIGATION_CAPTURING';
  /**
   * Application launched from web install API.
   */
  public const APP_LAUNCH_SOURCE_APPLICATION_LAUNCH_SOURCE_WEB_INSTALL_API = 'APPLICATION_LAUNCH_SOURCE_WEB_INSTALL_API';
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
   * App launch source.
   *
   * @var string
   */
  public $appLaunchSource;
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
   * App launch source.
   *
   * Accepted values: APPLICATION_LAUNCH_SOURCE_UNSPECIFIED,
   * APPLICATION_LAUNCH_SOURCE_APP_LIST_GRID,
   * APPLICATION_LAUNCH_SOURCE_APP_LIST_GRID_CONTEXT_MENU,
   * APPLICATION_LAUNCH_SOURCE_APP_LIST_QUERY,
   * APPLICATION_LAUNCH_SOURCE_APP_LIST_QUERY_CONTEXT_MENU,
   * APPLICATION_LAUNCH_SOURCE_APP_LIST_RECOMMENDATION,
   * APPLICATION_LAUNCH_SOURCE_PARENTAL_CONTROLS,
   * APPLICATION_LAUNCH_SOURCE_SHELF, APPLICATION_LAUNCH_SOURCE_FILE_MANAGER,
   * APPLICATION_LAUNCH_SOURCE_LINK, APPLICATION_LAUNCH_SOURCE_OMNIBOX,
   * APPLICATION_LAUNCH_SOURCE_CHROME_INTERNAL,
   * APPLICATION_LAUNCH_SOURCE_KEYBOARD, APPLICATION_LAUNCH_SOURCE_OTHER_APP,
   * APPLICATION_LAUNCH_SOURCE_MENU,
   * APPLICATION_LAUNCH_SOURCE_INSTALLED_NOTIFICATION,
   * APPLICATION_LAUNCH_SOURCE_TEST, APPLICATION_LAUNCH_SOURCE_ARC,
   * APPLICATION_LAUNCH_SOURCE_SHARESHEET,
   * APPLICATION_LAUNCH_SOURCE_RELEASE_NOTES_NOTIFICATION,
   * APPLICATION_LAUNCH_SOURCE_FULL_RESTORE,
   * APPLICATION_LAUNCH_SOURCE_SMART_TEXT_CONTEXT_MENU,
   * APPLICATION_LAUNCH_SOURCE_DISCOVER_TAB_NOTIFICATION,
   * APPLICATION_LAUNCH_SOURCE_MANAGEMENT_API, APPLICATION_LAUNCH_SOURCE_KIOSK,
   * APPLICATION_LAUNCH_SOURCE_COMMAND_LINE,
   * APPLICATION_LAUNCH_SOURCE_BACKGROUND_MODE,
   * APPLICATION_LAUNCH_SOURCE_NEW_TAB_PAGE,
   * APPLICATION_LAUNCH_SOURCE_INTENT_URL, APPLICATION_LAUNCH_SOURCE_OS_LOGIN,
   * APPLICATION_LAUNCH_SOURCE_PROTOCOL_HANDLER,
   * APPLICATION_LAUNCH_SOURCE_URL_HANDLER,
   * APPLICATION_LAUNCH_SOURCE_LOCK_SCREEN,
   * APPLICATION_LAUNCH_SOURCE_APP_HOME_PAGE,
   * APPLICATION_LAUNCH_SOURCE_REPARENTING,
   * APPLICATION_LAUNCH_SOURCE_PROFILE_MENU,
   * APPLICATION_LAUNCH_SOURCE_SYSTEM_TRAY_CALENDAR,
   * APPLICATION_LAUNCH_SOURCE_INSTALLER, APPLICATION_LAUNCH_SOURCE_FIRST_RUN,
   * APPLICATION_LAUNCH_SOURCE_WELCOME_TOUR,
   * APPLICATION_LAUNCH_SOURCE_FOCUS_MODE, APPLICATION_LAUNCH_SOURCE_SPARKY,
   * APPLICATION_LAUNCH_SOURCE_NAVIGATION_CAPTURING,
   * APPLICATION_LAUNCH_SOURCE_WEB_INSTALL_API
   *
   * @param self::APP_LAUNCH_SOURCE_* $appLaunchSource
   */
  public function setAppLaunchSource($appLaunchSource)
  {
    $this->appLaunchSource = $appLaunchSource;
  }
  /**
   * @return self::APP_LAUNCH_SOURCE_*
   */
  public function getAppLaunchSource()
  {
    return $this->appLaunchSource;
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
class_alias(GoogleChromeManagementV1TelemetryAppLaunchEvent::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1TelemetryAppLaunchEvent');
