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

namespace Google\Service\Datapipelines;

class GoogleCloudDatapipelinesV1Pipeline extends \Google\Model
{
  /**
   * The pipeline state isn't specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The pipeline is getting started or resumed. When finished, the pipeline
   * state will be 'PIPELINE_STATE_ACTIVE'.
   */
  public const STATE_STATE_RESUMING = 'STATE_RESUMING';
  /**
   * The pipeline is actively running.
   */
  public const STATE_STATE_ACTIVE = 'STATE_ACTIVE';
  /**
   * The pipeline is in the process of stopping. When finished, the pipeline
   * state will be 'PIPELINE_STATE_ARCHIVED'.
   */
  public const STATE_STATE_STOPPING = 'STATE_STOPPING';
  /**
   * The pipeline has been stopped. This is a terminal state and cannot be
   * undone.
   */
  public const STATE_STATE_ARCHIVED = 'STATE_ARCHIVED';
  /**
   * The pipeline is paused. This is a non-terminal state. When the pipeline is
   * paused, it will hold processing jobs, but can be resumed later. For a batch
   * pipeline, this means pausing the scheduler job. For a streaming pipeline,
   * creating a job snapshot to resume from will give the same effect.
   */
  public const STATE_STATE_PAUSED = 'STATE_PAUSED';
  /**
   * The pipeline type isn't specified.
   */
  public const TYPE_PIPELINE_TYPE_UNSPECIFIED = 'PIPELINE_TYPE_UNSPECIFIED';
  /**
   * A batch pipeline. It runs jobs on a specific schedule, and each job will
   * automatically terminate once execution is finished.
   */
  public const TYPE_PIPELINE_TYPE_BATCH = 'PIPELINE_TYPE_BATCH';
  /**
   * A streaming pipeline. The underlying job is continuously running until it
   * is manually terminated by the user. This type of pipeline doesn't have a
   * schedule to run on, and the linked job gets created when the pipeline is
   * created.
   */
  public const TYPE_PIPELINE_TYPE_STREAMING = 'PIPELINE_TYPE_STREAMING';
  /**
   * Output only. Immutable. The timestamp when the pipeline was initially
   * created. Set by the Data Pipelines service.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The display name of the pipeline. It can contain only letters
   * ([A-Za-z]), numbers ([0-9]), hyphens (-), and underscores (_).
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Number of jobs.
   *
   * @var int
   */
  public $jobCount;
  /**
   * Output only. Immutable. The timestamp when the pipeline was last modified.
   * Set by the Data Pipelines service.
   *
   * @var string
   */
  public $lastUpdateTime;
  /**
   * The pipeline name. For example:
   * `projects/PROJECT_ID/locations/LOCATION_ID/pipelines/PIPELINE_ID`. *
   * `PROJECT_ID` can contain letters ([A-Za-z]), numbers ([0-9]), hyphens (-),
   * colons (:), and periods (.). For more information, see [Identifying
   * projects](https://cloud.google.com/resource-manager/docs/creating-managing-
   * projects#identifying_projects). * `LOCATION_ID` is the canonical ID for the
   * pipeline's location. The list of available locations can be obtained by
   * calling `google.cloud.location.Locations.ListLocations`. Note that the Data
   * Pipelines service is not available in all regions. It depends on Cloud
   * Scheduler, an App Engine application, so it's only available in [App Engine
   * regions](https://cloud.google.com/about/locations#region). * `PIPELINE_ID`
   * is the ID of the pipeline. Must be unique for the selected project and
   * location.
   *
   * @var string
   */
  public $name;
  /**
   * Immutable. The sources of the pipeline (for example, Dataplex). The keys
   * and values are set by the corresponding sources during pipeline creation.
   *
   * @var string[]
   */
  public $pipelineSources;
  protected $scheduleInfoType = GoogleCloudDatapipelinesV1ScheduleSpec::class;
  protected $scheduleInfoDataType = '';
  /**
   * Optional. A service account email to be used with the Cloud Scheduler job.
   * If not specified, the default compute engine service account will be used.
   *
   * @var string
   */
  public $schedulerServiceAccountEmail;
  /**
   * Required. The state of the pipeline. When the pipeline is created, the
   * state is set to 'PIPELINE_STATE_ACTIVE' by default. State changes can be
   * requested by setting the state to stopping, paused, or resuming. State
   * cannot be changed through UpdatePipeline requests.
   *
   * @var string
   */
  public $state;
  /**
   * Required. The type of the pipeline. This field affects the scheduling of
   * the pipeline and the type of metrics to show for the pipeline.
   *
   * @var string
   */
  public $type;
  protected $workloadType = GoogleCloudDatapipelinesV1Workload::class;
  protected $workloadDataType = '';

