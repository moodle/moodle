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

class GoogleChromeManagementV1AppUsageData extends \Google\Model
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
   * App id.
   *
   * @var string
   */
  public $appId;
  /**
   * Application instance id. This will be unique per window/instance.
   *
   * @var string
   */
  public $appInstanceId;
  /**
   * Type of app.
   *
   * @var string
   */
  public $appType;
  /**
   * App foreground running time.
   *
   * @var string
   */
  public $runningDuration;

  /**
   * App id.
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
   * Application instance id. This will be unique per window/instance.
   *
   * @param string $appInstanceId
   */
  public function setAppInstanceId($appInstanceId)
  {
    $this->appInstanceId = $appInstanceId;
  }
  /**
   * @return string
   */
  public function getAppInstanceId()
  {
    return $this->appInstanceId;
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
   * App foreground running time.
   *
   * @param string $runningDuration
   */
  public function setRunningDuration($runningDuration)
  {
    $this->runningDuration = $runningDuration;
  }
  /**
   * @return string
   */
  public function getRunningDuration()
  {
    return $this->runningDuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1AppUsageData::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1AppUsageData');
