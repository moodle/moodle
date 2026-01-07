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

class StopLostModeStatus extends \Google\Model
{
  /**
   * Unspecified. This value is not used.
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * The device was taken out of lost mode.
   */
  public const STATUS_SUCCESS = 'SUCCESS';
  /**
   * The device is not in lost mode.
   */
  public const STATUS_NOT_IN_LOST_MODE = 'NOT_IN_LOST_MODE';
  /**
   * The status. See StopLostModeStatus.
   *
   * @var string
   */
  public $status;

  /**
   * The status. See StopLostModeStatus.
   *
   * Accepted values: STATUS_UNSPECIFIED, SUCCESS, NOT_IN_LOST_MODE
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
class_alias(StopLostModeStatus::class, 'Google_Service_AndroidManagement_StopLostModeStatus');
