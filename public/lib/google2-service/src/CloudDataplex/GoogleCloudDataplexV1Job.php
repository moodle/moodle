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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1Job extends \Google\Model
{
  /**
   * Service used to run the job is unspecified.
   */
  public const SERVICE_SERVICE_UNSPECIFIED = 'SERVICE_UNSPECIFIED';
  /**
   * Dataproc service is used to run this job.
   */
  public const SERVICE_DATAPROC = 'DATAPROC';
  /**
   * The job state is unknown.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The job is running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The job is cancelling.
   */
  public const STATE_CANCELLING = 'CANCELLING';
  /**
   * The job cancellation was successful.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The job completed successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The job is no longer running due to an error.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The job was cancelled outside of Dataplex Universal Catalog.
   */
  public const STATE_ABORTED = 'ABORTED';
  /**
   * The trigger is unspecified.
   */
  public const TRIGGER_TRIGGER_UNSPECIFIED = 'TRIGGER_UNSPECIFIED';
  /**
   * The job was triggered by Dataplex Universal Catalog based on trigger spec
   * from task definition.
   */
  public const TRIGGER_TASK_CONFIG = 'TASK_CONFIG';
  /**
   * The job was triggered by the explicit call of Task API.
   */
  public const TRIGGER_RUN_REQUEST = 'RUN_REQUEST';
  /**
   * Output only. The time when the job ended.
   *
   * @var string
   */
  public $endTime;
  protected $executionSpecType = GoogleCloudDataplexV1TaskExecutionSpec::class;
  protected $executionSpecDataType = '';
  /**
   * Output only. User-defined labels for the task.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Additional information about the current state.
   *
   * @var string
   */
  public $message;
  /**
   * Output only. The relative resource name of the job, of the form: projects/{
   * project_number}/locations/{location_id}/lakes/{lake_id}/tasks/{task_id}/job
   * s/{job_id}.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The number of times the job has been retried (excluding the
   * initial attempt).
   *
   * @var string
   */
  public $retryCount;
  /**
   * Output only. The underlying service running a job.
   *
   * @var string
   */
  public $service;
  /**
   * Output only. The full resource name for the job run under a particular
   * service.
   *
   * @var string
   */
  public $serviceJob;
  /**
   * Output only. The time when the job was started.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. Execution state for the job.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Job execution trigger.
   *
   * @var string
   */
  public $trigger;
  /**
   * Output only. System generated globally unique ID for the job.
   *
   * @var string
   */
  public $uid;

  /**
   * Output only. The time when the job ended.
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
   * Output only. Spec related to how a task is executed.
   *
   * @param GoogleCloudDataplexV1TaskExecutionSpec $executionSpec
   */
  public function setExecutionSpec(GoogleCloudDataplexV1TaskExecutionSpec $executionSpec)
  {
    $this->executionSpec = $executionSpec;
  }
  /**
   * @return GoogleCloudDataplexV1TaskExecutionSpec
   */
  public function getExecutionSpec()
  {
    return $this->executionSpec;
  }
  /**
   * Output only. User-defined labels for the task.
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
   * Output only. Additional information about the current state.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * Output only. The relative resource name of the job, of the form: projects/{
   * project_number}/locations/{location_id}/lakes/{lake_id}/tasks/{task_id}/job
   * s/{job_id}.
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
   * Output only. The number of times the job has been retried (excluding the
   * initial attempt).
   *
   * @param string $retryCount
   */
  public function setRetryCount($retryCount)
  {
    $this->retryCount = $retryCount;
  }
  /**
   * @return string
   */
  public function getRetryCount()
  {
    return $this->retryCount;
  }
  /**
   * Output only. The underlying service running a job.
   *
   * Accepted values: SERVICE_UNSPECIFIED, DATAPROC
   *
   * @param self::SERVICE_* $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return self::SERVICE_*
   */
  public function getService()
  {
    return $this->service;
  }
  /**
   * Output only. The full resource name for the job run under a particular
   * service.
   *
   * @param string $serviceJob
   */
  public function setServiceJob($serviceJob)
  {
    $this->serviceJob = $serviceJob;
  }
  /**
   * @return string
   */
  public function getServiceJob()
  {
    return $this->serviceJob;
  }
  /**
   * Output only. The time when the job was started.
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
   * Output only. Execution state for the job.
   *
   * Accepted values: STATE_UNSPECIFIED, RUNNING, CANCELLING, CANCELLED,
   * SUCCEEDED, FAILED, ABORTED
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
   * Output only. Job execution trigger.
   *
   * Accepted values: TRIGGER_UNSPECIFIED, TASK_CONFIG, RUN_REQUEST
   *
   * @param self::TRIGGER_* $trigger
   */
  public function setTrigger($trigger)
  {
    $this->trigger = $trigger;
  }
  /**
   * @return self::TRIGGER_*
   */
  public function getTrigger()
  {
    return $this->trigger;
  }
  /**
   * Output only. System generated globally unique ID for the job.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1Job::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1Job');
