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

class ActiveViewVideoViewabilityMetricConfig extends \Google\Model
{
  /**
   * Value is not specified or is unknown in this version.
   */
  public const MINIMUM_DURATION_VIDEO_DURATION_UNSPECIFIED = 'VIDEO_DURATION_UNSPECIFIED';
  /**
   * No duration value.
   */
  public const MINIMUM_DURATION_VIDEO_DURATION_SECONDS_NONE = 'VIDEO_DURATION_SECONDS_NONE';
  /**
   * 0 seconds.
   */
  public const MINIMUM_DURATION_VIDEO_DURATION_SECONDS_0 = 'VIDEO_DURATION_SECONDS_0';
  /**
   * 1 second.
   */
  public const MINIMUM_DURATION_VIDEO_DURATION_SECONDS_1 = 'VIDEO_DURATION_SECONDS_1';
  /**
   * 2 seconds.
   */
  public const MINIMUM_DURATION_VIDEO_DURATION_SECONDS_2 = 'VIDEO_DURATION_SECONDS_2';
  /**
   * 3 seconds.
   */
  public const MINIMUM_DURATION_VIDEO_DURATION_SECONDS_3 = 'VIDEO_DURATION_SECONDS_3';
  /**
   * 4 seconds.
   */
  public const MINIMUM_DURATION_VIDEO_DURATION_SECONDS_4 = 'VIDEO_DURATION_SECONDS_4';
  /**
   * 5 seconds.
   */
  public const MINIMUM_DURATION_VIDEO_DURATION_SECONDS_5 = 'VIDEO_DURATION_SECONDS_5';
  /**
   * 6 seconds.
   */
  public const MINIMUM_DURATION_VIDEO_DURATION_SECONDS_6 = 'VIDEO_DURATION_SECONDS_6';
  /**
   * 7 seconds.
   */
  public const MINIMUM_DURATION_VIDEO_DURATION_SECONDS_7 = 'VIDEO_DURATION_SECONDS_7';
  /**
   * 8 seconds.
   */
  public const MINIMUM_DURATION_VIDEO_DURATION_SECONDS_8 = 'VIDEO_DURATION_SECONDS_8';
  /**
   * 9 seconds.
   */
  public const MINIMUM_DURATION_VIDEO_DURATION_SECONDS_9 = 'VIDEO_DURATION_SECONDS_9';
  /**
   * 10 seconds.
   */
  public const MINIMUM_DURATION_VIDEO_DURATION_SECONDS_10 = 'VIDEO_DURATION_SECONDS_10';
  /**
   * 11 seconds.
   */
  public const MINIMUM_DURATION_VIDEO_DURATION_SECONDS_11 = 'VIDEO_DURATION_SECONDS_11';
  /**
   * 12 seconds.
   */
  public const MINIMUM_DURATION_VIDEO_DURATION_SECONDS_12 = 'VIDEO_DURATION_SECONDS_12';
  /**
   * 13 seconds.
   */
  public const MINIMUM_DURATION_VIDEO_DURATION_SECONDS_13 = 'VIDEO_DURATION_SECONDS_13';
  /**
   * 14 seconds.
   */
  public const MINIMUM_DURATION_VIDEO_DURATION_SECONDS_14 = 'VIDEO_DURATION_SECONDS_14';
  /**
   * 15 seconds.
   */
  public const MINIMUM_DURATION_VIDEO_DURATION_SECONDS_15 = 'VIDEO_DURATION_SECONDS_15';
  /**
   * 30 seconds.
   */
  public const MINIMUM_DURATION_VIDEO_DURATION_SECONDS_30 = 'VIDEO_DURATION_SECONDS_30';
  /**
   * 45 seconds.
   */
  public const MINIMUM_DURATION_VIDEO_DURATION_SECONDS_45 = 'VIDEO_DURATION_SECONDS_45';
  /**
   * 60 seconds.
   */
  public const MINIMUM_DURATION_VIDEO_DURATION_SECONDS_60 = 'VIDEO_DURATION_SECONDS_60';
  /**
   * Value is not specified or is unknown in this version.
   */
  public const MINIMUM_QUARTILE_VIDEO_DURATION_QUARTILE_UNSPECIFIED = 'VIDEO_DURATION_QUARTILE_UNSPECIFIED';
  /**
   * No quartile value.
   */
  public const MINIMUM_QUARTILE_VIDEO_DURATION_QUARTILE_NONE = 'VIDEO_DURATION_QUARTILE_NONE';
  /**
   * First quartile.
   */
  public const MINIMUM_QUARTILE_VIDEO_DURATION_QUARTILE_FIRST = 'VIDEO_DURATION_QUARTILE_FIRST';
  /**
   * Second quartile (midpoint).
   */
  public const MINIMUM_QUARTILE_VIDEO_DURATION_QUARTILE_SECOND = 'VIDEO_DURATION_QUARTILE_SECOND';
  /**
   * Third quartile.
   */
  public const MINIMUM_QUARTILE_VIDEO_DURATION_QUARTILE_THIRD = 'VIDEO_DURATION_QUARTILE_THIRD';
  /**
   * Fourth quartile (completion).
   */
  public const MINIMUM_QUARTILE_VIDEO_DURATION_QUARTILE_FOURTH = 'VIDEO_DURATION_QUARTILE_FOURTH';
  /**
   * Value is not specified or is unknown in this version.
   */
  public const MINIMUM_VIEWABILITY_VIEWABILITY_PERCENT_UNSPECIFIED = 'VIEWABILITY_PERCENT_UNSPECIFIED';
  /**
   * 0% viewable.
   */
  public const MINIMUM_VIEWABILITY_VIEWABILITY_PERCENT_0 = 'VIEWABILITY_PERCENT_0';
  /**
   * 25% viewable.
   */
  public const MINIMUM_VIEWABILITY_VIEWABILITY_PERCENT_25 = 'VIEWABILITY_PERCENT_25';
  /**
   * 50% viewable.
   */
  public const MINIMUM_VIEWABILITY_VIEWABILITY_PERCENT_50 = 'VIEWABILITY_PERCENT_50';
  /**
   * 75% viewable.
   */
  public const MINIMUM_VIEWABILITY_VIEWABILITY_PERCENT_75 = 'VIEWABILITY_PERCENT_75';
  /**
   * 100% viewable.
   */
  public const MINIMUM_VIEWABILITY_VIEWABILITY_PERCENT_100 = 'VIEWABILITY_PERCENT_100';
  /**
   * Value is not specified or is unknown in this version.
   */
  public const MINIMUM_VOLUME_VIDEO_VOLUME_PERCENT_UNSPECIFIED = 'VIDEO_VOLUME_PERCENT_UNSPECIFIED';
  /**
   * 0% volume.
   */
  public const MINIMUM_VOLUME_VIDEO_VOLUME_PERCENT_0 = 'VIDEO_VOLUME_PERCENT_0';
  /**
   * 10% volume.
   */
  public const MINIMUM_VOLUME_VIDEO_VOLUME_PERCENT_10 = 'VIDEO_VOLUME_PERCENT_10';
  /**
   * Required. The display name of the custom metric.
   *
   * @var string
   */
  public $displayName;
  /**
   * The minimum visible video duration required (in seconds) in order for an
   * impression to be recorded. You must specify minimum_duration,
   * minimum_quartile or both. If both are specified, an impression meets the
   * metric criteria if either requirement is met (whichever happens first).
   *
   * @var string
   */
  public $minimumDuration;
  /**
   * The minimum visible video duration required, based on the video quartiles,
   * in order for an impression to be recorded. You must specify
   * minimum_duration, minimum_quartile or both. If both are specified, an
   * impression meets the metric criteria if either requirement is met
   * (whichever happens first).
   *
   * @var string
   */
  public $minimumQuartile;
  /**
   * Required. The minimum percentage of the video ad's pixels visible on the
   * screen in order for an impression to be recorded.
   *
   * @var string
   */
  public $minimumViewability;
  /**
   * Required. The minimum percentage of the video ad's volume required in order
   * for an impression to be recorded.
   *
   * @var string
   */
  public $minimumVolume;

