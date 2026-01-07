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

namespace Google\Service\Dfareporting;

class ThirdPartyTrackingUrl extends \Google\Model
{
  /**
   * Used to count impressions of the ad after video buffering is complete.
   */
  public const THIRD_PARTY_URL_TYPE_IMPRESSION = 'IMPRESSION';
  /**
   * Used to track user clicks on the video.
   */
  public const THIRD_PARTY_URL_TYPE_CLICK_TRACKING = 'CLICK_TRACKING';
  /**
   * Used to track the number of times a user starts a video.
   */
  public const THIRD_PARTY_URL_TYPE_VIDEO_START = 'VIDEO_START';
  /**
   * Used to track the number of times the video plays to 25% of its length.
   */
  public const THIRD_PARTY_URL_TYPE_VIDEO_FIRST_QUARTILE = 'VIDEO_FIRST_QUARTILE';
  /**
   * Used to track the number of times the video plays to 50% of its length.
   */
  public const THIRD_PARTY_URL_TYPE_VIDEO_MIDPOINT = 'VIDEO_MIDPOINT';
  /**
   * Used to track the number of times the video plays to 75% of its length.
   */
  public const THIRD_PARTY_URL_TYPE_VIDEO_THIRD_QUARTILE = 'VIDEO_THIRD_QUARTILE';
  /**
   * Used to track the number of times the video plays to the end.
   */
  public const THIRD_PARTY_URL_TYPE_VIDEO_COMPLETE = 'VIDEO_COMPLETE';
  /**
   * Used to track the number of times a user mutes the video.
   */
  public const THIRD_PARTY_URL_TYPE_VIDEO_MUTE = 'VIDEO_MUTE';
  /**
   * Used to track the number of times a user pauses the video.
   */
  public const THIRD_PARTY_URL_TYPE_VIDEO_PAUSE = 'VIDEO_PAUSE';
  /**
   * Used to track the number of times a user replays the video.
   */
  public const THIRD_PARTY_URL_TYPE_VIDEO_REWIND = 'VIDEO_REWIND';
  /**
   * Used to track the number of times a user expands the video to full-screen
   * size.
   */
  public const THIRD_PARTY_URL_TYPE_VIDEO_FULLSCREEN = 'VIDEO_FULLSCREEN';
  /**
   * Used to track the number of times a user stops the video.
   */
  public const THIRD_PARTY_URL_TYPE_VIDEO_STOP = 'VIDEO_STOP';
  /**
   * Used to track the number of times a user performs a custom click, such as
   * clicking on a video hot spot.
   */
  public const THIRD_PARTY_URL_TYPE_VIDEO_CUSTOM = 'VIDEO_CUSTOM';
  /**
   * Used for DFA6 compatibility, this is deprecating in favor of event tags.
   */
  public const THIRD_PARTY_URL_TYPE_SURVEY = 'SURVEY';
  /**
   * Used by Studio RichMediaCreative, maps to its thirdPartyImpressionsUrl
   */
  public const THIRD_PARTY_URL_TYPE_RICH_MEDIA_IMPRESSION = 'RICH_MEDIA_IMPRESSION';
  /**
   * Used by Studio RichMediaCreative, maps to its
   * thirdPartyRichMediaImpressionsUrl
   */
  public const THIRD_PARTY_URL_TYPE_RICH_MEDIA_RM_IMPRESSION = 'RICH_MEDIA_RM_IMPRESSION';
  /**
   * Used by Studio RichMediaCreative, maps to its
   * thirdPartyBackupImageImpressionsUrl
   */
  public const THIRD_PARTY_URL_TYPE_RICH_MEDIA_BACKUP_IMPRESSION = 'RICH_MEDIA_BACKUP_IMPRESSION';
  /**
   * Used to track the number of times the video was skipped.
   */
  public const THIRD_PARTY_URL_TYPE_VIDEO_SKIP = 'VIDEO_SKIP';
  /**
   * Used to track the number of times the video plays to an offset determined
   * by the user.
   */
  public const THIRD_PARTY_URL_TYPE_VIDEO_PROGRESS = 'VIDEO_PROGRESS';
  /**
   * Third-party URL type for in-stream video and in-stream audio creatives.
   *
   * @var string
   */
  public $thirdPartyUrlType;
  /**
   * URL for the specified third-party URL type.
   *
   * @var string
   */
  public $url;

  /**
   * Third-party URL type for in-stream video and in-stream audio creatives.
   *
   * Accepted values: IMPRESSION, CLICK_TRACKING, VIDEO_START,
   * VIDEO_FIRST_QUARTILE, VIDEO_MIDPOINT, VIDEO_THIRD_QUARTILE, VIDEO_COMPLETE,
   * VIDEO_MUTE, VIDEO_PAUSE, VIDEO_REWIND, VIDEO_FULLSCREEN, VIDEO_STOP,
   * VIDEO_CUSTOM, SURVEY, RICH_MEDIA_IMPRESSION, RICH_MEDIA_RM_IMPRESSION,
   * RICH_MEDIA_BACKUP_IMPRESSION, VIDEO_SKIP, VIDEO_PROGRESS
   *
   * @param self::THIRD_PARTY_URL_TYPE_* $thirdPartyUrlType
   */
  public function setThirdPartyUrlType($thirdPartyUrlType)
  {
    $this->thirdPartyUrlType = $thirdPartyUrlType;
  }
  /**
   * @return self::THIRD_PARTY_URL_TYPE_*
   */
  public function getThirdPartyUrlType()
  {
    return $this->thirdPartyUrlType;
  }
  /**
   * URL for the specified third-party URL type.
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
class_alias(ThirdPartyTrackingUrl::class, 'Google_Service_Dfareporting_ThirdPartyTrackingUrl');
