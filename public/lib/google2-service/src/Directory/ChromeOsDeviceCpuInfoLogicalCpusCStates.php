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

namespace Google\Service\Directory;

class ChromeOsDeviceCpuInfoLogicalCpusCStates extends \Google\Model
{
  /**
   * Name of the state.
   *
   * @var string
   */
  public $displayName;
  /**
   * Time spent in the state since the last reboot.
   *
   * @var string
   */
  public $sessionDuration;

  /**
   * Name of the state.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Time spent in the state since the last reboot.
   *
   * @param string $sessionDuration
   */
  public function setSessionDuration($sessionDuration)
  {
    $this->sessionDuration = $sessionDuration;
  }
  /**
   * @return string
   */
  public function getSessionDuration()
  {
    return $this->sessionDuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChromeOsDeviceCpuInfoLogicalCpusCStates::class, 'Google_Service_Directory_ChromeOsDeviceCpuInfoLogicalCpusCStates');
