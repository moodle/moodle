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

class AlgorithmRulesSignal extends \Google\Model
{
  /**
   * Unknown signal.
   */
  public const ACTIVE_VIEW_SIGNAL_ACTIVE_VIEW_SIGNAL_UNSPECIFIED = 'ACTIVE_VIEW_SIGNAL_UNSPECIFIED';
  /**
   * Whether Active View detects that your ad has been viewed. Value is stored
   * in the boolValue field of the comparison value.
   */
  public const ACTIVE_VIEW_SIGNAL_ACTIVE_VIEW_VIEWED = 'ACTIVE_VIEW_VIEWED';
  /**
   * Whether Active View detects that your ad was audible. Value is stored in
   * the boolValue field of the comparison value.
   */
  public const ACTIVE_VIEW_SIGNAL_AUDIBLE = 'AUDIBLE';
  /**
   * Whether the video was completed. Value is stored in the boolValue field of
   * the comparison value.
   */
  public const ACTIVE_VIEW_SIGNAL_VIDEO_COMPLETED = 'VIDEO_COMPLETED';
  /**
   * The time the ad was on screen in seconds. Value is stored in the int64Value
   * field of the comparison value.
   */
  public const ACTIVE_VIEW_SIGNAL_TIME_ON_SCREEN = 'TIME_ON_SCREEN';
  /**
   * The size of the video player displaying the ad. Value is stored in the
   * videoPlayerSizeValue field of the comparison value.
   */
  public const ACTIVE_VIEW_SIGNAL_VIDEO_PLAYER_SIZE = 'VIDEO_PLAYER_SIZE';
  /**
   * Whether the ad was completed in view and audible. Value is stored in the
   * boolValue field of the comparison value.
   */
  public const ACTIVE_VIEW_SIGNAL_COMPLETED_IN_VIEW_AUDIBLE = 'COMPLETED_IN_VIEW_AUDIBLE';
  /**
   * Unknown signal.
   */
  public const CLICK_SIGNAL_CLICK_SIGNAL_UNSPECIFIED = 'CLICK_SIGNAL_UNSPECIFIED';
  /**
   * Whether the ad was clicked. Value is stored in the boolValue field of the
   * comparison value.
   */
  public const CLICK_SIGNAL_CLICK = 'CLICK';
  /**
   * Unknown signal.
   */
  public const IMPRESSION_SIGNAL_IMPRESSION_SIGNAL_UNSPECIFIED = 'IMPRESSION_SIGNAL_UNSPECIFIED';
  /**
   * The day of the week and hour of day the impression was made using browser's
   * local time zone. Value is stored in the dayAndTimeValue field of the
   * comparison value.
   */
  public const IMPRESSION_SIGNAL_DAY_AND_TIME = 'DAY_AND_TIME';
  /**
   * Device type. Value is stored in the deviceTypeValue field of the comparison
   * value.
   */
  public const IMPRESSION_SIGNAL_DEVICE_TYPE = 'DEVICE_TYPE';
  /**
   * Ad position. Value is stored in the onScreenPositionValue field of the
   * comparison value.
   */
  public const IMPRESSION_SIGNAL_AD_POSITION = 'AD_POSITION';
  /**
   * The operating system identifier. Value is stored in the int64Value field of
   * the comparison value.
   */
  public const IMPRESSION_SIGNAL_OPERATING_SYSTEM_ID = 'OPERATING_SYSTEM_ID';
  /**
   * The mobile model identifier. Value is stored in the int64Value field of the
   * comparison value.
   */
  public const IMPRESSION_SIGNAL_MOBILE_MODEL_ID = 'MOBILE_MODEL_ID';
  /**
   * Exchange. Value is stored in the exchangeValue field of the comparison
   * value.
   */
  public const IMPRESSION_SIGNAL_EXCHANGE = 'EXCHANGE';
  /**
   * Serving environment. Value is stored in the environmentValue field of the
   * comparison value.
   */
  public const IMPRESSION_SIGNAL_ENVIRONMENT = 'ENVIRONMENT';
  /**
   * The country or region identifier. Value is stored in the int64Value field
   * of the comparison value.
   */
  public const IMPRESSION_SIGNAL_COUNTRY_ID = 'COUNTRY_ID';
  /**
   * The city identifier. Value is stored in the int64Value field of the
   * comparison value.
   */
  public const IMPRESSION_SIGNAL_CITY_ID = 'CITY_ID';
  /**
   * The browser identifier. Value is stored in the int64Value field of the
   * comparison value.
   */
  public const IMPRESSION_SIGNAL_BROWSER_ID = 'BROWSER_ID';
  /**
   * Creative height and width in pixels. Value is stored in the
   * creativeDimensionValue field of the comparison value.
   */
  public const IMPRESSION_SIGNAL_CREATIVE_DIMENSION = 'CREATIVE_DIMENSION';
  /**
   * Video content duration. Value is stored in the contentDurationValue field
   * of the comparison value. The comparisonOperator field must be set to
   * `LIST_CONTAINS`.
   */
  public const IMPRESSION_SIGNAL_VIDEO_CONTENT_DURATION_BUCKET = 'VIDEO_CONTENT_DURATION_BUCKET';
  /**
   * Video delivery type. Value is stored in the contentStreamTypeValue field of
   * the comparison value. The comparisonOperator field must be set to
   * `LIST_CONTAINS`.
   */
  public const IMPRESSION_SIGNAL_VIDEO_DELIVERY_TYPE = 'VIDEO_DELIVERY_TYPE';
  /**
   * Video genre id. Value is stored in the contentGenreIdValue field of the
   * comparison value. The comparisonOperator field must be set to
   * `LIST_CONTAINS`.
   */
  public const IMPRESSION_SIGNAL_VIDEO_GENRE_ID = 'VIDEO_GENRE_ID';
  /**
   * Signal based on active views. This field is only supported for allowlisted
   * partners.
   *
   * @var string
   */
  public $activeViewSignal;
  /**
   * Signal based on clicks. This field is only supported for allowlisted
   * partners.
   *
   * @var string
   */
  public $clickSignal;
  /**
   * Signal based on impressions.
   *
   * @var string
   */
  public $impressionSignal;

