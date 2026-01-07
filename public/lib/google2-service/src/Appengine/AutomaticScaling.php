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

namespace Google\Service\Appengine;

class AutomaticScaling extends \Google\Model
{
  /**
   * The time period that the Autoscaler
   * (https://cloud.google.com/compute/docs/autoscaler/) should wait before it
   * starts collecting information from a new instance. This prevents the
   * autoscaler from collecting information when the instance is initializing,
   * during which the collected usage would not be reliable. Only applicable in
   * the App Engine flexible environment.
   *
   * @var string
   */
  public $coolDownPeriod;
  protected $cpuUtilizationType = CpuUtilization::class;
  protected $cpuUtilizationDataType = '';
  protected $diskUtilizationType = DiskUtilization::class;
  protected $diskUtilizationDataType = '';
  /**
   * Number of concurrent requests an automatic scaling instance can accept
   * before the scheduler spawns a new instance.Defaults to a runtime-specific
   * value.
   *
   * @var int
   */
  public $maxConcurrentRequests;
  /**
   * Maximum number of idle instances that should be maintained for this
   * version.
   *
   * @var int
   */
  public $maxIdleInstances;
  /**
   * Maximum amount of time that a request should wait in the pending queue
   * before starting a new instance to handle it.
   *
   * @var string
   */
  public $maxPendingLatency;
  /**
   * Maximum number of instances that should be started to handle requests for
   * this version.
   *
   * @var int
   */
  public $maxTotalInstances;
  /**
   * Minimum number of idle instances that should be maintained for this
   * version. Only applicable for the default version of a service.
   *
   * @var int
   */
  public $minIdleInstances;
  /**
   * Minimum amount of time a request should wait in the pending queue before
   * starting a new instance to handle it.
   *
   * @var string
   */
  public $minPendingLatency;
  /**
   * Minimum number of running instances that should be maintained for this
   * version.
   *
   * @var int
   */
  public $minTotalInstances;
  protected $networkUtilizationType = NetworkUtilization::class;
  protected $networkUtilizationDataType = '';
  protected $requestUtilizationType = RequestUtilization::class;
  protected $requestUtilizationDataType = '';
  protected $standardSchedulerSettingsType = StandardSchedulerSettings::class;
  protected $standardSchedulerSettingsDataType = '';

