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

class GoogleCloudDiscoveryengineV1MediaInfo extends \Google\Model
{
  /**
   * The media progress time in seconds, if applicable. For example, if the end
   * user has finished 90 seconds of a playback video, then
   * MediaInfo.media_progress_duration.seconds should be set to 90.
   *
   * @var string
   */
  public $mediaProgressDuration;
  /**
   * Media progress should be computed using only the media_progress_duration
   * relative to the media total length. This value must be between `[0, 1.0]`
   * inclusive. If this is not a playback or the progress cannot be computed
   * (e.g. ongoing livestream), this field should be unset.
   *
   * @var float
   */
  public $mediaProgressPercentage;

  /**
   * The media progress time in seconds, if applicable. For example, if the end
   * user has finished 90 seconds of a playback video, then
   * MediaInfo.media_progress_duration.seconds should be set to 90.
   *
   * @param string $mediaProgressDuration
   */
  public function setMediaProgressDuration($mediaProgressDuration)
  {
    $this->mediaProgressDuration = $mediaProgressDuration;
  }
  /**
   * @return string
   */
  public function getMediaProgressDuration()
  {
    return $this->mediaProgressDuration;
  }
  /**
   * Media progress should be computed using only the media_progress_duration
   * relative to the media total length. This value must be between `[0, 1.0]`
   * inclusive. If this is not a playback or the progress cannot be computed
   * (e.g. ongoing livestream), this field should be unset.
   *
   * @param float $mediaProgressPercentage
   */
  public function setMediaProgressPercentage($mediaProgressPercentage)
  {
    $this->mediaProgressPercentage = $mediaProgressPercentage;
  }
  /**
   * @return float
   */
  public function getMediaProgressPercentage()
  {
    return $this->mediaProgressPercentage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1MediaInfo::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1MediaInfo');
