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

class GoogleAdsSearchads360V0CommonPercentCpc extends \Google\Model
{
  /**
   * Maximum bid limit that can be set by the bid strategy. This is an optional
   * field entered by the advertiser and specified in local micros. Note: A zero
   * value is interpreted in the same way as having bid_ceiling undefined.
   *
   * @var string
   */
  public $cpcBidCeilingMicros;
  /**
   * Adjusts the bid for each auction upward or downward, depending on the
   * likelihood of a conversion. Individual bids may exceed
   * cpc_bid_ceiling_micros, but the average bid amount for a campaign should
   * not.
   *
   * @var bool
   */
  public $enhancedCpcEnabled;

  /**
   * Maximum bid limit that can be set by the bid strategy. This is an optional
   * field entered by the advertiser and specified in local micros. Note: A zero
   * value is interpreted in the same way as having bid_ceiling undefined.
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
   * Adjusts the bid for each auction upward or downward, depending on the
   * likelihood of a conversion. Individual bids may exceed
   * cpc_bid_ceiling_micros, but the average bid amount for a campaign should
   * not.
   *
   * @param bool $enhancedCpcEnabled
   */
  public function setEnhancedCpcEnabled($enhancedCpcEnabled)
  {
    $this->enhancedCpcEnabled = $enhancedCpcEnabled;
  }
  /**
   * @return bool
   */
  public function getEnhancedCpcEnabled()
  {
    return $this->enhancedCpcEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0CommonPercentCpc::class, 'Google_Service_SA360_GoogleAdsSearchads360V0CommonPercentCpc');
