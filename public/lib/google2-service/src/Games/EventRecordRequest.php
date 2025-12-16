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

namespace Google\Service\Games;

class EventRecordRequest extends \Google\Collection
{
  protected $collection_key = 'timePeriods';
  /**
   * The current time when this update was sent, in milliseconds, since 1970 UTC
   * (Unix Epoch).
   *
   * @var string
   */
  public $currentTimeMillis;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#eventRecordRequest`.
   *
   * @var string
   */
  public $kind;
  /**
   * The request ID used to identify this attempt to record events.
   *
   * @var string
   */
  public $requestId;
  protected $timePeriodsType = EventPeriodUpdate::class;
  protected $timePeriodsDataType = 'array';

  /**
   * The current time when this update was sent, in milliseconds, since 1970 UTC
   * (Unix Epoch).
   *
   * @param string $currentTimeMillis
   */
  public function setCurrentTimeMillis($currentTimeMillis)
  {
    $this->currentTimeMillis = $currentTimeMillis;
  }
  /**
   * @return string
   */
  public function getCurrentTimeMillis()
  {
    return $this->currentTimeMillis;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#eventRecordRequest`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The request ID used to identify this attempt to record events.
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
  /**
   * A list of the time period updates being made in this request.
   *
   * @param EventPeriodUpdate[] $timePeriods
   */
  public function setTimePeriods($timePeriods)
  {
    $this->timePeriods = $timePeriods;
  }
  /**
   * @return EventPeriodUpdate[]
   */
  public function getTimePeriods()
  {
    return $this->timePeriods;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EventRecordRequest::class, 'Google_Service_Games_EventRecordRequest');
