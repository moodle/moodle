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

class RddDataDistribution extends \Google\Model
{
  /**
   * @var string
   */
  public $address;
  /**
   * @var string
   */
  public $diskUsed;
  /**
   * @var string
   */
  public $memoryRemaining;
  /**
   * @var string
   */
  public $memoryUsed;
  /**
   * @var string
   */
  public $offHeapMemoryRemaining;
  /**
   * @var string
   */
  public $offHeapMemoryUsed;
  /**
   * @var string
   */
  public $onHeapMemoryRemaining;
  /**
   * @var string
   */
  public $onHeapMemoryUsed;

  /**
   * @param string $address
   */
  public function setAddress($address)
  {
    $this->address = $address;
  }
  /**
   * @return string
   */
  public function getAddress()
  {
    return $this->address;
  }
  /**
   * @param string $diskUsed
   */
  public function setDiskUsed($diskUsed)
  {
    $this->diskUsed = $diskUsed;
  }
  /**
   * @return string
   */
  public function getDiskUsed()
  {
    return $this->diskUsed;
  }
  /**
   * @param string $memoryRemaining
   */
  public function setMemoryRemaining($memoryRemaining)
  {
    $this->memoryRemaining = $memoryRemaining;
  }
  /**
   * @return string
   */
  public function getMemoryRemaining()
  {
    return $this->memoryRemaining;
  }
  /**
   * @param string $memoryUsed
   */
  public function setMemoryUsed($memoryUsed)
  {
    $this->memoryUsed = $memoryUsed;
  }
  /**
   * @return string
   */
  public function getMemoryUsed()
  {
    return $this->memoryUsed;
  }
  /**
   * @param string $offHeapMemoryRemaining
   */
  public function setOffHeapMemoryRemaining($offHeapMemoryRemaining)
  {
    $this->offHeapMemoryRemaining = $offHeapMemoryRemaining;
  }
  /**
   * @return string
   */
  public function getOffHeapMemoryRemaining()
  {
    return $this->offHeapMemoryRemaining;
  }
  /**
   * @param string $offHeapMemoryUsed
   */
  public function setOffHeapMemoryUsed($offHeapMemoryUsed)
  {
    $this->offHeapMemoryUsed = $offHeapMemoryUsed;
  }
  /**
   * @return string
   */
  public function getOffHeapMemoryUsed()
  {
    return $this->offHeapMemoryUsed;
  }
  /**
   * @param string $onHeapMemoryRemaining
   */
  public function setOnHeapMemoryRemaining($onHeapMemoryRemaining)
  {
    $this->onHeapMemoryRemaining = $onHeapMemoryRemaining;
  }
  /**
   * @return string
   */
  public function getOnHeapMemoryRemaining()
  {
    return $this->onHeapMemoryRemaining;
  }
  /**
   * @param string $onHeapMemoryUsed
   */
  public function setOnHeapMemoryUsed($onHeapMemoryUsed)
  {
    $this->onHeapMemoryUsed = $onHeapMemoryUsed;
  }
  /**
   * @return string
   */
  public function getOnHeapMemoryUsed()
  {
    return $this->onHeapMemoryUsed;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RddDataDistribution::class, 'Google_Service_Dataproc_RddDataDistribution');
