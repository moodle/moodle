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

class EventRecordFailure extends \Google\Model
{
  /**
   * An attempt was made to set an event that was not defined.
   */
  public const FAILURE_CAUSE_NOT_FOUND = 'NOT_FOUND';
  /**
   * An attempt was made to increment an event by a non-positive value.
   */
  public const FAILURE_CAUSE_INVALID_UPDATE_VALUE = 'INVALID_UPDATE_VALUE';
  /**
   * The ID of the event that was not updated.
   *
   * @var string
   */
  public $eventId;
  /**
   * The cause for the update failure.
   *
   * @var string
   */
  public $failureCause;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#eventRecordFailure`.
   *
   * @var string
   */
  public $kind;

  /**
   * The ID of the event that was not updated.
   *
   * @param string $eventId
   */
  public function setEventId($eventId)
  {
    $this->eventId = $eventId;
  }
  /**
   * @return string
   */
  public function getEventId()
  {
    return $this->eventId;
  }
  /**
   * The cause for the update failure.
   *
   * Accepted values: NOT_FOUND, INVALID_UPDATE_VALUE
   *
   * @param self::FAILURE_CAUSE_* $failureCause
   */
  public function setFailureCause($failureCause)
  {
    $this->failureCause = $failureCause;
  }
  /**
   * @return self::FAILURE_CAUSE_*
   */
  public function getFailureCause()
  {
    return $this->failureCause;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#eventRecordFailure`.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EventRecordFailure::class, 'Google_Service_Games_EventRecordFailure');
