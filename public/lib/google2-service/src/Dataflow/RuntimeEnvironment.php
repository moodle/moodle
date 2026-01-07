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

class RuntimeEnvironment extends \Google\Collection
{
  /**
   * The configuration is unknown, or unspecified.
   */
  public const IP_CONFIGURATION_WORKER_IP_UNSPECIFIED = 'WORKER_IP_UNSPECIFIED';
  /**
   * Workers should have public IP addresses.
   */
  public const IP_CONFIGURATION_WORKER_IP_PUBLIC = 'WORKER_IP_PUBLIC';
  /**
   * Workers should have private IP addresses.
   */
  public const IP_CONFIGURATION_WORKER_IP_PRIVATE = 'WORKER_IP_PRIVATE';
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
  protected $collection_key = 'additionalPipelineOptions';
  /**
   * Optional. Additional experiment flags for the job, specified with the
   * `--experiments` option.
   *
   * @var string[]
   */
  public $additionalExperiments;
  /**
   * Optional. Additional pipeline option flags for the job.
   *
   * @var string[]
   */
  public $additionalPipelineOptions;
  /**
   * Optional. Additional user labels to be specified for the job. Keys and
   * values should follow the restrictions specified in the [labeling
   * restrictions](https://cloud.google.com/compute/docs/labeling-
   * resources#restrictions) page. An object containing a list of "key": value
   * pairs. Example: { "name": "wrench", "mass": "1kg", "count": "3" }.
   *
   * @var string[]
   */
  public $additionalUserLabels;
  /**
   * Optional. Whether to bypass the safety checks for the job's temporary
   * directory. Use with caution.
   *
   * @var bool
   */
  public $bypassTempDirValidation;
  /**
   * Optional. The disk size, in gigabytes, to use on each remote Compute Engine
   * worker instance.
   *
   * @var int
   */
  public $diskSizeGb;
  /**
   * Optional. Whether to enable Streaming Engine for the job.
   *
   * @var bool
   */
  public $enableStreamingEngine;
  /**
   * Optional. Configuration for VM IPs.
   *
   * @var string
   */
  public $ipConfiguration;
  /**
   * Optional. Name for the Cloud KMS key for the job. Key format is:
   * projects//locations//keyRings//cryptoKeys/
   *
   * @var string
   */
  public $kmsKeyName;
  /**
   * Optional. The machine type to use for the job. Defaults to the value from
   * the template if not specified.
   *
   * @var string
   */
  public $machineType;
  /**
   * Optional. The maximum number of Google Compute Engine instances to be made
   * available to your pipeline during execution, from 1 to 1000. The default
   * value is 1.
   *
   * @var int
   */
  public $maxWorkers;
  /**
   * Optional. Network to which VMs will be assigned. If empty or unspecified,
   * the service will use the network "default".
   *
   * @var string
   */
  public $network;
  /**
   * Optional. The initial number of Google Compute Engine instances for the
   * job. The default value is 11.
   *
   * @var int
   */
  public $numWorkers;
  /**
   * Optional. The email address of the service account to run the job as.
   *
   * @var string
   */
  public $serviceAccountEmail;
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
   * Optional. Subnetwork to which VMs will be assigned, if desired. You can
   * specify a subnetwork using either a complete URL or an abbreviated path.
   * Expected to be of the form "https://www.googleapis.com/compute/v1/projects/
   * HOST_PROJECT_ID/regions/REGION/subnetworks/SUBNETWORK" or
   * "regions/REGION/subnetworks/SUBNETWORK". If the subnetwork is located in a
   * Shared VPC network, you must use the complete URL.
   *
   * @var string
   */
  public $subnetwork;
  /**
   * Required. The Cloud Storage path to use for temporary files. Must be a
   * valid Cloud Storage URL, beginning with `gs://`.
   *
   * @var string
   */
  public $tempLocation;
  /**
   * Required. The Compute Engine region
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
   * If both `worker_zone` and `zone` are set, `worker_zone` takes precedence.
   *
   * @var string
   */
  public $workerZone;
  /**
   * Optional. The Compute Engine [availability
   * zone](https://cloud.google.com/compute/docs/regions-zones/regions-zones)
   * for launching worker instances to run your pipeline. In the future,
   * worker_zone will take precedence.
   *
   * @var string
   */
  public $zone;

