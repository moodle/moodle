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

class GoogleCloudDatapipelinesV1Job extends \Google\Model
{
  /**
   * The job state isn't specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The job is waiting to start execution.
   */
  public const STATE_STATE_PENDING = 'STATE_PENDING';
  /**
   * The job is executing.
   */
  public const STATE_STATE_RUNNING = 'STATE_RUNNING';
  /**
   * The job has finished execution successfully.
   */
  public const STATE_STATE_DONE = 'STATE_DONE';
  /**
   * The job has finished execution with a failure.
   */
  public const STATE_STATE_FAILED = 'STATE_FAILED';
  /**
   * The job has been terminated upon user request.
   */
  public const STATE_STATE_CANCELLED = 'STATE_CANCELLED';
  /**
   * Output only. The time of job creation.
   *
   * @var string
   */
  public $createTime;
  protected $dataflowJobDetailsType = GoogleCloudDatapipelinesV1DataflowJobDetails::class;
  protected $dataflowJobDetailsDataType = '';
  /**
   * Output only. The time of job termination. This is absent if the job is
   * still running.
   *
   * @var string
   */
  public $endTime;
  /**
   * Output only. The internal ID for the job.
   *
   * @var string
   */
  public $id;
  /**
   * Required. The fully qualified resource name for the job.
   *
   * @var string
   */
  public $name;
  /**
   * The current state of the job.
   *
   * @var string
   */
  public $state;
  protected $statusType = GoogleRpcStatus::class;
  protected $statusDataType = '';

  /**
   * Output only. The time of job creation.
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
   * All the details that are specific to a Dataflow job.
   *
   * @param GoogleCloudDatapipelinesV1DataflowJobDetails $dataflowJobDetails
   */
  public function setDataflowJobDetails(GoogleCloudDatapipelinesV1DataflowJobDetails $dataflowJobDetails)
  {
    $this->dataflowJobDetails = $dataflowJobDetails;
  }
  /**
   * @return GoogleCloudDatapipelinesV1DataflowJobDetails
   */
  public function getDataflowJobDetails()
  {
    return $this->dataflowJobDetails;
  }
  /**
   * Output only. The time of job termination. This is absent if the job is
   * still running.
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
   * Output only. The internal ID for the job.
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
   * Required. The fully qualified resource name for the job.
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
   * The current state of the job.
   *
   * Accepted values: STATE_UNSPECIFIED, STATE_PENDING, STATE_RUNNING,
   * STATE_DONE, STATE_FAILED, STATE_CANCELLED
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
   * Status capturing any error code or message related to job creation or
   * execution.
   *
   * @param GoogleRpcStatus $status
   */
  public function setStatus(GoogleRpcStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatapipelinesV1Job::class, 'Google_Service_Datapipelines_GoogleCloudDatapipelinesV1Job');
