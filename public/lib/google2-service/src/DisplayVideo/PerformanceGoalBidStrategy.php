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

class PerformanceGoalBidStrategy extends \Google\Model
{
  /**
   * Type value is not specified or is unknown in this version.
   */
  public const PERFORMANCE_GOAL_TYPE_BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_UNSPECIFIED = 'BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_UNSPECIFIED';
  /**
   * Cost per action.
   */
  public const PERFORMANCE_GOAL_TYPE_BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CPA = 'BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CPA';
  /**
   * Cost per click.
   */
  public const PERFORMANCE_GOAL_TYPE_BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CPC = 'BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CPC';
  /**
   * Viewable CPM.
   */
  public const PERFORMANCE_GOAL_TYPE_BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_VIEWABLE_CPM = 'BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_VIEWABLE_CPM';
  /**
   * Custom bidding algorithm.
   */
  public const PERFORMANCE_GOAL_TYPE_BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CUSTOM_ALGO = 'BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CUSTOM_ALGO';
  /**
   * Completed inview and audible views.
   */
  public const PERFORMANCE_GOAL_TYPE_BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CIVA = 'BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CIVA';
  /**
   * Inview time over 10 secs views.
   */
  public const PERFORMANCE_GOAL_TYPE_BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_IVO_TEN = 'BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_IVO_TEN';
  /**
   * Viewable impressions.
   */
  public const PERFORMANCE_GOAL_TYPE_BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_AV_VIEWED = 'BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_AV_VIEWED';
  /**
   * Maximize reach.
   */
  public const PERFORMANCE_GOAL_TYPE_BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_REACH = 'BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_REACH';
  /**
   * The ID of the Custom Bidding Algorithm used by this strategy. Only
   * applicable when performance_goal_type is set to
   * `BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CUSTOM_ALGO`. Assigning a custom
   * bidding algorithm that uses floodlight activities not identified in
   * floodlightActivityConfigs will return an error.
   *
   * @var string
   */
  public $customBiddingAlgorithmId;
  /**
   * The maximum average CPM that may be bid, in micros of the advertiser's
   * currency. Must be greater than or equal to a billable unit of the given
   * currency. Not applicable when performance_goal_type is set to
   * `BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_VIEWABLE_CPM`. For example, 1500000
   * represents 1.5 standard units of the currency.
   *
   * @var string
   */
  public $maxAverageCpmBidAmountMicros;
  /**
   * Required. The performance goal the bidding strategy will attempt to meet or
   * beat, in micros of the advertiser's currency or in micro of the ROAS
   * (Return On Advertising Spend) value which is also based on advertiser's
   * currency. Must be greater than or equal to a billable unit of the given
   * currency and smaller or equal to upper bounds. Each performance_goal_type
   * has its upper bound: * when performance_goal_type is
   * `BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CPA`, upper bound is 10000.00 USD.
   * * when performance_goal_type is
   * `BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CPC`, upper bound is 1000.00 USD. *
   * when performance_goal_type is
   * `BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_VIEWABLE_CPM`, upper bound is
   * 1000.00 USD. * when performance_goal_type is
   * `BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CUSTOM_ALGO`, upper bound is
   * 1000.00 and lower bound is 0.01. Example: If set to
   * `BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_VIEWABLE_CPM`, the bid price will
   * be based on the probability that each available impression will be
   * viewable. For example, if viewable CPM target is $2 and an impression is
   * 40% likely to be viewable, the bid price will be $0.80 CPM (40% of $2). For
   * example, 1500000 represents 1.5 standard units of the currency or ROAS
   * value.
   *
   * @var string
   */
  public $performanceGoalAmountMicros;
  /**
   * Required. The type of the performance goal that the bidding strategy will
   * try to meet or beat. For line item level usage, the value must be one of: *
   * `BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CPA` *
   * `BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CPC` *
   * `BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_VIEWABLE_CPM` *
   * `BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CUSTOM_ALGO`.
   *
   * @var string
   */
  public $performanceGoalType;

