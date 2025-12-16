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

namespace Google\Service\CloudComposer;

class WorkerResource extends \Google\Model
{
  /**
   * Optional. CPU request and limit for a single Airflow worker replica.
   *
   * @var float
   */
  public $cpu;
  /**
   * Optional. Maximum number of workers for autoscaling.
   *
   * @var int
   */
  public $maxCount;
  /**
   * Optional. Memory (GB) request and limit for a single Airflow worker
   * replica.
   *
   * @var float
   */
  public $memoryGb;
  /**
   * Optional. Minimum number of workers for autoscaling.
   *
   * @var int
   */
  public $minCount;
  /**
   * Optional. Storage (GB) request and limit for a single Airflow worker
   * replica.
   *
   * @var float
   */
  public $storageGb;

  /**
   * Optional. CPU request and limit for a single Airflow worker replica.
   *
   * @param float $cpu
   */
  public function setCpu($cpu)
  {
    $this->cpu = $cpu;
  }
  /**
   * @return float
   */
  public function getCpu()
  {
    return $this->cpu;
  }
  /**
   * Optional. Maximum number of workers for autoscaling.
   *
   * @param int $maxCount
   */
  public function setMaxCount($maxCount)
  {
    $this->maxCount = $maxCount;
  }
  /**
   * @return int
   */
  public function getMaxCount()
  {
    return $this->maxCount;
  }
  /**
   * Optional. Memory (GB) request and limit for a single Airflow worker
   * replica.
   *
   * @param float $memoryGb
   */
  public function setMemoryGb($memoryGb)
  {
    $this->memoryGb = $memoryGb;
  }
  /**
   * @return float
   */
  public function getMemoryGb()
  {
    return $this->memoryGb;
  }
  /**
   * Optional. Minimum number of workers for autoscaling.
   *
   * @param int $minCount
   */
  public function setMinCount($minCount)
  {
    $this->minCount = $minCount;
  }
  /**
   * @return int
   */
  public function getMinCount()
  {
    return $this->minCount;
  }
  /**
   * Optional. Storage (GB) request and limit for a single Airflow worker
   * replica.
   *
   * @param float $storageGb
   */
  public function setStorageGb($storageGb)
  {
    $this->storageGb = $storageGb;
  }
  /**
   * @return float
   */
  public function getStorageGb()
  {
    return $this->storageGb;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkerResource::class, 'Google_Service_CloudComposer_WorkerResource');
