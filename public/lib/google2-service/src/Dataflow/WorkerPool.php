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

class WorkerPool extends \Google\Collection
{
  /**
   * The default set of packages to stage is unknown, or unspecified.
   */
  public const DEFAULT_PACKAGE_SET_DEFAULT_PACKAGE_SET_UNKNOWN = 'DEFAULT_PACKAGE_SET_UNKNOWN';
  /**
   * Indicates that no packages should be staged at the worker unless explicitly
   * specified by the job.
   */
  public const DEFAULT_PACKAGE_SET_DEFAULT_PACKAGE_SET_NONE = 'DEFAULT_PACKAGE_SET_NONE';
  /**
   * Stage packages typically useful to workers written in Java.
   */
  public const DEFAULT_PACKAGE_SET_DEFAULT_PACKAGE_SET_JAVA = 'DEFAULT_PACKAGE_SET_JAVA';
  /**
   * Stage packages typically useful to workers written in Python.
   */
  public const DEFAULT_PACKAGE_SET_DEFAULT_PACKAGE_SET_PYTHON = 'DEFAULT_PACKAGE_SET_PYTHON';
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
   * The teardown policy isn't specified, or is unknown.
   */
  public const TEARDOWN_POLICY_TEARDOWN_POLICY_UNKNOWN = 'TEARDOWN_POLICY_UNKNOWN';
  /**
   * Always teardown the resource.
   */
  public const TEARDOWN_POLICY_TEARDOWN_ALWAYS = 'TEARDOWN_ALWAYS';
  /**
   * Teardown the resource on success. This is useful for debugging failures.
   */
  public const TEARDOWN_POLICY_TEARDOWN_ON_SUCCESS = 'TEARDOWN_ON_SUCCESS';
  /**
   * Never teardown the resource. This is useful for debugging and development.
   */
  public const TEARDOWN_POLICY_TEARDOWN_NEVER = 'TEARDOWN_NEVER';
  protected $collection_key = 'sdkHarnessContainerImages';
  protected $autoscalingSettingsType = AutoscalingSettings::class;
  protected $autoscalingSettingsDataType = '';
  protected $dataDisksType = Disk::class;
  protected $dataDisksDataType = 'array';
  /**
   * The default package set to install. This allows the service to select a
   * default set of packages which are useful to worker harnesses written in a
   * particular language.
   *
   * @var string
   */
  public $defaultPackageSet;
  /**
   * Size of root disk for VMs, in GB. If zero or unspecified, the service will
   * attempt to choose a reasonable default.
   *
   * @var int
   */
  public $diskSizeGb;
  /**
   * Fully qualified source image for disks.
   *
   * @var string
   */
  public $diskSourceImage;
  /**
   * Type of root disk for VMs. If empty or unspecified, the service will
   * attempt to choose a reasonable default.
   *
   * @var string
   */
  public $diskType;
  /**
   * Configuration for VM IPs.
   *
   * @var string
   */
  public $ipConfiguration;
  /**
   * The kind of the worker pool; currently only `harness` and `shuffle` are
   * supported.
   *
   * @var string
   */
  public $kind;
  /**
   * Machine type (e.g. "n1-standard-1"). If empty or unspecified, the service
   * will attempt to choose a reasonable default.
   *
   * @var string
   */
  public $machineType;
  /**
   * Metadata to set on the Google Compute Engine VMs.
   *
   * @var string[]
   */
  public $metadata;
  /**
   * Network to which VMs will be assigned. If empty or unspecified, the service
   * will use the network "default".
   *
   * @var string
   */
  public $network;
  /**
   * The number of threads per worker harness. If empty or unspecified, the
   * service will choose a number of threads (according to the number of cores
   * on the selected machine type for batch, or 1 by convention for streaming).
   *
   * @var int
   */
  public $numThreadsPerWorker;
  /**
   * Number of Google Compute Engine workers in this pool needed to execute the
   * job. If zero or unspecified, the service will attempt to choose a
   * reasonable default.
   *
   * @var int
   */
  public $numWorkers;
  /**
   * The action to take on host maintenance, as defined by the Google Compute
   * Engine API.
   *
   * @var string
   */
  public $onHostMaintenance;
  protected $packagesType = Package::class;
  protected $packagesDataType = 'array';
  /**
   * Extra arguments for this worker pool.
   *
   * @var array[]
   */
  public $poolArgs;
  protected $sdkHarnessContainerImagesType = SdkHarnessContainerImage::class;
  protected $sdkHarnessContainerImagesDataType = 'array';
  /**
   * Subnetwork to which VMs will be assigned, if desired. Expected to be of the
   * form "regions/REGION/subnetworks/SUBNETWORK".
   *
   * @var string
   */
  public $subnetwork;
  protected $taskrunnerSettingsType = TaskRunnerSettings::class;
  protected $taskrunnerSettingsDataType = '';
  /**
   * Sets the policy for determining when to turndown worker pool. Allowed
   * values are: `TEARDOWN_ALWAYS`, `TEARDOWN_ON_SUCCESS`, and `TEARDOWN_NEVER`.
   * `TEARDOWN_ALWAYS` means workers are always torn down regardless of whether
   * the job succeeds. `TEARDOWN_ON_SUCCESS` means workers are torn down if the
   * job succeeds. `TEARDOWN_NEVER` means the workers are never torn down. If
   * the workers are not torn down by the service, they will continue to run and
   * use Google Compute Engine VM resources in the user's project until they are
   * explicitly terminated by the user. Because of this, Google recommends using
   * the `TEARDOWN_ALWAYS` policy except for small, manually supervised test
   * jobs. If unknown or unspecified, the service will attempt to choose a
   * reasonable default.
   *
   * @var string
   */
  public $teardownPolicy;
  /**
   * Required. Docker container image that executes the Cloud Dataflow worker
   * harness, residing in Google Container Registry. Deprecated for the Fn API
   * path. Use sdk_harness_container_images instead.
   *
   * @var string
   */
  public $workerHarnessContainerImage;
  /**
   * Zone to run the worker pools in. If empty or unspecified, the service will
   * attempt to choose a reasonable default.
   *
   * @var string
   */
  public $zone;

