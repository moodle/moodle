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

class YoutubeVideoDetails extends \Google\Model
{
  /**
   * Unknown or unspecified.
   */
  public const UNAVAILABLE_REASON_VIDEO_UNAVAILABLE_REASON_UNSPECIFIED = 'VIDEO_UNAVAILABLE_REASON_UNSPECIFIED';
  /**
   * The video is private.
   */
  public const UNAVAILABLE_REASON_VIDEO_UNAVAILABLE_REASON_PRIVATE = 'VIDEO_UNAVAILABLE_REASON_PRIVATE';
  /**
   * The video is deleted.
   */
  public const UNAVAILABLE_REASON_VIDEO_UNAVAILABLE_REASON_DELETED = 'VIDEO_UNAVAILABLE_REASON_DELETED';
  /**
   * The YouTube video ID which can be searched on YouTube webpage.
   *
   * @var string
   */
  public $id;
  /**
   * The reason why the video data is not available.
   *
   * @var string
   */
  public $unavailableReason;

  /**
   * The YouTube video ID which can be searched on YouTube webpage.
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
   * The reason why the video data is not available.
   *
   * Accepted values: VIDEO_UNAVAILABLE_REASON_UNSPECIFIED,
   * VIDEO_UNAVAILABLE_REASON_PRIVATE, VIDEO_UNAVAILABLE_REASON_DELETED
   *
   * @param self::UNAVAILABLE_REASON_* $unavailableReason
   */
  public function setUnavailableReason($unavailableReason)
  {
    $this->unavailableReason = $unavailableReason;
  }
  /**
   * @return self::UNAVAILABLE_REASON_*
   */
  public function getUnavailableReason()
  {
    return $this->unavailableReason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(YoutubeVideoDetails::class, 'Google_Service_DisplayVideo_YoutubeVideoDetails');