  /**
   * The time period that the Autoscaler
   * (https://cloud.google.com/compute/docs/autoscaler/) should wait before it
   * starts collecting information from a new instance. This prevents the
   * autoscaler from collecting information when the instance is initializing,
   * during which the collected usage would not be reliable. Only applicable in
   * the App Engine flexible environment.
   *
   * @param string $coolDownPeriod
   */
  public function setCoolDownPeriod($coolDownPeriod)
  {
    $this->coolDownPeriod = $coolDownPeriod;
  }
  /**
   * @return string
   */
  public function getCoolDownPeriod()
  {
    return $this->coolDownPeriod;
  }
  /**
   * Target scaling by CPU usage.
   *
   * @param CpuUtilization $cpuUtilization
   */
  public function setCpuUtilization(CpuUtilization $cpuUtilization)
  {
    $this->cpuUtilization = $cpuUtilization;
  }
  /**
   * @return CpuUtilization
   */
  public function getCpuUtilization()
  {
    return $this->cpuUtilization;
  }
  /**
   * Target scaling by disk usage.
   *
   * @param DiskUtilization $diskUtilization
   */
  public function setDiskUtilization(DiskUtilization $diskUtilization)
  {
    $this->diskUtilization = $diskUtilization;
  }
  /**
   * @return DiskUtilization
   */
  public function getDiskUtilization()
  {
    return $this->diskUtilization;
  }
  /**
   * Number of concurrent requests an automatic scaling instance can accept
   * before the scheduler spawns a new instance.Defaults to a runtime-specific
   * value.
   *
   * @param int $maxConcurrentRequests
   */
  public function setMaxConcurrentRequests($maxConcurrentRequests)
  {
    $this->maxConcurrentRequests = $maxConcurrentRequests;
  }
  /**
   * @return int
   */
  public function getMaxConcurrentRequests()
  {
    return $this->maxConcurrentRequests;
  }
  /**
   * Maximum number of idle instances that should be maintained for this
   * version.
   *
   * @param int $maxIdleInstances
   */
  public function setMaxIdleInstances($maxIdleInstances)
  {
    $this->maxIdleInstances = $maxIdleInstances;
  }
  /**
   * @return int
   */
  public function getMaxIdleInstances()
  {
    return $this->maxIdleInstances;
  }
  /**
   * Maximum amount of time that a request should wait in the pending queue
   * before starting a new instance to handle it.
   *
   * @param string $maxPendingLatency
   */
  public function setMaxPendingLatency($maxPendingLatency)
  {
    $this->maxPendingLatency = $maxPendingLatency;
  }
  /**
   * @return string
   */
  public function getMaxPendingLatency()
  {
    return $this->maxPendingLatency;
  }
  /**
   * Maximum number of instances that should be started to handle requests for
   * this version.
   *
   * @param int $maxTotalInstances
   */
  public function setMaxTotalInstances($maxTotalInstances)
  {
    $this->maxTotalInstances = $maxTotalInstances;
  }
  /**
   * @return int
   */
  public function getMaxTotalInstances()
  {
    return $this->maxTotalInstances;
  }
  /**
   * Minimum number of idle instances that should be maintained for this
   * version. Only applicable for the default version of a service.
   *
   * @param int $minIdleInstances
   */
  public function setMinIdleInstances($minIdleInstances)
  {
    $this->minIdleInstances = $minIdleInstances;
  }
  /**
   * @return int
   */
  public function getMinIdleInstances()
  {
    return $this->minIdleInstances;
  }
  /**
   * Minimum amount of time a request should wait in the pending queue before
   * starting a new instance to handle it.
   *
   * @param string $minPendingLatency
   */
  public function setMinPendingLatency($minPendingLatency)
  {
    $this->minPendingLatency = $minPendingLatency;
  }
  /**
   * @return string
   */
  public function getMinPendingLatency()
  {
    return $this->minPendingLatency;
  }
  /**
   * Minimum number of running instances that should be maintained for this
   * version.
   *
   * @param int $minTotalInstances
   */
  public function setMinTotalInstances($minTotalInstances)
  {
    $this->minTotalInstances = $minTotalInstances;
  }
  /**
   * @return int
   */
  public function getMinTotalInstances()
  {
    return $this->minTotalInstances;
  }
  /**
   * Target scaling by network usage.
   *
   * @param NetworkUtilization $networkUtilization
   */
  public function setNetworkUtilization(NetworkUtilization $networkUtilization)
  {
    $this->networkUtilization = $networkUtilization;
  }
  /**
   * @return NetworkUtilization
   */
  public function getNetworkUtilization()
  {
    return $this->networkUtilization;
  }
  /**
   * Target scaling by request utilization.
   *
   * @param RequestUtilization $requestUtilization
   */
  public function setRequestUtilization(RequestUtilization $requestUtilization)
  {
    $this->requestUtilization = $requestUtilization;
  }
  /**
   * @return RequestUtilization
   */
  public function getRequestUtilization()
  {
    return $this->requestUtilization;
  }
  /**
   * Scheduler settings for standard environment.
   *
   * @param StandardSchedulerSettings $standardSchedulerSettings
   */
  public function setStandardSchedulerSettings(StandardSchedulerSettings $standardSchedulerSettings)
  {
    $this->standardSchedulerSettings = $standardSchedulerSettings;
  }
  /**
   * @return StandardSchedulerSettings
   */
  public function getStandardSchedulerSettings()
  {
    return $this->standardSchedulerSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutomaticScaling::class, 'Google_Service_Appengine_AutomaticScaling');
