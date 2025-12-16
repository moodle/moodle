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

namespace Google\Service\AlertCenter;

class LoginDetails extends \Google\Model
{
  /**
   * Optional. The human-readable IP address (for example, `11.22.33.44`) that
   * is associated with the warning event.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * Optional. The successful login time that is associated with the warning
   * event. This isn't present for blocked login attempts.
   *
   * @var string
   */
  public $loginTime;

  /**
   * Optional. The human-readable IP address (for example, `11.22.33.44`) that
   * is associated with the warning event.
   *
   * @param string $ipAddress
   */
  public function setIpAddress($ipAddress)
  {
    $this->ipAddress = $ipAddress;
  }
  /**
   * @return string
   */
  public function getIpAddress()
  {
    return $this->ipAddress;
  }
  /**
   * Optional. The successful login time that is associated with the warning
   * event. This isn't present for blocked login attempts.
   *
   * @param string $loginTime
   */
  public function setLoginTime($loginTime)
  {
    $this->loginTime = $loginTime;
  }
  /**
   * @return string
   */
  public function getLoginTime()
  {
    return $this->loginTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LoginDetails::class, 'Google_Service_AlertCenter_LoginDetails');
