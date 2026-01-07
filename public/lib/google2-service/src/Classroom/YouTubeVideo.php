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

namespace Google\Service\Classroom;

class YouTubeVideo extends \Google\Model
{
  /**
   * URL that can be used to view the YouTube video. Read-only.
   *
   * @var string
   */
  public $alternateLink;
  /**
   * YouTube API resource ID.
   *
   * @var string
   */
  public $id;
  /**
   * URL of a thumbnail image of the YouTube video. Read-only.
   *
   * @var string
   */
  public $thumbnailUrl;
  /**
   * Title of the YouTube video. Read-only.
   *
   * @var string
   */
  public $title;

  /**
   * URL that can be used to view the YouTube video. Read-only.
   *
   * @param string $alternateLink
   */
  public function setAlternateLink($alternateLink)
  {
    $this->alternateLink = $alternateLink;
  }
  /**
   * @return string
   */
  public function getAlternateLink()
  {
    return $this->alternateLink;
  }
  /**
   * YouTube API resource ID.
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
   * URL of a thumbnail image of the YouTube video. Read-only.
   *
   * @param string $thumbnailUrl
   */
  public function setThumbnailUrl($thumbnailUrl)
  {
    $this->thumbnailUrl = $thumbnailUrl;
  }
  /**
   * @return string
   */
  public function getThumbnailUrl()
  {
    return $this->thumbnailUrl;
  }
  /**
   * Title of the YouTube video. Read-only.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(YouTubeVideo::class, 'Google_Service_Classroom_YouTubeVideo');
