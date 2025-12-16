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

class VideoAdSequenceSettings extends \Google\Collection
{
  /**
   * Unspecified or unknown.
   */
  public const MINIMUM_DURATION_VIDEO_AD_SEQUENCE_MINIMUM_DURATION_UNSPECIFIED = 'VIDEO_AD_SEQUENCE_MINIMUM_DURATION_UNSPECIFIED';
  /**
   * 7 days.
   */
  public const MINIMUM_DURATION_VIDEO_AD_SEQUENCE_MINIMUM_DURATION_WEEK = 'VIDEO_AD_SEQUENCE_MINIMUM_DURATION_WEEK';
  /**
   * 30 days.
   */
  public const MINIMUM_DURATION_VIDEO_AD_SEQUENCE_MINIMUM_DURATION_MONTH = 'VIDEO_AD_SEQUENCE_MINIMUM_DURATION_MONTH';
  protected $collection_key = 'steps';
  /**
   * The minimum time interval before the same user sees this sequence again.
   *
   * @var string
   */
  public $minimumDuration;
  protected $stepsType = VideoAdSequenceStep::class;
  protected $stepsDataType = 'array';

  /**
   * The minimum time interval before the same user sees this sequence again.
   *
   * Accepted values: VIDEO_AD_SEQUENCE_MINIMUM_DURATION_UNSPECIFIED,
   * VIDEO_AD_SEQUENCE_MINIMUM_DURATION_WEEK,
   * VIDEO_AD_SEQUENCE_MINIMUM_DURATION_MONTH
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
   * The steps of which the sequence consists.
   *
   * @param VideoAdSequenceStep[] $steps
   */
  public function setSteps($steps)
  {
    $this->steps = $steps;
  }
  /**
   * @return VideoAdSequenceStep[]
   */
  public function getSteps()
  {
    return $this->steps;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoAdSequenceSettings::class, 'Google_Service_DisplayVideo_VideoAdSequenceSettings');
