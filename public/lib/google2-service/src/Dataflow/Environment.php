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

class Environment extends \Google\Collection
{
  /**
   * Run in the default mode.
   */
  public const FLEX_RESOURCE_SCHEDULING_GOAL_FLEXRS_UNSPECIFIED = 'FLEXRS_UNSPECIFIED';
  /**
   * Optimize for lower execution time.
   */
  public const FLEX_RESOURCE_SCHEDULING_GOAL_FLEXRS_SPEED_OPTIMIZED = 'FLEXRS_SPEED_OPTIMIZED';
  /**
   * Optimize for lower cost.
   */
  public const FLEX_RESOURCE_SCHEDULING_GOAL_FLEXRS_COST_OPTIMIZED = 'FLEXRS_COST_OPTIMIZED';
  /**
   * Shuffle mode information is not available.
   */
  public const SHUFFLE_MODE_SHUFFLE_MODE_UNSPECIFIED = 'SHUFFLE_MODE_UNSPECIFIED';
  /**
   * Shuffle is done on the worker VMs.
   */
  public const SHUFFLE_MODE_VM_BASED = 'VM_BASED';
  /**
   * Shuffle is done on the service side.
   */
  public const SHUFFLE_MODE_SERVICE_BASED = 'SERVICE_BASED';
  /**
   * Run in the default mode.
   */
  public const STREAMING_MODE_STREAMING_MODE_UNSPECIFIED = 'STREAMING_MODE_UNSPECIFIED';
  /**
   * In this mode, message deduplication is performed against persistent state
   * to make sure each message is processed and committed to storage exactly
   * once.
   */
  public const STREAMING_MODE_STREAMING_MODE_EXACTLY_ONCE = 'STREAMING_MODE_EXACTLY_ONCE';
  /**
   * Message deduplication is not performed. Messages might be processed
   * multiple times, and the results are applied multiple times. Note: Setting
   * this value also enables Streaming Engine and Streaming Engine resource-
   * based billing.
   */
  public const STREAMING_MODE_STREAMING_MODE_AT_LEAST_ONCE = 'STREAMING_MODE_AT_LEAST_ONCE';
  protected $collection_key = 'workerPools';
  /**
   * The type of cluster manager API to use. If unknown or unspecified, the
   * service will attempt to choose a reasonable default. This should be in the
   * form of the API service name, e.g. "compute.googleapis.com".
   *
   * @var string
   */
  public $clusterManagerApiService;
  /**
   * Optional. The dataset for the current project where various workflow
   * related tables are stored. The supported resource type is: Google BigQuery:
   * bigquery.googleapis.com/{dataset}
   *
   * @var string
   */
  public $dataset;
  protected $debugOptionsType = DebugOptions::class;
  protected $debugOptionsDataType = '';
  /**
   * The list of experiments to enable. This field should be used for SDK
   * related experiments and not for service related experiments. The proper
   * field for service related experiments is service_options.
   *
   * @var string[]
   */
  public $experiments;
  /**
   * Optional. Which Flexible Resource Scheduling mode to run in.
   *
   * @var string
   */
  public $flexResourceSchedulingGoal;
  /**
   * Experimental settings.
   *
   * @var array[]
   */
  public $internalExperiments;
  /**
   * The Cloud Dataflow SDK pipeline options specified by the user. These
   * options are passed through the service and are used to recreate the SDK
   * pipeline options on the worker in a language agnostic and platform
   * independent way.
   *
   * @var array[]
   */
  public $sdkPipelineOptions;
  /**
   * Optional. Identity to run virtual machines as. Defaults to the default
   * account.
   *
   * @var string
   */
  public $serviceAccountEmail;
  /**
   * Optional. If set, contains the Cloud KMS key identifier used to encrypt
   * data at rest, AKA a Customer Managed Encryption Key (CMEK). Format:
   * projects/PROJECT_ID/locations/LOCATION/keyRings/KEY_RING/cryptoKeys/KEY
   *
   * @var string
   */
  public $serviceKmsKeyName;
  /**
   * Optional. The list of service options to enable. This field should be used
   * for service related experiments only. These experiments, when graduating to
   * GA, should be replaced by dedicated fields or become default (i.e. always
   * on).
   *
   * @var string[]
   */
  public $serviceOptions;
  /**
   * Output only. The shuffle mode used for the job.
   *
   * @var string
   */
  public $shuffleMode;
  /**
   * Optional. Specifies the Streaming Engine message processing guarantees.
   * Reduces cost and latency but might result in duplicate messages committed
   * to storage. Designed to run simple mapping streaming ETL jobs at the lowest
   * cost. For example, Change Data Capture (CDC) to BigQuery is a canonical use
   * case. For more information, see [Set the pipeline streaming
   * mode](https://cloud.google.com/dataflow/docs/guides/streaming-modes).
   *
   * @var string
   */
  public $streamingMode;
  /**
   * The prefix of the resources the system should use for temporary storage.
   * The system will append the suffix "/temp-{JOBNAME} to this resource prefix,
   * where {JOBNAME} is the value of the job_name field. The resulting bucket
   * and object prefix is used as the prefix of the resources used to store
   * temporary data needed during the job execution. NOTE: This will override
   * the value in taskrunner_settings. The supported resource type is: Google
   * Cloud Storage: storage.googleapis.com/{bucket}/{object}
   * bucket.storage.googleapis.com/{object}
   *
   * @var string
   */
  public $tempStoragePrefix;
  /**
   * Optional. True when any worker pool that uses public IPs is present.
   *
   * @var bool
   */
  public $usePublicIps;
  /**
   * Output only. Whether the job uses the Streaming Engine resource-based
   * billing model.
   *
   * @var bool
   */
  public $useStreamingEngineResourceBasedBilling;
  /**
   * Optional. A description of the process that generated the request.
   *
   * @var array[]
   */
  public $userAgent;
  /**
   * A structure describing which components and their versions of the service
   * are required in order to run the job.
   *
   * @var array[]
   */
  public $version;
  protected $workerPoolsType = WorkerPool::class;
  protected $workerPoolsDataType = 'array';
  /**
   * Optional. The Compute Engine region
   * (https://cloud.google.com/compute/docs/regions-zones/regions-zones) in
   * which worker processing should occur, e.g. "us-west1". Mutually exclusive
   * with worker_zone. If neither worker_region nor worker_zone is specified,
   * default to the control plane's region.
   *
   * @var string
   */
  public $workerRegion;
  /**
   * Optional. The Compute Engine zone
   * (https://cloud.google.com/compute/docs/regions-zones/regions-zones) in
   * which worker processing should occur, e.g. "us-west1-a". Mutually exclusive
   * with worker_region. If neither worker_region nor worker_zone is specified,
   * a zone in the control plane's region is chosen based on available capacity.
   *
   * @var string
   */
  public $workerZone;

