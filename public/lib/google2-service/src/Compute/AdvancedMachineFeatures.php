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

namespace Google\Service\Compute;

class AdvancedMachineFeatures extends \Google\Model
{
  /**
   * Architecturally defined non-LLC events.
   */
  public const PERFORMANCE_MONITORING_UNIT_ARCHITECTURAL = 'ARCHITECTURAL';
  /**
   * Most documented core/L2 and LLC events.
   */
  public const PERFORMANCE_MONITORING_UNIT_ENHANCED = 'ENHANCED';
  public const PERFORMANCE_MONITORING_UNIT_PERFORMANCE_MONITORING_UNIT_UNSPECIFIED = 'PERFORMANCE_MONITORING_UNIT_UNSPECIFIED';
  /**
   * Most documented core/L2 events.
   */
  public const PERFORMANCE_MONITORING_UNIT_STANDARD = 'STANDARD';
  /**
   * Whether to enable nested virtualization or not (default is false).
   *
   * @var bool
   */
  public $enableNestedVirtualization;
  /**
   * Whether to enable UEFI networking for instance creation.
   *
   * @var bool
   */
  public $enableUefiNetworking;
  /**
   * Type of Performance Monitoring Unit requested on instance.
   *
   * @var string
   */
  public $performanceMonitoringUnit;
  /**
   * The number of threads per physical core. To disable simultaneous
   * multithreading (SMT) set this to 1. If unset, the maximum number of threads
   * supported per core by the underlying processor is assumed.
   *
   * @var int
   */
  public $threadsPerCore;
  /**
   * Turbo frequency mode to use for the instance. Supported modes include: *
   * ALL_CORE_MAX
   *
   * Using empty string or not setting this field will use the platform-specific
   * default turbo mode.
   *
   * @var string
   */
  public $turboMode;
  /**
   * The number of physical cores to expose to an instance. Multiply by the
   * number of threads per core to compute the total number of virtual CPUs to
   * expose to the instance. If unset, the number of cores is inferred from the
   * instance's nominal CPU count and the underlying platform's SMT width.
   *
   * @var int
   */
  public $visibleCoreCount;

  /**
   * Whether to enable nested virtualization or not (default is false).
   *
   * @param bool $enableNestedVirtualization
   */
  public function setEnableNestedVirtualization($enableNestedVirtualization)
  {
    $this->enableNestedVirtualization = $enableNestedVirtualization;
  }
  /**
   * @return bool
   */
  public function getEnableNestedVirtualization()
  {
    return $this->enableNestedVirtualization;
  }
  /**
   * Whether to enable UEFI networking for instance creation.
   *
   * @param bool $enableUefiNetworking
   */
  public function setEnableUefiNetworking($enableUefiNetworking)
  {
    $this->enableUefiNetworking = $enableUefiNetworking;
  }
  /**
   * @return bool
   */
  public function getEnableUefiNetworking()
  {
    return $this->enableUefiNetworking;
  }
  /**
   * Type of Performance Monitoring Unit requested on instance.
   *
   * Accepted values: ARCHITECTURAL, ENHANCED,
   * PERFORMANCE_MONITORING_UNIT_UNSPECIFIED, STANDARD
   *
   * @param self::PERFORMANCE_MONITORING_UNIT_* $performanceMonitoringUnit
   */
  public function setPerformanceMonitoringUnit($performanceMonitoringUnit)
  {
    $this->performanceMonitoringUnit = $performanceMonitoringUnit;
  }
  /**
   * @return self::PERFORMANCE_MONITORING_UNIT_*
   */
  public function getPerformanceMonitoringUnit()
  {
    return $this->performanceMonitoringUnit;
  }
  /**
   * The number of threads per physical core. To disable simultaneous
   * multithreading (SMT) set this to 1. If unset, the maximum number of threads
   * supported per core by the underlying processor is assumed.
   *
   * @param int $threadsPerCore
   */
  public function setThreadsPerCore($threadsPerCore)
  {
    $this->threadsPerCore = $threadsPerCore;
  }
  /**
   * @return int
   */
  public function getThreadsPerCore()
  {
    return $this->threadsPerCore;
  }
  /**
   * Turbo frequency mode to use for the instance. Supported modes include: *
   * ALL_CORE_MAX
   *
   * Using empty string or not setting this field will use the platform-specific
   * default turbo mode.
   *
   * @param string $turboMode
   */
  public function setTurboMode($turboMode)
  {
    $this->turboMode = $turboMode;
  }
  /**
   * @return string
   */
  public function getTurboMode()
  {
    return $this->turboMode;
  }
  /**
   * The number of physical cores to expose to an instance. Multiply by the
   * number of threads per core to compute the total number of virtual CPUs to
   * expose to the instance. If unset, the number of cores is inferred from the
   * instance's nominal CPU count and the underlying platform's SMT width.
   *
   * @param int $visibleCoreCount
   */
  public function setVisibleCoreCount($visibleCoreCount)
  {
    $this->visibleCoreCount = $visibleCoreCount;
  }
  /**
   * @return int
   */
  public function getVisibleCoreCount()
  {
    return $this->visibleCoreCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdvancedMachineFeatures::class, 'Google_Service_Compute_AdvancedMachineFeatures');
