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

class GoogleCloudAiplatformV1HyperparameterTuningJob extends \Google\Collection
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
  protected $collection_key = 'trials';
  /**
   * Output only. Time when the HyperparameterTuningJob was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The display name of the HyperparameterTuningJob. The name can be
   * up to 128 characters long and can consist of any UTF-8 characters.
   *
   * @var string
   */
  public $displayName;
  protected $encryptionSpecType = GoogleCloudAiplatformV1EncryptionSpec::class;
  protected $encryptionSpecDataType = '';
  /**
   * Output only. Time when the HyperparameterTuningJob entered any of the
   * following states: `JOB_STATE_SUCCEEDED`, `JOB_STATE_FAILED`,
   * `JOB_STATE_CANCELLED`.
   *
   * @var string
   */
  public $endTime;
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';
  /**
   * The labels with user-defined metadata to organize HyperparameterTuningJobs.
   * Label keys and values can be no longer than 64 characters (Unicode
   * codepoints), can only contain lowercase letters, numeric characters,
   * underscores and dashes. International characters are allowed. See
   * https://goo.gl/xmQnxf for more information and examples of labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * The number of failed Trials that need to be seen before failing the
   * HyperparameterTuningJob. If set to 0, Vertex AI decides how many Trials
   * must fail before the whole job fails.
   *
   * @var int
   */
  public $maxFailedTrialCount;
  /**
   * Required. The desired total number of Trials.
   *
   * @var int
   */
  public $maxTrialCount;
  /**
   * Output only. Resource name of the HyperparameterTuningJob.
   *
   * @var string
   */
  public $name;
  /**
   * Required. The desired number of Trials to run in parallel.
   *
   * @var int
   */
  public $parallelTrialCount;
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
   * Output only. Time when the HyperparameterTuningJob for the first time
   * entered the `JOB_STATE_RUNNING` state.
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
  protected $studySpecType = GoogleCloudAiplatformV1StudySpec::class;
  protected $studySpecDataType = '';
  protected $trialJobSpecType = GoogleCloudAiplatformV1CustomJobSpec::class;
  protected $trialJobSpecDataType = '';
  protected $trialsType = GoogleCloudAiplatformV1Trial::class;
  protected $trialsDataType = 'array';
  /**
   * Output only. Time when the HyperparameterTuningJob was most recently
   * updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Time when the HyperparameterTuningJob was created.
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
   * Required. The display name of the HyperparameterTuningJob. The name can be
   * up to 128 characters long and can consist of any UTF-8 characters.
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
   * Customer-managed encryption key options for a HyperparameterTuningJob. If
   * this is set, then all resources created by the HyperparameterTuningJob will
   * be encrypted with the provided encryption key.
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
   * Output only. Time when the HyperparameterTuningJob entered any of the
   * following states: `JOB_STATE_SUCCEEDED`, `JOB_STATE_FAILED`,
   * `JOB_STATE_CANCELLED`.
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
   * Output only. Only populated when job's state is JOB_STATE_FAILED or
   * JOB_STATE_CANCELLED.
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
   * The labels with user-defined metadata to organize HyperparameterTuningJobs.
   * Label keys and values can be no longer than 64 characters (Unicode
   * codepoints), can only contain lowercase letters, numeric characters,
   * underscores and dashes. International characters are allowed. See
   * https://goo.gl/xmQnxf for more information and examples of labels.
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
   * The number of failed Trials that need to be seen before failing the
   * HyperparameterTuningJob. If set to 0, Vertex AI decides how many Trials
   * must fail before the whole job fails.
   *
   * @param int $maxFailedTrialCount
   */
  public function setMaxFailedTrialCount($maxFailedTrialCount)
  {
    $this->maxFailedTrialCount = $maxFailedTrialCount;
  }
  /**
   * @return int
   */
  public function getMaxFailedTrialCount()
  {
    return $this->maxFailedTrialCount;
  }
  /**
   * Required. The desired total number of Trials.
   *
   * @param int $maxTrialCount
   */
  public function setMaxTrialCount($maxTrialCount)
  {
    $this->maxTrialCount = $maxTrialCount;
  }
  /**
   * @return int
   */
  public function getMaxTrialCount()
  {
    return $this->maxTrialCount;
  }
  /**
   * Output only. Resource name of the HyperparameterTuningJob.
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
   * Required. The desired number of Trials to run in parallel.
   *
   * @param int $parallelTrialCount
   */
  public function setParallelTrialCount($parallelTrialCount)
  {
    $this->parallelTrialCount = $parallelTrialCount;
  }
  /**
   * @return int
   */
  public function getParallelTrialCount()
  {
    return $this->parallelTrialCount;
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
   * Output only. Time when the HyperparameterTuningJob for the first time
   * entered the `JOB_STATE_RUNNING` state.
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
   * Required. Study configuration of the HyperparameterTuningJob.
   *
   * @param GoogleCloudAiplatformV1StudySpec $studySpec
   */
  public function setStudySpec(GoogleCloudAiplatformV1StudySpec $studySpec)
  {
    $this->studySpec = $studySpec;
  }
  /**
   * @return GoogleCloudAiplatformV1StudySpec
   */
  public function getStudySpec()
  {
    return $this->studySpec;
  }
  /**
   * Required. The spec of a trial job. The same spec applies to the CustomJobs
   * created in all the trials.
   *
   * @param GoogleCloudAiplatformV1CustomJobSpec $trialJobSpec
   */
  public function setTrialJobSpec(GoogleCloudAiplatformV1CustomJobSpec $trialJobSpec)
  {
    $this->trialJobSpec = $trialJobSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1CustomJobSpec
   */
  public function getTrialJobSpec()
  {
    return $this->trialJobSpec;
  }
  /**
   * Output only. Trials of the HyperparameterTuningJob.
   *
   * @param GoogleCloudAiplatformV1Trial[] $trials
   */
  public function setTrials($trials)
  {
    $this->trials = $trials;
  }
  /**
   * @return GoogleCloudAiplatformV1Trial[]
   */
  public function getTrials()
  {
    return $this->trials;
  }
  /**
   * Output only. Time when the HyperparameterTuningJob was most recently
   * updated.
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
class_alias(GoogleCloudAiplatformV1HyperparameterTuningJob::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1HyperparameterTuningJob');
