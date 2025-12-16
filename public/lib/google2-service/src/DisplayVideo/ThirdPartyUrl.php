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

class ThirdPartyUrl extends \Google\Model
{
  /**
   * The type of third-party URL is unspecified or is unknown in this version.
   */
  public const TYPE_THIRD_PARTY_URL_TYPE_UNSPECIFIED = 'THIRD_PARTY_URL_TYPE_UNSPECIFIED';
  /**
   * Used to count impressions of the creative after the audio or video
   * buffering is complete.
   */
  public const TYPE_THIRD_PARTY_URL_TYPE_IMPRESSION = 'THIRD_PARTY_URL_TYPE_IMPRESSION';
  /**
   * Used to track user clicks on the audio or video.
   */
  public const TYPE_THIRD_PARTY_URL_TYPE_CLICK_TRACKING = 'THIRD_PARTY_URL_TYPE_CLICK_TRACKING';
  /**
   * Used to track the number of times a user starts the audio or video.
   */
  public const TYPE_THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_START = 'THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_START';
  /**
   * Used to track the number of times the audio or video plays to 25% of its
   * length.
   */
  public const TYPE_THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_FIRST_QUARTILE = 'THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_FIRST_QUARTILE';
  /**
   * Used to track the number of times the audio or video plays to 50% of its
   * length.
   */
  public const TYPE_THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_MIDPOINT = 'THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_MIDPOINT';
  /**
   * Used to track the number of times the audio or video plays to 75% of its
   * length.
   */
  public const TYPE_THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_THIRD_QUARTILE = 'THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_THIRD_QUARTILE';
  /**
   * Used to track the number of times the audio or video plays to the end.
   */
  public const TYPE_THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_COMPLETE = 'THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_COMPLETE';
  /**
   * Used to track the number of times a user mutes the audio or video.
   */
  public const TYPE_THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_MUTE = 'THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_MUTE';
  /**
   * Used to track the number of times a user pauses the audio or video.
   */
  public const TYPE_THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_PAUSE = 'THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_PAUSE';
  /**
   * Used to track the number of times a user replays the audio or video.
   */
  public const TYPE_THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_REWIND = 'THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_REWIND';
  /**
   * Used to track the number of times a user expands the player to full-screen
   * size.
   */
  public const TYPE_THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_FULLSCREEN = 'THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_FULLSCREEN';
  /**
   * Used to track the number of times a user stops the audio or video.
   */
  public const TYPE_THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_STOP = 'THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_STOP';
  /**
   * Used to track the number of times a user performs a custom click, such as
   * clicking on a video hot spot.
   */
  public const TYPE_THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_CUSTOM = 'THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_CUSTOM';
  /**
   * Used to track the number of times the audio or video was skipped.
   */
  public const TYPE_THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_SKIP = 'THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_SKIP';
  /**
   * Used to track the number of times the audio or video plays to an offset
   * determined by the progress_offset.
   */
  public const TYPE_THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_PROGRESS = 'THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_PROGRESS';
  /**
   * Optional. The type of interaction needs to be tracked by the tracking URL
   *
   * @var string
   */
  public $type;
  /**
   * Optional. Tracking URL used to track the interaction. Provide a URL with
   * optional path or query string, beginning with `https:`. For example,
   * `https://www.example.com/path`
   *
   * @var string
   */
  public $url;

  /**
   * Optional. The type of interaction needs to be tracked by the tracking URL
   *
   * Accepted values: THIRD_PARTY_URL_TYPE_UNSPECIFIED,
   * THIRD_PARTY_URL_TYPE_IMPRESSION, THIRD_PARTY_URL_TYPE_CLICK_TRACKING,
   * THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_START,
   * THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_FIRST_QUARTILE,
   * THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_MIDPOINT,
   * THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_THIRD_QUARTILE,
   * THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_COMPLETE,
   * THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_MUTE,
   * THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_PAUSE,
   * THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_REWIND,
   * THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_FULLSCREEN,
   * THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_STOP,
   * THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_CUSTOM,
   * THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_SKIP,
   * THIRD_PARTY_URL_TYPE_AUDIO_VIDEO_PROGRESS
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
   * Optional. Tracking URL used to track the interaction. Provide a URL with
   * optional path or query string, beginning with `https:`. For example,
   * `https://www.example.com/path`
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ThirdPartyUrl::class, 'Google_Service_DisplayVideo_ThirdPartyUrl');
