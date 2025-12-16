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

class GoogleCloudDiscoveryengineV1alphaConnectorRunEntityRunProgress extends \Google\Model
{
  /**
   * The current progress.
   *
   * @var string
   */
  public $currentCount;
  /**
   * Derived. The percentile of the progress.current_count / total_count. The
   * value is between [0, 1.0] inclusive.
   *
   * @var float
   */
  public $percentile;
  /**
   * The total.
   *
   * @var string
   */
  public $totalCount;

  /**
   * The current progress.
   *
   * @param string $currentCount
   */
  public function setCurrentCount($currentCount)
  {
    $this->currentCount = $currentCount;
  }
  /**
   * @return string
   */
  public function getCurrentCount()
  {
    return $this->currentCount;
  }
  /**
   * Derived. The percentile of the progress.current_count / total_count. The
   * value is between [0, 1.0] inclusive.
   *
   * @param float $percentile
   */
  public function setPercentile($percentile)
  {
    $this->percentile = $percentile;
  }
  /**
   * @return float
   */
  public function getPercentile()
  {
    return $this->percentile;
  }
  /**
   * The total.
   *
   * @param string $totalCount
   */
  public function setTotalCount($totalCount)
  {
    $this->totalCount = $totalCount;
  }
  /**
   * @return string
   */
  public function getTotalCount()
  {
    return $this->totalCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaConnectorRunEntityRunProgress::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaConnectorRunEntityRunProgress');
