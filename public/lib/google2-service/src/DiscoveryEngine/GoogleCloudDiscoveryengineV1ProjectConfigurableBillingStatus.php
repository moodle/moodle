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

class GoogleCloudDiscoveryengineV1ProjectConfigurableBillingStatus extends \Google\Model
{
  /**
   * Optional. The currently effective Indexing Core threshold. This is the
   * threshold against which Indexing Core usage is compared for overage
   * calculations.
   *
   * @var string
   */
  public $effectiveIndexingCoreThreshold;
  /**
   * Optional. The currently effective Search QPM threshold in queries per
   * minute. This is the threshold against which QPM usage is compared for
   * overage calculations.
   *
   * @var string
   */
  public $effectiveSearchQpmThreshold;
  /**
   * Optional. The start time of the currently active billing subscription.
   *
   * @var string
   */
  public $startTime;

  /**
   * Optional. The currently effective Indexing Core threshold. This is the
   * threshold against which Indexing Core usage is compared for overage
   * calculations.
   *
   * @param string $effectiveIndexingCoreThreshold
   */
  public function setEffectiveIndexingCoreThreshold($effectiveIndexingCoreThreshold)
  {
    $this->effectiveIndexingCoreThreshold = $effectiveIndexingCoreThreshold;
  }
  /**
   * @return string
   */
  public function getEffectiveIndexingCoreThreshold()
  {
    return $this->effectiveIndexingCoreThreshold;
  }
  /**
   * Optional. The currently effective Search QPM threshold in queries per
   * minute. This is the threshold against which QPM usage is compared for
   * overage calculations.
   *
   * @param string $effectiveSearchQpmThreshold
   */
  public function setEffectiveSearchQpmThreshold($effectiveSearchQpmThreshold)
  {
    $this->effectiveSearchQpmThreshold = $effectiveSearchQpmThreshold;
  }
  /**
   * @return string
   */
  public function getEffectiveSearchQpmThreshold()
  {
    return $this->effectiveSearchQpmThreshold;
  }
  /**
   * Optional. The start time of the currently active billing subscription.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1ProjectConfigurableBillingStatus::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1ProjectConfigurableBillingStatus');
