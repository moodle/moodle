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

class GoogleCloudAiplatformV1ModelDeploymentMonitoringJob extends \Google\Collection
{
  /**
   * Unspecified state.
   */
  public const SCHEDULE_STATE_MONITORING_SCHEDULE_STATE_UNSPECIFIED = 'MONITORING_SCHEDULE_STATE_UNSPECIFIED';
  /**
   * The pipeline is picked up and wait to run.
   */
  public const SCHEDULE_STATE_PENDING = 'PENDING';
  /**
   * The pipeline is offline and will be scheduled for next run.
   */
  public const SCHEDULE_STATE_OFFLINE = 'OFFLINE';
  /**
   * The pipeline is running.
   */
  public const SCHEDULE_STATE_RUNNING = 'RUNNING';
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
  protected $collection_key = 'modelDeploymentMonitoringObjectiveConfigs';
  /**
   * YAML schema file uri describing the format of a single instance that you
   * want Tensorflow Data Validation (TFDV) to analyze. If this field is empty,
   * all the feature data types are inferred from predict_instance_schema_uri,
   * meaning that TFDV will use the data in the exact format(data type) as
   * prediction request/response. If there are any data type differences between
   * predict instance and TFDV instance, this field can be used to override the
   * schema. For models trained with Vertex AI, this field must be set as all
   * the fields in predict instance formatted as string.
   *
   * @var string
   */
  public $analysisInstanceSchemaUri;
  protected $bigqueryTablesType = GoogleCloudAiplatformV1ModelDeploymentMonitoringBigQueryTable::class;
  protected $bigqueryTablesDataType = 'array';
  /**
   * Output only. Timestamp when this ModelDeploymentMonitoringJob was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The user-defined name of the ModelDeploymentMonitoringJob. The
   * name can be up to 128 characters long and can consist of any UTF-8
   * characters. Display name of a ModelDeploymentMonitoringJob.
   *
   * @var string
   */
  public $displayName;
  /**
   * If true, the scheduled monitoring pipeline logs are sent to Google Cloud
   * Logging, including pipeline status and anomalies detected. Please note the
   * logs incur cost, which are subject to [Cloud Logging
   * pricing](https://cloud.google.com/logging#pricing).
   *
   * @var bool
   */
  public $enableMonitoringPipelineLogs;
  protected $encryptionSpecType = GoogleCloudAiplatformV1EncryptionSpec::class;
  protected $encryptionSpecDataType = '';
  /**
   * Required. Endpoint resource name. Format:
   * `projects/{project}/locations/{location}/endpoints/{endpoint}`
   *
   * @var string
   */
  public $endpoint;
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';
  /**
   * The labels with user-defined metadata to organize your
   * ModelDeploymentMonitoringJob. Label keys and values can be no longer than
   * 64 characters (Unicode codepoints), can only contain lowercase letters,
   * numeric characters, underscores and dashes. International characters are
   * allowed. See https://goo.gl/xmQnxf for more information and examples of
   * labels.
   *
   * @var string[]
   */
  public $labels;
  protected $latestMonitoringPipelineMetadataType = GoogleCloudAiplatformV1ModelDeploymentMonitoringJobLatestMonitoringPipelineMetadata::class;
  protected $latestMonitoringPipelineMetadataDataType = '';
  /**
   * The TTL of BigQuery tables in user projects which stores logs. A day is the
   * basic unit of the TTL and we take the ceil of TTL/86400(a day). e.g. {
   * second: 3600} indicates ttl = 1 day.
   *
   * @var string
   */
  public $logTtl;
  protected $loggingSamplingStrategyType = GoogleCloudAiplatformV1SamplingStrategy::class;
  protected $loggingSamplingStrategyDataType = '';
  protected $modelDeploymentMonitoringObjectiveConfigsType = GoogleCloudAiplatformV1ModelDeploymentMonitoringObjectiveConfig::class;
  protected $modelDeploymentMonitoringObjectiveConfigsDataType = 'array';
  protected $modelDeploymentMonitoringScheduleConfigType = GoogleCloudAiplatformV1ModelDeploymentMonitoringScheduleConfig::class;
  protected $modelDeploymentMonitoringScheduleConfigDataType = '';
  protected $modelMonitoringAlertConfigType = GoogleCloudAiplatformV1ModelMonitoringAlertConfig::class;
  protected $modelMonitoringAlertConfigDataType = '';
  /**
   * Output only. Resource name of a ModelDeploymentMonitoringJob.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Timestamp when this monitoring pipeline will be scheduled to
   * run for the next round.
   *
   * @var string
   */
  public $nextScheduleTime;
  /**
   * YAML schema file uri describing the format of a single instance, which are
   * given to format this Endpoint's prediction (and explanation). If not set,
   * we will generate predict schema from collected predict requests.
   *
   * @var string
   */
  public $predictInstanceSchemaUri;
  /**
   * Sample Predict instance, same format as PredictRequest.instances, this can
   * be set as a replacement of
   * ModelDeploymentMonitoringJob.predict_instance_schema_uri. If not set, we
   * will generate predict schema from collected predict requests.
   *
   * @var array
   */
  public $samplePredictInstance;
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
   * Output only. Schedule state when the monitoring job is in Running state.
   *
   * @var string
   */
  public $scheduleState;
  /**
   * Output only. The detailed state of the monitoring job. When the job is
   * still creating, the state will be 'PENDING'. Once the job is successfully
   * created, the state will be 'RUNNING'. Pause the job, the state will be
   * 'PAUSED'. Resume the job, the state will return to 'RUNNING'.
   *
   * @var string
   */
  public $state;
  protected $statsAnomaliesBaseDirectoryType = GoogleCloudAiplatformV1GcsDestination::class;
  protected $statsAnomaliesBaseDirectoryDataType = '';
  /**
   * Output only. Timestamp when this ModelDeploymentMonitoringJob was updated
   * most recently.
   *
   * @var string
   */
  public $updateTime;

