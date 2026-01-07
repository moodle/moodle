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

namespace Google\Service\CloudTasks;

class Attempt extends \Google\Model
{
  /**
   * Output only. The time that this attempt was dispatched. `dispatch_time`
   * will be truncated to the nearest microsecond.
   *
   * @var string
   */
  public $dispatchTime;
  protected $responseStatusType = Status::class;
  protected $responseStatusDataType = '';
  /**
   * Output only. The time that this attempt response was received.
   * `response_time` will be truncated to the nearest microsecond.
   *
   * @var string
   */
  public $responseTime;
  /**
   * Output only. The time that this attempt was scheduled. `schedule_time` will
   * be truncated to the nearest microsecond.
   *
   * @var string
   */
  public $scheduleTime;

  /**
   * Output only. The time that this attempt was dispatched. `dispatch_time`
   * will be truncated to the nearest microsecond.
   *
   * @param string $dispatchTime
   */
  public function setDispatchTime($dispatchTime)
  {
    $this->dispatchTime = $dispatchTime;
  }
  /**
   * @return string
   */
  public function getDispatchTime()
  {
    return $this->dispatchTime;
  }
  /**
   * Output only. The response from the worker for this attempt. If
   * `response_time` is unset, then the task has not been attempted or is
   * currently running and the `response_status` field is meaningless.
   *
   * @param Status $responseStatus
   */
  public function setResponseStatus(Status $responseStatus)
  {
    $this->responseStatus = $responseStatus;
  }
  /**
   * @return Status
   */
  public function getResponseStatus()
  {
    return $this->responseStatus;
  }
  /**
   * Output only. The time that this attempt response was received.
   * `response_time` will be truncated to the nearest microsecond.
   *
   * @param string $responseTime
   */
  public function setResponseTime($responseTime)
  {
    $this->responseTime = $responseTime;
  }
  /**
   * @return string
   */
  public function getResponseTime()
  {
    return $this->responseTime;
  }
  /**
   * Output only. The time that this attempt was scheduled. `schedule_time` will
   * be truncated to the nearest microsecond.
   *
   * @param string $scheduleTime
   */
  public function setScheduleTime($scheduleTime)
  {
    $this->scheduleTime = $scheduleTime;
  }
  /**
   * @return string
   */
  public function getScheduleTime()
  {
    return $this->scheduleTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Attempt::class, 'Google_Service_CloudTasks_Attempt');
