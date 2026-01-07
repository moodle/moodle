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

namespace Google\Service\Datastream;

class BackfillJob extends \Google\Collection
{
  /**
   * Default value.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Backfill job was never started for the stream object (stream has backfill
   * strategy defined as manual or object was explicitly excluded from automatic
   * backfill).
   */
  public const STATE_NOT_STARTED = 'NOT_STARTED';
  /**
   * Backfill job will start pending available resources.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * Backfill job is running.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Backfill job stopped (next job run will start from beginning).
   */
  public const STATE_STOPPED = 'STOPPED';
  /**
   * Backfill job failed (due to an error).
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Backfill completed successfully.
   */
  public const STATE_COMPLETED = 'COMPLETED';
  /**
   * Backfill job failed since the table structure is currently unsupported for
   * backfill.
   */
  public const STATE_UNSUPPORTED = 'UNSUPPORTED';
  /**
   * Default value.
   */
  public const TRIGGER_TRIGGER_UNSPECIFIED = 'TRIGGER_UNSPECIFIED';
  /**
   * Object backfill job was triggered automatically according to the stream's
   * backfill strategy.
   */
  public const TRIGGER_AUTOMATIC = 'AUTOMATIC';
  /**
   * Object backfill job was triggered manually using the dedicated API.
   */
  public const TRIGGER_MANUAL = 'MANUAL';
  protected $collection_key = 'errors';
  protected $errorsType = Error::class;
  protected $errorsDataType = 'array';
  /**
   * Output only. Backfill job's end time.
   *
   * @var string
   */
  public $lastEndTime;
  /**
   * Output only. Backfill job's start time.
   *
   * @var string
   */
  public $lastStartTime;
  /**
   * Output only. Backfill job state.
   *
   * @var string
   */
  public $state;
  /**
   * Backfill job's triggering reason.
   *
   * @var string
   */
  public $trigger;

  /**
   * Output only. Errors which caused the backfill job to fail.
   *
   * @param Error[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return Error[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Output only. Backfill job's end time.
   *
   * @param string $lastEndTime
   */
  public function setLastEndTime($lastEndTime)
  {
    $this->lastEndTime = $lastEndTime;
  }
  /**
   * @return string
   */
  public function getLastEndTime()
  {
    return $this->lastEndTime;
  }
  /**
   * Output only. Backfill job's start time.
   *
   * @param string $lastStartTime
   */
  public function setLastStartTime($lastStartTime)
  {
    $this->lastStartTime = $lastStartTime;
  }
  /**
   * @return string
   */
  public function getLastStartTime()
  {
    return $this->lastStartTime;
  }
  /**
   * Output only. Backfill job state.
   *
   * Accepted values: STATE_UNSPECIFIED, NOT_STARTED, PENDING, ACTIVE, STOPPED,
   * FAILED, COMPLETED, UNSUPPORTED
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
  /**
   * Backfill job's triggering reason.
   *
   * Accepted values: TRIGGER_UNSPECIFIED, AUTOMATIC, MANUAL
   *
   * @param self::TRIGGER_* $trigger
   */
  public function setTrigger($trigger)
  {
    $this->trigger = $trigger;
  }
  /**
   * @return self::TRIGGER_*
   */
  public function getTrigger()
  {
    return $this->trigger;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackfillJob::class, 'Google_Service_Datastream_BackfillJob');
