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

class GoogleAppsScriptTypeExecutionApiConfig extends \Google\Model
{
  /**
   * Default value, should not be used.
   */
  public const ACCESS_UNKNOWN_ACCESS = 'UNKNOWN_ACCESS';
  /**
   * Only the user who deployed the web app or executable can access it. Note
   * that this is not necessarily the owner of the script project.
   */
  public const ACCESS_MYSELF = 'MYSELF';
  /**
   * Only users in the same domain as the user who deployed the web app or
   * executable can access it.
   */
  public const ACCESS_DOMAIN = 'DOMAIN';
  /**
   * Any logged in user can access the web app or executable.
   */
  public const ACCESS_ANYONE = 'ANYONE';
  /**
   * Any user, logged in or not, can access the web app or executable.
   */
  public const ACCESS_ANYONE_ANONYMOUS = 'ANYONE_ANONYMOUS';
  /**
   * Who has permission to run the API executable.
   *
   * @var string
   */
  public $access;

  /**
   * Who has permission to run the API executable.
   *
   * Accepted values: UNKNOWN_ACCESS, MYSELF, DOMAIN, ANYONE, ANYONE_ANONYMOUS
   *
   * @param self::ACCESS_* $access
   */
  public function setAccess($access)
  {
    $this->access = $access;
  }
  /**
   * @return self::ACCESS_*
   */
  public function getAccess()
  {
    return $this->access;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsScriptTypeExecutionApiConfig::class, 'Google_Service_Script_GoogleAppsScriptTypeExecutionApiConfig');
