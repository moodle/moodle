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

namespace Google\Service\Batch;

class TaskStatus extends \Google\Collection
{
  /**
   * Unknown state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The Task is created and waiting for resources.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The Task is assigned to at least one VM.
   */
  public const STATE_ASSIGNED = 'ASSIGNED';
  /**
   * The Task is running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The Task has failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The Task has succeeded.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The Task has not been executed when the Job finishes.
   */
  public const STATE_UNEXECUTED = 'UNEXECUTED';
  protected $collection_key = 'statusEvents';
  /**
   * Task state.
   *
   * @var string
   */
  public $state;
  protected $statusEventsType = StatusEvent::class;
  protected $statusEventsDataType = 'array';

  /**
   * Task state.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, ASSIGNED, RUNNING, FAILED,
   * SUCCEEDED, UNEXECUTED
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
   * Detailed info about why the state is reached.
   *
   * @param StatusEvent[] $statusEvents
   */
  public function setStatusEvents($statusEvents)
  {
    $this->statusEvents = $statusEvents;
  }
  /**
   * @return StatusEvent[]
   */
  public function getStatusEvents()
  {
    return $this->statusEvents;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TaskStatus::class, 'Google_Service_Batch_TaskStatus');
