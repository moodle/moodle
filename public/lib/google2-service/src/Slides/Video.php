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

namespace Google\Service\Slides;

class Video extends \Google\Model
{
  /**
   * The video source is unspecified.
   */
  public const SOURCE_SOURCE_UNSPECIFIED = 'SOURCE_UNSPECIFIED';
  /**
   * The video source is YouTube.
   */
  public const SOURCE_YOUTUBE = 'YOUTUBE';
  /**
   * The video source is Google Drive.
   */
  public const SOURCE_DRIVE = 'DRIVE';
  /**
   * The video source's unique identifier for this video.
   *
   * @var string
   */
  public $id;
  /**
   * The video source.
   *
   * @var string
   */
  public $source;
  /**
   * An URL to a video. The URL is valid as long as the source video exists and
   * sharing settings do not change.
   *
   * @var string
   */
  public $url;
  protected $videoPropertiesType = VideoProperties::class;
  protected $videoPropertiesDataType = '';

  /**
   * The video source's unique identifier for this video.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The video source.
   *
   * Accepted values: SOURCE_UNSPECIFIED, YOUTUBE, DRIVE
   *
   * @param self::SOURCE_* $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return self::SOURCE_*
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * An URL to a video. The URL is valid as long as the source video exists and
   * sharing settings do not change.
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
  /**
   * The properties of the video.
   *
   * @param VideoProperties $videoProperties
   */
  public function setVideoProperties(VideoProperties $videoProperties)
  {
    $this->videoProperties = $videoProperties;
  }
  /**
   * @return VideoProperties
   */
  public function getVideoProperties()
  {
    return $this->videoProperties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Video::class, 'Google_Service_Slides_Video');
