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

namespace Google\Service\Container;

class AdvancedMachineFeatures extends \Google\Model
{
  /**
   * PMU not enabled.
   */
  public const PERFORMANCE_MONITORING_UNIT_PERFORMANCE_MONITORING_UNIT_UNSPECIFIED = 'PERFORMANCE_MONITORING_UNIT_UNSPECIFIED';
  /**
   * Architecturally defined non-LLC events.
   */
  public const PERFORMANCE_MONITORING_UNIT_ARCHITECTURAL = 'ARCHITECTURAL';
  /**
   * Most documented core/L2 events.
   */
  public const PERFORMANCE_MONITORING_UNIT_STANDARD = 'STANDARD';
  /**
   * Most documented core/L2 and LLC events.
   */
  public const PERFORMANCE_MONITORING_UNIT_ENHANCED = 'ENHANCED';
  /**
   * Whether or not to enable nested virtualization (defaults to false).
   *
   * @var bool
   */
  public $enableNestedVirtualization;
  /**
   * Type of Performance Monitoring Unit (PMU) requested on node pool instances.
   * If unset, PMU will not be available to the node.
   *
   * @var string
   */
  public $performanceMonitoringUnit;
  /**
   * The number of threads per physical core. To disable simultaneous
   * multithreading (SMT) set this to 1. If unset, the maximum number of threads
   * supported per core by the underlying processor is assumed.
   *
   * @var string
   */
  public $threadsPerCore;

  /**
   * Whether or not to enable nested virtualization (defaults to false).
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
   * Type of Performance Monitoring Unit (PMU) requested on node pool instances.
   * If unset, PMU will not be available to the node.
   *
   * Accepted values: PERFORMANCE_MONITORING_UNIT_UNSPECIFIED, ARCHITECTURAL,
   * STANDARD, ENHANCED
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
   * @param string $threadsPerCore
   */
  public function setThreadsPerCore($threadsPerCore)
  {
    $this->threadsPerCore = $threadsPerCore;
  }
  /**
   * @return string
   */
  public function getThreadsPerCore()
  {
    return $this->threadsPerCore;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdvancedMachineFeatures::class, 'Google_Service_Container_AdvancedMachineFeatures');
