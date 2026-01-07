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

class GoogleCloudAiplatformV1BatchPredictionJob extends \Google\Collection
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
  protected $collection_key = 'partialFailures';
  protected $completionStatsType = GoogleCloudAiplatformV1CompletionStats::class;
  protected $completionStatsDataType = '';
  /**
   * Output only. Time when the BatchPredictionJob was created.
   *
   * @var string
   */
  public $createTime;
  protected $dedicatedResourcesType = GoogleCloudAiplatformV1BatchDedicatedResources::class;
  protected $dedicatedResourcesDataType = '';
  /**
   * For custom-trained Models and AutoML Tabular Models, the container of the
   * DeployedModel instances will send `stderr` and `stdout` streams to Cloud
   * Logging by default. Please note that the logs incur cost, which are subject
   * to [Cloud Logging pricing](https://cloud.google.com/logging/pricing). User
   * can disable container logging by setting this flag to true.
   *
   * @var bool
   */
  public $disableContainerLogging;
  /**
   * Required. The user-defined name of this BatchPredictionJob.
   *
   * @var string
   */
  public $displayName;
  protected $encryptionSpecType = GoogleCloudAiplatformV1EncryptionSpec::class;
  protected $encryptionSpecDataType = '';
  /**
   * Output only. Time when the BatchPredictionJob entered any of the following
   * states: `JOB_STATE_SUCCEEDED`, `JOB_STATE_FAILED`, `JOB_STATE_CANCELLED`.
   *
   * @var string
   */
  public $endTime;
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';
  protected $explanationSpecType = GoogleCloudAiplatformV1ExplanationSpec::class;
  protected $explanationSpecDataType = '';
  /**
   * Generate explanation with the batch prediction results. When set to `true`,
   * the batch prediction output changes based on the `predictions_format` field
   * of the BatchPredictionJob.output_config object: * `bigquery`: output
   * includes a column named `explanation`. The value is a struct that conforms
   * to the Explanation object. * `jsonl`: The JSON objects on each line include
   * an additional entry keyed `explanation`. The value of the entry is a JSON
   * object that conforms to the Explanation object. * `csv`: Generating
   * explanations for CSV format is not supported. If this field is set to true,
   * either the Model.explanation_spec or explanation_spec must be populated.
   *
   * @var bool
   */
  public $generateExplanation;
  protected $inputConfigType = GoogleCloudAiplatformV1BatchPredictionJobInputConfig::class;
  protected $inputConfigDataType = '';
  protected $instanceConfigType = GoogleCloudAiplatformV1BatchPredictionJobInstanceConfig::class;
  protected $instanceConfigDataType = '';
  /**
   * The labels with user-defined metadata to organize BatchPredictionJobs.
   * Label keys and values can be no longer than 64 characters (Unicode
   * codepoints), can only contain lowercase letters, numeric characters,
   * underscores and dashes. International characters are allowed. See
   * https://goo.gl/xmQnxf for more information and examples of labels.
   *
   * @var string[]
   */
  public $labels;
  protected $manualBatchTuningParametersType = GoogleCloudAiplatformV1ManualBatchTuningParameters::class;
  protected $manualBatchTuningParametersDataType = '';
  /**
   * The name of the Model resource that produces the predictions via this job,
   * must share the same ancestor Location. Starting this job has no impact on
   * any existing deployments of the Model and their resources. Exactly one of
   * model, unmanaged_container_model, or endpoint must be set. The model
   * resource name may contain version id or version alias to specify the
   * version. Example:
   * `projects/{project}/locations/{location}/models/{model}@2` or
   * `projects/{project}/locations/{location}/models/{model}@golden` if no
   * version is specified, the default version will be deployed. The model
   * resource could also be a publisher model. Example:
   * `publishers/{publisher}/models/{model}` or `projects/{project}/locations/{l
   * ocation}/publishers/{publisher}/models/{model}`
   *
   * @var string
   */
  public $model;
  /**
   * The parameters that govern the predictions. The schema of the parameters
   * may be specified via the Model's PredictSchemata's parameters_schema_uri.
   *
   * @var array
   */
  public $modelParameters;
  /**
   * Output only. The version ID of the Model that produces the predictions via
   * this job.
   *
   * @var string
   */
  public $modelVersionId;
  /**
   * Output only. Resource name of the BatchPredictionJob.
   *
   * @var string
   */
  public $name;
  protected $outputConfigType = GoogleCloudAiplatformV1BatchPredictionJobOutputConfig::class;
  protected $outputConfigDataType = '';
  protected $outputInfoType = GoogleCloudAiplatformV1BatchPredictionJobOutputInfo::class;
  protected $outputInfoDataType = '';
  protected $partialFailuresType = GoogleRpcStatus::class;
  protected $partialFailuresDataType = 'array';
  protected $resourcesConsumedType = GoogleCloudAiplatformV1ResourcesConsumed::class;
  protected $resourcesConsumedDataType = '';
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
   * The service account that the DeployedModel's container runs as. If not
   * specified, a system generated one will be used, which has minimal
   * permissions and the custom container, if used, may not have enough
   * permission to access other Google Cloud resources. Users deploying the
   * Model must have the `iam.serviceAccounts.actAs` permission on this service
   * account.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Output only. Time when the BatchPredictionJob for the first time entered
   * the `JOB_STATE_RUNNING` state.
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
  protected $unmanagedContainerModelType = GoogleCloudAiplatformV1UnmanagedContainerModel::class;
  protected $unmanagedContainerModelDataType = '';
  /**
   * Output only. Time when the BatchPredictionJob was most recently updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Statistics on completed and failed prediction instances.
   *
   * @param GoogleCloudAiplatformV1CompletionStats $completionStats
   */
  public function setCompletionStats(GoogleCloudAiplatformV1CompletionStats $completionStats)
  {
    $this->completionStats = $completionStats;
  }
  /**
   * @return GoogleCloudAiplatformV1CompletionStats
   */
  public function getCompletionStats()
  {
    return $this->completionStats;
  }
  /**
   * Output only. Time when the BatchPredictionJob was created.
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
   * The config of resources used by the Model during the batch prediction. If
   * the Model supports DEDICATED_RESOURCES this config may be provided (and the
   * job will use these resources), if the Model doesn't support
   * AUTOMATIC_RESOURCES, this config must be provided.
   *
   * @param GoogleCloudAiplatformV1BatchDedicatedResources $dedicatedResources
   */
  public function setDedicatedResources(GoogleCloudAiplatformV1BatchDedicatedResources $dedicatedResources)
  {
    $this->dedicatedResources = $dedicatedResources;
  }
  /**
   * @return GoogleCloudAiplatformV1BatchDedicatedResources
   */
  public function getDedicatedResources()
  {
    return $this->dedicatedResources;
  }
  /**
   * For custom-trained Models and AutoML Tabular Models, the container of the
   * DeployedModel instances will send `stderr` and `stdout` streams to Cloud
   * Logging by default. Please note that the logs incur cost, which are subject
   * to [Cloud Logging pricing](https://cloud.google.com/logging/pricing). User
   * can disable container logging by setting this flag to true.
   *
   * @param bool $disableContainerLogging
   */
  public function setDisableContainerLogging($disableContainerLogging)
  {
    $this->disableContainerLogging = $disableContainerLogging;
  }
  /**
   * @return bool
   */
  public function getDisableContainerLogging()
  {
    return $this->disableContainerLogging;
  }
  /**
   * Required. The user-defined name of this BatchPredictionJob.
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
   * Customer-managed encryption key options for a BatchPredictionJob. If this
   * is set, then all resources created by the BatchPredictionJob will be
   * encrypted with the provided encryption key.
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
   * Output only. Time when the BatchPredictionJob entered any of the following
   * states: `JOB_STATE_SUCCEEDED`, `JOB_STATE_FAILED`, `JOB_STATE_CANCELLED`.
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
   * Output only. Only populated when the job's state is JOB_STATE_FAILED or
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
   * Explanation configuration for this BatchPredictionJob. Can be specified
   * only if generate_explanation is set to `true`. This value overrides the
   * value of Model.explanation_spec. All fields of explanation_spec are
   * optional in the request. If a field of the explanation_spec object is not
   * populated, the corresponding field of the Model.explanation_spec object is
   * inherited.
   *
   * @param GoogleCloudAiplatformV1ExplanationSpec $explanationSpec
   */
  public function setExplanationSpec(GoogleCloudAiplatformV1ExplanationSpec $explanationSpec)
  {
    $this->explanationSpec = $explanationSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1ExplanationSpec
   */
  public function getExplanationSpec()
  {
    return $this->explanationSpec;
  }
  /**
   * Generate explanation with the batch prediction results. When set to `true`,
   * the batch prediction output changes based on the `predictions_format` field
   * of the BatchPredictionJob.output_config object: * `bigquery`: output
   * includes a column named `explanation`. The value is a struct that conforms
   * to the Explanation object. * `jsonl`: The JSON objects on each line include
   * an additional entry keyed `explanation`. The value of the entry is a JSON
   * object that conforms to the Explanation object. * `csv`: Generating
   * explanations for CSV format is not supported. If this field is set to true,
   * either the Model.explanation_spec or explanation_spec must be populated.
   *
   * @param bool $generateExplanation
   */
  public function setGenerateExplanation($generateExplanation)
  {
    $this->generateExplanation = $generateExplanation;
  }
  /**
   * @return bool
   */
  public function getGenerateExplanation()
  {
    return $this->generateExplanation;
  }
  /**
   * Required. Input configuration of the instances on which predictions are
   * performed. The schema of any single instance may be specified via the
   * Model's PredictSchemata's instance_schema_uri.
   *
   * @param GoogleCloudAiplatformV1BatchPredictionJobInputConfig $inputConfig
   */
  public function setInputConfig(GoogleCloudAiplatformV1BatchPredictionJobInputConfig $inputConfig)
  {
    $this->inputConfig = $inputConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1BatchPredictionJobInputConfig
   */
  public function getInputConfig()
  {
    return $this->inputConfig;
  }
  /**
   * Configuration for how to convert batch prediction input instances to the
   * prediction instances that are sent to the Model.
   *
   * @param GoogleCloudAiplatformV1BatchPredictionJobInstanceConfig $instanceConfig
   */
  public function setInstanceConfig(GoogleCloudAiplatformV1BatchPredictionJobInstanceConfig $instanceConfig)
  {
    $this->instanceConfig = $instanceConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1BatchPredictionJobInstanceConfig
   */
  public function getInstanceConfig()
  {
    return $this->instanceConfig;
  }
  /**
   * The labels with user-defined metadata to organize BatchPredictionJobs.
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
   * Immutable. Parameters configuring the batch behavior. Currently only
   * applicable when dedicated_resources are used (in other cases Vertex AI does
   * the tuning itself).
   *
   * @param GoogleCloudAiplatformV1ManualBatchTuningParameters $manualBatchTuningParameters
   */
  public function setManualBatchTuningParameters(GoogleCloudAiplatformV1ManualBatchTuningParameters $manualBatchTuningParameters)
  {
    $this->manualBatchTuningParameters = $manualBatchTuningParameters;
  }
  /**
   * @return GoogleCloudAiplatformV1ManualBatchTuningParameters
   */
  public function getManualBatchTuningParameters()
  {
    return $this->manualBatchTuningParameters;
  }
  /**
   * The name of the Model resource that produces the predictions via this job,
   * must share the same ancestor Location. Starting this job has no impact on
   * any existing deployments of the Model and their resources. Exactly one of
   * model, unmanaged_container_model, or endpoint must be set. The model
   * resource name may contain version id or version alias to specify the
   * version. Example:
   * `projects/{project}/locations/{location}/models/{model}@2` or
   * `projects/{project}/locations/{location}/models/{model}@golden` if no
   * version is specified, the default version will be deployed. The model
   * resource could also be a publisher model. Example:
   * `publishers/{publisher}/models/{model}` or `projects/{project}/locations/{l
   * ocation}/publishers/{publisher}/models/{model}`
   *
   * @param string $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
  /**
   * The parameters that govern the predictions. The schema of the parameters
   * may be specified via the Model's PredictSchemata's parameters_schema_uri.
   *
   * @param array $modelParameters
   */
  public function setModelParameters($modelParameters)
  {
    $this->modelParameters = $modelParameters;
  }
  /**
   * @return array
   */
  public function getModelParameters()
  {
    return $this->modelParameters;
  }
  /**
   * Output only. The version ID of the Model that produces the predictions via
   * this job.
   *
   * @param string $modelVersionId
   */
  public function setModelVersionId($modelVersionId)
  {
    $this->modelVersionId = $modelVersionId;
  }
  /**
   * @return string
   */
  public function getModelVersionId()
  {
    return $this->modelVersionId;
  }
  /**
   * Output only. Resource name of the BatchPredictionJob.
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
   * Required. The Configuration specifying where output predictions should be
   * written. The schema of any single prediction may be specified as a
   * concatenation of Model's PredictSchemata's instance_schema_uri and
   * prediction_schema_uri.
   *
   * @param GoogleCloudAiplatformV1BatchPredictionJobOutputConfig $outputConfig
   */
  public function setOutputConfig(GoogleCloudAiplatformV1BatchPredictionJobOutputConfig $outputConfig)
  {
    $this->outputConfig = $outputConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1BatchPredictionJobOutputConfig
   */
  public function getOutputConfig()
  {
    return $this->outputConfig;
  }
  /**
   * Output only. Information further describing the output of this job.
   *
   * @param GoogleCloudAiplatformV1BatchPredictionJobOutputInfo $outputInfo
   */
  public function setOutputInfo(GoogleCloudAiplatformV1BatchPredictionJobOutputInfo $outputInfo)
  {
    $this->outputInfo = $outputInfo;
  }
  /**
   * @return GoogleCloudAiplatformV1BatchPredictionJobOutputInfo
   */
  public function getOutputInfo()
  {
    return $this->outputInfo;
  }
  /**
   * Output only. Partial failures encountered. For example, single files that
   * can't be read. This field never exceeds 20 entries. Status details fields
   * contain standard Google Cloud error details.
   *
   * @param GoogleRpcStatus[] $partialFailures
   */
  public function setPartialFailures($partialFailures)
  {
    $this->partialFailures = $partialFailures;
  }
  /**
   * @return GoogleRpcStatus[]
   */
  public function getPartialFailures()
  {
    return $this->partialFailures;
  }
  /**
   * Output only. Information about resources that had been consumed by this
   * job. Provided in real time at best effort basis, as well as a final value
   * once the job completes. Note: This field currently may be not populated for
   * batch predictions that use AutoML Models.
   *
   * @param GoogleCloudAiplatformV1ResourcesConsumed $resourcesConsumed
   */
  public function setResourcesConsumed(GoogleCloudAiplatformV1ResourcesConsumed $resourcesConsumed)
  {
    $this->resourcesConsumed = $resourcesConsumed;
  }
  /**
   * @return GoogleCloudAiplatformV1ResourcesConsumed
   */
  public function getResourcesConsumed()
  {
    return $this->resourcesConsumed;
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
   * The service account that the DeployedModel's container runs as. If not
   * specified, a system generated one will be used, which has minimal
   * permissions and the custom container, if used, may not have enough
   * permission to access other Google Cloud resources. Users deploying the
   * Model must have the `iam.serviceAccounts.actAs` permission on this service
   * account.
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
   * Output only. Time when the BatchPredictionJob for the first time entered
   * the `JOB_STATE_RUNNING` state.
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
   * Contains model information necessary to perform batch prediction without
   * requiring uploading to model registry. Exactly one of model,
   * unmanaged_container_model, or endpoint must be set.
   *
   * @param GoogleCloudAiplatformV1UnmanagedContainerModel $unmanagedContainerModel
   */
  public function setUnmanagedContainerModel(GoogleCloudAiplatformV1UnmanagedContainerModel $unmanagedContainerModel)
  {
    $this->unmanagedContainerModel = $unmanagedContainerModel;
  }
  /**
   * @return GoogleCloudAiplatformV1UnmanagedContainerModel
   */
  public function getUnmanagedContainerModel()
  {
    return $this->unmanagedContainerModel;
  }
  /**
   * Output only. Time when the BatchPredictionJob was most recently updated.
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
class_alias(GoogleCloudAiplatformV1BatchPredictionJob::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1BatchPredictionJob');
