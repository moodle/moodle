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

class ReauthSettings extends \Google\Model
{
  /**
   * @var string
   */
  public $maxInactivity;
  /**
   * @var string
   */
  public $reauthMethod;
  /**
   * @var string
   */
  public $sessionLength;
  /**
   * @var bool
   */
  public $sessionLengthEnabled;
  /**
   * @var bool
   */
  public $useOidcMaxAge;

  /**
   * @param string
   */
  public function setMaxInactivity($maxInactivity)
  {
    $this->maxInactivity = $maxInactivity;
  }
  /**
   * @return string
   */
  public function getMaxInactivity()
  {
    return $this->maxInactivity;
  }
  /**
   * @param string
   */
  public function setReauthMethod($reauthMethod)
  {
    $this->reauthMethod = $reauthMethod;
  }
  /**
   * @return string
   */
  public function getReauthMethod()
  {
    return $this->reauthMethod;
  }
  /**
   * @param string
   */
  public function setSessionLength($sessionLength)
  {
    $this->sessionLength = $sessionLength;
  }
  /**
   * @return string
   */
  public function getSessionLength()
  {
    return $this->sessionLength;
  }
  /**
   * @param bool
   */
  public function setSessionLengthEnabled($sessionLengthEnabled)
  {
    $this->sessionLengthEnabled = $sessionLengthEnabled;
  }
  /**
   * @return bool
   */
  public function getSessionLengthEnabled()
  {
    return $this->sessionLengthEnabled;
  }
  /**
   * @param bool
   */
  public function setUseOidcMaxAge($useOidcMaxAge)
  {
    $this->useOidcMaxAge = $useOidcMaxAge;
  }
  /**
   * @return bool
   */
  public function getUseOidcMaxAge()
  {
    return $this->useOidcMaxAge;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReauthSettings::class, 'Google_Service_AccessContextManager_ReauthSettings');
