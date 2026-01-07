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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0CommonMaximizeConversions extends \Google\Model
{
  /**
   * Maximum bid limit that can be set by the bid strategy. The limit applies to
   * all keywords managed by the strategy. Mutable for portfolio bidding
   * strategies only.
   *
   * @var string
   */
  public $cpcBidCeilingMicros;
  /**
   * Minimum bid limit that can be set by the bid strategy. The limit applies to
   * all keywords managed by the strategy. Mutable for portfolio bidding
   * strategies only.
   *
   * @var string
   */
  public $cpcBidFloorMicros;
  /**
   * The target cost-per-action (CPA) option. This is the average amount that
   * you would like to spend per conversion action specified in micro units of
   * the bidding strategy's currency. If set, the bid strategy will get as many
   * conversions as possible at or below the target cost-per-action. If the
   * target CPA is not set, the bid strategy will aim to achieve the lowest
   * possible CPA given the budget.
   *
   * @var string
   */
  public $targetCpaMicros;

  /**
   * Maximum bid limit that can be set by the bid strategy. The limit applies to
   * all keywords managed by the strategy. Mutable for portfolio bidding
   * strategies only.
   *
   * @param string $cpcBidCeilingMicros
   */
  public function setCpcBidCeilingMicros($cpcBidCeilingMicros)
  {
    $this->cpcBidCeilingMicros = $cpcBidCeilingMicros;
  }
  /**
   * @return string
   */
  public function getCpcBidCeilingMicros()
  {
    return $this->cpcBidCeilingMicros;
  }
  /**
   * Minimum bid limit that can be set by the bid strategy. The limit applies to
   * all keywords managed by the strategy. Mutable for portfolio bidding
   * strategies only.
   *
   * @param string $cpcBidFloorMicros
   */
  public function setCpcBidFloorMicros($cpcBidFloorMicros)
  {
    $this->cpcBidFloorMicros = $cpcBidFloorMicros;
  }
  /**
   * @return string
   */
  public function getCpcBidFloorMicros()
  {
    return $this->cpcBidFloorMicros;
  }
  /**
   * The target cost-per-action (CPA) option. This is the average amount that
   * you would like to spend per conversion action specified in micro units of
   * the bidding strategy's currency. If set, the bid strategy will get as many
   * conversions as possible at or below the target cost-per-action. If the
   * target CPA is not set, the bid strategy will aim to achieve the lowest
   * possible CPA given the budget.
   *
   * @param string $targetCpaMicros
   */
  public function setTargetCpaMicros($targetCpaMicros)
  {
    $this->targetCpaMicros = $targetCpaMicros;
  }
  /**
   * @return string
   */
  public function getTargetCpaMicros()
  {
    return $this->targetCpaMicros;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0CommonMaximizeConversions::class, 'Google_Service_SA360_GoogleAdsSearchads360V0CommonMaximizeConversions');
