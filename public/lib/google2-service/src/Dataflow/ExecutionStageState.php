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

class ExecutionStageState extends \Google\Model
{
  /**
   * The job's run state isn't specified.
   */
  public const EXECUTION_STAGE_STATE_JOB_STATE_UNKNOWN = 'JOB_STATE_UNKNOWN';
  /**
   * `JOB_STATE_STOPPED` indicates that the job has not yet started to run.
   */
  public const EXECUTION_STAGE_STATE_JOB_STATE_STOPPED = 'JOB_STATE_STOPPED';
  /**
   * `JOB_STATE_RUNNING` indicates that the job is currently running.
   */
  public const EXECUTION_STAGE_STATE_JOB_STATE_RUNNING = 'JOB_STATE_RUNNING';
  /**
   * `JOB_STATE_DONE` indicates that the job has successfully completed. This is
   * a terminal job state. This state may be set by the Cloud Dataflow service,
   * as a transition from `JOB_STATE_RUNNING`. It may also be set via a Cloud
   * Dataflow `UpdateJob` call, if the job has not yet reached a terminal state.
   */
  public const EXECUTION_STAGE_STATE_JOB_STATE_DONE = 'JOB_STATE_DONE';
  /**
   * `JOB_STATE_FAILED` indicates that the job has failed. This is a terminal
   * job state. This state may only be set by the Cloud Dataflow service, and
   * only as a transition from `JOB_STATE_RUNNING`.
   */
  public const EXECUTION_STAGE_STATE_JOB_STATE_FAILED = 'JOB_STATE_FAILED';
  /**
   * `JOB_STATE_CANCELLED` indicates that the job has been explicitly cancelled.
   * This is a terminal job state. This state may only be set via a Cloud
   * Dataflow `UpdateJob` call, and only if the job has not yet reached another
   * terminal state.
   */
  public const EXECUTION_STAGE_STATE_JOB_STATE_CANCELLED = 'JOB_STATE_CANCELLED';
  /**
   * `JOB_STATE_UPDATED` indicates that the job was successfully updated,
   * meaning that this job was stopped and another job was started, inheriting
   * state from this one. This is a terminal job state. This state may only be
   * set by the Cloud Dataflow service, and only as a transition from
   * `JOB_STATE_RUNNING`.
   */
  public const EXECUTION_STAGE_STATE_JOB_STATE_UPDATED = 'JOB_STATE_UPDATED';
  /**
   * `JOB_STATE_DRAINING` indicates that the job is in the process of draining.
   * A draining job has stopped pulling from its input sources and is processing
   * any data that remains in-flight. This state may be set via a Cloud Dataflow
   * `UpdateJob` call, but only as a transition from `JOB_STATE_RUNNING`. Jobs
   * that are draining may only transition to `JOB_STATE_DRAINED`,
   * `JOB_STATE_CANCELLED`, or `JOB_STATE_FAILED`.
   */
  public const EXECUTION_STAGE_STATE_JOB_STATE_DRAINING = 'JOB_STATE_DRAINING';
  /**
   * `JOB_STATE_DRAINED` indicates that the job has been drained. A drained job
   * terminated by stopping pulling from its input sources and processing any
   * data that remained in-flight when draining was requested. This state is a
   * terminal state, may only be set by the Cloud Dataflow service, and only as
   * a transition from `JOB_STATE_DRAINING`.
   */
  public const EXECUTION_STAGE_STATE_JOB_STATE_DRAINED = 'JOB_STATE_DRAINED';
  /**
   * `JOB_STATE_PENDING` indicates that the job has been created but is not yet
   * running. Jobs that are pending may only transition to `JOB_STATE_RUNNING`,
   * or `JOB_STATE_FAILED`.
   */
  public const EXECUTION_STAGE_STATE_JOB_STATE_PENDING = 'JOB_STATE_PENDING';
  /**
   * `JOB_STATE_CANCELLING` indicates that the job has been explicitly cancelled
   * and is in the process of stopping. Jobs that are cancelling may only
   * transition to `JOB_STATE_CANCELLED` or `JOB_STATE_FAILED`.
   */
  public const EXECUTION_STAGE_STATE_JOB_STATE_CANCELLING = 'JOB_STATE_CANCELLING';
  /**
   * `JOB_STATE_QUEUED` indicates that the job has been created but is being
   * delayed until launch. Jobs that are queued may only transition to
   * `JOB_STATE_PENDING` or `JOB_STATE_CANCELLED`.
   */
  public const EXECUTION_STAGE_STATE_JOB_STATE_QUEUED = 'JOB_STATE_QUEUED';
  /**
   * `JOB_STATE_RESOURCE_CLEANING_UP` indicates that the batch job's associated
   * resources are currently being cleaned up after a successful run. Currently,
   * this is an opt-in feature, please reach out to Cloud support team if you
   * are interested.
   */
  public const EXECUTION_STAGE_STATE_JOB_STATE_RESOURCE_CLEANING_UP = 'JOB_STATE_RESOURCE_CLEANING_UP';
  /**
   * `JOB_STATE_PAUSING` is not implemented yet.
   */
  public const EXECUTION_STAGE_STATE_JOB_STATE_PAUSING = 'JOB_STATE_PAUSING';
  /**
   * `JOB_STATE_PAUSED` is not implemented yet.
   */
  public const EXECUTION_STAGE_STATE_JOB_STATE_PAUSED = 'JOB_STATE_PAUSED';
  /**
   * The time at which the stage transitioned to this state.
   *
   * @var string
   */
  public $currentStateTime;
  /**
   * The name of the execution stage.
   *
   * @var string
   */
  public $executionStageName;
  /**
   * Executions stage states allow the same set of values as JobState.
   *
   * @var string
   */
  public $executionStageState;

  /**
   * The time at which the stage transitioned to this state.
   *
   * @param string $currentStateTime
   */
  public function setCurrentStateTime($currentStateTime)
  {
    $this->currentStateTime = $currentStateTime;
  }
  /**
   * @return string
   */
  public function getCurrentStateTime()
  {
    return $this->currentStateTime;
  }
  /**
   * The name of the execution stage.
   *
   * @param string $executionStageName
   */
  public function setExecutionStageName($executionStageName)
  {
    $this->executionStageName = $executionStageName;
  }
  /**
   * @return string
   */
  public function getExecutionStageName()
  {
    return $this->executionStageName;
  }
  /**
   * Executions stage states allow the same set of values as JobState.
   *
   * Accepted values: JOB_STATE_UNKNOWN, JOB_STATE_STOPPED, JOB_STATE_RUNNING,
   * JOB_STATE_DONE, JOB_STATE_FAILED, JOB_STATE_CANCELLED, JOB_STATE_UPDATED,
   * JOB_STATE_DRAINING, JOB_STATE_DRAINED, JOB_STATE_PENDING,
   * JOB_STATE_CANCELLING, JOB_STATE_QUEUED, JOB_STATE_RESOURCE_CLEANING_UP,
   * JOB_STATE_PAUSING, JOB_STATE_PAUSED
   *
   * @param self::EXECUTION_STAGE_STATE_* $executionStageState
   */
  public function setExecutionStageState($executionStageState)
  {
    $this->executionStageState = $executionStageState;
  }
  /**
   * @return self::EXECUTION_STAGE_STATE_*
   */
  public function getExecutionStageState()
  {
    return $this->executionStageState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExecutionStageState::class, 'Google_Service_Dataflow_ExecutionStageState');
