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

namespace Google\Service\Dataflow;

class GPUUsage extends \Google\Model
{
  /**
   * Required. Timestamp of the measurement.
   *
   * @var string
   */
  public $timestamp;
  protected $utilizationType = GPUUtilization::class;
  protected $utilizationDataType = '';

  /**
   * Required. Timestamp of the measurement.
   *
   * @param string $timestamp
   */
  public function setTimestamp($timestamp)
  {
    $this->timestamp = $timestamp;
  }
  /**
   * @return string
   */
  public function getTimestamp()
  {
    return $this->timestamp;
  }
  /**
   * Required. Utilization info about the GPU.
   *
   * @param GPUUtilization $utilization
   */
  public function setUtilization(GPUUtilization $utilization)
  {
    $this->utilization = $utilization;
  }
  /**
   * @return GPUUtilization
   */
  public function getUtilization()
  {
    return $this->utilization;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GPUUsage::class, 'Google_Service_Dataflow_GPUUsage');