  /**
   * Required. The display name of the custom metric.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The minimum visible video duration required (in seconds) in order for an
   * impression to be recorded. You must specify minimum_duration,
   * minimum_quartile or both. If both are specified, an impression meets the
   * metric criteria if either requirement is met (whichever happens first).
   *
   * Accepted values: VIDEO_DURATION_UNSPECIFIED, VIDEO_DURATION_SECONDS_NONE,
   * VIDEO_DURATION_SECONDS_0, VIDEO_DURATION_SECONDS_1,
   * VIDEO_DURATION_SECONDS_2, VIDEO_DURATION_SECONDS_3,
   * VIDEO_DURATION_SECONDS_4, VIDEO_DURATION_SECONDS_5,
   * VIDEO_DURATION_SECONDS_6, VIDEO_DURATION_SECONDS_7,
   * VIDEO_DURATION_SECONDS_8, VIDEO_DURATION_SECONDS_9,
   * VIDEO_DURATION_SECONDS_10, VIDEO_DURATION_SECONDS_11,
   * VIDEO_DURATION_SECONDS_12, VIDEO_DURATION_SECONDS_13,
   * VIDEO_DURATION_SECONDS_14, VIDEO_DURATION_SECONDS_15,
   * VIDEO_DURATION_SECONDS_30, VIDEO_DURATION_SECONDS_45,
   * VIDEO_DURATION_SECONDS_60
   *
   * @param self::MINIMUM_DURATION_* $minimumDuration
   */
  public function setMinimumDuration($minimumDuration)
  {
    $this->minimumDuration = $minimumDuration;
  }
  /**
   * @return self::MINIMUM_DURATION_*
   */
  public function getMinimumDuration()
  {
    return $this->minimumDuration;
  }
  /**
   * The minimum visible video duration required, based on the video quartiles,
   * in order for an impression to be recorded. You must specify
   * minimum_duration, minimum_quartile or both. If both are specified, an
   * impression meets the metric criteria if either requirement is met
   * (whichever happens first).
   *
   * Accepted values: VIDEO_DURATION_QUARTILE_UNSPECIFIED,
   * VIDEO_DURATION_QUARTILE_NONE, VIDEO_DURATION_QUARTILE_FIRST,
   * VIDEO_DURATION_QUARTILE_SECOND, VIDEO_DURATION_QUARTILE_THIRD,
   * VIDEO_DURATION_QUARTILE_FOURTH
   *
   * @param self::MINIMUM_QUARTILE_* $minimumQuartile
   */
  public function setMinimumQuartile($minimumQuartile)
  {
    $this->minimumQuartile = $minimumQuartile;
  }
  /**
   * @return self::MINIMUM_QUARTILE_*
   */
  public function getMinimumQuartile()
  {
    return $this->minimumQuartile;
  }
  /**
   * Required. The minimum percentage of the video ad's pixels visible on the
   * screen in order for an impression to be recorded.
   *
   * Accepted values: VIEWABILITY_PERCENT_UNSPECIFIED, VIEWABILITY_PERCENT_0,
   * VIEWABILITY_PERCENT_25, VIEWABILITY_PERCENT_50, VIEWABILITY_PERCENT_75,
   * VIEWABILITY_PERCENT_100
   *
   * @param self::MINIMUM_VIEWABILITY_* $minimumViewability
   */
  public function setMinimumViewability($minimumViewability)
  {
    $this->minimumViewability = $minimumViewability;
  }
  /**
   * @return self::MINIMUM_VIEWABILITY_*
   */
  public function getMinimumViewability()
  {
    return $this->minimumViewability;
  }
  /**
   * Required. The minimum percentage of the video ad's volume required in order
   * for an impression to be recorded.
   *
   * Accepted values: VIDEO_VOLUME_PERCENT_UNSPECIFIED, VIDEO_VOLUME_PERCENT_0,
   * VIDEO_VOLUME_PERCENT_10
   *
   * @param self::MINIMUM_VOLUME_* $minimumVolume
   */
  public function setMinimumVolume($minimumVolume)
  {
    $this->minimumVolume = $minimumVolume;
  }
  /**
   * @return self::MINIMUM_VOLUME_*
   */
  public function getMinimumVolume()
  {
    return $this->minimumVolume;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ActiveViewVideoViewabilityMetricConfig::class, 'Google_Service_DisplayVideo_ActiveViewVideoViewabilityMetricConfig');
