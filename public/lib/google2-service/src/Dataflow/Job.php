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

class Job extends \Google\Collection
{
  /**
   * The job's run state isn't specified.
   */
  public const CURRENT_STATE_JOB_STATE_UNKNOWN = 'JOB_STATE_UNKNOWN';
  /**
   * `JOB_STATE_STOPPED` indicates that the job has not yet started to run.
   */
  public const CURRENT_STATE_JOB_STATE_STOPPED = 'JOB_STATE_STOPPED';
  /**
   * `JOB_STATE_RUNNING` indicates that the job is currently running.
   */
  public const CURRENT_STATE_JOB_STATE_RUNNING = 'JOB_STATE_RUNNING';
  /**
   * `JOB_STATE_DONE` indicates that the job has successfully completed. This is
   * a terminal job state. This state may be set by the Cloud Dataflow service,
   * as a transition from `JOB_STATE_RUNNING`. It may also be set via a Cloud
   * Dataflow `UpdateJob` call, if the job has not yet reached a terminal state.
   */
  public const CURRENT_STATE_JOB_STATE_DONE = 'JOB_STATE_DONE';
  /**
   * `JOB_STATE_FAILED` indicates that the job has failed. This is a terminal
   * job state. This state may only be set by the Cloud Dataflow service, and
   * only as a transition from `JOB_STATE_RUNNING`.
   */
  public const CURRENT_STATE_JOB_STATE_FAILED = 'JOB_STATE_FAILED';
  /**
   * `JOB_STATE_CANCELLED` indicates that the job has been explicitly cancelled.
   * This is a terminal job state. This state may only be set via a Cloud
   * Dataflow `UpdateJob` call, and only if the job has not yet reached another
   * terminal state.
   */
  public const CURRENT_STATE_JOB_STATE_CANCELLED = 'JOB_STATE_CANCELLED';
  /**
   * `JOB_STATE_UPDATED` indicates that the job was successfully updated,
   * meaning that this job was stopped and another job was started, inheriting
   * state from this one. This is a terminal job state. This state may only be
   * set by the Cloud Dataflow service, and only as a transition from
   * `JOB_STATE_RUNNING`.
   */
  public const CURRENT_STATE_JOB_STATE_UPDATED = 'JOB_STATE_UPDATED';
  /**
   * `JOB_STATE_DRAINING` indicates that the job is in the process of draining.
   * A draining job has stopped pulling from its input sources and is processing
   * any data that remains in-flight. This state may be set via a Cloud Dataflow
   * `UpdateJob` call, but only as a transition from `JOB_STATE_RUNNING`. Jobs
   * that are draining may only transition to `JOB_STATE_DRAINED`,
   * `JOB_STATE_CANCELLED`, or `JOB_STATE_FAILED`.
   */
  public const CURRENT_STATE_JOB_STATE_DRAINING = 'JOB_STATE_DRAINING';
  /**
   * `JOB_STATE_DRAINED` indicates that the job has been drained. A drained job
   * terminated by stopping pulling from its input sources and processing any
   * data that remained in-flight when draining was requested. This state is a
   * terminal state, may only be set by the Cloud Dataflow service, and only as
   * a transition from `JOB_STATE_DRAINING`.
   */
  public const CURRENT_STATE_JOB_STATE_DRAINED = 'JOB_STATE_DRAINED';
  /**
   * `JOB_STATE_PENDING` indicates that the job has been created but is not yet
   * running. Jobs that are pending may only transition to `JOB_STATE_RUNNING`,
   * or `JOB_STATE_FAILED`.
   */
  public const CURRENT_STATE_JOB_STATE_PENDING = 'JOB_STATE_PENDING';
  /**
   * `JOB_STATE_CANCELLING` indicates that the job has been explicitly cancelled
   * and is in the process of stopping. Jobs that are cancelling may only
   * transition to `JOB_STATE_CANCELLED` or `JOB_STATE_FAILED`.
   */
  public const CURRENT_STATE_JOB_STATE_CANCELLING = 'JOB_STATE_CANCELLING';
  /**
   * `JOB_STATE_QUEUED` indicates that the job has been created but is being
   * delayed until launch. Jobs that are queued may only transition to
   * `JOB_STATE_PENDING` or `JOB_STATE_CANCELLED`.
   */
  public const CURRENT_STATE_JOB_STATE_QUEUED = 'JOB_STATE_QUEUED';
  /**
   * `JOB_STATE_RESOURCE_CLEANING_UP` indicates that the batch job's associated
   * resources are currently being cleaned up after a successful run. Currently,
   * this is an opt-in feature, please reach out to Cloud support team if you
   * are interested.
   */
  public const CURRENT_STATE_JOB_STATE_RESOURCE_CLEANING_UP = 'JOB_STATE_RESOURCE_CLEANING_UP';
  /**
   * `JOB_STATE_PAUSING` is not implemented yet.
   */
  public const CURRENT_STATE_JOB_STATE_PAUSING = 'JOB_STATE_PAUSING';
  /**
   * `JOB_STATE_PAUSED` is not implemented yet.
   */
  public const CURRENT_STATE_JOB_STATE_PAUSED = 'JOB_STATE_PAUSED';
  /**
   * The job's run state isn't specified.
   */
  public const REQUESTED_STATE_JOB_STATE_UNKNOWN = 'JOB_STATE_UNKNOWN';
  /**
   * `JOB_STATE_STOPPED` indicates that the job has not yet started to run.
   */
  public const REQUESTED_STATE_JOB_STATE_STOPPED = 'JOB_STATE_STOPPED';
  /**
   * `JOB_STATE_RUNNING` indicates that the job is currently running.
   */
  public const REQUESTED_STATE_JOB_STATE_RUNNING = 'JOB_STATE_RUNNING';
  /**
   * `JOB_STATE_DONE` indicates that the job has successfully completed. This is
   * a terminal job state. This state may be set by the Cloud Dataflow service,
   * as a transition from `JOB_STATE_RUNNING`. It may also be set via a Cloud
   * Dataflow `UpdateJob` call, if the job has not yet reached a terminal state.
   */
  public const REQUESTED_STATE_JOB_STATE_DONE = 'JOB_STATE_DONE';
  /**
   * `JOB_STATE_FAILED` indicates that the job has failed. This is a terminal
   * job state. This state may only be set by the Cloud Dataflow service, and
   * only as a transition from `JOB_STATE_RUNNING`.
   */
  public const REQUESTED_STATE_JOB_STATE_FAILED = 'JOB_STATE_FAILED';
  /**
   * `JOB_STATE_CANCELLED` indicates that the job has been explicitly cancelled.
   * This is a terminal job state. This state may only be set via a Cloud
   * Dataflow `UpdateJob` call, and only if the job has not yet reached another
   * terminal state.
   */
  public const REQUESTED_STATE_JOB_STATE_CANCELLED = 'JOB_STATE_CANCELLED';
  /**
   * `JOB_STATE_UPDATED` indicates that the job was successfully updated,
   * meaning that this job was stopped and another job was started, inheriting
   * state from this one. This is a terminal job state. This state may only be
   * set by the Cloud Dataflow service, and only as a transition from
   * `JOB_STATE_RUNNING`.
   */
  public const REQUESTED_STATE_JOB_STATE_UPDATED = 'JOB_STATE_UPDATED';
  /**
   * `JOB_STATE_DRAINING` indicates that the job is in the process of draining.
   * A draining job has stopped pulling from its input sources and is processing
   * any data that remains in-flight. This state may be set via a Cloud Dataflow
   * `UpdateJob` call, but only as a transition from `JOB_STATE_RUNNING`. Jobs
   * that are draining may only transition to `JOB_STATE_DRAINED`,
   * `JOB_STATE_CANCELLED`, or `JOB_STATE_FAILED`.
   */
  public const REQUESTED_STATE_JOB_STATE_DRAINING = 'JOB_STATE_DRAINING';
  /**
   * `JOB_STATE_DRAINED` indicates that the job has been drained. A drained job
   * terminated by stopping pulling from its input sources and processing any
   * data that remained in-flight when draining was requested. This state is a
   * terminal state, may only be set by the Cloud Dataflow service, and only as
   * a transition from `JOB_STATE_DRAINING`.
   */
  public const REQUESTED_STATE_JOB_STATE_DRAINED = 'JOB_STATE_DRAINED';
  /**
   * `JOB_STATE_PENDING` indicates that the job has been created but is not yet
   * running. Jobs that are pending may only transition to `JOB_STATE_RUNNING`,
   * or `JOB_STATE_FAILED`.
   */
  public const REQUESTED_STATE_JOB_STATE_PENDING = 'JOB_STATE_PENDING';
  /**
   * `JOB_STATE_CANCELLING` indicates that the job has been explicitly cancelled
   * and is in the process of stopping. Jobs that are cancelling may only
   * transition to `JOB_STATE_CANCELLED` or `JOB_STATE_FAILED`.
   */
  public const REQUESTED_STATE_JOB_STATE_CANCELLING = 'JOB_STATE_CANCELLING';
  /**
   * `JOB_STATE_QUEUED` indicates that the job has been created but is being
   * delayed until launch. Jobs that are queued may only transition to
   * `JOB_STATE_PENDING` or `JOB_STATE_CANCELLED`.
   */
  public const REQUESTED_STATE_JOB_STATE_QUEUED = 'JOB_STATE_QUEUED';
  /**
   * `JOB_STATE_RESOURCE_CLEANING_UP` indicates that the batch job's associated
   * resources are currently being cleaned up after a successful run. Currently,
   * this is an opt-in feature, please reach out to Cloud support team if you
   * are interested.
   */
  public const REQUESTED_STATE_JOB_STATE_RESOURCE_CLEANING_UP = 'JOB_STATE_RESOURCE_CLEANING_UP';
  /**
   * `JOB_STATE_PAUSING` is not implemented yet.
   */
  public const REQUESTED_STATE_JOB_STATE_PAUSING = 'JOB_STATE_PAUSING';
  /**
   * `JOB_STATE_PAUSED` is not implemented yet.
   */
  public const REQUESTED_STATE_JOB_STATE_PAUSED = 'JOB_STATE_PAUSED';
  /**
   * The type of the job is unspecified, or unknown.
   */
  public const TYPE_JOB_TYPE_UNKNOWN = 'JOB_TYPE_UNKNOWN';
  /**
   * A batch job with a well-defined end point: data is read, data is processed,
   * data is written, and the job is done.
   */
  public const TYPE_JOB_TYPE_BATCH = 'JOB_TYPE_BATCH';
  /**
   * A continuously streaming job with no end: data is read, processed, and
   * written continuously.
   */
  public const TYPE_JOB_TYPE_STREAMING = 'JOB_TYPE_STREAMING';
  protected $collection_key = 'tempFiles';
  /**
   * The client's unique identifier of the job, re-used across retried attempts.
   * If this field is set, the service will ensure its uniqueness. The request
   * to create a job will fail if the service has knowledge of a previously
   * submitted job with the same client's ID and job name. The caller may use
   * this field to ensure idempotence of job creation across retried attempts to
   * create a job. By default, the field is empty and, in that case, the service
   * ignores it.
   *
   * @var string
   */
  public $clientRequestId;
  /**
   * The timestamp when the job was initially created. Immutable and set by the
   * Cloud Dataflow service.
   *
   * @var string
   */
  public $createTime;
  /**
   * If this is specified, the job's initial state is populated from the given
   * snapshot.
   *
   * @var string
   */
  public $createdFromSnapshotId;
  /**
   * The current state of the job. Jobs are created in the `JOB_STATE_STOPPED`
   * state unless otherwise specified. A job in the `JOB_STATE_RUNNING` state
   * may asynchronously enter a terminal state. After a job has reached a
   * terminal state, no further state updates may be made. This field might be
   * mutated by the Dataflow service; callers cannot mutate it.
   *
   * @var string
   */
  public $currentState;
  /**
   * The timestamp associated with the current state.
   *
   * @var string
   */
  public $currentStateTime;
  protected $environmentType = Environment::class;
  protected $environmentDataType = '';
  protected $executionInfoType = JobExecutionInfo::class;
  protected $executionInfoDataType = '';
  /**
   * The unique ID of this job. This field is set by the Dataflow service when
   * the job is created, and is immutable for the life of the job.
   *
   * @var string
   */
  public $id;
  protected $jobMetadataType = JobMetadata::class;
  protected $jobMetadataDataType = '';
  /**
   * User-defined labels for this job. The labels map can contain no more than
   * 64 entries. Entries of the labels map are UTF8 strings that comply with the
   * following restrictions: * Keys must conform to regexp: \p{Ll}\p{Lo}{0,62} *
   * Values must conform to regexp: [\p{Ll}\p{Lo}\p{N}_-]{0,63} * Both keys and
   * values are additionally constrained to be <= 128 bytes in size.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. The [regional endpoint]
   * (https://cloud.google.com/dataflow/docs/concepts/regional-endpoints) that
   * contains this job.
   *
   * @var string
   */
  public $location;
  /**
   * Optional. The user-specified Dataflow job name. Only one active job with a
   * given name can exist in a project within one region at any given time. Jobs
   * in different regions can have the same name. If a caller attempts to create
   * a job with the same name as an active job that already exists, the attempt
   * returns the existing job. The name must match the regular expression
   * `[a-z]([-a-z0-9]{0,1022}[a-z0-9])?`
   *
   * @var string
   */
  public $name;
  protected $pipelineDescriptionType = PipelineDescription::class;
  protected $pipelineDescriptionDataType = '';
  /**
   * The ID of the Google Cloud project that the job belongs to.
   *
   * @var string
   */
  public $projectId;
  /**
   * If this job is an update of an existing job, this field is the job ID of
   * the job it replaced. When sending a `CreateJobRequest`, you can update a
   * job by specifying it here. The job named here is stopped, and its
   * intermediate state is transferred to this job.
   *
   * @var string
   */
  public $replaceJobId;
  /**
   * If another job is an update of this job (and thus, this job is in
   * `JOB_STATE_UPDATED`), this field contains the ID of that job.
   *
   * @var string
   */
  public $replacedByJobId;
  /**
   * The job's requested state. Applies to `UpdateJob` requests. Set
   * `requested_state` with `UpdateJob` requests to switch between the states
   * `JOB_STATE_STOPPED` and `JOB_STATE_RUNNING`. You can also use `UpdateJob`
   * requests to change a job's state from `JOB_STATE_RUNNING` to
   * `JOB_STATE_CANCELLED`, `JOB_STATE_DONE`, or `JOB_STATE_DRAINED`. These
   * states irrevocably terminate the job if it hasn't already reached a
   * terminal state. This field has no effect on `CreateJob` requests.
   *
   * @var string
   */
  public $requestedState;
  protected $runtimeUpdatableParamsType = RuntimeUpdatableParams::class;
  protected $runtimeUpdatableParamsDataType = '';
  /**
   * Output only. Reserved for future use. This field is set only in responses
   * from the server; it is ignored if it is set in any requests.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Reserved for future use. This field is set only in responses from the
   * server; it is ignored if it is set in any requests.
   *
   * @var bool
   */
  public $satisfiesPzs;
  protected $serviceResourcesType = ServiceResources::class;
  protected $serviceResourcesDataType = '';
  protected $stageStatesType = ExecutionStageState::class;
  protected $stageStatesDataType = 'array';
  /**
   * The timestamp when the job was started (transitioned to JOB_STATE_PENDING).
   * Flexible resource scheduling jobs are started with some delay after job
   * creation, so start_time is unset before start and is updated when the job
   * is started by the Cloud Dataflow service. For other jobs, start_time always
   * equals to create_time and is immutable and set by the Cloud Dataflow
   * service.
   *
   * @var string
   */
  public $startTime;
  protected $stepsType = Step::class;
  protected $stepsDataType = 'array';
  /**
   * The Cloud Storage location where the steps are stored.
   *
   * @var string
   */
  public $stepsLocation;
  /**
   * A set of files the system should be aware of that are used for temporary
   * storage. These temporary files will be removed on job completion. No
   * duplicates are allowed. No file patterns are supported. The supported files
   * are: Google Cloud Storage: storage.googleapis.com/{bucket}/{object}
   * bucket.storage.googleapis.com/{object}
   *
   * @var string[]
   */
  public $tempFiles;
  /**
   * Optional. The map of transform name prefixes of the job to be replaced to
   * the corresponding name prefixes of the new job.
   *
   * @var string[]
   */
  public $transformNameMapping;
  /**
   * Optional. The type of Dataflow job.
   *
   * @var string
   */
  public $type;

