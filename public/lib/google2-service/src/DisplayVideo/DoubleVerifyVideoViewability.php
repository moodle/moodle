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

class DoubleVerifyVideoViewability extends \Google\Model
{
  /**
   * This enum is only a placeholder and it doesn't specify any impressions
   * options.
   */
  public const PLAYER_IMPRESSION_RATE_PLAYER_SIZE_400X300_UNSPECIFIED = 'PLAYER_SIZE_400X300_UNSPECIFIED';
  /**
   * Sites with 95%+ of impressions.
   */
  public const PLAYER_IMPRESSION_RATE_PLAYER_SIZE_400X300_95 = 'PLAYER_SIZE_400X300_95';
  /**
   * Sites with 70%+ of impressions.
   */
  public const PLAYER_IMPRESSION_RATE_PLAYER_SIZE_400X300_70 = 'PLAYER_SIZE_400X300_70';
  /**
   * Sites with 25%+ of impressions.
   */
  public const PLAYER_IMPRESSION_RATE_PLAYER_SIZE_400X300_25 = 'PLAYER_SIZE_400X300_25';
  /**
   * Sites with 5%+ of impressions.
   */
  public const PLAYER_IMPRESSION_RATE_PLAYER_SIZE_400X300_5 = 'PLAYER_SIZE_400X300_5';
  /**
   * This enum is only a placeholder and it doesn't specify any video IAB
   * viewable rate options.
   */
  public const VIDEO_IAB_VIDEO_IAB_UNSPECIFIED = 'VIDEO_IAB_UNSPECIFIED';
  /**
   * Target web and app inventory to maximize IAB viewable rate 80% or higher.
   */
  public const VIDEO_IAB_IAB_VIEWABILITY_80_PERCENT_HIGHER = 'IAB_VIEWABILITY_80_PERCENT_HIGHER';
  /**
   * Target web and app inventory to maximize IAB viewable rate 75% or higher.
   */
  public const VIDEO_IAB_IAB_VIEWABILITY_75_PERCENT_HIGHER = 'IAB_VIEWABILITY_75_PERCENT_HIGHER';
  /**
   * Target web and app inventory to maximize IAB viewable rate 70% or higher.
   */
  public const VIDEO_IAB_IAB_VIEWABILITY_70_PERCENT_HIGHER = 'IAB_VIEWABILITY_70_PERCENT_HIGHER';
  /**
   * Target web and app inventory to maximize IAB viewable rate 65% or higher.
   */
  public const VIDEO_IAB_IAB_VIEWABILITY_65_PERCENT_HIHGER = 'IAB_VIEWABILITY_65_PERCENT_HIHGER';
  /**
   * Target web and app inventory to maximize IAB viewable rate 60% or higher.
   */
  public const VIDEO_IAB_IAB_VIEWABILITY_60_PERCENT_HIGHER = 'IAB_VIEWABILITY_60_PERCENT_HIGHER';
  /**
   * Target web and app inventory to maximize IAB viewable rate 55% or higher.
   */
  public const VIDEO_IAB_IAB_VIEWABILITY_55_PERCENT_HIHGER = 'IAB_VIEWABILITY_55_PERCENT_HIHGER';
  /**
   * Target web and app inventory to maximize IAB viewable rate 50% or higher.
   */
  public const VIDEO_IAB_IAB_VIEWABILITY_50_PERCENT_HIGHER = 'IAB_VIEWABILITY_50_PERCENT_HIGHER';
  /**
   * Target web and app inventory to maximize IAB viewable rate 40% or higher.
   */
  public const VIDEO_IAB_IAB_VIEWABILITY_40_PERCENT_HIHGER = 'IAB_VIEWABILITY_40_PERCENT_HIHGER';
  /**
   * Target web and app inventory to maximize IAB viewable rate 30% or higher.
   */
  public const VIDEO_IAB_IAB_VIEWABILITY_30_PERCENT_HIHGER = 'IAB_VIEWABILITY_30_PERCENT_HIHGER';
  /**
   * This enum is only a placeholder and it doesn't specify any video viewable
   * rate options.
   */
  public const VIDEO_VIEWABLE_RATE_VIDEO_VIEWABLE_RATE_UNSPECIFIED = 'VIDEO_VIEWABLE_RATE_UNSPECIFIED';
  /**
   * Target web inventory to maximize fully viewable rate 40% or higher.
   */
  public const VIDEO_VIEWABLE_RATE_VIEWED_PERFORMANCE_40_PERCENT_HIGHER = 'VIEWED_PERFORMANCE_40_PERCENT_HIGHER';
  /**
   * Target web inventory to maximize fully viewable rate 35% or higher.
   */
  public const VIDEO_VIEWABLE_RATE_VIEWED_PERFORMANCE_35_PERCENT_HIGHER = 'VIEWED_PERFORMANCE_35_PERCENT_HIGHER';
  /**
   * Target web inventory to maximize fully viewable rate 30% or higher.
   */
  public const VIDEO_VIEWABLE_RATE_VIEWED_PERFORMANCE_30_PERCENT_HIGHER = 'VIEWED_PERFORMANCE_30_PERCENT_HIGHER';
  /**
   * Target web inventory to maximize fully viewable rate 25% or higher.
   */
  public const VIDEO_VIEWABLE_RATE_VIEWED_PERFORMANCE_25_PERCENT_HIGHER = 'VIEWED_PERFORMANCE_25_PERCENT_HIGHER';
  /**
   * Target web inventory to maximize fully viewable rate 20% or higher.
   */
  public const VIDEO_VIEWABLE_RATE_VIEWED_PERFORMANCE_20_PERCENT_HIGHER = 'VIEWED_PERFORMANCE_20_PERCENT_HIGHER';
  /**
   * Target web inventory to maximize fully viewable rate 10% or higher.
   */
  public const VIDEO_VIEWABLE_RATE_VIEWED_PERFORMANCE_10_PERCENT_HIGHER = 'VIEWED_PERFORMANCE_10_PERCENT_HIGHER';
  /**
   * Target inventory to maximize impressions with 400x300 or greater player
   * size.
   *
   * @var string
   */
  public $playerImpressionRate;
  /**
   * Target web inventory to maximize IAB viewable rate.
   *
   * @var string
   */
  public $videoIab;
  /**
   * Target web inventory to maximize fully viewable rate.
   *
   * @var string
   */
  public $videoViewableRate;

