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

namespace Google\Service\Dataflow;

class AutoscalingEvent extends \Google\Model
{
  /**
   * Default type for the enum. Value should never be returned.
   */
  public const EVENT_TYPE_TYPE_UNKNOWN = 'TYPE_UNKNOWN';
  /**
   * The TARGET_NUM_WORKERS_CHANGED type should be used when the target worker
   * pool size has changed at the start of an actuation. An event should always
   * be specified as TARGET_NUM_WORKERS_CHANGED if it reflects a change in the
   * target_num_workers.
   */
  public const EVENT_TYPE_TARGET_NUM_WORKERS_CHANGED = 'TARGET_NUM_WORKERS_CHANGED';
  /**
   * The CURRENT_NUM_WORKERS_CHANGED type should be used when actual worker pool
   * size has been changed, but the target_num_workers has not changed.
   */
  public const EVENT_TYPE_CURRENT_NUM_WORKERS_CHANGED = 'CURRENT_NUM_WORKERS_CHANGED';
  /**
   * The ACTUATION_FAILURE type should be used when we want to report an error
   * to the user indicating why the current number of workers in the pool could
   * not be changed. Displayed in the current status and history widgets.
   */
  public const EVENT_TYPE_ACTUATION_FAILURE = 'ACTUATION_FAILURE';
  /**
   * Used when we want to report to the user a reason why we are not currently
   * adjusting the number of workers. Should specify both target_num_workers,
   * current_num_workers and a decision_message.
   */
  public const EVENT_TYPE_NO_CHANGE = 'NO_CHANGE';
  /**
   * The current number of workers the job has.
   *
   * @var string
   */
  public $currentNumWorkers;
  protected $descriptionType = StructuredMessage::class;
  protected $descriptionDataType = '';
  /**
   * The type of autoscaling event to report.
   *
   * @var string
   */
  public $eventType;
  /**
   * The target number of workers the worker pool wants to resize to use.
   *
   * @var string
   */
  public $targetNumWorkers;
  /**
   * The time this event was emitted to indicate a new target or current
   * num_workers value.
   *
   * @var string
   */
  public $time;
  /**
   * A short and friendly name for the worker pool this event refers to.
   *
   * @var string
   */
  public $workerPool;

  /**
   * The current number of workers the job has.
   *
   * @param string $currentNumWorkers
   */
  public function setCurrentNumWorkers($currentNumWorkers)
  {
    $this->currentNumWorkers = $currentNumWorkers;
  }
  /**
   * @return string
   */
  public function getCurrentNumWorkers()
  {
    return $this->currentNumWorkers;
  }
  /**
   * A message describing why the system decided to adjust the current number of
   * workers, why it failed, or why the system decided to not make any changes
   * to the number of workers.
   *
   * @param StructuredMessage $description
   */
  public function setDescription(StructuredMessage $description)
  {
    $this->description = $description;
  }
  /**
   * @return StructuredMessage
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The type of autoscaling event to report.
   *
   * Accepted values: TYPE_UNKNOWN, TARGET_NUM_WORKERS_CHANGED,
   * CURRENT_NUM_WORKERS_CHANGED, ACTUATION_FAILURE, NO_CHANGE
   *
   * @param self::EVENT_TYPE_* $eventType
   */
  public function setEventType($eventType)
  {
    $this->eventType = $eventType;
  }
  /**
   * @return self::EVENT_TYPE_*
   */
  public function getEventType()
  {
    return $this->eventType;
  }
  /**
   * The target number of workers the worker pool wants to resize to use.
   *
   * @param string $targetNumWorkers
   */
  public function setTargetNumWorkers($targetNumWorkers)
  {
    $this->targetNumWorkers = $targetNumWorkers;
  }
  /**
   * @return string
   */
  public function getTargetNumWorkers()
  {
    return $this->targetNumWorkers;
  }
  /**
   * The time this event was emitted to indicate a new target or current
   * num_workers value.
   *
   * @param string $time
   */
  public function setTime($time)
  {
    $this->time = $time;
  }
  /**
   * @return string
   */
  public function getTime()
  {
    return $this->time;
  }
  /**
   * A short and friendly name for the worker pool this event refers to.
   *
   * @param string $workerPool
   */
  public function setWorkerPool($workerPool)
  {
    $this->workerPool = $workerPool;
  }
  /**
   * @return string
   */
  public function getWorkerPool()
  {
    return $this->workerPool;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutoscalingEvent::class, 'Google_Service_Dataflow_AutoscalingEvent');
