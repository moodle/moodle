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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1Schedule extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The Schedule is active. Runs are being scheduled on the user-specified
   * timespec.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The schedule is paused. No new runs will be created until the schedule is
   * resumed. Already started runs will be allowed to complete.
   */
  public const STATE_PAUSED = 'PAUSED';
  /**
   * The Schedule is completed. No new runs will be scheduled. Already started
   * runs will be allowed to complete. Schedules in completed state cannot be
   * paused or resumed.
   */
  public const STATE_COMPLETED = 'COMPLETED';
  /**
   * Optional. Whether new scheduled runs can be queued when max_concurrent_runs
   * limit is reached. If set to true, new runs will be queued instead of
   * skipped. Default to false.
   *
   * @var bool
   */
  public $allowQueueing;
  /**
   * Output only. Whether to backfill missed runs when the schedule is resumed
   * from PAUSED state. If set to true, all missed runs will be scheduled. New
   * runs will be scheduled after the backfill is complete. Default to false.
   *
   * @var bool
   */
  public $catchUp;
  protected $createNotebookExecutionJobRequestType = GoogleCloudAiplatformV1CreateNotebookExecutionJobRequest::class;
  protected $createNotebookExecutionJobRequestDataType = '';
  protected $createPipelineJobRequestType = GoogleCloudAiplatformV1CreatePipelineJobRequest::class;
  protected $createPipelineJobRequestDataType = '';
  /**
   * Output only. Timestamp when this Schedule was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Cron schedule (https://en.wikipedia.org/wiki/Cron) to launch scheduled
   * runs. To explicitly set a timezone to the cron tab, apply a prefix in the
   * cron tab: "CRON_TZ=${IANA_TIME_ZONE}" or "TZ=${IANA_TIME_ZONE}". The
   * ${IANA_TIME_ZONE} may only be a valid string from IANA time zone database.
   * For example, "CRON_TZ=America/New_York 1 * * * *", or "TZ=America/New_York
   * 1 * * * *".
   *
   * @var string
   */
  public $cron;
  /**
   * Required. User provided name of the Schedule. The name can be up to 128
   * characters long and can consist of any UTF-8 characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. Timestamp after which no new runs can be scheduled. If specified,
   * The schedule will be completed when either end_time is reached or when
   * scheduled_run_count >= max_run_count. If not specified, new runs will keep
   * getting scheduled until this Schedule is paused or deleted. Already
   * scheduled runs will be allowed to complete. Unset if not specified.
   *
   * @var string
   */
  public $endTime;
  /**
   * Output only. Timestamp when this Schedule was last paused. Unset if never
   * paused.
   *
   * @var string
   */
  public $lastPauseTime;
  /**
   * Output only. Timestamp when this Schedule was last resumed. Unset if never
   * resumed from pause.
   *
   * @var string
   */
  public $lastResumeTime;
  protected $lastScheduledRunResponseType = GoogleCloudAiplatformV1ScheduleRunResponse::class;
  protected $lastScheduledRunResponseDataType = '';
  /**
   * Required. Maximum number of runs that can be started concurrently for this
   * Schedule. This is the limit for starting the scheduled requests and not the
   * execution of the operations/jobs created by the requests (if applicable).
   *
   * @var string
   */
  public $maxConcurrentRunCount;
  /**
   * Optional. Maximum run count of the schedule. If specified, The schedule
   * will be completed when either started_run_count >= max_run_count or when
   * end_time is reached. If not specified, new runs will keep getting scheduled
   * until this Schedule is paused or deleted. Already scheduled runs will be
   * allowed to complete. Unset if not specified.
   *
   * @var string
   */
  public $maxRunCount;
  /**
   * Immutable. The resource name of the Schedule.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Timestamp when this Schedule should schedule the next run.
   * Having a next_run_time in the past means the runs are being started behind
   * schedule.
   *
   * @var string
   */
  public $nextRunTime;
  /**
   * Optional. Timestamp after which the first run can be scheduled. Default to
   * Schedule create time if not specified.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. The number of runs started by this schedule.
   *
   * @var string
   */
  public $startedRunCount;
  /**
   * Output only. The state of this Schedule.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Timestamp when this Schedule was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Whether new scheduled runs can be queued when max_concurrent_runs
   * limit is reached. If set to true, new runs will be queued instead of
   * skipped. Default to false.
   *
   * @param bool $allowQueueing
   */
  public function setAllowQueueing($allowQueueing)
  {
    $this->allowQueueing = $allowQueueing;
  }
  /**
   * @return bool
   */
  public function getAllowQueueing()
  {
    return $this->allowQueueing;
  }
  /**
   * Output only. Whether to backfill missed runs when the schedule is resumed
   * from PAUSED state. If set to true, all missed runs will be scheduled. New
   * runs will be scheduled after the backfill is complete. Default to false.
   *
   * @param bool $catchUp
   */
  public function setCatchUp($catchUp)
  {
    $this->catchUp = $catchUp;
  }
  /**
   * @return bool
   */
  public function getCatchUp()
  {
    return $this->catchUp;
  }
  /**
   * Request for NotebookService.CreateNotebookExecutionJob.
   *
   * @param GoogleCloudAiplatformV1CreateNotebookExecutionJobRequest $createNotebookExecutionJobRequest
   */
  public function setCreateNotebookExecutionJobRequest(GoogleCloudAiplatformV1CreateNotebookExecutionJobRequest $createNotebookExecutionJobRequest)
  {
    $this->createNotebookExecutionJobRequest = $createNotebookExecutionJobRequest;
  }
  /**
   * @return GoogleCloudAiplatformV1CreateNotebookExecutionJobRequest
   */
  public function getCreateNotebookExecutionJobRequest()
  {
    return $this->createNotebookExecutionJobRequest;
  }
  /**
   * Request for PipelineService.CreatePipelineJob.
   * CreatePipelineJobRequest.parent field is required (format:
   * projects/{project}/locations/{location}).
   *
   * @param GoogleCloudAiplatformV1CreatePipelineJobRequest $createPipelineJobRequest
   */
  public function setCreatePipelineJobRequest(GoogleCloudAiplatformV1CreatePipelineJobRequest $createPipelineJobRequest)
  {
    $this->createPipelineJobRequest = $createPipelineJobRequest;
  }
  /**
   * @return GoogleCloudAiplatformV1CreatePipelineJobRequest
   */
  public function getCreatePipelineJobRequest()
  {
    return $this->createPipelineJobRequest;
  }
  /**
   * Output only. Timestamp when this Schedule was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Cron schedule (https://en.wikipedia.org/wiki/Cron) to launch scheduled
   * runs. To explicitly set a timezone to the cron tab, apply a prefix in the
   * cron tab: "CRON_TZ=${IANA_TIME_ZONE}" or "TZ=${IANA_TIME_ZONE}". The
   * ${IANA_TIME_ZONE} may only be a valid string from IANA time zone database.
   * For example, "CRON_TZ=America/New_York 1 * * * *", or "TZ=America/New_York
   * 1 * * * *".
   *
   * @param string $cron
   */
  public function setCron($cron)
  {
    $this->cron = $cron;
  }
  /**
   * @return string
   */
  public function getCron()
  {
    return $this->cron;
  }
  /**
   * Required. User provided name of the Schedule. The name can be up to 128
   * characters long and can consist of any UTF-8 characters.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Optional. Timestamp after which no new runs can be scheduled. If specified,
   * The schedule will be completed when either end_time is reached or when
   * scheduled_run_count >= max_run_count. If not specified, new runs will keep
   * getting scheduled until this Schedule is paused or deleted. Already
   * scheduled runs will be allowed to complete. Unset if not specified.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Output only. Timestamp when this Schedule was last paused. Unset if never
   * paused.
   *
   * @param string $lastPauseTime
   */
  public function setLastPauseTime($lastPauseTime)
  {
    $this->lastPauseTime = $lastPauseTime;
  }
  /**
   * @return string
   */
  public function getLastPauseTime()
  {
    return $this->lastPauseTime;
  }
  /**
   * Output only. Timestamp when this Schedule was last resumed. Unset if never
   * resumed from pause.
   *
   * @param string $lastResumeTime
   */
  public function setLastResumeTime($lastResumeTime)
  {
    $this->lastResumeTime = $lastResumeTime;
  }
  /**
   * @return string
   */
  public function getLastResumeTime()
  {
    return $this->lastResumeTime;
  }
  /**
   * Output only. Response of the last scheduled run. This is the response for
   * starting the scheduled requests and not the execution of the
   * operations/jobs created by the requests (if applicable). Unset if no run
   * has been scheduled yet.
   *
   * @param GoogleCloudAiplatformV1ScheduleRunResponse $lastScheduledRunResponse
   */
  public function setLastScheduledRunResponse(GoogleCloudAiplatformV1ScheduleRunResponse $lastScheduledRunResponse)
  {
    $this->lastScheduledRunResponse = $lastScheduledRunResponse;
  }
  /**
   * @return GoogleCloudAiplatformV1ScheduleRunResponse
   */
  public function getLastScheduledRunResponse()
  {
    return $this->lastScheduledRunResponse;
  }
  /**
   * Required. Maximum number of runs that can be started concurrently for this
   * Schedule. This is the limit for starting the scheduled requests and not the
   * execution of the operations/jobs created by the requests (if applicable).
   *
   * @param string $maxConcurrentRunCount
   */
  public function setMaxConcurrentRunCount($maxConcurrentRunCount)
  {
    $this->maxConcurrentRunCount = $maxConcurrentRunCount;
  }
  /**
   * @return string
   */
  public function getMaxConcurrentRunCount()
  {
    return $this->maxConcurrentRunCount;
  }
  /**
   * Optional. Maximum run count of the schedule. If specified, The schedule
   * will be completed when either started_run_count >= max_run_count or when
   * end_time is reached. If not specified, new runs will keep getting scheduled
   * until this Schedule is paused or deleted. Already scheduled runs will be
   * allowed to complete. Unset if not specified.
   *
   * @param string $maxRunCount
   */
  public function setMaxRunCount($maxRunCount)
  {
    $this->maxRunCount = $maxRunCount;
  }
  /**
   * @return string
   */
  public function getMaxRunCount()
  {
    return $this->maxRunCount;
  }
  /**
   * Immutable. The resource name of the Schedule.
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
   * Output only. Timestamp when this Schedule should schedule the next run.
   * Having a next_run_time in the past means the runs are being started behind
   * schedule.
   *
   * @param string $nextRunTime
   */
  public function setNextRunTime($nextRunTime)
  {
    $this->nextRunTime = $nextRunTime;
  }
  /**
   * @return string
   */
  public function getNextRunTime()
  {
    return $this->nextRunTime;
  }
  /**
   * Optional. Timestamp after which the first run can be scheduled. Default to
   * Schedule create time if not specified.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Output only. The number of runs started by this schedule.
   *
   * @param string $startedRunCount
   */
  public function setStartedRunCount($startedRunCount)
  {
    $this->startedRunCount = $startedRunCount;
  }
  /**
   * @return string
   */
  public function getStartedRunCount()
  {
    return $this->startedRunCount;
  }
  /**
   * Output only. The state of this Schedule.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, PAUSED, COMPLETED
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
   * Output only. Timestamp when this Schedule was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Schedule::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Schedule');
