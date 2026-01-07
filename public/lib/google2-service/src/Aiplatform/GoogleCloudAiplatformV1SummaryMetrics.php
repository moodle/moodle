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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1SummaryMetrics extends \Google\Model
{
  /**
   * Optional. The number of items that failed to be evaluated.
   *
   * @var int
   */
  public $failedItems;
  /**
   * Optional. Map of metric name to metric value.
   *
   * @var array[]
   */
  public $metrics;
  /**
   * Optional. The total number of items that were evaluated.
   *
   * @var int
   */
  public $totalItems;

  /**
   * Optional. The number of items that failed to be evaluated.
   *
   * @param int $failedItems
   */
  public function setFailedItems($failedItems)
  {
    $this->failedItems = $failedItems;
  }
  /**
   * @return int
   */
  public function getFailedItems()
  {
    return $this->failedItems;
  }
  /**
   * Optional. Map of metric name to metric value.
   *
   * @param array[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return array[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * Optional. The total number of items that were evaluated.
   *
   * @param int $totalItems
   */
  public function setTotalItems($totalItems)
  {
    $this->totalItems = $totalItems;
  }
  /**
   * @return int
   */
  public function getTotalItems()
  {
    return $this->totalItems;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SummaryMetrics::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SummaryMetrics');
