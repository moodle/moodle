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

class GoogleCloudDataplexV1JobEvent extends \Google\Model
{
  /**
   * The job execution trigger is unspecified.
   */
  public const EXECUTION_TRIGGER_EXECUTION_TRIGGER_UNSPECIFIED = 'EXECUTION_TRIGGER_UNSPECIFIED';
  /**
   * The job was triggered by Dataplex Universal Catalog based on trigger spec
   * from task definition.
   */
  public const EXECUTION_TRIGGER_TASK_CONFIG = 'TASK_CONFIG';
  /**
   * The job was triggered by the explicit call of Task API.
   */
  public const EXECUTION_TRIGGER_RUN_REQUEST = 'RUN_REQUEST';
  /**
   * Unspecified service.
   */
  public const SERVICE_SERVICE_UNSPECIFIED = 'SERVICE_UNSPECIFIED';
  /**
   * Cloud Dataproc.
   */
  public const SERVICE_DATAPROC = 'DATAPROC';
  /**
   * Unspecified job state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Job successfully completed.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Job was unsuccessful.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Job was cancelled by the user.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * Job was cancelled or aborted via the service executing the job.
   */
  public const STATE_ABORTED = 'ABORTED';
  /**
   * Unspecified job type.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Spark jobs.
   */
  public const TYPE_SPARK = 'SPARK';
  /**
   * Notebook jobs.
   */
  public const TYPE_NOTEBOOK = 'NOTEBOOK';
  /**
   * The time when the job ended running.
   *
   * @var string
   */
  public $endTime;
  /**
   * Job execution trigger.
   *
   * @var string
   */
  public $executionTrigger;
  /**
   * The unique id identifying the job.
   *
   * @var string
   */
  public $jobId;
  /**
   * The log message.
   *
   * @var string
   */
  public $message;
  /**
   * The number of retries.
   *
   * @var int
   */
  public $retries;
  /**
   * The service used to execute the job.
   *
   * @var string
   */
  public $service;
  /**
   * The reference to the job within the service.
   *
   * @var string
   */
  public $serviceJob;
  /**
   * The time when the job started running.
   *
   * @var string
   */
  public $startTime;
  /**
   * The job state on completion.
   *
   * @var string
   */
  public $state;
  /**
   * The type of the job.
   *
   * @var string
   */
  public $type;

  /**
   * The time when the job ended running.
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
   * Job execution trigger.
   *
   * Accepted values: EXECUTION_TRIGGER_UNSPECIFIED, TASK_CONFIG, RUN_REQUEST
   *
   * @param self::EXECUTION_TRIGGER_* $executionTrigger
   */
  public function setExecutionTrigger($executionTrigger)
  {
    $this->executionTrigger = $executionTrigger;
  }
  /**
   * @return self::EXECUTION_TRIGGER_*
   */
  public function getExecutionTrigger()
  {
    return $this->executionTrigger;
  }
  /**
   * The unique id identifying the job.
   *
   * @param string $jobId
   */
  public function setJobId($jobId)
  {
    $this->jobId = $jobId;
  }
  /**
   * @return string
   */
  public function getJobId()
  {
    return $this->jobId;
  }
  /**
   * The log message.
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
   * The number of retries.
   *
   * @param int $retries
   */
  public function setRetries($retries)
  {
    $this->retries = $retries;
  }
  /**
   * @return int
   */
  public function getRetries()
  {
    return $this->retries;
  }
  /**
   * The service used to execute the job.
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
   * The reference to the job within the service.
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
   * The time when the job started running.
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
   * The job state on completion.
   *
   * Accepted values: STATE_UNSPECIFIED, SUCCEEDED, FAILED, CANCELLED, ABORTED
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
   * The type of the job.
   *
   * Accepted values: TYPE_UNSPECIFIED, SPARK, NOTEBOOK
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
class_alias(GoogleCloudDataplexV1JobEvent::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1JobEvent');
