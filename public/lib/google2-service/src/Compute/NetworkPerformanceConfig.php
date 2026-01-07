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

namespace Google\Service\Compute;

class NetworkPerformanceConfig extends \Google\Model
{
  public const TOTAL_EGRESS_BANDWIDTH_TIER_DEFAULT = 'DEFAULT';
  public const TOTAL_EGRESS_BANDWIDTH_TIER_TIER_1 = 'TIER_1';
  /**
   * @var string
   */
  public $totalEgressBandwidthTier;

  /**
   * @param self::TOTAL_EGRESS_BANDWIDTH_TIER_* $totalEgressBandwidthTier
   */
  public function setTotalEgressBandwidthTier($totalEgressBandwidthTier)
  {
    $this->totalEgressBandwidthTier = $totalEgressBandwidthTier;
  }
  /**
   * @return self::TOTAL_EGRESS_BANDWIDTH_TIER_*
   */
  public function getTotalEgressBandwidthTier()
  {
    return $this->totalEgressBandwidthTier;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkPerformanceConfig::class, 'Google_Service_Compute_NetworkPerformanceConfig');