  /**
   * Signal based on active views. This field is only supported for allowlisted
   * partners.
   *
   * Accepted values: ACTIVE_VIEW_SIGNAL_UNSPECIFIED, ACTIVE_VIEW_VIEWED,
   * AUDIBLE, VIDEO_COMPLETED, TIME_ON_SCREEN, VIDEO_PLAYER_SIZE,
   * COMPLETED_IN_VIEW_AUDIBLE
   *
   * @param self::ACTIVE_VIEW_SIGNAL_* $activeViewSignal
   */
  public function setActiveViewSignal($activeViewSignal)
  {
    $this->activeViewSignal = $activeViewSignal;
  }
  /**
   * @return self::ACTIVE_VIEW_SIGNAL_*
   */
  public function getActiveViewSignal()
  {
    return $this->activeViewSignal;
  }
  /**
   * Signal based on clicks. This field is only supported for allowlisted
   * partners.
   *
   * Accepted values: CLICK_SIGNAL_UNSPECIFIED, CLICK
   *
   * @param self::CLICK_SIGNAL_* $clickSignal
   */
  public function setClickSignal($clickSignal)
  {
    $this->clickSignal = $clickSignal;
  }
  /**
   * @return self::CLICK_SIGNAL_*
   */
  public function getClickSignal()
  {
    return $this->clickSignal;
  }
  /**
   * Signal based on impressions.
   *
   * Accepted values: IMPRESSION_SIGNAL_UNSPECIFIED, DAY_AND_TIME, DEVICE_TYPE,
   * AD_POSITION, OPERATING_SYSTEM_ID, MOBILE_MODEL_ID, EXCHANGE, ENVIRONMENT,
   * COUNTRY_ID, CITY_ID, BROWSER_ID, CREATIVE_DIMENSION,
   * VIDEO_CONTENT_DURATION_BUCKET, VIDEO_DELIVERY_TYPE, VIDEO_GENRE_ID
   *
   * @param self::IMPRESSION_SIGNAL_* $impressionSignal
   */
  public function setImpressionSignal($impressionSignal)
  {
    $this->impressionSignal = $impressionSignal;
  }
  /**
   * @return self::IMPRESSION_SIGNAL_*
   */
  public function getImpressionSignal()
  {
    return $this->impressionSignal;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AlgorithmRulesSignal::class, 'Google_Service_DisplayVideo_AlgorithmRulesSignal');
