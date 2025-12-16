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

namespace Google\Service\Verifiedaccess;

class Antivirus extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * No antivirus was detected on the device.
   */
  public const STATE_MISSING = 'MISSING';
  /**
   * At least one antivirus was installed on the device but none was enabled.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * At least one antivirus was enabled on the device.
   */
  public const STATE_ENABLED = 'ENABLED';
  /**
   * Output only. The state of the antivirus on the device. Introduced in Chrome
   * M136.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The state of the antivirus on the device. Introduced in Chrome
   * M136.
   *
   * Accepted values: STATE_UNSPECIFIED, MISSING, DISABLED, ENABLED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Antivirus::class, 'Google_Service_Verifiedaccess_Antivirus');
