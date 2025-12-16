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

namespace Google\Service\Backupdr;

class AdvancedMachineFeatures extends \Google\Model
{
  /**
   * Optional. Whether to enable nested virtualization or not (default is
   * false).
   *
   * @var bool
   */
  public $enableNestedVirtualization;
  /**
   * Optional. Whether to enable UEFI networking for instance creation.
   *
   * @var bool
   */
  public $enableUefiNetworking;
  /**
   * Optional. The number of threads per physical core. To disable simultaneous
   * multithreading (SMT) set this to 1. If unset, the maximum number of threads
   * supported per core by the underlying processor is assumed.
   *
   * @var int
   */
  public $threadsPerCore;
  /**
   * Optional. The number of physical cores to expose to an instance. Multiply
   * by the number of threads per core to compute the total number of virtual
   * CPUs to expose to the instance. If unset, the number of cores is inferred
   * from the instance's nominal CPU count and the underlying platform's SMT
   * width.
   *
   * @var int
   */
  public $visibleCoreCount;

  /**
   * Optional. Whether to enable nested virtualization or not (default is
   * false).
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
   * Optional. Whether to enable UEFI networking for instance creation.
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
   * Optional. The number of threads per physical core. To disable simultaneous
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
   * Optional. The number of physical cores to expose to an instance. Multiply
   * by the number of threads per core to compute the total number of virtual
   * CPUs to expose to the instance. If unset, the number of cores is inferred
   * from the instance's nominal CPU count and the underlying platform's SMT
   * width.
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
class_alias(AdvancedMachineFeatures::class, 'Google_Service_Backupdr_AdvancedMachineFeatures');
