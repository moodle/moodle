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

namespace Google\Service\CloudScheduler;

class Job extends \Google\Model
{
  /**
   * Unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The job is executing normally.
   */
  public const STATE_ENABLED = 'ENABLED';
  /**
   * The job is paused by the user. It will not execute. A user can
   * intentionally pause the job using PauseJobRequest.
   */
  public const STATE_PAUSED = 'PAUSED';
  /**
   * The job is disabled by the system due to error. The user cannot directly
   * set a job to be disabled.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * The job state resulting from a failed CloudScheduler.UpdateJob operation.
   * To recover a job from this state, retry CloudScheduler.UpdateJob until a
   * successful response is received.
   */
  public const STATE_UPDATE_FAILED = 'UPDATE_FAILED';
  protected $appEngineHttpTargetType = AppEngineHttpTarget::class;
  protected $appEngineHttpTargetDataType = '';
  /**
   * The deadline for job attempts. If the request handler does not respond by
   * this deadline then the request is cancelled and the attempt is marked as a
   * `DEADLINE_EXCEEDED` failure. The failed attempt can be viewed in execution
   * logs. Cloud Scheduler will retry the job according to the RetryConfig. The
   * default and the allowed values depend on the type of target: * For HTTP
   * targets, the default is 3 minutes. The deadline must be in the interval [15
   * seconds, 30 minutes]. * For App Engine HTTP targets, 0 indicates that the
   * request has the default deadline. The default deadline depends on the
   * scaling type of the service: 10 minutes for standard apps with automatic
   * scaling, 24 hours for standard apps with manual and basic scaling, and 60
   * minutes for flex apps. If the request deadline is set, it must be in the
   * interval [15 seconds, 24 hours 15 seconds]. * For Pub/Sub targets, this
   * field is ignored.
   *
   * @var string
   */
  public $attemptDeadline;
  /**
   * Optionally caller-specified in CreateJob or UpdateJob. A human-readable
   * description for the job. This string must not contain more than 500
   * characters.
   *
   * @var string
   */
  public $description;
  protected $httpTargetType = HttpTarget::class;
  protected $httpTargetDataType = '';
  /**
   * Output only. The time the last job attempt started.
   *
   * @var string
   */
  public $lastAttemptTime;
  /**
   * Optionally caller-specified in CreateJob, after which it becomes output
   * only. The job name. For example:
   * `projects/PROJECT_ID/locations/LOCATION_ID/jobs/JOB_ID`. * `PROJECT_ID` can
   * contain letters ([A-Za-z]), numbers ([0-9]), hyphens (-), colons (:), or
   * periods (.). For more information, see [Identifying projects](/resource-
   * manager/docs/creating-managing-projects#identifying_projects) *
   * `LOCATION_ID` is the canonical ID for the job's location. The list of
   * available locations can be obtained by calling [locations.list](/scheduler/
   * docs/reference/rest/v1/projects.locations/list). For more information, see
   * [Cloud Scheduler locations](/scheduler/docs/locations). * `JOB_ID` can
   * contain only letters ([A-Za-z]), numbers ([0-9]), hyphens (-), or
   * underscores (_). The maximum length is 500 characters.
   *
   * @var string
   */
  public $name;
  protected $pubsubTargetType = PubsubTarget::class;
  protected $pubsubTargetDataType = '';
  protected $retryConfigType = RetryConfig::class;
  protected $retryConfigDataType = '';
  /**
   * Output only. Whether or not this Job satisfies the requirements of physical
   * zone separation
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Required, except when used with UpdateJob. Describes the schedule on which
   * the job will be executed. The schedule can be either of the following
   * types: * [Crontab](https://en.wikipedia.org/wiki/Cron#Overview) * English-
   * like [schedule](/scheduler/docs/configuring/cron-job-schedules) As a
   * general rule, execution `n + 1` of a job will not begin until execution `n`
   * has finished. Cloud Scheduler will never allow two simultaneously
   * outstanding executions. For example, this implies that if the `n+1`th
   * execution is scheduled to run at 16:00 but the `n`th execution takes until
   * 16:15, the `n+1`th execution will not start until `16:15`. A scheduled
   * start time will be delayed if the previous execution has not ended when its
   * scheduled time occurs. If retry_count > 0 and a job attempt fails, the job
   * will be tried a total of retry_count times, with exponential backoff, until
   * the next scheduled start time. If retry_count is 0, a job attempt will not
   * be retried if it fails. Instead the Cloud Scheduler system will wait for
   * the next scheduled execution time. Setting retry_count to 0 does not
   * prevent failed jobs from running according to schedule after the failure.
   *
   * @var string
   */
  public $schedule;
  /**
   * Output only. The next time the job is scheduled. Note that this may be a
   * retry of a previously failed attempt or the next execution time according
   * to the schedule.
   *
   * @var string
   */
  public $scheduleTime;
  /**
   * Output only. State of the job.
   *
   * @var string
   */
  public $state;
  protected $statusType = Status::class;
  protected $statusDataType = '';
  /**
   * Specifies the time zone to be used in interpreting schedule. The value of
   * this field must be a time zone name from the [tz
   * database](http://en.wikipedia.org/wiki/Tz_database). Note that some time
   * zones include a provision for daylight savings time. The rules for daylight
   * saving time are determined by the chosen tz. For UTC use the string "utc".
   * If a time zone is not specified, the default will be in UTC (also known as
   * GMT).
   *
   * @var string
   */
  public $timeZone;
  /**
   * Output only. The creation time of the job.
   *
   * @var string
   */
  public $userUpdateTime;

