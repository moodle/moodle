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

namespace Google\Service\DataFusion;

class MaintenanceEvent extends \Google\Model
{
  /**
   * The state of the maintenance event is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The maintenance is scheduled but has not started.
   */
  public const STATE_SCHEDULED = 'SCHEDULED';
  /**
   * The maintenance has been started.
   */
  public const STATE_STARTED = 'STARTED';
  /**
   * The maintenance has been completed.
   */
  public const STATE_COMPLETED = 'COMPLETED';
  /**
   * Output only. The end time of the maintenance event provided in [RFC
   * 3339](https://www.ietf.org/rfc/rfc3339.txt) format. Example:
   * "2024-01-02T12:04:06-06:00" This field will be empty if the maintenance
   * event is not yet complete.
   *
   * @var string
   */
  public $endTime;
  /**
   * Output only. The start time of the maintenance event provided in [RFC
   * 3339](https://www.ietf.org/rfc/rfc3339.txt) format. Example:
   * "2024-01-01T12:04:06-04:00"
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. The state of the maintenance event.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The end time of the maintenance event provided in [RFC
   * 3339](https://www.ietf.org/rfc/rfc3339.txt) format. Example:
   * "2024-01-02T12:04:06-06:00" This field will be empty if the maintenance
   * event is not yet complete.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Output only. The start time of the maintenance event provided in [RFC
   * 3339](https://www.ietf.org/rfc/rfc3339.txt) format. Example:
   * "2024-01-01T12:04:06-04:00"
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Output only. The state of the maintenance event.
   *
   * Accepted values: STATE_UNSPECIFIED, SCHEDULED, STARTED, COMPLETED
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
class_alias(MaintenanceEvent::class, 'Google_Service_DataFusion_MaintenanceEvent');
