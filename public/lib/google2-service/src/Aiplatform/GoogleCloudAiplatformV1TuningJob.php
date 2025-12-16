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

class GoogleCloudAiplatformV1TuningJob extends \Google\Model
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
   * The base model that is being tuned. See [Supported
   * models](https://cloud.google.com/vertex-ai/generative-ai/docs/model-
   * reference/tuning#supported_models).
   *
   * @var string
   */
  public $baseModel;
  /**
   * Output only. Time when the TuningJob was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The description of the TuningJob.
   *
   * @var string
   */
  public $description;
  protected $encryptionSpecType = GoogleCloudAiplatformV1EncryptionSpec::class;
  protected $encryptionSpecDataType = '';
  /**
   * Output only. Time when the TuningJob entered any of the following
   * JobStates: `JOB_STATE_SUCCEEDED`, `JOB_STATE_FAILED`,
   * `JOB_STATE_CANCELLED`, `JOB_STATE_EXPIRED`.
   *
   * @var string
   */
  public $endTime;
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';
  /**
   * Output only. The Experiment associated with this TuningJob.
   *
   * @var string
   */
  public $experiment;
  /**
   * Optional. The labels with user-defined metadata to organize TuningJob and
   * generated resources such as Model and Endpoint. Label keys and values can
   * be no longer than 64 characters (Unicode codepoints), can only contain
   * lowercase letters, numeric characters, underscores and dashes.
   * International characters are allowed. See https://goo.gl/xmQnxf for more
   * information and examples of labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Identifier. Resource name of a TuningJob. Format:
   * `projects/{project}/locations/{location}/tuningJobs/{tuning_job}`
   *
   * @var string
   */
  public $name;
  protected $preTunedModelType = GoogleCloudAiplatformV1PreTunedModel::class;
  protected $preTunedModelDataType = '';
  protected $preferenceOptimizationSpecType = GoogleCloudAiplatformV1PreferenceOptimizationSpec::class;
  protected $preferenceOptimizationSpecDataType = '';
  /**
   * The service account that the tuningJob workload runs as. If not specified,
   * the Vertex AI Secure Fine-Tuned Service Agent in the project will be used.
   * See https://cloud.google.com/iam/docs/service-agents#vertex-ai-secure-fine-
   * tuning-service-agent Users starting the pipeline must have the
   * `iam.serviceAccounts.actAs` permission on this service account.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Output only. Time when the TuningJob for the first time entered the
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
  protected $supervisedTuningSpecType = GoogleCloudAiplatformV1SupervisedTuningSpec::class;
  protected $supervisedTuningSpecDataType = '';
  protected $tunedModelType = GoogleCloudAiplatformV1TunedModel::class;
  protected $tunedModelDataType = '';
  /**
   * Optional. The display name of the TunedModel. The name can be up to 128
   * characters long and can consist of any UTF-8 characters. For continuous
   * tuning, tuned_model_display_name will by default use the same display name
   * as the pre-tuned model. If a new display name is provided, the tuning job
   * will create a new model instead of a new version.
   *
   * @var string
   */
  public $tunedModelDisplayName;
  protected $tuningDataStatsType = GoogleCloudAiplatformV1TuningDataStats::class;
  protected $tuningDataStatsDataType = '';
  /**
   * Output only. Time when the TuningJob was most recently updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * The base model that is being tuned. See [Supported
   * models](https://cloud.google.com/vertex-ai/generative-ai/docs/model-
   * reference/tuning#supported_models).
   *
   * @param string $baseModel
   */
  public function setBaseModel($baseModel)
  {
    $this->baseModel = $baseModel;
  }
  /**
   * @return string
   */
  public function getBaseModel()
  {
    return $this->baseModel;
  }
  /**
   * Output only. Time when the TuningJob was created.
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
   * Optional. The description of the TuningJob.
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
   * Customer-managed encryption key options for a TuningJob. If this is set,
   * then all resources created by the TuningJob will be encrypted with the
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
   * Output only. Time when the TuningJob entered any of the following
   * JobStates: `JOB_STATE_SUCCEEDED`, `JOB_STATE_FAILED`,
   * `JOB_STATE_CANCELLED`, `JOB_STATE_EXPIRED`.
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
   * Output only. The Experiment associated with this TuningJob.
   *
   * @param string $experiment
   */
  public function setExperiment($experiment)
  {
    $this->experiment = $experiment;
  }
  /**
   * @return string
   */
  public function getExperiment()
  {
    return $this->experiment;
  }
  /**
   * Optional. The labels with user-defined metadata to organize TuningJob and
   * generated resources such as Model and Endpoint. Label keys and values can
   * be no longer than 64 characters (Unicode codepoints), can only contain
   * lowercase letters, numeric characters, underscores and dashes.
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
   * Output only. Identifier. Resource name of a TuningJob. Format:
   * `projects/{project}/locations/{location}/tuningJobs/{tuning_job}`
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
   * The pre-tuned model for continuous tuning.
   *
   * @param GoogleCloudAiplatformV1PreTunedModel $preTunedModel
   */
  public function setPreTunedModel(GoogleCloudAiplatformV1PreTunedModel $preTunedModel)
  {
    $this->preTunedModel = $preTunedModel;
  }
  /**
   * @return GoogleCloudAiplatformV1PreTunedModel
   */
  public function getPreTunedModel()
  {
    return $this->preTunedModel;
  }
  /**
   * Tuning Spec for Preference Optimization.
   *
   * @param GoogleCloudAiplatformV1PreferenceOptimizationSpec $preferenceOptimizationSpec
   */
  public function setPreferenceOptimizationSpec(GoogleCloudAiplatformV1PreferenceOptimizationSpec $preferenceOptimizationSpec)
  {
    $this->preferenceOptimizationSpec = $preferenceOptimizationSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1PreferenceOptimizationSpec
   */
  public function getPreferenceOptimizationSpec()
  {
    return $this->preferenceOptimizationSpec;
  }
  /**
   * The service account that the tuningJob workload runs as. If not specified,
   * the Vertex AI Secure Fine-Tuned Service Agent in the project will be used.
   * See https://cloud.google.com/iam/docs/service-agents#vertex-ai-secure-fine-
   * tuning-service-agent Users starting the pipeline must have the
   * `iam.serviceAccounts.actAs` permission on this service account.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Output only. Time when the TuningJob for the first time entered the
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
   * Tuning Spec for Supervised Fine Tuning.
   *
   * @param GoogleCloudAiplatformV1SupervisedTuningSpec $supervisedTuningSpec
   */
  public function setSupervisedTuningSpec(GoogleCloudAiplatformV1SupervisedTuningSpec $supervisedTuningSpec)
  {
    $this->supervisedTuningSpec = $supervisedTuningSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1SupervisedTuningSpec
   */
  public function getSupervisedTuningSpec()
  {
    return $this->supervisedTuningSpec;
  }
  /**
   * Output only. The tuned model resources associated with this TuningJob.
   *
   * @param GoogleCloudAiplatformV1TunedModel $tunedModel
   */
  public function setTunedModel(GoogleCloudAiplatformV1TunedModel $tunedModel)
  {
    $this->tunedModel = $tunedModel;
  }
  /**
   * @return GoogleCloudAiplatformV1TunedModel
   */
  public function getTunedModel()
  {
    return $this->tunedModel;
  }
  /**
   * Optional. The display name of the TunedModel. The name can be up to 128
   * characters long and can consist of any UTF-8 characters. For continuous
   * tuning, tuned_model_display_name will by default use the same display name
   * as the pre-tuned model. If a new display name is provided, the tuning job
   * will create a new model instead of a new version.
   *
   * @param string $tunedModelDisplayName
   */
  public function setTunedModelDisplayName($tunedModelDisplayName)
  {
    $this->tunedModelDisplayName = $tunedModelDisplayName;
  }
  /**
   * @return string
   */
  public function getTunedModelDisplayName()
  {
    return $this->tunedModelDisplayName;
  }
  /**
   * Output only. The tuning data statistics associated with this TuningJob.
   *
   * @param GoogleCloudAiplatformV1TuningDataStats $tuningDataStats
   */
  public function setTuningDataStats(GoogleCloudAiplatformV1TuningDataStats $tuningDataStats)
  {
    $this->tuningDataStats = $tuningDataStats;
  }
  /**
   * @return GoogleCloudAiplatformV1TuningDataStats
   */
  public function getTuningDataStats()
  {
    return $this->tuningDataStats;
  }
  /**
   * Output only. Time when the TuningJob was most recently updated.
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
class_alias(GoogleCloudAiplatformV1TuningJob::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1TuningJob');