  /**
   * Target inventory to maximize impressions with 400x300 or greater player
   * size.
   *
   * Accepted values: PLAYER_SIZE_400X300_UNSPECIFIED, PLAYER_SIZE_400X300_95,
   * PLAYER_SIZE_400X300_70, PLAYER_SIZE_400X300_25, PLAYER_SIZE_400X300_5
   *
   * @param self::PLAYER_IMPRESSION_RATE_* $playerImpressionRate
   */
  public function setPlayerImpressionRate($playerImpressionRate)
  {
    $this->playerImpressionRate = $playerImpressionRate;
  }
  /**
   * @return self::PLAYER_IMPRESSION_RATE_*
   */
  public function getPlayerImpressionRate()
  {
    return $this->playerImpressionRate;
  }
  /**
   * Target web inventory to maximize IAB viewable rate.
   *
   * Accepted values: VIDEO_IAB_UNSPECIFIED, IAB_VIEWABILITY_80_PERCENT_HIGHER,
   * IAB_VIEWABILITY_75_PERCENT_HIGHER, IAB_VIEWABILITY_70_PERCENT_HIGHER,
   * IAB_VIEWABILITY_65_PERCENT_HIHGER, IAB_VIEWABILITY_60_PERCENT_HIGHER,
   * IAB_VIEWABILITY_55_PERCENT_HIHGER, IAB_VIEWABILITY_50_PERCENT_HIGHER,
   * IAB_VIEWABILITY_40_PERCENT_HIHGER, IAB_VIEWABILITY_30_PERCENT_HIHGER
   *
   * @param self::VIDEO_IAB_* $videoIab
   */
  public function setVideoIab($videoIab)
  {
    $this->videoIab = $videoIab;
  }
  /**
   * @return self::VIDEO_IAB_*
   */
  public function getVideoIab()
  {
    return $this->videoIab;
  }
  /**
   * Target web inventory to maximize fully viewable rate.
   *
   * Accepted values: VIDEO_VIEWABLE_RATE_UNSPECIFIED,
   * VIEWED_PERFORMANCE_40_PERCENT_HIGHER, VIEWED_PERFORMANCE_35_PERCENT_HIGHER,
   * VIEWED_PERFORMANCE_30_PERCENT_HIGHER, VIEWED_PERFORMANCE_25_PERCENT_HIGHER,
   * VIEWED_PERFORMANCE_20_PERCENT_HIGHER, VIEWED_PERFORMANCE_10_PERCENT_HIGHER
   *
   * @param self::VIDEO_VIEWABLE_RATE_* $videoViewableRate
   */
  public function setVideoViewableRate($videoViewableRate)
  {
    $this->videoViewableRate = $videoViewableRate;
  }
  /**
   * @return self::VIDEO_VIEWABLE_RATE_*
   */
  public function getVideoViewableRate()
  {
    return $this->videoViewableRate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DoubleVerifyVideoViewability::class, 'Google_Service_DisplayVideo_DoubleVerifyVideoViewability');
