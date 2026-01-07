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

namespace Google\Service\SecurityCommandCenter;

class Job extends \Google\Model
{
  /**
   * Unspecified represents an unknown state and should not be used.
   */
  public const STATE_JOB_STATE_UNSPECIFIED = 'JOB_STATE_UNSPECIFIED';
  /**
   * Job is scheduled and pending for run
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * Job in progress
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * Job has completed with success
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Job has completed but with failure
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Optional. If the job did not complete successfully, this field describes
   * why.
   *
   * @var int
   */
  public $errorCode;
  /**
   * Optional. Gives the location where the job ran, such as `US` or `europe-
   * west1`
   *
   * @var string
   */
  public $location;
  /**
   * The fully-qualified name for a job. e.g. `projects//jobs/`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. State of the job, such as `RUNNING` or `PENDING`.
   *
   * @var string
   */
  public $state;

  /**
   * Optional. If the job did not complete successfully, this field describes
   * why.
   *
   * @param int $errorCode
   */
  public function setErrorCode($errorCode)
  {
    $this->errorCode = $errorCode;
  }
  /**
   * @return int
   */
  public function getErrorCode()
  {
    return $this->errorCode;
  }
  /**
   * Optional. Gives the location where the job ran, such as `US` or `europe-
   * west1`
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The fully-qualified name for a job. e.g. `projects//jobs/`
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. State of the job, such as `RUNNING` or `PENDING`.
   *
   * Accepted values: JOB_STATE_UNSPECIFIED, PENDING, RUNNING, SUCCEEDED, FAILED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Job::class, 'Google_Service_SecurityCommandCenter_Job');
