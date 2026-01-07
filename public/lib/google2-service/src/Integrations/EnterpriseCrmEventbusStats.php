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

class EnterpriseCrmEventbusStats extends \Google\Model
{
  protected $dimensionsType = EnterpriseCrmEventbusStatsDimensions::class;
  protected $dimensionsDataType = '';
  /**
   * Average duration in seconds.
   *
   * @var 
   */
  public $durationInSeconds;
  /**
   * Average error rate.
   *
   * @var 
   */
  public $errorRate;
  /**
   * Queries per second.
   *
   * @var 
   */
  public $qps;
  /**
   * Average warning rate.
   *
   * @var 
   */
  public $warningRate;

  /**
   * Dimensions that these stats have been aggregated on.
   *
   * @param EnterpriseCrmEventbusStatsDimensions $dimensions
   */
  public function setDimensions(EnterpriseCrmEventbusStatsDimensions $dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return EnterpriseCrmEventbusStatsDimensions
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  public function setDurationInSeconds($durationInSeconds)
  {
    $this->durationInSeconds = $durationInSeconds;
  }
  public function getDurationInSeconds()
  {
    return $this->durationInSeconds;
  }
  public function setErrorRate($errorRate)
  {
    $this->errorRate = $errorRate;
  }
  public function getErrorRate()
  {
    return $this->errorRate;
  }
  public function setQps($qps)
  {
    $this->qps = $qps;
  }
  public function getQps()
  {
    return $this->qps;
  }
  public function setWarningRate($warningRate)
  {
    $this->warningRate = $warningRate;
  }
  public function getWarningRate()
  {
    return $this->warningRate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusStats::class, 'Google_Service_Integrations_EnterpriseCrmEventbusStats');
