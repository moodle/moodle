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

namespace Google\Service\Script;

class EntryPoint extends \Google\Model
{
  /**
   * An unspecified entry point.
   */
  public const ENTRY_POINT_TYPE_ENTRY_POINT_TYPE_UNSPECIFIED = 'ENTRY_POINT_TYPE_UNSPECIFIED';
  /**
   * A web application entry point.
   */
  public const ENTRY_POINT_TYPE_WEB_APP = 'WEB_APP';
  /**
   * An API executable entry point.
   */
  public const ENTRY_POINT_TYPE_EXECUTION_API = 'EXECUTION_API';
  /**
   * An Add-On entry point.
   */
  public const ENTRY_POINT_TYPE_ADD_ON = 'ADD_ON';
  protected $addOnType = GoogleAppsScriptTypeAddOnEntryPoint::class;
  protected $addOnDataType = '';
  /**
   * The type of the entry point.
   *
   * @var string
   */
  public $entryPointType;
  protected $executionApiType = GoogleAppsScriptTypeExecutionApiEntryPoint::class;
  protected $executionApiDataType = '';
  protected $webAppType = GoogleAppsScriptTypeWebAppEntryPoint::class;
  protected $webAppDataType = '';

  /**
   * Add-on properties.
   *
   * @param GoogleAppsScriptTypeAddOnEntryPoint $addOn
   */
  public function setAddOn(GoogleAppsScriptTypeAddOnEntryPoint $addOn)
  {
    $this->addOn = $addOn;
  }
  /**
   * @return GoogleAppsScriptTypeAddOnEntryPoint
   */
  public function getAddOn()
  {
    return $this->addOn;
  }
  /**
   * The type of the entry point.
   *
   * Accepted values: ENTRY_POINT_TYPE_UNSPECIFIED, WEB_APP, EXECUTION_API,
   * ADD_ON
   *
   * @param self::ENTRY_POINT_TYPE_* $entryPointType
   */
  public function setEntryPointType($entryPointType)
  {
    $this->entryPointType = $entryPointType;
  }
  /**
   * @return self::ENTRY_POINT_TYPE_*
   */
  public function getEntryPointType()
  {
    return $this->entryPointType;
  }
  /**
   * An entry point specification for Apps Script API execution calls.
   *
   * @param GoogleAppsScriptTypeExecutionApiEntryPoint $executionApi
   */
  public function setExecutionApi(GoogleAppsScriptTypeExecutionApiEntryPoint $executionApi)
  {
    $this->executionApi = $executionApi;
  }
  /**
   * @return GoogleAppsScriptTypeExecutionApiEntryPoint
   */
  public function getExecutionApi()
  {
    return $this->executionApi;
  }
  /**
   * An entry point specification for web apps.
   *
   * @param GoogleAppsScriptTypeWebAppEntryPoint $webApp
   */
  public function setWebApp(GoogleAppsScriptTypeWebAppEntryPoint $webApp)
  {
    $this->webApp = $webApp;
  }
  /**
   * @return GoogleAppsScriptTypeWebAppEntryPoint
   */
  public function getWebApp()
  {
    return $this->webApp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EntryPoint::class, 'Google_Service_Script_EntryPoint');
