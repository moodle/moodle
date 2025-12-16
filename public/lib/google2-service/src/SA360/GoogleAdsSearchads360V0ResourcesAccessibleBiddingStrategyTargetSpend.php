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

class GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyTargetSpend extends \Google\Model
{
  /**
   * Output only. Maximum bid limit that can be set by the bid strategy. The
   * limit applies to all keywords managed by the strategy.
   *
   * @var string
   */
  public $cpcBidCeilingMicros;
  /**
   * Output only. The spend target under which to maximize clicks. A TargetSpend
   * bidder will attempt to spend the smaller of this value or the natural
   * throttling spend amount. If not specified, the budget is used as the spend
   * target. This field is deprecated and should no longer be used. See
   * https://ads-developers.googleblog.com/2020/05/reminder-about-sunset-
   * creation-of.html for details.
   *
   * @deprecated
   * @var string
   */
  public $targetSpendMicros;

  /**
   * Output only. Maximum bid limit that can be set by the bid strategy. The
   * limit applies to all keywords managed by the strategy.
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
   * Output only. The spend target under which to maximize clicks. A TargetSpend
   * bidder will attempt to spend the smaller of this value or the natural
   * throttling spend amount. If not specified, the budget is used as the spend
   * target. This field is deprecated and should no longer be used. See
   * https://ads-developers.googleblog.com/2020/05/reminder-about-sunset-
   * creation-of.html for details.
   *
   * @deprecated
   * @param string $targetSpendMicros
   */
  public function setTargetSpendMicros($targetSpendMicros)
  {
    $this->targetSpendMicros = $targetSpendMicros;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getTargetSpendMicros()
  {
    return $this->targetSpendMicros;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyTargetSpend::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyTargetSpend');
