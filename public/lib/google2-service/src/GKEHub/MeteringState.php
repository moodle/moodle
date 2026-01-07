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

namespace Google\Service\GKEHub;

class MeteringState extends \Google\Model
{
  /**
   * The time stamp of the most recent measurement of the number of vCPUs in the
   * cluster.
   *
   * @var string
   */
  public $lastMeasurementTime;
  /**
   * The vCPUs capacity in the cluster according to the most recent measurement
   * (1/1000 precision).
   *
   * @var float
   */
  public $preciseLastMeasuredClusterVcpuCapacity;

  /**
   * The time stamp of the most recent measurement of the number of vCPUs in the
   * cluster.
   *
   * @param string $lastMeasurementTime
   */
  public function setLastMeasurementTime($lastMeasurementTime)
  {
    $this->lastMeasurementTime = $lastMeasurementTime;
  }
  /**
   * @return string
   */
  public function getLastMeasurementTime()
  {
    return $this->lastMeasurementTime;
  }
  /**
   * The vCPUs capacity in the cluster according to the most recent measurement
   * (1/1000 precision).
   *
   * @param float $preciseLastMeasuredClusterVcpuCapacity
   */
  public function setPreciseLastMeasuredClusterVcpuCapacity($preciseLastMeasuredClusterVcpuCapacity)
  {
    $this->preciseLastMeasuredClusterVcpuCapacity = $preciseLastMeasuredClusterVcpuCapacity;
  }
  /**
   * @return float
   */
  public function getPreciseLastMeasuredClusterVcpuCapacity()
  {
    return $this->preciseLastMeasuredClusterVcpuCapacity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MeteringState::class, 'Google_Service_GKEHub_MeteringState');
