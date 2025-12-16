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

namespace Google\Service\Monitoring;

class PerformanceThreshold extends \Google\Model
{
  protected $basicSliPerformanceType = BasicSli::class;
  protected $basicSliPerformanceDataType = '';
  protected $performanceType = RequestBasedSli::class;
  protected $performanceDataType = '';
  /**
   * If window performance >= threshold, the window is counted as good.
   *
   * @var 
   */
  public $threshold;

  /**
   * BasicSli to evaluate to judge window quality.
   *
   * @param BasicSli $basicSliPerformance
   */
  public function setBasicSliPerformance(BasicSli $basicSliPerformance)
  {
    $this->basicSliPerformance = $basicSliPerformance;
  }
  /**
   * @return BasicSli
   */
  public function getBasicSliPerformance()
  {
    return $this->basicSliPerformance;
  }
  /**
   * RequestBasedSli to evaluate to judge window quality.
   *
   * @param RequestBasedSli $performance
   */
  public function setPerformance(RequestBasedSli $performance)
  {
    $this->performance = $performance;
  }
  /**
   * @return RequestBasedSli
   */
  public function getPerformance()
  {
    return $this->performance;
  }
  public function setThreshold($threshold)
  {
    $this->threshold = $threshold;
  }
  public function getThreshold()
  {
    return $this->threshold;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PerformanceThreshold::class, 'Google_Service_Monitoring_PerformanceThreshold');