  /**
   * YAML schema file uri describing the format of a single instance that you
   * want Tensorflow Data Validation (TFDV) to analyze. If this field is empty,
   * all the feature data types are inferred from predict_instance_schema_uri,
   * meaning that TFDV will use the data in the exact format(data type) as
   * prediction request/response. If there are any data type differences between
   * predict instance and TFDV instance, this field can be used to override the
   * schema. For models trained with Vertex AI, this field must be set as all
   * the fields in predict instance formatted as string.
   *
   * @param string $analysisInstanceSchemaUri
   */
  public function setAnalysisInstanceSchemaUri($analysisInstanceSchemaUri)
  {
    $this->analysisInstanceSchemaUri = $analysisInstanceSchemaUri;
  }
  /**
   * @return string
   */
  public function getAnalysisInstanceSchemaUri()
  {
    return $this->analysisInstanceSchemaUri;
  }
  /**
   * Output only. The created bigquery tables for the job under customer
   * project. Customer could do their own query & analysis. There could be 4 log
   * tables in maximum: 1. Training data logging predict request/response 2.
   * Serving data logging predict request/response
   *
   * @param GoogleCloudAiplatformV1ModelDeploymentMonitoringBigQueryTable[] $bigqueryTables
   */
  public function setBigqueryTables($bigqueryTables)
  {
    $this->bigqueryTables = $bigqueryTables;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelDeploymentMonitoringBigQueryTable[]
   */
  public function getBigqueryTables()
  {
    return $this->bigqueryTables;
  }
  /**
   * Output only. Timestamp when this ModelDeploymentMonitoringJob was created.
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
   * Required. The user-defined name of the ModelDeploymentMonitoringJob. The
   * name can be up to 128 characters long and can consist of any UTF-8
   * characters. Display name of a ModelDeploymentMonitoringJob.
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
   * If true, the scheduled monitoring pipeline logs are sent to Google Cloud
   * Logging, including pipeline status and anomalies detected. Please note the
   * logs incur cost, which are subject to [Cloud Logging
   * pricing](https://cloud.google.com/logging#pricing).
   *
   * @param bool $enableMonitoringPipelineLogs
   */
  public function setEnableMonitoringPipelineLogs($enableMonitoringPipelineLogs)
  {
    $this->enableMonitoringPipelineLogs = $enableMonitoringPipelineLogs;
  }
  /**
   * @return bool
   */
  public function getEnableMonitoringPipelineLogs()
  {
    return $this->enableMonitoringPipelineLogs;
  }
  /**
   * Customer-managed encryption key spec for a ModelDeploymentMonitoringJob. If
   * set, this ModelDeploymentMonitoringJob and all sub-resources of this
   * ModelDeploymentMonitoringJob will be secured by this key.
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
   * Required. Endpoint resource name. Format:
   * `projects/{project}/locations/{location}/endpoints/{endpoint}`
   *
   * @param string $endpoint
   */
  public function setEndpoint($endpoint)
  {
    $this->endpoint = $endpoint;
  }
  /**
   * @return string
   */
  public function getEndpoint()
  {
    return $this->endpoint;
  }
  /**
   * Output only. Only populated when the job's state is `JOB_STATE_FAILED` or
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
   * The labels with user-defined metadata to organize your
   * ModelDeploymentMonitoringJob. Label keys and values can be no longer than
   * 64 characters (Unicode codepoints), can only contain lowercase letters,
   * numeric characters, underscores and dashes. International characters are
   * allowed. See https://goo.gl/xmQnxf for more information and examples of
   * labels.
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
   * Output only. Latest triggered monitoring pipeline metadata.
   *
   * @param GoogleCloudAiplatformV1ModelDeploymentMonitoringJobLatestMonitoringPipelineMetadata $latestMonitoringPipelineMetadata
   */
  public function setLatestMonitoringPipelineMetadata(GoogleCloudAiplatformV1ModelDeploymentMonitoringJobLatestMonitoringPipelineMetadata $latestMonitoringPipelineMetadata)
  {
    $this->latestMonitoringPipelineMetadata = $latestMonitoringPipelineMetadata;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelDeploymentMonitoringJobLatestMonitoringPipelineMetadata
   */
  public function getLatestMonitoringPipelineMetadata()
  {
    return $this->latestMonitoringPipelineMetadata;
  }
  /**
   * The TTL of BigQuery tables in user projects which stores logs. A day is the
   * basic unit of the TTL and we take the ceil of TTL/86400(a day). e.g. {
   * second: 3600} indicates ttl = 1 day.
   *
   * @param string $logTtl
   */
  public function setLogTtl($logTtl)
  {
    $this->logTtl = $logTtl;
  }
  /**
   * @return string
   */
  public function getLogTtl()
  {
    return $this->logTtl;
  }
  /**
   * Required. Sample Strategy for logging.
   *
   * @param GoogleCloudAiplatformV1SamplingStrategy $loggingSamplingStrategy
   */
  public function setLoggingSamplingStrategy(GoogleCloudAiplatformV1SamplingStrategy $loggingSamplingStrategy)
  {
    $this->loggingSamplingStrategy = $loggingSamplingStrategy;
  }
  /**
   * @return GoogleCloudAiplatformV1SamplingStrategy
   */
  public function getLoggingSamplingStrategy()
  {
    return $this->loggingSamplingStrategy;
  }
  /**
   * Required. The config for monitoring objectives. This is a per DeployedModel
   * config. Each DeployedModel needs to be configured separately.
   *
   * @param GoogleCloudAiplatformV1ModelDeploymentMonitoringObjectiveConfig[] $modelDeploymentMonitoringObjectiveConfigs
   */
  public function setModelDeploymentMonitoringObjectiveConfigs($modelDeploymentMonitoringObjectiveConfigs)
  {
    $this->modelDeploymentMonitoringObjectiveConfigs = $modelDeploymentMonitoringObjectiveConfigs;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelDeploymentMonitoringObjectiveConfig[]
   */
  public function getModelDeploymentMonitoringObjectiveConfigs()
  {
    return $this->modelDeploymentMonitoringObjectiveConfigs;
  }
  /**
   * Required. Schedule config for running the monitoring job.
   *
   * @param GoogleCloudAiplatformV1ModelDeploymentMonitoringScheduleConfig $modelDeploymentMonitoringScheduleConfig
   */
  public function setModelDeploymentMonitoringScheduleConfig(GoogleCloudAiplatformV1ModelDeploymentMonitoringScheduleConfig $modelDeploymentMonitoringScheduleConfig)
  {
    $this->modelDeploymentMonitoringScheduleConfig = $modelDeploymentMonitoringScheduleConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelDeploymentMonitoringScheduleConfig
   */
  public function getModelDeploymentMonitoringScheduleConfig()
  {
    return $this->modelDeploymentMonitoringScheduleConfig;
  }
  /**
   * Alert config for model monitoring.
   *
   * @param GoogleCloudAiplatformV1ModelMonitoringAlertConfig $modelMonitoringAlertConfig
   */
  public function setModelMonitoringAlertConfig(GoogleCloudAiplatformV1ModelMonitoringAlertConfig $modelMonitoringAlertConfig)
  {
    $this->modelMonitoringAlertConfig = $modelMonitoringAlertConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelMonitoringAlertConfig
   */
  public function getModelMonitoringAlertConfig()
  {
    return $this->modelMonitoringAlertConfig;
  }
  /**
   * Output only. Resource name of a ModelDeploymentMonitoringJob.
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
   * Output only. Timestamp when this monitoring pipeline will be scheduled to
   * run for the next round.
   *
   * @param string $nextScheduleTime
   */
  public function setNextScheduleTime($nextScheduleTime)
  {
    $this->nextScheduleTime = $nextScheduleTime;
  }
  /**
   * @return string
   */
  public function getNextScheduleTime()
  {
    return $this->nextScheduleTime;
  }
  /**
   * YAML schema file uri describing the format of a single instance, which are
   * given to format this Endpoint's prediction (and explanation). If not set,
   * we will generate predict schema from collected predict requests.
   *
   * @param string $predictInstanceSchemaUri
   */
  public function setPredictInstanceSchemaUri($predictInstanceSchemaUri)
  {
    $this->predictInstanceSchemaUri = $predictInstanceSchemaUri;
  }
  /**
   * @return string
   */
  public function getPredictInstanceSchemaUri()
  {
    return $this->predictInstanceSchemaUri;
  }
  /**
   * Sample Predict instance, same format as PredictRequest.instances, this can
   * be set as a replacement of
   * ModelDeploymentMonitoringJob.predict_instance_schema_uri. If not set, we
   * will generate predict schema from collected predict requests.
   *
   * @param array $samplePredictInstance
   */
  public function setSamplePredictInstance($samplePredictInstance)
  {
    $this->samplePredictInstance = $samplePredictInstance;
  }
  /**
   * @return array
   */
  public function getSamplePredictInstance()
  {
    return $this->samplePredictInstance;
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
   * Output only. Schedule state when the monitoring job is in Running state.
   *
   * Accepted values: MONITORING_SCHEDULE_STATE_UNSPECIFIED, PENDING, OFFLINE,
   * RUNNING
   *
   * @param self::SCHEDULE_STATE_* $scheduleState
   */
  public function setScheduleState($scheduleState)
  {
    $this->scheduleState = $scheduleState;
  }
  /**
   * @return self::SCHEDULE_STATE_*
   */
  public function getScheduleState()
  {
    return $this->scheduleState;
  }
  /**
   * Output only. The detailed state of the monitoring job. When the job is
   * still creating, the state will be 'PENDING'. Once the job is successfully
   * created, the state will be 'RUNNING'. Pause the job, the state will be
   * 'PAUSED'. Resume the job, the state will return to 'RUNNING'.
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
   * Stats anomalies base folder path.
   *
   * @param GoogleCloudAiplatformV1GcsDestination $statsAnomaliesBaseDirectory
   */
  public function setStatsAnomaliesBaseDirectory(GoogleCloudAiplatformV1GcsDestination $statsAnomaliesBaseDirectory)
  {
    $this->statsAnomaliesBaseDirectory = $statsAnomaliesBaseDirectory;
  }
  /**
   * @return GoogleCloudAiplatformV1GcsDestination
   */
  public function getStatsAnomaliesBaseDirectory()
  {
    return $this->statsAnomaliesBaseDirectory;
  }
  /**
   * Output only. Timestamp when this ModelDeploymentMonitoringJob was updated
   * most recently.
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
class_alias(GoogleCloudAiplatformV1ModelDeploymentMonitoringJob::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ModelDeploymentMonitoringJob');
