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

namespace Google\Service\AIPlatformNotebooks;

class Event extends \Google\Model
{
  /**
   * Event is not specified.
   */
  public const TYPE_EVENT_TYPE_UNSPECIFIED = 'EVENT_TYPE_UNSPECIFIED';
  /**
   * The instance / runtime is idle
   */
  public const TYPE_IDLE = 'IDLE';
  /**
   * The instance / runtime is available. This event indicates that instance /
   * runtime underlying compute is operational.
   */
  public const TYPE_HEARTBEAT = 'HEARTBEAT';
  /**
   * The instance / runtime health is available. This event indicates that
   * instance / runtime health information.
   */
  public const TYPE_HEALTH = 'HEALTH';
  /**
   * The instance / runtime is available. This event allows instance / runtime
   * to send Host maintenance information to Control Plane.
   * https://cloud.google.com/compute/docs/gpus/gpu-host-maintenance
   */
  public const TYPE_MAINTENANCE = 'MAINTENANCE';
  /**
   * The instance / runtime is available. This event indicates that the instance
   * had metadata that needs to be modified.
   */
  public const TYPE_METADATA_CHANGE = 'METADATA_CHANGE';
  /**
   * Optional. Event details. This field is used to pass event information.
   *
   * @var string[]
   */
  public $details;
  /**
   * Optional. Event report time.
   *
   * @var string
   */
  public $reportTime;
  /**
   * Optional. Event type.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. Event details. This field is used to pass event information.
   *
   * @param string[] $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return string[]
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * Optional. Event report time.
   *
   * @param string $reportTime
   */
  public function setReportTime($reportTime)
  {
    $this->reportTime = $reportTime;
  }
  /**
   * @return string
   */
  public function getReportTime()
  {
    return $this->reportTime;
  }
  /**
   * Optional. Event type.
   *
   * Accepted values: EVENT_TYPE_UNSPECIFIED, IDLE, HEARTBEAT, HEALTH,
   * MAINTENANCE, METADATA_CHANGE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Event::class, 'Google_Service_AIPlatformNotebooks_Event');