  /**
   * App Engine HTTP target.
   *
   * @param AppEngineHttpTarget $appEngineHttpTarget
   */
  public function setAppEngineHttpTarget(AppEngineHttpTarget $appEngineHttpTarget)
  {
    $this->appEngineHttpTarget = $appEngineHttpTarget;
  }
  /**
   * @return AppEngineHttpTarget
   */
  public function getAppEngineHttpTarget()
  {
    return $this->appEngineHttpTarget;
  }
  /**
   * The deadline for job attempts. If the request handler does not respond by
   * this deadline then the request is cancelled and the attempt is marked as a
   * `DEADLINE_EXCEEDED` failure. The failed attempt can be viewed in execution
   * logs. Cloud Scheduler will retry the job according to the RetryConfig. The
   * default and the allowed values depend on the type of target: * For HTTP
   * targets, the default is 3 minutes. The deadline must be in the interval [15
   * seconds, 30 minutes]. * For App Engine HTTP targets, 0 indicates that the
   * request has the default deadline. The default deadline depends on the
   * scaling type of the service: 10 minutes for standard apps with automatic
   * scaling, 24 hours for standard apps with manual and basic scaling, and 60
   * minutes for flex apps. If the request deadline is set, it must be in the
   * interval [15 seconds, 24 hours 15 seconds]. * For Pub/Sub targets, this
   * field is ignored.
   *
   * @param string $attemptDeadline
   */
  public function setAttemptDeadline($attemptDeadline)
  {
    $this->attemptDeadline = $attemptDeadline;
  }
  /**
   * @return string
   */
  public function getAttemptDeadline()
  {
    return $this->attemptDeadline;
  }
  /**
   * Optionally caller-specified in CreateJob or UpdateJob. A human-readable
   * description for the job. This string must not contain more than 500
   * characters.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * HTTP target.
   *
   * @param HttpTarget $httpTarget
   */
  public function setHttpTarget(HttpTarget $httpTarget)
  {
    $this->httpTarget = $httpTarget;
  }
  /**
   * @return HttpTarget
   */
  public function getHttpTarget()
  {
    return $this->httpTarget;
  }
  /**
   * Output only. The time the last job attempt started.
   *
   * @param string $lastAttemptTime
   */
  public function setLastAttemptTime($lastAttemptTime)
  {
    $this->lastAttemptTime = $lastAttemptTime;
  }
  /**
   * @return string
   */
  public function getLastAttemptTime()
  {
    return $this->lastAttemptTime;
  }
  /**
   * Optionally caller-specified in CreateJob, after which it becomes output
   * only. The job name. For example:
   * `projects/PROJECT_ID/locations/LOCATION_ID/jobs/JOB_ID`. * `PROJECT_ID` can
   * contain letters ([A-Za-z]), numbers ([0-9]), hyphens (-), colons (:), or
   * periods (.). For more information, see [Identifying projects](/resource-
   * manager/docs/creating-managing-projects#identifying_projects) *
   * `LOCATION_ID` is the canonical ID for the job's location. The list of
   * available locations can be obtained by calling [locations.list](/scheduler/
   * docs/reference/rest/v1/projects.locations/list). For more information, see
   * [Cloud Scheduler locations](/scheduler/docs/locations). * `JOB_ID` can
   * contain only letters ([A-Za-z]), numbers ([0-9]), hyphens (-), or
   * underscores (_). The maximum length is 500 characters.
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
   * Pub/Sub target.
   *
   * @param PubsubTarget $pubsubTarget
   */
  public function setPubsubTarget(PubsubTarget $pubsubTarget)
  {
    $this->pubsubTarget = $pubsubTarget;
  }
  /**
   * @return PubsubTarget
   */
  public function getPubsubTarget()
  {
    return $this->pubsubTarget;
  }
  /**
   * Settings that determine the retry behavior.
   *
   * @param RetryConfig $retryConfig
   */
  public function setRetryConfig(RetryConfig $retryConfig)
  {
    $this->retryConfig = $retryConfig;
  }
  /**
   * @return RetryConfig
   */
  public function getRetryConfig()
  {
    return $this->retryConfig;
  }
  /**
   * Output only. Whether or not this Job satisfies the requirements of physical
   * zone separation
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Required, except when used with UpdateJob. Describes the schedule on which
   * the job will be executed. The schedule can be either of the following
   * types: * [Crontab](https://en.wikipedia.org/wiki/Cron#Overview) * English-
   * like [schedule](/scheduler/docs/configuring/cron-job-schedules) As a
   * general rule, execution `n + 1` of a job will not begin until execution `n`
   * has finished. Cloud Scheduler will never allow two simultaneously
   * outstanding executions. For example, this implies that if the `n+1`th
   * execution is scheduled to run at 16:00 but the `n`th execution takes until
   * 16:15, the `n+1`th execution will not start until `16:15`. A scheduled
   * start time will be delayed if the previous execution has not ended when its
   * scheduled time occurs. If retry_count > 0 and a job attempt fails, the job
   * will be tried a total of retry_count times, with exponential backoff, until
   * the next scheduled start time. If retry_count is 0, a job attempt will not
   * be retried if it fails. Instead the Cloud Scheduler system will wait for
   * the next scheduled execution time. Setting retry_count to 0 does not
   * prevent failed jobs from running according to schedule after the failure.
   *
   * @param string $schedule
   */
  public function setSchedule($schedule)
  {
    $this->schedule = $schedule;
  }
  /**
   * @return string
   */
  public function getSchedule()
  {
    return $this->schedule;
  }
  /**
   * Output only. The next time the job is scheduled. Note that this may be a
   * retry of a previously failed attempt or the next execution time according
   * to the schedule.
   *
   * @param string $scheduleTime
   */
  public function setScheduleTime($scheduleTime)
  {
    $this->scheduleTime = $scheduleTime;
  }
  /**
   * @return string
   */
  public function getScheduleTime()
  {
    return $this->scheduleTime;
  }
  /**
   * Output only. State of the job.
   *
   * Accepted values: STATE_UNSPECIFIED, ENABLED, PAUSED, DISABLED,
   * UPDATE_FAILED
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
   * Output only. The response from the target for the last attempted execution.
   *
   * @param Status $status
   */
  public function setStatus(Status $status)
  {
    $this->status = $status;
  }
  /**
   * @return Status
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Specifies the time zone to be used in interpreting schedule. The value of
   * this field must be a time zone name from the [tz
   * database](http://en.wikipedia.org/wiki/Tz_database). Note that some time
   * zones include a provision for daylight savings time. The rules for daylight
   * saving time are determined by the chosen tz. For UTC use the string "utc".
   * If a time zone is not specified, the default will be in UTC (also known as
   * GMT).
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
  /**
   * Output only. The creation time of the job.
   *
   * @param string $userUpdateTime
   */
  public function setUserUpdateTime($userUpdateTime)
  {
    $this->userUpdateTime = $userUpdateTime;
  }
  /**
   * @return string
   */
  public function getUserUpdateTime()
  {
    return $this->userUpdateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Job::class, 'Google_Service_CloudScheduler_Job');
