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

namespace Google\Service\Dataproc;

class MemoryMetrics extends \Google\Model
{
  /**
   * @var string
   */
  public $totalOffHeapStorageMemory;
  /**
   * @var string
   */
  public $totalOnHeapStorageMemory;
  /**
   * @var string
   */
  public $usedOffHeapStorageMemory;
  /**
   * @var string
   */
  public $usedOnHeapStorageMemory;

  /**
   * @param string $totalOffHeapStorageMemory
   */
  public function setTotalOffHeapStorageMemory($totalOffHeapStorageMemory)
  {
    $this->totalOffHeapStorageMemory = $totalOffHeapStorageMemory;
  }
  /**
   * @return string
   */
  public function getTotalOffHeapStorageMemory()
  {
    return $this->totalOffHeapStorageMemory;
  }
  /**
   * @param string $totalOnHeapStorageMemory
   */
  public function setTotalOnHeapStorageMemory($totalOnHeapStorageMemory)
  {
    $this->totalOnHeapStorageMemory = $totalOnHeapStorageMemory;
  }
  /**
   * @return string
   */
  public function getTotalOnHeapStorageMemory()
  {
    return $this->totalOnHeapStorageMemory;
  }
  /**
   * @param string $usedOffHeapStorageMemory
   */
  public function setUsedOffHeapStorageMemory($usedOffHeapStorageMemory)
  {
    $this->usedOffHeapStorageMemory = $usedOffHeapStorageMemory;
  }
  /**
   * @return string
   */
  public function getUsedOffHeapStorageMemory()
  {
    return $this->usedOffHeapStorageMemory;
  }
  /**
   * @param string $usedOnHeapStorageMemory
   */
  public function setUsedOnHeapStorageMemory($usedOnHeapStorageMemory)
  {
    $this->usedOnHeapStorageMemory = $usedOnHeapStorageMemory;
  }
  /**
   * @return string
   */
  public function getUsedOnHeapStorageMemory()
  {
    return $this->usedOnHeapStorageMemory;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MemoryMetrics::class, 'Google_Service_Dataproc_MemoryMetrics');
