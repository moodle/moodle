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

class FlexTemplateRuntimeEnvironment extends \Google\Collection
{
  /**
   * The algorithm is unknown, or unspecified.
   */
  public const AUTOSCALING_ALGORITHM_AUTOSCALING_ALGORITHM_UNKNOWN = 'AUTOSCALING_ALGORITHM_UNKNOWN';
  /**
   * Disable autoscaling.
   */
  public const AUTOSCALING_ALGORITHM_AUTOSCALING_ALGORITHM_NONE = 'AUTOSCALING_ALGORITHM_NONE';
  /**
   * Increase worker count over time to reduce job execution time.
   */
  public const AUTOSCALING_ALGORITHM_AUTOSCALING_ALGORITHM_BASIC = 'AUTOSCALING_ALGORITHM_BASIC';
  /**
   * Run in the default mode.
   */
  public const FLEXRS_GOAL_FLEXRS_UNSPECIFIED = 'FLEXRS_UNSPECIFIED';
  /**
   * Optimize for lower execution time.
   */
  public const FLEXRS_GOAL_FLEXRS_SPEED_OPTIMIZED = 'FLEXRS_SPEED_OPTIMIZED';
  /**
   * Optimize for lower cost.
   */
  public const FLEXRS_GOAL_FLEXRS_COST_OPTIMIZED = 'FLEXRS_COST_OPTIMIZED';
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
   * Additional experiment flags for the job.
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
   * Additional user labels to be specified for the job. Keys and values must
   * follow the restrictions specified in the [labeling
   * restrictions](https://cloud.google.com/compute/docs/labeling-
   * resources#restrictions) page. An object containing a list of "key": value
   * pairs. Example: { "name": "wrench", "mass": "1kg", "count": "3" }.
   *
   * @var string[]
   */
  public $additionalUserLabels;
  /**
   * The algorithm to use for autoscaling
   *
   * @var string
   */
  public $autoscalingAlgorithm;
  /**
   * Worker disk size, in gigabytes.
   *
   * @var int
   */
  public $diskSizeGb;
  /**
   * If true, when processing time is spent almost entirely on garbage
   * collection (GC), saves a heap dump before ending the thread or process. If
   * false, ends the thread or process without saving a heap dump. Does not save
   * a heap dump when the Java Virtual Machine (JVM) has an out of memory error
   * during processing. The location of the heap file is either echoed back to
   * the user, or the user is given the opportunity to download the heap file.
   *
   * @var bool
   */
  public $dumpHeapOnOom;
  /**
   * If true serial port logging will be enabled for the launcher VM.
   *
   * @var bool
   */
  public $enableLauncherVmSerialPortLogging;
  /**
   * Whether to enable Streaming Engine for the job.
   *
   * @var bool
   */
  public $enableStreamingEngine;
  /**
   * Set FlexRS goal for the job.
   * https://cloud.google.com/dataflow/docs/guides/flexrs
   *
   * @var string
   */
  public $flexrsGoal;
  /**
   * Configuration for VM IPs.
   *
   * @var string
   */
  public $ipConfiguration;
  /**
   * Name for the Cloud KMS key for the job. Key format is:
   * projects//locations//keyRings//cryptoKeys/
   *
   * @var string
   */
  public $kmsKeyName;
  /**
   * The machine type to use for launching the job. The default is
   * n1-standard-1.
   *
   * @var string
   */
  public $launcherMachineType;
  /**
   * The machine type to use for the job. Defaults to the value from the
   * template if not specified.
   *
   * @var string
   */
  public $machineType;
  /**
   * The maximum number of Google Compute Engine instances to be made available
   * to your pipeline during execution, from 1 to 1000.
   *
   * @var int
   */
  public $maxWorkers;
  /**
   * Network to which VMs will be assigned. If empty or unspecified, the service
   * will use the network "default".
   *
   * @var string
   */
  public $network;
  /**
   * The initial number of Google Compute Engine instances for the job.
   *
   * @var int
   */
  public $numWorkers;
  /**
   * Cloud Storage bucket (directory) to upload heap dumps to. Enabling this
   * field implies that `dump_heap_on_oom` is set to true.
   *
   * @var string
   */
  public $saveHeapDumpsToGcsPath;
  /**
   * Docker registry location of container image to use for the 'worker harness.
   * Default is the container for the version of the SDK. Note this field is
   * only valid for portable pipelines.
   *
   * @var string
   */
  public $sdkContainerImage;
  /**
   * The email address of the service account to run the job as.
   *
   * @var string
   */
  public $serviceAccountEmail;
  /**
   * The Cloud Storage path for staging local files. Must be a valid Cloud
   * Storage URL, beginning with `gs://`.
   *
   * @var string
   */
  public $stagingLocation;
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
   * Subnetwork to which VMs will be assigned, if desired. You can specify a
   * subnetwork using either a complete URL or an abbreviated path. Expected to
   * be of the form "https://www.googleapis.com/compute/v1/projects/HOST_PROJECT
   * _ID/regions/REGION/subnetworks/SUBNETWORK" or
   * "regions/REGION/subnetworks/SUBNETWORK". If the subnetwork is located in a
   * Shared VPC network, you must use the complete URL.
   *
   * @var string
   */
  public $subnetwork;
  /**
   * The Cloud Storage path to use for temporary files. Must be a valid Cloud
   * Storage URL, beginning with `gs://`.
   *
   * @var string
   */
  public $tempLocation;
  /**
   * The Compute Engine region (https://cloud.google.com/compute/docs/regions-
   * zones/regions-zones) in which worker processing should occur, e.g. "us-
   * west1". Mutually exclusive with worker_zone. If neither worker_region nor
   * worker_zone is specified, default to the control plane's region.
   *
   * @var string
   */
  public $workerRegion;
  /**
   * The Compute Engine zone (https://cloud.google.com/compute/docs/regions-
   * zones/regions-zones) in which worker processing should occur, e.g. "us-
   * west1-a". Mutually exclusive with worker_region. If neither worker_region
   * nor worker_zone is specified, a zone in the control plane's region is
   * chosen based on available capacity. If both `worker_zone` and `zone` are
   * set, `worker_zone` takes precedence.
   *
   * @var string
   */
  public $workerZone;
  /**
   * The Compute Engine [availability
   * zone](https://cloud.google.com/compute/docs/regions-zones/regions-zones)
   * for launching worker instances to run your pipeline. In the future,
   * worker_zone will take precedence.
   *
   * @var string
   */
  public $zone;

  /**
   * Additional experiment flags for the job.
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
   * Additional user labels to be specified for the job. Keys and values must
   * follow the restrictions specified in the [labeling
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
   * The algorithm to use for autoscaling
   *
   * Accepted values: AUTOSCALING_ALGORITHM_UNKNOWN, AUTOSCALING_ALGORITHM_NONE,
   * AUTOSCALING_ALGORITHM_BASIC
   *
   * @param self::AUTOSCALING_ALGORITHM_* $autoscalingAlgorithm
   */
  public function setAutoscalingAlgorithm($autoscalingAlgorithm)
  {
    $this->autoscalingAlgorithm = $autoscalingAlgorithm;
  }
  /**
   * @return self::AUTOSCALING_ALGORITHM_*
   */
  public function getAutoscalingAlgorithm()
  {
    return $this->autoscalingAlgorithm;
  }
  /**
   * Worker disk size, in gigabytes.
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
   * If true, when processing time is spent almost entirely on garbage
   * collection (GC), saves a heap dump before ending the thread or process. If
   * false, ends the thread or process without saving a heap dump. Does not save
   * a heap dump when the Java Virtual Machine (JVM) has an out of memory error
   * during processing. The location of the heap file is either echoed back to
   * the user, or the user is given the opportunity to download the heap file.
   *
   * @param bool $dumpHeapOnOom
   */
  public function setDumpHeapOnOom($dumpHeapOnOom)
  {
    $this->dumpHeapOnOom = $dumpHeapOnOom;
  }
  /**
   * @return bool
   */
  public function getDumpHeapOnOom()
  {
    return $this->dumpHeapOnOom;
  }
  /**
   * If true serial port logging will be enabled for the launcher VM.
   *
   * @param bool $enableLauncherVmSerialPortLogging
   */
  public function setEnableLauncherVmSerialPortLogging($enableLauncherVmSerialPortLogging)
  {
    $this->enableLauncherVmSerialPortLogging = $enableLauncherVmSerialPortLogging;
  }
  /**
   * @return bool
   */
  public function getEnableLauncherVmSerialPortLogging()
  {
    return $this->enableLauncherVmSerialPortLogging;
  }
  /**
   * Whether to enable Streaming Engine for the job.
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
   * Set FlexRS goal for the job.
   * https://cloud.google.com/dataflow/docs/guides/flexrs
   *
   * Accepted values: FLEXRS_UNSPECIFIED, FLEXRS_SPEED_OPTIMIZED,
   * FLEXRS_COST_OPTIMIZED
   *
   * @param self::FLEXRS_GOAL_* $flexrsGoal
   */
  public function setFlexrsGoal($flexrsGoal)
  {
    $this->flexrsGoal = $flexrsGoal;
  }
  /**
   * @return self::FLEXRS_GOAL_*
   */
  public function getFlexrsGoal()
  {
    return $this->flexrsGoal;
  }
  /**
   * Configuration for VM IPs.
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
   * Name for the Cloud KMS key for the job. Key format is:
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
   * The machine type to use for launching the job. The default is
   * n1-standard-1.
   *
   * @param string $launcherMachineType
   */
  public function setLauncherMachineType($launcherMachineType)
  {
    $this->launcherMachineType = $launcherMachineType;
  }
  /**
   * @return string
   */
  public function getLauncherMachineType()
  {
    return $this->launcherMachineType;
  }
  /**
   * The machine type to use for the job. Defaults to the value from the
   * template if not specified.
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
   * The maximum number of Google Compute Engine instances to be made available
   * to your pipeline during execution, from 1 to 1000.
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
   * Network to which VMs will be assigned. If empty or unspecified, the service
   * will use the network "default".
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
   * The initial number of Google Compute Engine instances for the job.
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
   * Cloud Storage bucket (directory) to upload heap dumps to. Enabling this
   * field implies that `dump_heap_on_oom` is set to true.
   *
   * @param string $saveHeapDumpsToGcsPath
   */
  public function setSaveHeapDumpsToGcsPath($saveHeapDumpsToGcsPath)
  {
    $this->saveHeapDumpsToGcsPath = $saveHeapDumpsToGcsPath;
  }
  /**
   * @return string
   */
  public function getSaveHeapDumpsToGcsPath()
  {
    return $this->saveHeapDumpsToGcsPath;
  }
  /**
   * Docker registry location of container image to use for the 'worker harness.
   * Default is the container for the version of the SDK. Note this field is
   * only valid for portable pipelines.
   *
   * @param string $sdkContainerImage
   */
  public function setSdkContainerImage($sdkContainerImage)
  {
    $this->sdkContainerImage = $sdkContainerImage;
  }
  /**
   * @return string
   */
  public function getSdkContainerImage()
  {
    return $this->sdkContainerImage;
  }
  /**
   * The email address of the service account to run the job as.
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
   * The Cloud Storage path for staging local files. Must be a valid Cloud
   * Storage URL, beginning with `gs://`.
   *
   * @param string $stagingLocation
   */
  public function setStagingLocation($stagingLocation)
  {
    $this->stagingLocation = $stagingLocation;
  }
  /**
   * @return string
   */
  public function getStagingLocation()
  {
    return $this->stagingLocation;
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
   * Subnetwork to which VMs will be assigned, if desired. You can specify a
   * subnetwork using either a complete URL or an abbreviated path. Expected to
   * be of the form "https://www.googleapis.com/compute/v1/projects/HOST_PROJECT
   * _ID/regions/REGION/subnetworks/SUBNETWORK" or
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
   * The Cloud Storage path to use for temporary files. Must be a valid Cloud
   * Storage URL, beginning with `gs://`.
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
   * The Compute Engine region (https://cloud.google.com/compute/docs/regions-
   * zones/regions-zones) in which worker processing should occur, e.g. "us-
   * west1". Mutually exclusive with worker_zone. If neither worker_region nor
   * worker_zone is specified, default to the control plane's region.
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
   * The Compute Engine zone (https://cloud.google.com/compute/docs/regions-
   * zones/regions-zones) in which worker processing should occur, e.g. "us-
   * west1-a". Mutually exclusive with worker_region. If neither worker_region
   * nor worker_zone is specified, a zone in the control plane's region is
   * chosen based on available capacity. If both `worker_zone` and `zone` are
   * set, `worker_zone` takes precedence.
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
   * The Compute Engine [availability
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
class_alias(FlexTemplateRuntimeEnvironment::class, 'Google_Service_Dataflow_FlexTemplateRuntimeEnvironment');
