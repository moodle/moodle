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

class MastheadAd extends \Google\Collection
{
  /**
   * Not specified or unknown.
   */
  public const VIDEO_ASPECT_RATIO_VIDEO_ASPECT_RATIO_UNSPECIFIED = 'VIDEO_ASPECT_RATIO_UNSPECIFIED';
  /**
   * The video is stretched and the top and bottom are cropped.
   */
  public const VIDEO_ASPECT_RATIO_VIDEO_ASPECT_RATIO_WIDESCREEN = 'VIDEO_ASPECT_RATIO_WIDESCREEN';
  /**
   * The video uses a fixed 16:9 aspect ratio.
   */
  public const VIDEO_ASPECT_RATIO_VIDEO_ASPECT_RATIO_FIXED_16_9 = 'VIDEO_ASPECT_RATIO_FIXED_16_9';
  protected $collection_key = 'companionYoutubeVideos';
  /**
   * The duration of time the video will autoplay.
   *
   * @var string
   */
  public $autoplayVideoDuration;
  /**
   * The amount of time in milliseconds after which the video will start to
   * play.
   *
   * @var string
   */
  public $autoplayVideoStartMillisecond;
  /**
   * The text on the call-to-action button.
   *
   * @var string
   */
  public $callToActionButtonLabel;
  /**
   * The destination URL for the call-to-action button.
   *
   * @var string
   */
  public $callToActionFinalUrl;
  /**
   * The tracking URL for the call-to-action button.
   *
   * @var string
   */
  public $callToActionTrackingUrl;
  protected $companionYoutubeVideosType = YoutubeVideoDetails::class;
  protected $companionYoutubeVideosDataType = 'array';
  /**
   * The description of the ad.
   *
   * @var string
   */
  public $description;
  /**
   * The headline of the ad.
   *
   * @var string
   */
  public $headline;
  /**
   * Whether to show a background or banner that appears at the top of a YouTube
   * page.
   *
   * @var bool
   */
  public $showChannelArt;
  protected $videoType = YoutubeVideoDetails::class;
  protected $videoDataType = '';
  /**
   * The aspect ratio of the autoplaying YouTube video on the Masthead.
   *
   * @var string
   */
  public $videoAspectRatio;

  /**
   * The duration of time the video will autoplay.
   *
   * @param string $autoplayVideoDuration
   */
  public function setAutoplayVideoDuration($autoplayVideoDuration)
  {
    $this->autoplayVideoDuration = $autoplayVideoDuration;
  }
  /**
   * @return string
   */
  public function getAutoplayVideoDuration()
  {
    return $this->autoplayVideoDuration;
  }
  /**
   * The amount of time in milliseconds after which the video will start to
   * play.
   *
   * @param string $autoplayVideoStartMillisecond
   */
  public function setAutoplayVideoStartMillisecond($autoplayVideoStartMillisecond)
  {
    $this->autoplayVideoStartMillisecond = $autoplayVideoStartMillisecond;
  }
  /**
   * @return string
   */
  public function getAutoplayVideoStartMillisecond()
  {
    return $this->autoplayVideoStartMillisecond;
  }
  /**
   * The text on the call-to-action button.
   *
   * @param string $callToActionButtonLabel
   */
  public function setCallToActionButtonLabel($callToActionButtonLabel)
  {
    $this->callToActionButtonLabel = $callToActionButtonLabel;
  }
  /**
   * @return string
   */
  public function getCallToActionButtonLabel()
  {
    return $this->callToActionButtonLabel;
  }
  /**
   * The destination URL for the call-to-action button.
   *
   * @param string $callToActionFinalUrl
   */
  public function setCallToActionFinalUrl($callToActionFinalUrl)
  {
    $this->callToActionFinalUrl = $callToActionFinalUrl;
  }
  /**
   * @return string
   */
  public function getCallToActionFinalUrl()
  {
    return $this->callToActionFinalUrl;
  }
  /**
   * The tracking URL for the call-to-action button.
   *
   * @param string $callToActionTrackingUrl
   */
  public function setCallToActionTrackingUrl($callToActionTrackingUrl)
  {
    $this->callToActionTrackingUrl = $callToActionTrackingUrl;
  }
  /**
   * @return string
   */
  public function getCallToActionTrackingUrl()
  {
    return $this->callToActionTrackingUrl;
  }
  /**
   * The videos that appear next to the Masthead Ad on desktop. Can be no more
   * than two.
   *
   * @param YoutubeVideoDetails[] $companionYoutubeVideos
   */
  public function setCompanionYoutubeVideos($companionYoutubeVideos)
  {
    $this->companionYoutubeVideos = $companionYoutubeVideos;
  }
  /**
   * @return YoutubeVideoDetails[]
   */
  public function getCompanionYoutubeVideos()
  {
    return $this->companionYoutubeVideos;
  }
  /**
   * The description of the ad.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The headline of the ad.
   *
   * @param string $headline
   */
  public function setHeadline($headline)
  {
    $this->headline = $headline;
  }
  /**
   * @return string
   */
  public function getHeadline()
  {
    return $this->headline;
  }
  /**
   * Whether to show a background or banner that appears at the top of a YouTube
   * page.
   *
   * @param bool $showChannelArt
   */
  public function setShowChannelArt($showChannelArt)
  {
    $this->showChannelArt = $showChannelArt;
  }
  /**
   * @return bool
   */
  public function getShowChannelArt()
  {
    return $this->showChannelArt;
  }
  /**
   * The YouTube video used by the ad.
   *
   * @param YoutubeVideoDetails $video
   */
  public function setVideo(YoutubeVideoDetails $video)
  {
    $this->video = $video;
  }
  /**
   * @return YoutubeVideoDetails
   */
  public function getVideo()
  {
    return $this->video;
  }
  /**
   * The aspect ratio of the autoplaying YouTube video on the Masthead.
   *
   * Accepted values: VIDEO_ASPECT_RATIO_UNSPECIFIED,
   * VIDEO_ASPECT_RATIO_WIDESCREEN, VIDEO_ASPECT_RATIO_FIXED_16_9
   *
   * @param self::VIDEO_ASPECT_RATIO_* $videoAspectRatio
   */
  public function setVideoAspectRatio($videoAspectRatio)
  {
    $this->videoAspectRatio = $videoAspectRatio;
  }
  /**
   * @return self::VIDEO_ASPECT_RATIO_*
   */
  public function getVideoAspectRatio()
  {
    return $this->videoAspectRatio;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MastheadAd::class, 'Google_Service_DisplayVideo_MastheadAd');