  /**
   * Output only. Immutable. The timestamp when the pipeline was initially
   * created. Set by the Data Pipelines service.
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
   * Required. The display name of the pipeline. It can contain only letters
   * ([A-Za-z]), numbers ([0-9]), hyphens (-), and underscores (_).
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
   * Output only. Number of jobs.
   *
   * @param int $jobCount
   */
  public function setJobCount($jobCount)
  {
    $this->jobCount = $jobCount;
  }
  /**
   * @return int
   */
  public function getJobCount()
  {
    return $this->jobCount;
  }
  /**
   * Output only. Immutable. The timestamp when the pipeline was last modified.
   * Set by the Data Pipelines service.
   *
   * @param string $lastUpdateTime
   */
  public function setLastUpdateTime($lastUpdateTime)
  {
    $this->lastUpdateTime = $lastUpdateTime;
  }
  /**
   * @return string
   */
  public function getLastUpdateTime()
  {
    return $this->lastUpdateTime;
  }
  /**
   * The pipeline name. For example:
   * `projects/PROJECT_ID/locations/LOCATION_ID/pipelines/PIPELINE_ID`. *
   * `PROJECT_ID` can contain letters ([A-Za-z]), numbers ([0-9]), hyphens (-),
   * colons (:), and periods (.). For more information, see [Identifying
   * projects](https://cloud.google.com/resource-manager/docs/creating-managing-
   * projects#identifying_projects). * `LOCATION_ID` is the canonical ID for the
   * pipeline's location. The list of available locations can be obtained by
   * calling `google.cloud.location.Locations.ListLocations`. Note that the Data
   * Pipelines service is not available in all regions. It depends on Cloud
   * Scheduler, an App Engine application, so it's only available in [App Engine
   * regions](https://cloud.google.com/about/locations#region). * `PIPELINE_ID`
   * is the ID of the pipeline. Must be unique for the selected project and
   * location.
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
   * Immutable. The sources of the pipeline (for example, Dataplex). The keys
   * and values are set by the corresponding sources during pipeline creation.
   *
   * @param string[] $pipelineSources
   */
  public function setPipelineSources($pipelineSources)
  {
    $this->pipelineSources = $pipelineSources;
  }
  /**
   * @return string[]
   */
  public function getPipelineSources()
  {
    return $this->pipelineSources;
  }
  /**
   * Internal scheduling information for a pipeline. If this information is
   * provided, periodic jobs will be created per the schedule. If not, users are
   * responsible for creating jobs externally.
   *
   * @param GoogleCloudDatapipelinesV1ScheduleSpec $scheduleInfo
   */
  public function setScheduleInfo(GoogleCloudDatapipelinesV1ScheduleSpec $scheduleInfo)
  {
    $this->scheduleInfo = $scheduleInfo;
  }
  /**
   * @return GoogleCloudDatapipelinesV1ScheduleSpec
   */
  public function getScheduleInfo()
  {
    return $this->scheduleInfo;
  }
  /**
   * Optional. A service account email to be used with the Cloud Scheduler job.
   * If not specified, the default compute engine service account will be used.
   *
   * @param string $schedulerServiceAccountEmail
   */
  public function setSchedulerServiceAccountEmail($schedulerServiceAccountEmail)
  {
    $this->schedulerServiceAccountEmail = $schedulerServiceAccountEmail;
  }
  /**
   * @return string
   */
  public function getSchedulerServiceAccountEmail()
  {
    return $this->schedulerServiceAccountEmail;
  }
  /**
   * Required. The state of the pipeline. When the pipeline is created, the
   * state is set to 'PIPELINE_STATE_ACTIVE' by default. State changes can be
   * requested by setting the state to stopping, paused, or resuming. State
   * cannot be changed through UpdatePipeline requests.
   *
   * Accepted values: STATE_UNSPECIFIED, STATE_RESUMING, STATE_ACTIVE,
   * STATE_STOPPING, STATE_ARCHIVED, STATE_PAUSED
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
   * Required. The type of the pipeline. This field affects the scheduling of
   * the pipeline and the type of metrics to show for the pipeline.
   *
   * Accepted values: PIPELINE_TYPE_UNSPECIFIED, PIPELINE_TYPE_BATCH,
   * PIPELINE_TYPE_STREAMING
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Workload information for creating new jobs.
   *
   * @param GoogleCloudDatapipelinesV1Workload $workload
   */
  public function setWorkload(GoogleCloudDatapipelinesV1Workload $workload)
  {
    $this->workload = $workload;
  }
  /**
   * @return GoogleCloudDatapipelinesV1Workload
   */
  public function getWorkload()
  {
    return $this->workload;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatapipelinesV1Pipeline::class, 'Google_Service_Datapipelines_GoogleCloudDatapipelinesV1Pipeline');
