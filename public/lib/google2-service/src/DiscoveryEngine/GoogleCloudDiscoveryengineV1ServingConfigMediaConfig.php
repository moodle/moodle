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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1ServingConfigMediaConfig extends \Google\Model
{
  /**
   * Specifies the content freshness used for recommendation result. Contents
   * will be demoted if contents were published for more than content freshness
   * cutoff days.
   *
   * @var int
   */
  public $contentFreshnessCutoffDays;
  /**
   * Specifies the content watched percentage threshold for demotion. Threshold
   * value must be between [0, 1.0] inclusive.
   *
   * @var float
   */
  public $contentWatchedPercentageThreshold;
  /**
   * Specifies the content watched minutes threshold for demotion.
   *
   * @var float
   */
  public $contentWatchedSecondsThreshold;
  /**
   * Optional. Specifies the number of days to look back for demoting watched
   * content. If set to zero or unset, defaults to the maximum of 365 days.
   *
   * @var int
   */
  public $demoteContentWatchedPastDays;
  /**
   * Specifies the event type used for demoting recommendation result. Currently
   * supported values: * `view-item`: Item viewed. * `media-play`: Start/resume
   * watching a video, playing a song, etc. * `media-complete`: Finished or
   * stopped midway through a video, song, etc. If unset, watch history demotion
   * will not be applied. Content freshness demotion will still be applied.
   *
   * @var string
   */
  public $demotionEventType;

  /**
   * Specifies the content freshness used for recommendation result. Contents
   * will be demoted if contents were published for more than content freshness
   * cutoff days.
   *
   * @param int $contentFreshnessCutoffDays
   */
  public function setContentFreshnessCutoffDays($contentFreshnessCutoffDays)
  {
    $this->contentFreshnessCutoffDays = $contentFreshnessCutoffDays;
  }
  /**
   * @return int
   */
  public function getContentFreshnessCutoffDays()
  {
    return $this->contentFreshnessCutoffDays;
  }
  /**
   * Specifies the content watched percentage threshold for demotion. Threshold
   * value must be between [0, 1.0] inclusive.
   *
   * @param float $contentWatchedPercentageThreshold
   */
  public function setContentWatchedPercentageThreshold($contentWatchedPercentageThreshold)
  {
    $this->contentWatchedPercentageThreshold = $contentWatchedPercentageThreshold;
  }
  /**
   * @return float
   */
  public function getContentWatchedPercentageThreshold()
  {
    return $this->contentWatchedPercentageThreshold;
  }
  /**
   * Specifies the content watched minutes threshold for demotion.
   *
   * @param float $contentWatchedSecondsThreshold
   */
  public function setContentWatchedSecondsThreshold($contentWatchedSecondsThreshold)
  {
    $this->contentWatchedSecondsThreshold = $contentWatchedSecondsThreshold;
  }
  /**
   * @return float
   */
  public function getContentWatchedSecondsThreshold()
  {
    return $this->contentWatchedSecondsThreshold;
  }
  /**
   * Optional. Specifies the number of days to look back for demoting watched
   * content. If set to zero or unset, defaults to the maximum of 365 days.
   *
   * @param int $demoteContentWatchedPastDays
   */
  public function setDemoteContentWatchedPastDays($demoteContentWatchedPastDays)
  {
    $this->demoteContentWatchedPastDays = $demoteContentWatchedPastDays;
  }
  /**
   * @return int
   */
  public function getDemoteContentWatchedPastDays()
  {
    return $this->demoteContentWatchedPastDays;
  }
  /**
   * Specifies the event type used for demoting recommendation result. Currently
   * supported values: * `view-item`: Item viewed. * `media-play`: Start/resume
   * watching a video, playing a song, etc. * `media-complete`: Finished or
   * stopped midway through a video, song, etc. If unset, watch history demotion
   * will not be applied. Content freshness demotion will still be applied.
   *
   * @param string $demotionEventType
   */
  public function setDemotionEventType($demotionEventType)
  {
    $this->demotionEventType = $demotionEventType;
  }
  /**
   * @return string
   */
  public function getDemotionEventType()
  {
    return $this->demotionEventType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1ServingConfigMediaConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1ServingConfigMediaConfig');