  /**
   * The ID of the Custom Bidding Algorithm used by this strategy. Only
   * applicable when performance_goal_type is set to
   * `BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CUSTOM_ALGO`. Assigning a custom
   * bidding algorithm that uses floodlight activities not identified in
   * floodlightActivityConfigs will return an error.
   *
   * @param string $customBiddingAlgorithmId
   */
  public function setCustomBiddingAlgorithmId($customBiddingAlgorithmId)
  {
    $this->customBiddingAlgorithmId = $customBiddingAlgorithmId;
  }
  /**
   * @return string
   */
  public function getCustomBiddingAlgorithmId()
  {
    return $this->customBiddingAlgorithmId;
  }
  /**
   * The maximum average CPM that may be bid, in micros of the advertiser's
   * currency. Must be greater than or equal to a billable unit of the given
   * currency. Not applicable when performance_goal_type is set to
   * `BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_VIEWABLE_CPM`. For example, 1500000
   * represents 1.5 standard units of the currency.
   *
   * @param string $maxAverageCpmBidAmountMicros
   */
  public function setMaxAverageCpmBidAmountMicros($maxAverageCpmBidAmountMicros)
  {
    $this->maxAverageCpmBidAmountMicros = $maxAverageCpmBidAmountMicros;
  }
  /**
   * @return string
   */
  public function getMaxAverageCpmBidAmountMicros()
  {
    return $this->maxAverageCpmBidAmountMicros;
  }
  /**
   * Required. The performance goal the bidding strategy will attempt to meet or
   * beat, in micros of the advertiser's currency or in micro of the ROAS
   * (Return On Advertising Spend) value which is also based on advertiser's
   * currency. Must be greater than or equal to a billable unit of the given
   * currency and smaller or equal to upper bounds. Each performance_goal_type
   * has its upper bound: * when performance_goal_type is
   * `BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CPA`, upper bound is 10000.00 USD.
   * * when performance_goal_type is
   * `BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CPC`, upper bound is 1000.00 USD. *
   * when performance_goal_type is
   * `BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_VIEWABLE_CPM`, upper bound is
   * 1000.00 USD. * when performance_goal_type is
   * `BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CUSTOM_ALGO`, upper bound is
   * 1000.00 and lower bound is 0.01. Example: If set to
   * `BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_VIEWABLE_CPM`, the bid price will
   * be based on the probability that each available impression will be
   * viewable. For example, if viewable CPM target is $2 and an impression is
   * 40% likely to be viewable, the bid price will be $0.80 CPM (40% of $2). For
   * example, 1500000 represents 1.5 standard units of the currency or ROAS
   * value.
   *
   * @param string $performanceGoalAmountMicros
   */
  public function setPerformanceGoalAmountMicros($performanceGoalAmountMicros)
  {
    $this->performanceGoalAmountMicros = $performanceGoalAmountMicros;
  }
  /**
   * @return string
   */
  public function getPerformanceGoalAmountMicros()
  {
    return $this->performanceGoalAmountMicros;
  }
  /**
   * Required. The type of the performance goal that the bidding strategy will
   * try to meet or beat. For line item level usage, the value must be one of: *
   * `BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CPA` *
   * `BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CPC` *
   * `BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_VIEWABLE_CPM` *
   * `BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CUSTOM_ALGO`.
   *
   * Accepted values: BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_UNSPECIFIED,
   * BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CPA,
   * BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CPC,
   * BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_VIEWABLE_CPM,
   * BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CUSTOM_ALGO,
   * BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_CIVA,
   * BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_IVO_TEN,
   * BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_AV_VIEWED,
   * BIDDING_STRATEGY_PERFORMANCE_GOAL_TYPE_REACH
   *
   * @param self::PERFORMANCE_GOAL_TYPE_* $performanceGoalType
   */
  public function setPerformanceGoalType($performanceGoalType)
  {
    $this->performanceGoalType = $performanceGoalType;
  }
  /**
   * @return self::PERFORMANCE_GOAL_TYPE_*
   */
  public function getPerformanceGoalType()
  {
    return $this->performanceGoalType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PerformanceGoalBidStrategy::class, 'Google_Service_DisplayVideo_PerformanceGoalBidStrategy');
