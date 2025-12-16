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

class GoogleAdsSearchads360V0CommonTargetRestriction extends \Google\Model
{
  /**
   * Not specified.
   */
  public const TARGETING_DIMENSION_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const TARGETING_DIMENSION_UNKNOWN = 'UNKNOWN';
  /**
   * Keyword criteria, for example, 'mars cruise'. KEYWORD may be used as a
   * custom bid dimension. Keywords are always a targeting dimension, so may not
   * be set as a target "ALL" dimension with TargetRestriction.
   */
  public const TARGETING_DIMENSION_KEYWORD = 'KEYWORD';
  /**
   * Audience criteria, which include user list, user interest, custom affinity,
   * and custom in market.
   */
  public const TARGETING_DIMENSION_AUDIENCE = 'AUDIENCE';
  /**
   * Topic criteria for targeting categories of content, for example,
   * 'category::Animals>Pets' Used for Display and Video targeting.
   */
  public const TARGETING_DIMENSION_TOPIC = 'TOPIC';
  /**
   * Criteria for targeting gender.
   */
  public const TARGETING_DIMENSION_GENDER = 'GENDER';
  /**
   * Criteria for targeting age ranges.
   */
  public const TARGETING_DIMENSION_AGE_RANGE = 'AGE_RANGE';
  /**
   * Placement criteria, which include websites like 'www.flowers4sale.com', as
   * well as mobile applications, mobile app categories, YouTube videos, and
   * YouTube channels.
   */
  public const TARGETING_DIMENSION_PLACEMENT = 'PLACEMENT';
  /**
   * Criteria for parental status targeting.
   */
  public const TARGETING_DIMENSION_PARENTAL_STATUS = 'PARENTAL_STATUS';
  /**
   * Criteria for income range targeting.
   */
  public const TARGETING_DIMENSION_INCOME_RANGE = 'INCOME_RANGE';
  /**
   * Indicates whether to restrict your ads to show only for the criteria you
   * have selected for this targeting_dimension, or to target all values for
   * this targeting_dimension and show ads based on your targeting in other
   * TargetingDimensions. A value of `true` means that these criteria will only
   * apply bid modifiers, and not affect targeting. A value of `false` means
   * that these criteria will restrict targeting as well as applying bid
   * modifiers.
   *
   * @var bool
   */
  public $bidOnly;
  /**
   * The targeting dimension that these settings apply to.
   *
   * @var string
   */
  public $targetingDimension;

  /**
   * Indicates whether to restrict your ads to show only for the criteria you
   * have selected for this targeting_dimension, or to target all values for
   * this targeting_dimension and show ads based on your targeting in other
   * TargetingDimensions. A value of `true` means that these criteria will only
   * apply bid modifiers, and not affect targeting. A value of `false` means
   * that these criteria will restrict targeting as well as applying bid
   * modifiers.
   *
   * @param bool $bidOnly
   */
  public function setBidOnly($bidOnly)
  {
    $this->bidOnly = $bidOnly;
  }
  /**
   * @return bool
   */
  public function getBidOnly()
  {
    return $this->bidOnly;
  }
  /**
   * The targeting dimension that these settings apply to.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, KEYWORD, AUDIENCE, TOPIC, GENDER,
   * AGE_RANGE, PLACEMENT, PARENTAL_STATUS, INCOME_RANGE
   *
   * @param self::TARGETING_DIMENSION_* $targetingDimension
   */
  public function setTargetingDimension($targetingDimension)
  {
    $this->targetingDimension = $targetingDimension;
  }
  /**
   * @return self::TARGETING_DIMENSION_*
   */
  public function getTargetingDimension()
  {
    return $this->targetingDimension;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0CommonTargetRestriction::class, 'Google_Service_SA360_GoogleAdsSearchads360V0CommonTargetRestriction');
