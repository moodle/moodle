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

class GoogleCloudAiplatformV1CustomJob extends \Google\Model
{
  /**
   * The job state is unspecified.
   */
  public const STATE_JOB_STATE_UNSPECIFIED = 'JOB_STATE_UNSPECIFIED';
  /**
   * The job has been just created or resumed and processing has not yet begun.
   */
  public const STATE_JOB_STATE_QUEUED = 'JOB_STATE_QUEUED';
  /**
   * The service is preparing to run the job.
   */
  public const STATE_JOB_STATE_PENDING = 'JOB_STATE_PENDING';
  /**
   * The job is in progress.
   */
  public const STATE_JOB_STATE_RUNNING = 'JOB_STATE_RUNNING';
  /**
   * The job completed successfully.
   */
  public const STATE_JOB_STATE_SUCCEEDED = 'JOB_STATE_SUCCEEDED';
  /**
   * The job failed.
   */
  public const STATE_JOB_STATE_FAILED = 'JOB_STATE_FAILED';
  /**
   * The job is being cancelled. From this state the job may only go to either
   * `JOB_STATE_SUCCEEDED`, `JOB_STATE_FAILED` or `JOB_STATE_CANCELLED`.
   */
  public const STATE_JOB_STATE_CANCELLING = 'JOB_STATE_CANCELLING';
  /**
   * The job has been cancelled.
   */
  public const STATE_JOB_STATE_CANCELLED = 'JOB_STATE_CANCELLED';
  /**
   * The job has been stopped, and can be resumed.
   */
  public const STATE_JOB_STATE_PAUSED = 'JOB_STATE_PAUSED';
  /**
   * The job has expired.
   */
  public const STATE_JOB_STATE_EXPIRED = 'JOB_STATE_EXPIRED';
  /**
   * The job is being updated. Only jobs in the `RUNNING` state can be updated.
   * After updating, the job goes back to the `RUNNING` state.
   */
  public const STATE_JOB_STATE_UPDATING = 'JOB_STATE_UPDATING';
  /**
   * The job is partially succeeded, some results may be missing due to errors.
   */
  public const STATE_JOB_STATE_PARTIALLY_SUCCEEDED = 'JOB_STATE_PARTIALLY_SUCCEEDED';
  /**
   * Output only. Time when the CustomJob was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The display name of the CustomJob. The name can be up to 128
   * characters long and can consist of any UTF-8 characters.
   *
   * @var string
   */
  public $displayName;
  protected $encryptionSpecType = GoogleCloudAiplatformV1EncryptionSpec::class;
  protected $encryptionSpecDataType = '';
  /**
   * Output only. Time when the CustomJob entered any of the following states:
   * `JOB_STATE_SUCCEEDED`, `JOB_STATE_FAILED`, `JOB_STATE_CANCELLED`.
   *
   * @var string
   */
  public $endTime;
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';
  protected $jobSpecType = GoogleCloudAiplatformV1CustomJobSpec::class;
  protected $jobSpecDataType = '';
  /**
   * The labels with user-defined metadata to organize CustomJobs. Label keys
   * and values can be no longer than 64 characters (Unicode codepoints), can
   * only contain lowercase letters, numeric characters, underscores and dashes.
   * International characters are allowed. See https://goo.gl/xmQnxf for more
   * information and examples of labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Resource name of a CustomJob.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. Time when the CustomJob for the first time entered the
   * `JOB_STATE_RUNNING` state.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. The detailed state of the job.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Time when the CustomJob was most recently updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Output only. URIs for accessing [interactive
   * shells](https://cloud.google.com/vertex-ai/docs/training/monitor-debug-
   * interactive-shell) (one URI for each training node). Only available if
   * job_spec.enable_web_access is `true`. The keys are names of each node in
   * the training job; for example, `workerpool0-0` for the primary node,
   * `workerpool1-0` for the first node in the second worker pool, and
   * `workerpool1-1` for the second node in the second worker pool. The values
   * are the URIs for each node's interactive shell.
   *
   * @var string[]
   */
  public $webAccessUris;

  /**
   * Output only. Time when the CustomJob was created.
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
   * Required. The display name of the CustomJob. The name can be up to 128
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
   * Customer-managed encryption key options for a CustomJob. If this is set,
   * then all resources created by the CustomJob will be encrypted with the
   * provided encryption key.
   *
   * @param GoogleCloudAiplatformV1EncryptionSpec $encryptionSpec
   */
  public function setEncryptionSpec(GoogleCloudAiplatformV1EncryptionSpec $encryptionSpec)
  {
    $this->encryptionSpec = $encryptionSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1EncryptionSpec
   */
  public function getEncryptionSpec()
  {
    return $this->encryptionSpec;
  }
  /**
   * Output only. Time when the CustomJob entered any of the following states:
   * `JOB_STATE_SUCCEEDED`, `JOB_STATE_FAILED`, `JOB_STATE_CANCELLED`.
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
   * Output only. Only populated when job's state is `JOB_STATE_FAILED` or
   * `JOB_STATE_CANCELLED`.
   *
   * @param GoogleRpcStatus $error
   */
  public function setError(GoogleRpcStatus $error)
  {
    $this->error = $error;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Required. Job spec.
   *
   * @param GoogleCloudAiplatformV1CustomJobSpec $jobSpec
   */
  public function setJobSpec(GoogleCloudAiplatformV1CustomJobSpec $jobSpec)
  {
    $this->jobSpec = $jobSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1CustomJobSpec
   */
  public function getJobSpec()
  {
    return $this->jobSpec;
  }
  /**
   * The labels with user-defined metadata to organize CustomJobs. Label keys
   * and values can be no longer than 64 characters (Unicode codepoints), can
   * only contain lowercase letters, numeric characters, underscores and dashes.
   * International characters are allowed. See https://goo.gl/xmQnxf for more
   * information and examples of labels.
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
   * Output only. Resource name of a CustomJob.
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
   * Output only. Reserved for future use.
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
   * Output only. Reserved for future use.
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
   * Output only. Time when the CustomJob for the first time entered the
   * `JOB_STATE_RUNNING` state.
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
   * Output only. The detailed state of the job.
   *
   * Accepted values: JOB_STATE_UNSPECIFIED, JOB_STATE_QUEUED,
   * JOB_STATE_PENDING, JOB_STATE_RUNNING, JOB_STATE_SUCCEEDED,
   * JOB_STATE_FAILED, JOB_STATE_CANCELLING, JOB_STATE_CANCELLED,
   * JOB_STATE_PAUSED, JOB_STATE_EXPIRED, JOB_STATE_UPDATING,
   * JOB_STATE_PARTIALLY_SUCCEEDED
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
   * Output only. Time when the CustomJob was most recently updated.
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
  /**
   * Output only. URIs for accessing [interactive
   * shells](https://cloud.google.com/vertex-ai/docs/training/monitor-debug-
   * interactive-shell) (one URI for each training node). Only available if
   * job_spec.enable_web_access is `true`. The keys are names of each node in
   * the training job; for example, `workerpool0-0` for the primary node,
   * `workerpool1-0` for the first node in the second worker pool, and
   * `workerpool1-1` for the second node in the second worker pool. The values
   * are the URIs for each node's interactive shell.
   *
   * @param string[] $webAccessUris
   */
  public function setWebAccessUris($webAccessUris)
  {
    $this->webAccessUris = $webAccessUris;
  }
  /**
   * @return string[]
   */
  public function getWebAccessUris()
  {
    return $this->webAccessUris;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1CustomJob::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1CustomJob');
