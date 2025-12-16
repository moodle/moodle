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

class GoogleCloudDatapipelinesV1FlexTemplateRuntimeEnvironment extends \Google\Collection
{
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
  protected $collection_key = 'additionalExperiments';
  /**
   * Additional experiment flags for the job.
   *
   * @var string[]
   */
  public $additionalExperiments;
  /**
   * Additional user labels to be specified for the job. Keys and values must
   * follow the restrictions specified in the [labeling
   * restrictions](https://cloud.google.com/compute/docs/labeling-
   * resources#restrictions). An object containing a list of key/value pairs.
   * Example: `{ "name": "wrench", "mass": "1kg", "count": "3" }`.
   *
   * @var string[]
   */
  public $additionalUserLabels;
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
   * The machine type to use for the job. Defaults to the value from the
   * template if not specified.
   *
   * @var string
   */
  public $machineType;
  /**
   * The maximum number of Compute Engine instances to be made available to your
   * pipeline during execution, from 1 to 1000.
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
   * The initial number of Compute Engine instances for the job.
   *
   * @var int
   */
  public $numWorkers;
  /**
   * The email address of the service account to run the job as.
   *
   * @var string
   */
  public $serviceAccountEmail;
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
   * worker_zone is specified, defaults to the control plane region.
   *
   * @var string
   */
  public $workerRegion;
  /**
   * The Compute Engine zone (https://cloud.google.com/compute/docs/regions-
   * zones/regions-zones) in which worker processing should occur, e.g. "us-
   * west1-a". Mutually exclusive with worker_region. If neither worker_region
   * nor worker_zone is specified, a zone in the control plane region is chosen
   * based on available capacity. If both `worker_zone` and `zone` are set,
   * `worker_zone` takes precedence.
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
   * Additional user labels to be specified for the job. Keys and values must
   * follow the restrictions specified in the [labeling
   * restrictions](https://cloud.google.com/compute/docs/labeling-
   * resources#restrictions). An object containing a list of key/value pairs.
   * Example: `{ "name": "wrench", "mass": "1kg", "count": "3" }`.
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
   * The maximum number of Compute Engine instances to be made available to your
   * pipeline during execution, from 1 to 1000.
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
   * The initial number of Compute Engine instances for the job.
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
   * worker_zone is specified, defaults to the control plane region.
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
   * nor worker_zone is specified, a zone in the control plane region is chosen
   * based on available capacity. If both `worker_zone` and `zone` are set,
   * `worker_zone` takes precedence.
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
class_alias(GoogleCloudDatapipelinesV1FlexTemplateRuntimeEnvironment::class, 'Google_Service_Datapipelines_GoogleCloudDatapipelinesV1FlexTemplateRuntimeEnvironment');