  /**
   * The type of cluster manager API to use. If unknown or unspecified, the
   * service will attempt to choose a reasonable default. This should be in the
   * form of the API service name, e.g. "compute.googleapis.com".
   *
   * @param string $clusterManagerApiService
   */
  public function setClusterManagerApiService($clusterManagerApiService)
  {
    $this->clusterManagerApiService = $clusterManagerApiService;
  }
  /**
   * @return string
   */
  public function getClusterManagerApiService()
  {
    return $this->clusterManagerApiService;
  }
  /**
   * Optional. The dataset for the current project where various workflow
   * related tables are stored. The supported resource type is: Google BigQuery:
   * bigquery.googleapis.com/{dataset}
   *
   * @param string $dataset
   */
  public function setDataset($dataset)
  {
    $this->dataset = $dataset;
  }
  /**
   * @return string
   */
  public function getDataset()
  {
    return $this->dataset;
  }
  /**
   * Optional. Any debugging options to be supplied to the job.
   *
   * @param DebugOptions $debugOptions
   */
  public function setDebugOptions(DebugOptions $debugOptions)
  {
    $this->debugOptions = $debugOptions;
  }
  /**
   * @return DebugOptions
   */
  public function getDebugOptions()
  {
    return $this->debugOptions;
  }
  /**
   * The list of experiments to enable. This field should be used for SDK
   * related experiments and not for service related experiments. The proper
   * field for service related experiments is service_options.
   *
   * @param string[] $experiments
   */
  public function setExperiments($experiments)
  {
    $this->experiments = $experiments;
  }
  /**
   * @return string[]
   */
  public function getExperiments()
  {
    return $this->experiments;
  }
  /**
   * Optional. Which Flexible Resource Scheduling mode to run in.
   *
   * Accepted values: FLEXRS_UNSPECIFIED, FLEXRS_SPEED_OPTIMIZED,
   * FLEXRS_COST_OPTIMIZED
   *
   * @param self::FLEX_RESOURCE_SCHEDULING_GOAL_* $flexResourceSchedulingGoal
   */
  public function setFlexResourceSchedulingGoal($flexResourceSchedulingGoal)
  {
    $this->flexResourceSchedulingGoal = $flexResourceSchedulingGoal;
  }
  /**
   * @return self::FLEX_RESOURCE_SCHEDULING_GOAL_*
   */
  public function getFlexResourceSchedulingGoal()
  {
    return $this->flexResourceSchedulingGoal;
  }
  /**
   * Experimental settings.
   *
   * @param array[] $internalExperiments
   */
  public function setInternalExperiments($internalExperiments)
  {
    $this->internalExperiments = $internalExperiments;
  }
  /**
   * @return array[]
   */
  public function getInternalExperiments()
  {
    return $this->internalExperiments;
  }
  /**
   * The Cloud Dataflow SDK pipeline options specified by the user. These
   * options are passed through the service and are used to recreate the SDK
   * pipeline options on the worker in a language agnostic and platform
   * independent way.
   *
   * @param array[] $sdkPipelineOptions
   */
  public function setSdkPipelineOptions($sdkPipelineOptions)
  {
    $this->sdkPipelineOptions = $sdkPipelineOptions;
  }
  /**
   * @return array[]
   */
  public function getSdkPipelineOptions()
  {
    return $this->sdkPipelineOptions;
  }
  /**
   * Optional. Identity to run virtual machines as. Defaults to the default
   * account.
   *
   * @param string $serviceAccountEmail
   */
  public function setServiceAccountEmail($serviceAccountEmail)
  {
    $this->serviceAccountEmail = $serviceAccountEmail;
  }
  /**
   * @return string
   */
  public function getServiceAccountEmail()
  {
    return $this->serviceAccountEmail;
  }
  /**
   * Optional. If set, contains the Cloud KMS key identifier used to encrypt
   * data at rest, AKA a Customer Managed Encryption Key (CMEK). Format:
   * projects/PROJECT_ID/locations/LOCATION/keyRings/KEY_RING/cryptoKeys/KEY
   *
   * @param string $serviceKmsKeyName
   */
  public function setServiceKmsKeyName($serviceKmsKeyName)
  {
    $this->serviceKmsKeyName = $serviceKmsKeyName;
  }
  /**
   * @return string
   */
  public function getServiceKmsKeyName()
  {
    return $this->serviceKmsKeyName;
  }
  /**
   * Optional. The list of service options to enable. This field should be used
   * for service related experiments only. These experiments, when graduating to
   * GA, should be replaced by dedicated fields or become default (i.e. always
   * on).
   *
   * @param string[] $serviceOptions
   */
  public function setServiceOptions($serviceOptions)
  {
    $this->serviceOptions = $serviceOptions;
  }
  /**
   * @return string[]
   */
  public function getServiceOptions()
  {
    return $this->serviceOptions;
  }
  /**
   * Output only. The shuffle mode used for the job.
   *
   * Accepted values: SHUFFLE_MODE_UNSPECIFIED, VM_BASED, SERVICE_BASED
   *
   * @param self::SHUFFLE_MODE_* $shuffleMode
   */
  public function setShuffleMode($shuffleMode)
  {
    $this->shuffleMode = $shuffleMode;
  }
  /**
   * @return self::SHUFFLE_MODE_*
   */
  public function getShuffleMode()
  {
    return $this->shuffleMode;
  }
  /**
   * Optional. Specifies the Streaming Engine message processing guarantees.
   * Reduces cost and latency but might result in duplicate messages committed
   * to storage. Designed to run simple mapping streaming ETL jobs at the lowest
   * cost. For example, Change Data Capture (CDC) to BigQuery is a canonical use
   * case. For more information, see [Set the pipeline streaming
   * mode](https://cloud.google.com/dataflow/docs/guides/streaming-modes).
   *
   * Accepted values: STREAMING_MODE_UNSPECIFIED, STREAMING_MODE_EXACTLY_ONCE,
   * STREAMING_MODE_AT_LEAST_ONCE
   *
   * @param self::STREAMING_MODE_* $streamingMode
   */
  public function setStreamingMode($streamingMode)
  {
    $this->streamingMode = $streamingMode;
  }
  /**
   * @return self::STREAMING_MODE_*
   */
  public function getStreamingMode()
  {
    return $this->streamingMode;
  }
  /**
   * The prefix of the resources the system should use for temporary storage.
   * The system will append the suffix "/temp-{JOBNAME} to this resource prefix,
   * where {JOBNAME} is the value of the job_name field. The resulting bucket
   * and object prefix is used as the prefix of the resources used to store
   * temporary data needed during the job execution. NOTE: This will override
   * the value in taskrunner_settings. The supported resource type is: Google
   * Cloud Storage: storage.googleapis.com/{bucket}/{object}
   * bucket.storage.googleapis.com/{object}
   *
   * @param string $tempStoragePrefix
   */
  public function setTempStoragePrefix($tempStoragePrefix)
  {
    $this->tempStoragePrefix = $tempStoragePrefix;
  }
  /**
   * @return string
   */
  public function getTempStoragePrefix()
  {
    return $this->tempStoragePrefix;
  }
  /**
   * Optional. True when any worker pool that uses public IPs is present.
   *
   * @param bool $usePublicIps
   */
  public function setUsePublicIps($usePublicIps)
  {
    $this->usePublicIps = $usePublicIps;
  }
  /**
   * @return bool
   */
  public function getUsePublicIps()
  {
    return $this->usePublicIps;
  }
  /**
   * Output only. Whether the job uses the Streaming Engine resource-based
   * billing model.
   *
   * @param bool $useStreamingEngineResourceBasedBilling
   */
  public function setUseStreamingEngineResourceBasedBilling($useStreamingEngineResourceBasedBilling)
  {
    $this->useStreamingEngineResourceBasedBilling = $useStreamingEngineResourceBasedBilling;
  }
  /**
   * @return bool
   */
  public function getUseStreamingEngineResourceBasedBilling()
  {
    return $this->useStreamingEngineResourceBasedBilling;
  }
  /**
   * Optional. A description of the process that generated the request.
   *
   * @param array[] $userAgent
   */
  public function setUserAgent($userAgent)
  {
    $this->userAgent = $userAgent;
  }
  /**
   * @return array[]
   */
  public function getUserAgent()
  {
    return $this->userAgent;
  }
  /**
   * A structure describing which components and their versions of the service
   * are required in order to run the job.
   *
   * @param array[] $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return array[]
   */
  public function getVersion()
  {
    return $this->version;
  }
  /**
   * The worker pools. At least one "harness" worker pool must be specified in
   * order for the job to have workers.
   *
   * @param WorkerPool[] $workerPools
   */
  public function setWorkerPools($workerPools)
  {
    $this->workerPools = $workerPools;
  }
  /**
   * @return WorkerPool[]
   */
  public function getWorkerPools()
  {
    return $this->workerPools;
  }
  /**
   * Optional. The Compute Engine region
   * (https://cloud.google.com/compute/docs/regions-zones/regions-zones) in
   * which worker processing should occur, e.g. "us-west1". Mutually exclusive
   * with worker_zone. If neither worker_region nor worker_zone is specified,
   * default to the control plane's region.
   *
   * @param string $workerRegion
   */
  public function setWorkerRegion($workerRegion)
  {
    $this->workerRegion = $workerRegion;
  }
  /**
   * @return string
   */
  public function getWorkerRegion()
  {
    return $this->workerRegion;
  }
  /**
   * Optional. The Compute Engine zone
   * (https://cloud.google.com/compute/docs/regions-zones/regions-zones) in
   * which worker processing should occur, e.g. "us-west1-a". Mutually exclusive
   * with worker_region. If neither worker_region nor worker_zone is specified,
   * a zone in the control plane's region is chosen based on available capacity.
   *
   * @param string $workerZone
   */
  public function setWorkerZone($workerZone)
  {
    $this->workerZone = $workerZone;
  }
  /**
   * @return string
   */
  public function getWorkerZone()
  {
    return $this->workerZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Environment::class, 'Google_Service_Dataflow_Environment');
