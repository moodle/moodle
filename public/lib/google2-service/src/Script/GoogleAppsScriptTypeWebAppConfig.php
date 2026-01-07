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

class GoogleAppsScriptTypeWebAppConfig extends \Google\Model
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
   * Default value, should not be used.
   */
  public const EXECUTE_AS_UNKNOWN_EXECUTE_AS = 'UNKNOWN_EXECUTE_AS';
  /**
   * The script runs as the user accessing the web app.
   */
  public const EXECUTE_AS_USER_ACCESSING = 'USER_ACCESSING';
  /**
   * The script runs as the user who deployed the web app. Note that this is not
   * necessarily the owner of the script project.
   */
  public const EXECUTE_AS_USER_DEPLOYING = 'USER_DEPLOYING';
  /**
   * Who has permission to run the web app.
   *
   * @var string
   */
  public $access;
  /**
   * Who to execute the web app as.
   *
   * @var string
   */
  public $executeAs;

  /**
   * Who has permission to run the web app.
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
  /**
   * Who to execute the web app as.
   *
   * Accepted values: UNKNOWN_EXECUTE_AS, USER_ACCESSING, USER_DEPLOYING
   *
   * @param self::EXECUTE_AS_* $executeAs
   */
  public function setExecuteAs($executeAs)
  {
    $this->executeAs = $executeAs;
  }
  /**
   * @return self::EXECUTE_AS_*
   */
  public function getExecuteAs()
  {
    return $this->executeAs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsScriptTypeWebAppConfig::class, 'Google_Service_Script_GoogleAppsScriptTypeWebAppConfig');
