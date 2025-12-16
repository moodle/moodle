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

class JobStatus extends \Google\Collection
{
  /**
   * Job state unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Job is admitted (validated and persisted) and waiting for resources.
   */
  public const STATE_QUEUED = 'QUEUED';
  /**
   * Job is scheduled to run as soon as resource allocation is ready. The
   * resource allocation may happen at a later time but with a high chance to
   * succeed.
   */
  public const STATE_SCHEDULED = 'SCHEDULED';
  /**
   * Resource allocation has been successful. At least one Task in the Job is
   * RUNNING.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * All Tasks in the Job have finished successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * At least one Task in the Job has failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The Job will be deleted, but has not been deleted yet. Typically this is
   * because resources used by the Job are still being cleaned up.
   */
  public const STATE_DELETION_IN_PROGRESS = 'DELETION_IN_PROGRESS';
  /**
   * The Job cancellation is in progress, this is because the resources used by
   * the Job are still being cleaned up.
   */
  public const STATE_CANCELLATION_IN_PROGRESS = 'CANCELLATION_IN_PROGRESS';
  /**
   * The Job has been cancelled, the task executions were stopped and the
   * resources were cleaned up.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  protected $collection_key = 'statusEvents';
  /**
   * The duration of time that the Job spent in status RUNNING.
   *
   * @var string
   */
  public $runDuration;
  /**
   * Job state
   *
   * @var string
   */
  public $state;
  protected $statusEventsType = StatusEvent::class;
  protected $statusEventsDataType = 'array';
  protected $taskGroupsType = TaskGroupStatus::class;
  protected $taskGroupsDataType = 'map';

  /**
   * The duration of time that the Job spent in status RUNNING.
   *
   * @param string $runDuration
   */
  public function setRunDuration($runDuration)
  {
    $this->runDuration = $runDuration;
  }
  /**
   * @return string
   */
  public function getRunDuration()
  {
    return $this->runDuration;
  }
  /**
   * Job state
   *
   * Accepted values: STATE_UNSPECIFIED, QUEUED, SCHEDULED, RUNNING, SUCCEEDED,
   * FAILED, DELETION_IN_PROGRESS, CANCELLATION_IN_PROGRESS, CANCELLED
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
   * Job status events
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
  /**
   * Aggregated task status for each TaskGroup in the Job. The map key is
   * TaskGroup ID.
   *
   * @param TaskGroupStatus[] $taskGroups
   */
  public function setTaskGroups($taskGroups)
  {
    $this->taskGroups = $taskGroups;
  }
  /**
   * @return TaskGroupStatus[]
   */
  public function getTaskGroups()
  {
    return $this->taskGroups;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JobStatus::class, 'Google_Service_Batch_JobStatus');