  /**
   * Optional. Additional experiment flags for the job, specified with the
   * `--experiments` option.
   *
   * @param string[] $additionalExperiments
   */
  public function setAdditionalExperiments($additionalExperiments)
  {
    $this->additionalExperiments = $additionalExperiments;
  }
  /**
   * @return string[]
   */
  public function getAdditionalExperiments()
  {
    return $this->additionalExperiments;
  }
  /**
   * Optional. Additional pipeline option flags for the job.
   *
   * @param string[] $additionalPipelineOptions
   */
  public function setAdditionalPipelineOptions($additionalPipelineOptions)
  {
    $this->additionalPipelineOptions = $additionalPipelineOptions;
  }
  /**
   * @return string[]
   */
  public function getAdditionalPipelineOptions()
  {
    return $this->additionalPipelineOptions;
  }
  /**
   * Optional. Additional user labels to be specified for the job. Keys and
   * values should follow the restrictions specified in the [labeling
   * restrictions](https://cloud.google.com/compute/docs/labeling-
   * resources#restrictions) page. An object containing a list of "key": value
   * pairs. Example: { "name": "wrench", "mass": "1kg", "count": "3" }.
   *
   * @param string[] $additionalUserLabels
   */
  public function setAdditionalUserLabels($additionalUserLabels)
  {
    $this->additionalUserLabels = $additionalUserLabels;
  }
  /**
   * @return string[]
   */
  public function getAdditionalUserLabels()
  {
    return $this->additionalUserLabels;
  }
  /**
   * Optional. Whether to bypass the safety checks for the job's temporary
   * directory. Use with caution.
   *
   * @param bool $bypassTempDirValidation
   */
  public function setBypassTempDirValidation($bypassTempDirValidation)
  {
    $this->bypassTempDirValidation = $bypassTempDirValidation;
  }
  /**
   * @return bool
   */
  public function getBypassTempDirValidation()
  {
    return $this->bypassTempDirValidation;
  }
  /**
   * Optional. The disk size, in gigabytes, to use on each remote Compute Engine
   * worker instance.
   *
   * @param int $diskSizeGb
   */
  public function setDiskSizeGb($diskSizeGb)
  {
    $this->diskSizeGb = $diskSizeGb;
  }
  /**
   * @return int
   */
  public function getDiskSizeGb()
  {
    return $this->diskSizeGb;
  }
  /**
   * Optional. Whether to enable Streaming Engine for the job.
   *
   * @param bool $enableStreamingEngine
   */
  public function setEnableStreamingEngine($enableStreamingEngine)
  {
    $this->enableStreamingEngine = $enableStreamingEngine;
  }
  /**
   * @return bool
   */
  public function getEnableStreamingEngine()
  {
    return $this->enableStreamingEngine;
  }
  /**
   * Optional. Configuration for VM IPs.
   *
   * Accepted values: WORKER_IP_UNSPECIFIED, WORKER_IP_PUBLIC, WORKER_IP_PRIVATE
   *
   * @param self::IP_CONFIGURATION_* $ipConfiguration
   */
  public function setIpConfiguration($ipConfiguration)
  {
    $this->ipConfiguration = $ipConfiguration;
  }
  /**
   * @return self::IP_CONFIGURATION_*
   */
  public function getIpConfiguration()
  {
    return $this->ipConfiguration;
  }
  /**
   * Optional. Name for the Cloud KMS key for the job. Key format is:
   * projects//locations//keyRings//cryptoKeys/
   *
   * @param string $kmsKeyName
   */
  public function setKmsKeyName($kmsKeyName)
  {
    $this->kmsKeyName = $kmsKeyName;
  }
  /**
   * @return string
   */
  public function getKmsKeyName()
  {
    return $this->kmsKeyName;
  }
  /**
   * Optional. The machine type to use for the job. Defaults to the value from
   * the template if not specified.
   *
   * @param string $machineType
   */
  public function setMachineType($machineType)
  {
    $this->machineType = $machineType;
  }
  /**
   * @return string
   */
  public function getMachineType()
  {
    return $this->machineType;
  }
  /**
   * Optional. The maximum number of Google Compute Engine instances to be made
   * available to your pipeline during execution, from 1 to 1000. The default
   * value is 1.
   *
   * @param int $maxWorkers
   */
  public function setMaxWorkers($maxWorkers)
  {
    $this->maxWorkers = $maxWorkers;
  }
  /**
   * @return int
   */
  public function getMaxWorkers()
  {
    return $this->maxWorkers;
  }
  /**
   * Optional. Network to which VMs will be assigned. If empty or unspecified,
   * the service will use the network "default".
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Optional. The initial number of Google Compute Engine instances for the
   * job. The default value is 11.
   *
   * @param int $numWorkers
   */
  public function setNumWorkers($numWorkers)
  {
    $this->numWorkers = $numWorkers;
  }
  /**
   * @return int
   */
  public function getNumWorkers()
  {
    return $this->numWorkers;
  }
  /**
   * Optional. The email address of the service account to run the job as.
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
   * Optional. Subnetwork to which VMs will be assigned, if desired. You can
   * specify a subnetwork using either a complete URL or an abbreviated path.
   * Expected to be of the form "https://www.googleapis.com/compute/v1/projects/
   * HOST_PROJECT_ID/regions/REGION/subnetworks/SUBNETWORK" or
   * "regions/REGION/subnetworks/SUBNETWORK". If the subnetwork is located in a
   * Shared VPC network, you must use the complete URL.
   *
   * @param string $subnetwork
   */
  public function setSubnetwork($subnetwork)
  {
    $this->subnetwork = $subnetwork;
  }
  /**
   * @return string
   */
  public function getSubnetwork()
  {
    return $this->subnetwork;
  }
  /**
   * Required. The Cloud Storage path to use for temporary files. Must be a
   * valid Cloud Storage URL, beginning with `gs://`.
   *
   * @param string $tempLocation
   */
  public function setTempLocation($tempLocation)
  {
    $this->tempLocation = $tempLocation;
  }
  /**
   * @return string
   */
  public function getTempLocation()
  {
    return $this->tempLocation;
  }
  /**
   * Required. The Compute Engine region
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
   * If both `worker_zone` and `zone` are set, `worker_zone` takes precedence.
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
  /**
   * Optional. The Compute Engine [availability
   * zone](https://cloud.google.com/compute/docs/regions-zones/regions-zones)
   * for launching worker instances to run your pipeline. In the future,
   * worker_zone will take precedence.
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RuntimeEnvironment::class, 'Google_Service_Dataflow_RuntimeEnvironment');
