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

class VideoDiscoveryAd extends \Google\Model
{
  /**
   * Unknown or unspecified.
   */
  public const THUMBNAIL_THUMBNAIL_UNSPECIFIED = 'THUMBNAIL_UNSPECIFIED';
  /**
   * The default thumbnail, can be auto-generated or user-uploaded.
   */
  public const THUMBNAIL_THUMBNAIL_DEFAULT = 'THUMBNAIL_DEFAULT';
  /**
   * Thumbnail 1, generated from the video.
   */
  public const THUMBNAIL_THUMBNAIL_1 = 'THUMBNAIL_1';
  /**
   * Thumbnail 2, generated from the video.
   */
  public const THUMBNAIL_THUMBNAIL_2 = 'THUMBNAIL_2';
  /**
   * Thumbnail 3, generated from the video.
   */
  public const THUMBNAIL_THUMBNAIL_3 = 'THUMBNAIL_3';
  /**
   * First text line for the ad.
   *
   * @var string
   */
  public $description1;
  /**
   * Second text line for the ad.
   *
   * @var string
   */
  public $description2;
  /**
   * The headline of ad.
   *
   * @var string
   */
  public $headline;
  /**
   * Thumbnail image used in the ad.
   *
   * @var string
   */
  public $thumbnail;
  protected $videoType = YoutubeVideoDetails::class;
  protected $videoDataType = '';

  /**
   * First text line for the ad.
   *
   * @param string $description1
   */
  public function setDescription1($description1)
  {
    $this->description1 = $description1;
  }
  /**
   * @return string
   */
  public function getDescription1()
  {
    return $this->description1;
  }
  /**
   * Second text line for the ad.
   *
   * @param string $description2
   */
  public function setDescription2($description2)
  {
    $this->description2 = $description2;
  }
  /**
   * @return string
   */
  public function getDescription2()
  {
    return $this->description2;
  }
  /**
   * The headline of ad.
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
   * Thumbnail image used in the ad.
   *
   * Accepted values: THUMBNAIL_UNSPECIFIED, THUMBNAIL_DEFAULT, THUMBNAIL_1,
   * THUMBNAIL_2, THUMBNAIL_3
   *
   * @param self::THUMBNAIL_* $thumbnail
   */
  public function setThumbnail($thumbnail)
  {
    $this->thumbnail = $thumbnail;
  }
  /**
   * @return self::THUMBNAIL_*
   */
  public function getThumbnail()
  {
    return $this->thumbnail;
  }
  /**
   * The YouTube video the ad promotes.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoDiscoveryAd::class, 'Google_Service_DisplayVideo_VideoDiscoveryAd');
