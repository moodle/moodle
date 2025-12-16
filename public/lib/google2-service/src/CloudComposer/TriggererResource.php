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

class TriggererResource extends \Google\Model
{
  /**
   * Optional. The number of triggerers.
   *
   * @var int
   */
  public $count;
  /**
   * Optional. CPU request and limit for a single Airflow triggerer replica.
   *
   * @var float
   */
  public $cpu;
  /**
   * Optional. Memory (GB) request and limit for a single Airflow triggerer
   * replica.
   *
   * @var float
   */
  public $memoryGb;

  /**
   * Optional. The number of triggerers.
   *
   * @param int $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return int
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * Optional. CPU request and limit for a single Airflow triggerer replica.
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
   * Optional. Memory (GB) request and limit for a single Airflow triggerer
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TriggererResource::class, 'Google_Service_CloudComposer_TriggererResource');