  /**
   * Settings for autoscaling of this WorkerPool.
   *
   * @param AutoscalingSettings $autoscalingSettings
   */
  public function setAutoscalingSettings(AutoscalingSettings $autoscalingSettings)
  {
    $this->autoscalingSettings = $autoscalingSettings;
  }
  /**
   * @return AutoscalingSettings
   */
  public function getAutoscalingSettings()
  {
    return $this->autoscalingSettings;
  }
  /**
   * Data disks that are used by a VM in this workflow.
   *
   * @param Disk[] $dataDisks
   */
  public function setDataDisks($dataDisks)
  {
    $this->dataDisks = $dataDisks;
  }
  /**
   * @return Disk[]
   */
  public function getDataDisks()
  {
    return $this->dataDisks;
  }
  /**
   * The default package set to install. This allows the service to select a
   * default set of packages which are useful to worker harnesses written in a
   * particular language.
   *
   * Accepted values: DEFAULT_PACKAGE_SET_UNKNOWN, DEFAULT_PACKAGE_SET_NONE,
   * DEFAULT_PACKAGE_SET_JAVA, DEFAULT_PACKAGE_SET_PYTHON
   *
   * @param self::DEFAULT_PACKAGE_SET_* $defaultPackageSet
   */
  public function setDefaultPackageSet($defaultPackageSet)
  {
    $this->defaultPackageSet = $defaultPackageSet;
  }
  /**
   * @return self::DEFAULT_PACKAGE_SET_*
   */
  public function getDefaultPackageSet()
  {
    return $this->defaultPackageSet;
  }
  /**
   * Size of root disk for VMs, in GB. If zero or unspecified, the service will
   * attempt to choose a reasonable default.
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
   * Fully qualified source image for disks.
   *
   * @param string $diskSourceImage
   */
  public function setDiskSourceImage($diskSourceImage)
  {
    $this->diskSourceImage = $diskSourceImage;
  }
  /**
   * @return string
   */
  public function getDiskSourceImage()
  {
    return $this->diskSourceImage;
  }
  /**
   * Type of root disk for VMs. If empty or unspecified, the service will
   * attempt to choose a reasonable default.
   *
   * @param string $diskType
   */
  public function setDiskType($diskType)
  {
    $this->diskType = $diskType;
  }
  /**
   * @return string
   */
  public function getDiskType()
  {
    return $this->diskType;
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
   * The kind of the worker pool; currently only `harness` and `shuffle` are
   * supported.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Machine type (e.g. "n1-standard-1"). If empty or unspecified, the service
   * will attempt to choose a reasonable default.
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
   * Metadata to set on the Google Compute Engine VMs.
   *
   * @param string[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return string[]
   */
  public function getMetadata()
  {
    return $this->metadata;
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
   * The number of threads per worker harness. If empty or unspecified, the
   * service will choose a number of threads (according to the number of cores
   * on the selected machine type for batch, or 1 by convention for streaming).
   *
   * @param int $numThreadsPerWorker
   */
  public function setNumThreadsPerWorker($numThreadsPerWorker)
  {
    $this->numThreadsPerWorker = $numThreadsPerWorker;
  }
  /**
   * @return int
   */
  public function getNumThreadsPerWorker()
  {
    return $this->numThreadsPerWorker;
  }
  /**
   * Number of Google Compute Engine workers in this pool needed to execute the
   * job. If zero or unspecified, the service will attempt to choose a
   * reasonable default.
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
   * The action to take on host maintenance, as defined by the Google Compute
   * Engine API.
   *
   * @param string $onHostMaintenance
   */
  public function setOnHostMaintenance($onHostMaintenance)
  {
    $this->onHostMaintenance = $onHostMaintenance;
  }
  /**
   * @return string
   */
  public function getOnHostMaintenance()
  {
    return $this->onHostMaintenance;
  }
  /**
   * Packages to be installed on workers.
   *
   * @param Package[] $packages
   */
  public function setPackages($packages)
  {
    $this->packages = $packages;
  }
  /**
   * @return Package[]
   */
  public function getPackages()
  {
    return $this->packages;
  }
  /**
   * Extra arguments for this worker pool.
   *
   * @param array[] $poolArgs
   */
  public function setPoolArgs($poolArgs)
  {
    $this->poolArgs = $poolArgs;
  }
  /**
   * @return array[]
   */
  public function getPoolArgs()
  {
    return $this->poolArgs;
  }
  /**
   * Set of SDK harness containers needed to execute this pipeline. This will
   * only be set in the Fn API path. For non-cross-language pipelines this
   * should have only one entry. Cross-language pipelines will have two or more
   * entries.
   *
   * @param SdkHarnessContainerImage[] $sdkHarnessContainerImages
   */
  public function setSdkHarnessContainerImages($sdkHarnessContainerImages)
  {
    $this->sdkHarnessContainerImages = $sdkHarnessContainerImages;
  }
  /**
   * @return SdkHarnessContainerImage[]
   */
  public function getSdkHarnessContainerImages()
  {
    return $this->sdkHarnessContainerImages;
  }
  /**
   * Subnetwork to which VMs will be assigned, if desired. Expected to be of the
   * form "regions/REGION/subnetworks/SUBNETWORK".
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
   * Settings passed through to Google Compute Engine workers when using the
   * standard Dataflow task runner. Users should ignore this field.
   *
   * @param TaskRunnerSettings $taskrunnerSettings
   */
  public function setTaskrunnerSettings(TaskRunnerSettings $taskrunnerSettings)
  {
    $this->taskrunnerSettings = $taskrunnerSettings;
  }
  /**
   * @return TaskRunnerSettings
   */
  public function getTaskrunnerSettings()
  {
    return $this->taskrunnerSettings;
  }
  /**
   * Sets the policy for determining when to turndown worker pool. Allowed
   * values are: `TEARDOWN_ALWAYS`, `TEARDOWN_ON_SUCCESS`, and `TEARDOWN_NEVER`.
   * `TEARDOWN_ALWAYS` means workers are always torn down regardless of whether
   * the job succeeds. `TEARDOWN_ON_SUCCESS` means workers are torn down if the
   * job succeeds. `TEARDOWN_NEVER` means the workers are never torn down. If
   * the workers are not torn down by the service, they will continue to run and
   * use Google Compute Engine VM resources in the user's project until they are
   * explicitly terminated by the user. Because of this, Google recommends using
   * the `TEARDOWN_ALWAYS` policy except for small, manually supervised test
   * jobs. If unknown or unspecified, the service will attempt to choose a
   * reasonable default.
   *
   * Accepted values: TEARDOWN_POLICY_UNKNOWN, TEARDOWN_ALWAYS,
   * TEARDOWN_ON_SUCCESS, TEARDOWN_NEVER
   *
   * @param self::TEARDOWN_POLICY_* $teardownPolicy
   */
  public function setTeardownPolicy($teardownPolicy)
  {
    $this->teardownPolicy = $teardownPolicy;
  }
  /**
   * @return self::TEARDOWN_POLICY_*
   */
  public function getTeardownPolicy()
  {
    return $this->teardownPolicy;
  }
  /**
   * Required. Docker container image that executes the Cloud Dataflow worker
   * harness, residing in Google Container Registry. Deprecated for the Fn API
   * path. Use sdk_harness_container_images instead.
   *
   * @param string $workerHarnessContainerImage
   */
  public function setWorkerHarnessContainerImage($workerHarnessContainerImage)
  {
    $this->workerHarnessContainerImage = $workerHarnessContainerImage;
  }
  /**
   * @return string
   */
  public function getWorkerHarnessContainerImage()
  {
    return $this->workerHarnessContainerImage;
  }
  /**
   * Zone to run the worker pools in. If empty or unspecified, the service will
   * attempt to choose a reasonable default.
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
class_alias(WorkerPool::class, 'Google_Service_Dataflow_WorkerPool');
