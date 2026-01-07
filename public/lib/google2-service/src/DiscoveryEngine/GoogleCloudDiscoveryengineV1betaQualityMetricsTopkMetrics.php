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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaQualityMetricsTopkMetrics extends \Google\Model
{
  /**
   * The top-1 value.
   *
   * @var 
   */
  public $top1;
  /**
   * The top-10 value.
   *
   * @var 
   */
  public $top10;
  /**
   * The top-3 value.
   *
   * @var 
   */
  public $top3;
  /**
   * The top-5 value.
   *
   * @var 
   */
  public $top5;

  public function setTop1($top1)
  {
    $this->top1 = $top1;
  }
  public function getTop1()
  {
    return $this->top1;
  }
  public function setTop10($top10)
  {
    $this->top10 = $top10;
  }
  public function getTop10()
  {
    return $this->top10;
  }
  public function setTop3($top3)
  {
    $this->top3 = $top3;
  }
  public function getTop3()
  {
    return $this->top3;
  }
  public function setTop5($top5)
  {
    $this->top5 = $top5;
  }
  public function getTop5()
  {
    return $this->top5;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaQualityMetricsTopkMetrics::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaQualityMetricsTopkMetrics');
