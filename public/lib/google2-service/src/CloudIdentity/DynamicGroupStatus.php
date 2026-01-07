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

namespace Google\Service\CloudIdentity;

class DynamicGroupStatus extends \Google\Model
{
  /**
   * Default.
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * The dynamic group is up-to-date.
   */
  public const STATUS_UP_TO_DATE = 'UP_TO_DATE';
  /**
   * The dynamic group has just been created and memberships are being updated.
   */
  public const STATUS_UPDATING_MEMBERSHIPS = 'UPDATING_MEMBERSHIPS';
  /**
   * Group is in an unrecoverable state and its memberships can't be updated.
   */
  public const STATUS_INVALID_QUERY = 'INVALID_QUERY';
  /**
   * Status of the dynamic group.
   *
   * @var string
   */
  public $status;
  /**
   * The latest time at which the dynamic group is guaranteed to be in the given
   * status. If status is `UP_TO_DATE`, the latest time at which the dynamic
   * group was confirmed to be up-to-date. If status is `UPDATING_MEMBERSHIPS`,
   * the time at which dynamic group was created.
   *
   * @var string
   */
  public $statusTime;

  /**
   * Status of the dynamic group.
   *
   * Accepted values: STATUS_UNSPECIFIED, UP_TO_DATE, UPDATING_MEMBERSHIPS,
   * INVALID_QUERY
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
  /**
   * The latest time at which the dynamic group is guaranteed to be in the given
   * status. If status is `UP_TO_DATE`, the latest time at which the dynamic
   * group was confirmed to be up-to-date. If status is `UPDATING_MEMBERSHIPS`,
   * the time at which dynamic group was created.
   *
   * @param string $statusTime
   */
  public function setStatusTime($statusTime)
  {
    $this->statusTime = $statusTime;
  }
  /**
   * @return string
   */
  public function getStatusTime()
  {
    return $this->statusTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DynamicGroupStatus::class, 'Google_Service_CloudIdentity_DynamicGroupStatus');
