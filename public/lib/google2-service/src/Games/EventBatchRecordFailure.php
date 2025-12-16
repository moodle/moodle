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

class EventBatchRecordFailure extends \Google\Model
{
  /**
   * A batch request was issued with more events than are allowed in a single
   * batch.
   */
  public const FAILURE_CAUSE_TOO_LARGE = 'TOO_LARGE';
  /**
   * A batch was sent with data too far in the past to record.
   */
  public const FAILURE_CAUSE_TIME_PERIOD_EXPIRED = 'TIME_PERIOD_EXPIRED';
  /**
   * A batch was sent with a time range that was too short.
   */
  public const FAILURE_CAUSE_TIME_PERIOD_SHORT = 'TIME_PERIOD_SHORT';
  /**
   * A batch was sent with a time range that was too long.
   */
  public const FAILURE_CAUSE_TIME_PERIOD_LONG = 'TIME_PERIOD_LONG';
  /**
   * An attempt was made to record a batch of data which was already seen.
   */
  public const FAILURE_CAUSE_ALREADY_UPDATED = 'ALREADY_UPDATED';
  /**
   * An attempt was made to record data faster than the server will apply
   * updates.
   */
  public const FAILURE_CAUSE_RECORD_RATE_HIGH = 'RECORD_RATE_HIGH';
  /**
   * The cause for the update failure.
   *
   * @var string
   */
  public $failureCause;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#eventBatchRecordFailure`.
   *
   * @var string
   */
  public $kind;
  protected $rangeType = EventPeriodRange::class;
  protected $rangeDataType = '';

  /**
   * The cause for the update failure.
   *
   * Accepted values: TOO_LARGE, TIME_PERIOD_EXPIRED, TIME_PERIOD_SHORT,
   * TIME_PERIOD_LONG, ALREADY_UPDATED, RECORD_RATE_HIGH
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
   * string `games#eventBatchRecordFailure`.
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
   * The time range which was rejected; empty for a request-wide failure.
   *
   * @param EventPeriodRange $range
   */
  public function setRange(EventPeriodRange $range)
  {
    $this->range = $range;
  }
  /**
   * @return EventPeriodRange
   */
  public function getRange()
  {
    return $this->range;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EventBatchRecordFailure::class, 'Google_Service_Games_EventBatchRecordFailure');