  /**
   * The client's unique identifier of the job, re-used across retried attempts.
   * If this field is set, the service will ensure its uniqueness. The request
   * to create a job will fail if the service has knowledge of a previously
   * submitted job with the same client's ID and job name. The caller may use
   * this field to ensure idempotence of job creation across retried attempts to
   * create a job. By default, the field is empty and, in that case, the service
   * ignores it.
   *
   * @param string $clientRequestId
   */
  public function setClientRequestId($clientRequestId)
  {
    $this->clientRequestId = $clientRequestId;
  }
  /**
   * @return string
   */
  public function getClientRequestId()
  {
    return $this->clientRequestId;
  }
  /**
   * The timestamp when the job was initially created. Immutable and set by the
   * Cloud Dataflow service.
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
   * If this is specified, the job's initial state is populated from the given
   * snapshot.
   *
   * @param string $createdFromSnapshotId
   */
  public function setCreatedFromSnapshotId($createdFromSnapshotId)
  {
    $this->createdFromSnapshotId = $createdFromSnapshotId;
  }
  /**
   * @return string
   */
  public function getCreatedFromSnapshotId()
  {
    return $this->createdFromSnapshotId;
  }
  /**
   * The current state of the job. Jobs are created in the `JOB_STATE_STOPPED`
   * state unless otherwise specified. A job in the `JOB_STATE_RUNNING` state
   * may asynchronously enter a terminal state. After a job has reached a
   * terminal state, no further state updates may be made. This field might be
   * mutated by the Dataflow service; callers cannot mutate it.
   *
   * Accepted values: JOB_STATE_UNKNOWN, JOB_STATE_STOPPED, JOB_STATE_RUNNING,
   * JOB_STATE_DONE, JOB_STATE_FAILED, JOB_STATE_CANCELLED, JOB_STATE_UPDATED,
   * JOB_STATE_DRAINING, JOB_STATE_DRAINED, JOB_STATE_PENDING,
   * JOB_STATE_CANCELLING, JOB_STATE_QUEUED, JOB_STATE_RESOURCE_CLEANING_UP,
   * JOB_STATE_PAUSING, JOB_STATE_PAUSED
   *
   * @param self::CURRENT_STATE_* $currentState
   */
  public function setCurrentState($currentState)
  {
    $this->currentState = $currentState;
  }
  /**
   * @return self::CURRENT_STATE_*
   */
  public function getCurrentState()
  {
    return $this->currentState;
  }
  /**
   * The timestamp associated with the current state.
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
   * Optional. The environment for the job.
   *
   * @param Environment $environment
   */
  public function setEnvironment(Environment $environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return Environment
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * Deprecated.
   *
   * @param JobExecutionInfo $executionInfo
   */
  public function setExecutionInfo(JobExecutionInfo $executionInfo)
  {
    $this->executionInfo = $executionInfo;
  }
  /**
   * @return JobExecutionInfo
   */
  public function getExecutionInfo()
  {
    return $this->executionInfo;
  }
  /**
   * The unique ID of this job. This field is set by the Dataflow service when
   * the job is created, and is immutable for the life of the job.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * This field is populated by the Dataflow service to support filtering jobs
   * by the metadata values provided here. Populated for ListJobs and all GetJob
   * views SUMMARY and higher.
   *
   * @param JobMetadata $jobMetadata
   */
  public function setJobMetadata(JobMetadata $jobMetadata)
  {
    $this->jobMetadata = $jobMetadata;
  }
  /**
   * @return JobMetadata
   */
  public function getJobMetadata()
  {
    return $this->jobMetadata;
  }
  /**
   * User-defined labels for this job. The labels map can contain no more than
   * 64 entries. Entries of the labels map are UTF8 strings that comply with the
   * following restrictions: * Keys must conform to regexp: \p{Ll}\p{Lo}{0,62} *
   * Values must conform to regexp: [\p{Ll}\p{Lo}\p{N}_-]{0,63} * Both keys and
   * values are additionally constrained to be <= 128 bytes in size.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Optional. The [regional endpoint]
   * (https://cloud.google.com/dataflow/docs/concepts/regional-endpoints) that
   * contains this job.
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
   * Optional. The user-specified Dataflow job name. Only one active job with a
   * given name can exist in a project within one region at any given time. Jobs
   * in different regions can have the same name. If a caller attempts to create
   * a job with the same name as an active job that already exists, the attempt
   * returns the existing job. The name must match the regular expression
   * `[a-z]([-a-z0-9]{0,1022}[a-z0-9])?`
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
   * Preliminary field: The format of this data may change at any time. A
   * description of the user pipeline and stages through which it is executed.
   * Created by Cloud Dataflow service. Only retrieved with JOB_VIEW_DESCRIPTION
   * or JOB_VIEW_ALL.
   *
   * @param PipelineDescription $pipelineDescription
   */
  public function setPipelineDescription(PipelineDescription $pipelineDescription)
  {
    $this->pipelineDescription = $pipelineDescription;
  }
  /**
   * @return PipelineDescription
   */
  public function getPipelineDescription()
  {
    return $this->pipelineDescription;
  }
  /**
   * The ID of the Google Cloud project that the job belongs to.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * If this job is an update of an existing job, this field is the job ID of
   * the job it replaced. When sending a `CreateJobRequest`, you can update a
   * job by specifying it here. The job named here is stopped, and its
   * intermediate state is transferred to this job.
   *
   * @param string $replaceJobId
   */
  public function setReplaceJobId($replaceJobId)
  {
    $this->replaceJobId = $replaceJobId;
  }
  /**
   * @return string
   */
  public function getReplaceJobId()
  {
    return $this->replaceJobId;
  }
  /**
   * If another job is an update of this job (and thus, this job is in
   * `JOB_STATE_UPDATED`), this field contains the ID of that job.
   *
   * @param string $replacedByJobId
   */
  public function setReplacedByJobId($replacedByJobId)
  {
    $this->replacedByJobId = $replacedByJobId;
  }
  /**
   * @return string
   */
  public function getReplacedByJobId()
  {
    return $this->replacedByJobId;
  }
  /**
   * The job's requested state. Applies to `UpdateJob` requests. Set
   * `requested_state` with `UpdateJob` requests to switch between the states
   * `JOB_STATE_STOPPED` and `JOB_STATE_RUNNING`. You can also use `UpdateJob`
   * requests to change a job's state from `JOB_STATE_RUNNING` to
   * `JOB_STATE_CANCELLED`, `JOB_STATE_DONE`, or `JOB_STATE_DRAINED`. These
   * states irrevocably terminate the job if it hasn't already reached a
   * terminal state. This field has no effect on `CreateJob` requests.
   *
   * Accepted values: JOB_STATE_UNKNOWN, JOB_STATE_STOPPED, JOB_STATE_RUNNING,
   * JOB_STATE_DONE, JOB_STATE_FAILED, JOB_STATE_CANCELLED, JOB_STATE_UPDATED,
   * JOB_STATE_DRAINING, JOB_STATE_DRAINED, JOB_STATE_PENDING,
   * JOB_STATE_CANCELLING, JOB_STATE_QUEUED, JOB_STATE_RESOURCE_CLEANING_UP,
   * JOB_STATE_PAUSING, JOB_STATE_PAUSED
   *
   * @param self::REQUESTED_STATE_* $requestedState
   */
  public function setRequestedState($requestedState)
  {
    $this->requestedState = $requestedState;
  }
  /**
   * @return self::REQUESTED_STATE_*
   */
  public function getRequestedState()
  {
    return $this->requestedState;
  }
  /**
   * This field may ONLY be modified at runtime using the projects.jobs.update
   * method to adjust job behavior. This field has no effect when specified at
   * job creation.
   *
   * @param RuntimeUpdatableParams $runtimeUpdatableParams
   */
  public function setRuntimeUpdatableParams(RuntimeUpdatableParams $runtimeUpdatableParams)
  {
    $this->runtimeUpdatableParams = $runtimeUpdatableParams;
  }
  /**
   * @return RuntimeUpdatableParams
   */
  public function getRuntimeUpdatableParams()
  {
    return $this->runtimeUpdatableParams;
  }
  /**
   * Output only. Reserved for future use. This field is set only in responses
   * from the server; it is ignored if it is set in any requests.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Reserved for future use. This field is set only in responses from the
   * server; it is ignored if it is set in any requests.
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
   * Output only. Resources used by the Dataflow Service to run the job.
   *
   * @param ServiceResources $serviceResources
   */
  public function setServiceResources(ServiceResources $serviceResources)
  {
    $this->serviceResources = $serviceResources;
  }
  /**
   * @return ServiceResources
   */
  public function getServiceResources()
  {
    return $this->serviceResources;
  }
  /**
   * This field may be mutated by the Cloud Dataflow service; callers cannot
   * mutate it.
   *
   * @param ExecutionStageState[] $stageStates
   */
  public function setStageStates($stageStates)
  {
    $this->stageStates = $stageStates;
  }
  /**
   * @return ExecutionStageState[]
   */
  public function getStageStates()
  {
    return $this->stageStates;
  }
  /**
   * The timestamp when the job was started (transitioned to JOB_STATE_PENDING).
   * Flexible resource scheduling jobs are started with some delay after job
   * creation, so start_time is unset before start and is updated when the job
   * is started by the Cloud Dataflow service. For other jobs, start_time always
   * equals to create_time and is immutable and set by the Cloud Dataflow
   * service.
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
   * Exactly one of step or steps_location should be specified. The top-level
   * steps that constitute the entire job. Only retrieved with JOB_VIEW_ALL.
   *
   * @param Step[] $steps
   */
  public function setSteps($steps)
  {
    $this->steps = $steps;
  }
  /**
   * @return Step[]
   */
  public function getSteps()
  {
    return $this->steps;
  }
  /**
   * The Cloud Storage location where the steps are stored.
   *
   * @param string $stepsLocation
   */
  public function setStepsLocation($stepsLocation)
  {
    $this->stepsLocation = $stepsLocation;
  }
  /**
   * @return string
   */
  public function getStepsLocation()
  {
    return $this->stepsLocation;
  }
  /**
   * A set of files the system should be aware of that are used for temporary
   * storage. These temporary files will be removed on job completion. No
   * duplicates are allowed. No file patterns are supported. The supported files
   * are: Google Cloud Storage: storage.googleapis.com/{bucket}/{object}
   * bucket.storage.googleapis.com/{object}
   *
   * @param string[] $tempFiles
   */
  public function setTempFiles($tempFiles)
  {
    $this->tempFiles = $tempFiles;
  }
  /**
   * @return string[]
   */
  public function getTempFiles()
  {
    return $this->tempFiles;
  }
  /**
   * Optional. The map of transform name prefixes of the job to be replaced to
   * the corresponding name prefixes of the new job.
   *
   * @param string[] $transformNameMapping
   */
  public function setTransformNameMapping($transformNameMapping)
  {
    $this->transformNameMapping = $transformNameMapping;
  }
  /**
   * @return string[]
   */
  public function getTransformNameMapping()
  {
    return $this->transformNameMapping;
  }
  /**
   * Optional. The type of Dataflow job.
   *
   * Accepted values: JOB_TYPE_UNKNOWN, JOB_TYPE_BATCH, JOB_TYPE_STREAMING
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Job::class, 'Google_Service_Dataflow_Job');
