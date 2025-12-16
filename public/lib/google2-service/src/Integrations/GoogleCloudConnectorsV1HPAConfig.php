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

namespace Google\Service\Integrations;

class GoogleCloudConnectorsV1HPAConfig extends \Google\Model
{
  /**
   * Output only. Percent CPU utilization where HPA triggers autoscaling.
   *
   * @var string
   */
  public $cpuUtilizationThreshold;
  /**
   * Output only. Percent Memory utilization where HPA triggers autoscaling.
   *
   * @var string
   */
  public $memoryUtilizationThreshold;

  /**
   * Output only. Percent CPU utilization where HPA triggers autoscaling.
   *
   * @param string $cpuUtilizationThreshold
   */
  public function setCpuUtilizationThreshold($cpuUtilizationThreshold)
  {
    $this->cpuUtilizationThreshold = $cpuUtilizationThreshold;
  }
  /**
   * @return string
   */
  public function getCpuUtilizationThreshold()
  {
    return $this->cpuUtilizationThreshold;
  }
  /**
   * Output only. Percent Memory utilization where HPA triggers autoscaling.
   *
   * @param string $memoryUtilizationThreshold
   */
  public function setMemoryUtilizationThreshold($memoryUtilizationThreshold)
  {
    $this->memoryUtilizationThreshold = $memoryUtilizationThreshold;
  }
  /**
   * @return string
   */
  public function getMemoryUtilizationThreshold()
  {
    return $this->memoryUtilizationThreshold;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudConnectorsV1HPAConfig::class, 'Google_Service_Integrations_GoogleCloudConnectorsV1HPAConfig');
