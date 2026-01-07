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

namespace Google\Service\AccessContextManager;

class AccessSettings extends \Google\Collection
{
  protected $collection_key = 'accessLevels';
  /**
   * Optional. Access level that a user must have to be granted access. Only one
   * access level is supported, not multiple. This repeated field must have
   * exactly one element. Example:
   * "accessPolicies/9522/accessLevels/device_trusted"
   *
   * @var string[]
   */
  public $accessLevels;
  protected $sessionSettingsType = SessionSettings::class;
  protected $sessionSettingsDataType = '';

  /**
   * Optional. Access level that a user must have to be granted access. Only one
   * access level is supported, not multiple. This repeated field must have
   * exactly one element. Example:
   * "accessPolicies/9522/accessLevels/device_trusted"
   *
   * @param string[] $accessLevels
   */
  public function setAccessLevels($accessLevels)
  {
    $this->accessLevels = $accessLevels;
  }
  /**
   * @return string[]
   */
  public function getAccessLevels()
  {
    return $this->accessLevels;
  }
  /**
   * Optional. Session settings applied to user access on a given AccessScope.
   *
   * @param SessionSettings $sessionSettings
   */
  public function setSessionSettings(SessionSettings $sessionSettings)
  {
    $this->sessionSettings = $sessionSettings;
  }
  /**
   * @return SessionSettings
   */
  public function getSessionSettings()
  {
    return $this->sessionSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccessSettings::class, 'Google_Service_AccessContextManager_AccessSettings');
