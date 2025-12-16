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

class DoubleVerifyDisplayViewability extends \Google\Model
{
  /**
   * This enum is only a placeholder and it doesn't specify any IAB viewed rate
   * options.
   */
  public const IAB_IAB_VIEWED_RATE_UNSPECIFIED = 'IAB_VIEWED_RATE_UNSPECIFIED';
  /**
   * Target web and app inventory to maximize IAB viewable rate 80% or higher.
   */
  public const IAB_IAB_VIEWED_RATE_80_PERCENT_HIGHER = 'IAB_VIEWED_RATE_80_PERCENT_HIGHER';
  /**
   * Target web and app inventory to maximize IAB viewable rate 75% or higher.
   */
  public const IAB_IAB_VIEWED_RATE_75_PERCENT_HIGHER = 'IAB_VIEWED_RATE_75_PERCENT_HIGHER';
  /**
   * Target web and app inventory to maximize IAB viewable rate 70% or higher.
   */
  public const IAB_IAB_VIEWED_RATE_70_PERCENT_HIGHER = 'IAB_VIEWED_RATE_70_PERCENT_HIGHER';
  /**
   * Target web and app inventory to maximize IAB viewable rate 65% or higher.
   */
  public const IAB_IAB_VIEWED_RATE_65_PERCENT_HIGHER = 'IAB_VIEWED_RATE_65_PERCENT_HIGHER';
  /**
   * Target web and app inventory to maximize IAB viewable rate 60% or higher.
   */
  public const IAB_IAB_VIEWED_RATE_60_PERCENT_HIGHER = 'IAB_VIEWED_RATE_60_PERCENT_HIGHER';
  /**
   * Target web and app inventory to maximize IAB viewable rate 55% or higher.
   */
  public const IAB_IAB_VIEWED_RATE_55_PERCENT_HIGHER = 'IAB_VIEWED_RATE_55_PERCENT_HIGHER';
  /**
   * Target web and app inventory to maximize IAB viewable rate 50% or higher.
   */
  public const IAB_IAB_VIEWED_RATE_50_PERCENT_HIGHER = 'IAB_VIEWED_RATE_50_PERCENT_HIGHER';
  /**
   * Target web and app inventory to maximize IAB viewable rate 40% or higher.
   */
  public const IAB_IAB_VIEWED_RATE_40_PERCENT_HIGHER = 'IAB_VIEWED_RATE_40_PERCENT_HIGHER';
  /**
   * Target web and app inventory to maximize IAB viewable rate 30% or higher.
   */
  public const IAB_IAB_VIEWED_RATE_30_PERCENT_HIGHER = 'IAB_VIEWED_RATE_30_PERCENT_HIGHER';
  /**
   * This enum is only a placeholder and it doesn't specify any average view
   * duration options.
   */
  public const VIEWABLE_DURING_AVERAGE_VIEW_DURATION_UNSPECIFIED = 'AVERAGE_VIEW_DURATION_UNSPECIFIED';
  /**
   * Target web and app inventory to maximize 100% viewable duration 5 seconds
   * or more.
   */
  public const VIEWABLE_DURING_AVERAGE_VIEW_DURATION_5_SEC = 'AVERAGE_VIEW_DURATION_5_SEC';
  /**
   * Target web and app inventory to maximize 100% viewable duration 10 seconds
   * or more.
   */
  public const VIEWABLE_DURING_AVERAGE_VIEW_DURATION_10_SEC = 'AVERAGE_VIEW_DURATION_10_SEC';
  /**
   * Target web and app inventory to maximize 100% viewable duration 15 seconds
   * or more.
   */
  public const VIEWABLE_DURING_AVERAGE_VIEW_DURATION_15_SEC = 'AVERAGE_VIEW_DURATION_15_SEC';
  /**
   * Target web and app inventory to maximize IAB viewable rate.
   *
   * @var string
   */
  public $iab;
  /**
   * Target web and app inventory to maximize 100% viewable duration.
   *
   * @var string
   */
  public $viewableDuring;

  /**
   * Target web and app inventory to maximize IAB viewable rate.
   *
   * Accepted values: IAB_VIEWED_RATE_UNSPECIFIED,
   * IAB_VIEWED_RATE_80_PERCENT_HIGHER, IAB_VIEWED_RATE_75_PERCENT_HIGHER,
   * IAB_VIEWED_RATE_70_PERCENT_HIGHER, IAB_VIEWED_RATE_65_PERCENT_HIGHER,
   * IAB_VIEWED_RATE_60_PERCENT_HIGHER, IAB_VIEWED_RATE_55_PERCENT_HIGHER,
   * IAB_VIEWED_RATE_50_PERCENT_HIGHER, IAB_VIEWED_RATE_40_PERCENT_HIGHER,
   * IAB_VIEWED_RATE_30_PERCENT_HIGHER
   *
   * @param self::IAB_* $iab
   */
  public function setIab($iab)
  {
    $this->iab = $iab;
  }
  /**
   * @return self::IAB_*
   */
  public function getIab()
  {
    return $this->iab;
  }
  /**
   * Target web and app inventory to maximize 100% viewable duration.
   *
   * Accepted values: AVERAGE_VIEW_DURATION_UNSPECIFIED,
   * AVERAGE_VIEW_DURATION_5_SEC, AVERAGE_VIEW_DURATION_10_SEC,
   * AVERAGE_VIEW_DURATION_15_SEC
   *
   * @param self::VIEWABLE_DURING_* $viewableDuring
   */
  public function setViewableDuring($viewableDuring)
  {
    $this->viewableDuring = $viewableDuring;
  }
  /**
   * @return self::VIEWABLE_DURING_*
   */
  public function getViewableDuring()
  {
    return $this->viewableDuring;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DoubleVerifyDisplayViewability::class, 'Google_Service_DisplayVideo_DoubleVerifyDisplayViewability');
