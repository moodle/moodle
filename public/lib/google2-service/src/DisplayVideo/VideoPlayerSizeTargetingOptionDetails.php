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

class VideoPlayerSizeTargetingOptionDetails extends \Google\Model
{
  /**
   * Video player size is not specified in this version. This enum is a place
   * holder for a default value and does not represent a real video player size.
   */
  public const VIDEO_PLAYER_SIZE_VIDEO_PLAYER_SIZE_UNSPECIFIED = 'VIDEO_PLAYER_SIZE_UNSPECIFIED';
  /**
   * The dimensions of the video player are less than 400×300 (desktop), or up
   * to 20% of screen covered (mobile).
   */
  public const VIDEO_PLAYER_SIZE_VIDEO_PLAYER_SIZE_SMALL = 'VIDEO_PLAYER_SIZE_SMALL';
  /**
   * The dimensions of the video player are between 400x300 and 1280x720 pixels
   * (desktop), or 20% to 90% of the screen covered (mobile).
   */
  public const VIDEO_PLAYER_SIZE_VIDEO_PLAYER_SIZE_LARGE = 'VIDEO_PLAYER_SIZE_LARGE';
  /**
   * The dimensions of the video player are 1280×720 or greater (desktop), or
   * over 90% of the screen covered (mobile).
   */
  public const VIDEO_PLAYER_SIZE_VIDEO_PLAYER_SIZE_HD = 'VIDEO_PLAYER_SIZE_HD';
  /**
   * The dimensions of the video player are unknown.
   */
  public const VIDEO_PLAYER_SIZE_VIDEO_PLAYER_SIZE_UNKNOWN = 'VIDEO_PLAYER_SIZE_UNKNOWN';
  /**
   * Output only. The video player size.
   *
   * @var string
   */
  public $videoPlayerSize;

  /**
   * Output only. The video player size.
   *
   * Accepted values: VIDEO_PLAYER_SIZE_UNSPECIFIED, VIDEO_PLAYER_SIZE_SMALL,
   * VIDEO_PLAYER_SIZE_LARGE, VIDEO_PLAYER_SIZE_HD, VIDEO_PLAYER_SIZE_UNKNOWN
   *
   * @param self::VIDEO_PLAYER_SIZE_* $videoPlayerSize
   */
  public function setVideoPlayerSize($videoPlayerSize)
  {
    $this->videoPlayerSize = $videoPlayerSize;
  }
  /**
   * @return self::VIDEO_PLAYER_SIZE_*
   */
  public function getVideoPlayerSize()
  {
    return $this->videoPlayerSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoPlayerSizeTargetingOptionDetails::class, 'Google_Service_DisplayVideo_VideoPlayerSizeTargetingOptionDetails');
