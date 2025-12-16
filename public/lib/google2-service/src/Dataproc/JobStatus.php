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

namespace Google\Service\Dataproc;

class JobStatus extends \Google\Model
{
  /**
   * The job state is unknown.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The job is pending; it has been submitted, but is not yet running.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * Job has been received by the service and completed initial setup; it will
   * soon be submitted to the cluster.
   */
  public const STATE_SETUP_DONE = 'SETUP_DONE';
  /**
   * The job is running on the cluster.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * A CancelJob request has been received, but is pending.
   */
  public const STATE_CANCEL_PENDING = 'CANCEL_PENDING';
  /**
   * Transient in-flight resources have been canceled, and the request to cancel
   * the running job has been issued to the cluster.
   */
  public const STATE_CANCEL_STARTED = 'CANCEL_STARTED';
  /**
   * The job cancellation was successful.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The job has completed successfully.
   */
  public const STATE_DONE = 'DONE';
  /**
   * The job has completed, but encountered an error.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * Job attempt has failed. The detail field contains failure details for this
   * attempt.Applies to restartable jobs only.
   */
  public const STATE_ATTEMPT_FAILURE = 'ATTEMPT_FAILURE';
  /**
   * The job substate is unknown.
   */
  public const SUBSTATE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The Job is submitted to the agent.Applies to RUNNING state.
   */
  public const SUBSTATE_SUBMITTED = 'SUBMITTED';
  /**
   * The Job has been received and is awaiting execution (it might be waiting
   * for a condition to be met). See the "details" field for the reason for the
   * delay.Applies to RUNNING state.
   */
  public const SUBSTATE_QUEUED = 'QUEUED';
  /**
   * The agent-reported status is out of date, which can be caused by a loss of
   * communication between the agent and Dataproc. If the agent does not send a
   * timely update, the job will fail.Applies to RUNNING state.
   */
  public const SUBSTATE_STALE_STATUS = 'STALE_STATUS';
  /**
   * Optional. Output only. Job state details, such as an error description if
   * the state is ERROR.
   *
   * @var string
   */
  public $details;
  /**
   * Output only. A state message specifying the overall job state.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The time when this state was entered.
   *
   * @var string
   */
  public $stateStartTime;
  /**
   * Output only. Additional state information, which includes status reported
   * by the agent.
   *
   * @var string
   */
  public $substate;

  /**
   * Optional. Output only. Job state details, such as an error description if
   * the state is ERROR.
   *
   * @param string $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return string
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * Output only. A state message specifying the overall job state.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, SETUP_DONE, RUNNING,
   * CANCEL_PENDING, CANCEL_STARTED, CANCELLED, DONE, ERROR, ATTEMPT_FAILURE
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
   * Output only. The time when this state was entered.
   *
   * @param string $stateStartTime
   */
  public function setStateStartTime($stateStartTime)
  {
    $this->stateStartTime = $stateStartTime;
  }
  /**
   * @return string
   */
  public function getStateStartTime()
  {
    return $this->stateStartTime;
  }
  /**
   * Output only. Additional state information, which includes status reported
   * by the agent.
   *
   * Accepted values: UNSPECIFIED, SUBMITTED, QUEUED, STALE_STATUS
   *
   * @param self::SUBSTATE_* $substate
   */
  public function setSubstate($substate)
  {
    $this->substate = $substate;
  }
  /**
   * @return self::SUBSTATE_*
   */
  public function getSubstate()
  {
    return $this->substate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JobStatus::class, 'Google_Service_Dataproc_JobStatus');
