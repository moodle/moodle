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

namespace Google\Service\AndroidManagement;

class StartLostModeStatus extends \Google\Model
{
  /**
   * Unspecified. This value is not used.
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * The device was put into lost mode.
   */
  public const STATUS_SUCCESS = 'SUCCESS';
  /**
   * The device could not be put into lost mode because the admin reset the
   * device's password recently.
   */
  public const STATUS_RESET_PASSWORD_RECENTLY = 'RESET_PASSWORD_RECENTLY';
  /**
   * The device could not be put into lost mode because the user exited lost
   * mode recently.
   */
  public const STATUS_USER_EXIT_LOST_MODE_RECENTLY = 'USER_EXIT_LOST_MODE_RECENTLY';
  /**
   * The device is already in lost mode.
   */
  public const STATUS_ALREADY_IN_LOST_MODE = 'ALREADY_IN_LOST_MODE';
  /**
   * The status. See StartLostModeStatus.
   *
   * @var string
   */
  public $status;

  /**
   * The status. See StartLostModeStatus.
   *
   * Accepted values: STATUS_UNSPECIFIED, SUCCESS, RESET_PASSWORD_RECENTLY,
   * USER_EXIT_LOST_MODE_RECENTLY, ALREADY_IN_LOST_MODE
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StartLostModeStatus::class, 'Google_Service_AndroidManagement_StartLostModeStatus');
