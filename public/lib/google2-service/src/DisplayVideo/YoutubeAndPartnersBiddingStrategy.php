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

namespace Google\Service\DisplayVideo;

class YoutubeAndPartnersBiddingStrategy extends \Google\Model
{
  /**
   * Bidding source is not specified or unknown.
   */
  public const AD_GROUP_EFFECTIVE_TARGET_CPA_SOURCE_BIDDING_SOURCE_UNSPECIFIED = 'BIDDING_SOURCE_UNSPECIFIED';
  /**
   * Bidding value is inherited from the line item.
   */
  public const AD_GROUP_EFFECTIVE_TARGET_CPA_SOURCE_BIDDING_SOURCE_LINE_ITEM = 'BIDDING_SOURCE_LINE_ITEM';
  /**
   * Bidding value is defined in the ad group.
   */
  public const AD_GROUP_EFFECTIVE_TARGET_CPA_SOURCE_BIDDING_SOURCE_AD_GROUP = 'BIDDING_SOURCE_AD_GROUP';
  /**
   * Type is not specified or unknown.
   */
  public const TYPE_YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_UNSPECIFIED = 'YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_UNSPECIFIED';
  /**
   * A bidding strategy that pays a configurable amount per video view.
   */
  public const TYPE_YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_MANUAL_CPV = 'YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_MANUAL_CPV';
  /**
   * A bidding strategy that pays a configurable amount per impression.
   */
  public const TYPE_YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_MANUAL_CPM = 'YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_MANUAL_CPM';
  /**
   * A bidding strategy that automatically optimizes conversions per dollar.
   */
  public const TYPE_YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_TARGET_CPA = 'YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_TARGET_CPA';
  /**
   * A bidding strategy that pays a configurable amount per impression.
   */
  public const TYPE_YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_TARGET_CPM = 'YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_TARGET_CPM';
  /**
   * A bidding strategy for YouTube Instant Reserve line items that pays a fixed
   * amount per impression.
   */
  public const TYPE_YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_RESERVE_CPM = 'YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_RESERVE_CPM';
  /**
   * An automated bidding strategy that sets bids to achieve maximum lift.
   */
  public const TYPE_YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_MAXIMIZE_LIFT = 'YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_MAXIMIZE_LIFT';
  /**
   * A bidding strategy that automatically maximizes number of conversions given
   * a daily budget.
   */
  public const TYPE_YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_MAXIMIZE_CONVERSIONS = 'YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_MAXIMIZE_CONVERSIONS';
  /**
   * A bidding strategy that automatically optimizes cost per video view.
   */
  public const TYPE_YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_TARGET_CPV = 'YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_TARGET_CPV';
  /**
   * A bidding strategy that automatically maximizes revenue while averaging a
   * specific target Return On Ad Spend (ROAS).
   */
  public const TYPE_YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_TARGET_ROAS = 'YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_TARGET_ROAS';
  /**
   * A bidding strategy that automatically sets bids to maximize revenue while
   * spending your budget.
   */
  public const TYPE_YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_MAXIMIZE_CONVERSION_VALUE = 'YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_MAXIMIZE_CONVERSION_VALUE';
  /**
   * Output only. Source of the effective target CPA value for ad group.
   *
   * @var string
   */
  public $adGroupEffectiveTargetCpaSource;
  /**
   * Output only. The effective target CPA for ad group, in micros of
   * advertiser's currency.
   *
   * @var string
   */
  public $adGroupEffectiveTargetCpaValue;
  /**
   * The type of the bidding strategy.
   *
   * @var string
   */
  public $type;
  /**
   * The value used by the bidding strategy. When the bidding strategy is
   * assigned at the line item level, this field is only applicable for the
   * following strategy types: *
   * `YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_TARGET_CPA` *
   * `YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_TARGET_ROAS` *
   * `YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_RESERVE_SHARE_OF_VOICE` When
   * the bidding strategy is assigned at the ad group level, this field is only
   * applicable for the following strategy types: *
   * `YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_MANUAL_CPM` *
   * `YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_MANUAL_CPV` *
   * `YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_TARGET_CPA` *
   * `YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_TARGET_CPM` *
   * `YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_RESERVE_CPM` *
   * `YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_TARGET_ROAS` If not using an
   * applicable strategy, the value of this field will be 0.
   *
   * @var string
   */
  public $value;

  /**
   * Output only. Source of the effective target CPA value for ad group.
   *
   * Accepted values: BIDDING_SOURCE_UNSPECIFIED, BIDDING_SOURCE_LINE_ITEM,
   * BIDDING_SOURCE_AD_GROUP
   *
   * @param self::AD_GROUP_EFFECTIVE_TARGET_CPA_SOURCE_* $adGroupEffectiveTargetCpaSource
   */
  public function setAdGroupEffectiveTargetCpaSource($adGroupEffectiveTargetCpaSource)
  {
    $this->adGroupEffectiveTargetCpaSource = $adGroupEffectiveTargetCpaSource;
  }
  /**
   * @return self::AD_GROUP_EFFECTIVE_TARGET_CPA_SOURCE_*
   */
  public function getAdGroupEffectiveTargetCpaSource()
  {
    return $this->adGroupEffectiveTargetCpaSource;
  }
  /**
   * Output only. The effective target CPA for ad group, in micros of
   * advertiser's currency.
   *
   * @param string $adGroupEffectiveTargetCpaValue
   */
  public function setAdGroupEffectiveTargetCpaValue($adGroupEffectiveTargetCpaValue)
  {
    $this->adGroupEffectiveTargetCpaValue = $adGroupEffectiveTargetCpaValue;
  }
  /**
   * @return string
   */
  public function getAdGroupEffectiveTargetCpaValue()
  {
    return $this->adGroupEffectiveTargetCpaValue;
  }
  /**
   * The type of the bidding strategy.
   *
   * Accepted values: YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_UNSPECIFIED,
   * YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_MANUAL_CPV,
   * YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_MANUAL_CPM,
   * YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_TARGET_CPA,
   * YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_TARGET_CPM,
   * YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_RESERVE_CPM,
   * YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_MAXIMIZE_LIFT,
   * YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_MAXIMIZE_CONVERSIONS,
   * YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_TARGET_CPV,
   * YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_TARGET_ROAS,
   * YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_MAXIMIZE_CONVERSION_VALUE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * The value used by the bidding strategy. When the bidding strategy is
   * assigned at the line item level, this field is only applicable for the
   * following strategy types: *
   * `YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_TARGET_CPA` *
   * `YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_TARGET_ROAS` *
   * `YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_RESERVE_SHARE_OF_VOICE` When
   * the bidding strategy is assigned at the ad group level, this field is only
   * applicable for the following strategy types: *
   * `YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_MANUAL_CPM` *
   * `YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_MANUAL_CPV` *
   * `YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_TARGET_CPA` *
   * `YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_TARGET_CPM` *
   * `YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_RESERVE_CPM` *
   * `YOUTUBE_AND_PARTNERS_BIDDING_STRATEGY_TYPE_TARGET_ROAS` If not using an
   * applicable strategy, the value of this field will be 0.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(YoutubeAndPartnersBiddingStrategy::class, 'Google_Service_DisplayVideo_YoutubeAndPartnersBiddingStrategy');
