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

namespace Google\Service\YouTube;

class VideoAbuseReport extends \Google\Model
{
  /**
   * Additional comments regarding the abuse report.
   *
   * @var string
   */
  public $comments;
  /**
   * The language that the content was viewed in.
   *
   * @var string
   */
  public $language;
  /**
   * The high-level, or primary, reason that the content is abusive. The value
   * is an abuse report reason ID.
   *
   * @var string
   */
  public $reasonId;
  /**
   * The specific, or secondary, reason that this content is abusive (if
   * available). The value is an abuse report reason ID that is a valid
   * secondary reason for the primary reason.
   *
   * @var string
   */
  public $secondaryReasonId;
  /**
   * The ID that YouTube uses to uniquely identify the video.
   *
   * @var string
   */
  public $videoId;

  /**
   * Additional comments regarding the abuse report.
   *
   * @param string $comments
   */
  public function setComments($comments)
  {
    $this->comments = $comments;
  }
  /**
   * @return string
   */
  public function getComments()
  {
    return $this->comments;
  }
  /**
   * The language that the content was viewed in.
   *
   * @param string $language
   */
  public function setLanguage($language)
  {
    $this->language = $language;
  }
  /**
   * @return string
   */
  public function getLanguage()
  {
    return $this->language;
  }
  /**
   * The high-level, or primary, reason that the content is abusive. The value
   * is an abuse report reason ID.
   *
   * @param string $reasonId
   */
  public function setReasonId($reasonId)
  {
    $this->reasonId = $reasonId;
  }
  /**
   * @return string
   */
  public function getReasonId()
  {
    return $this->reasonId;
  }
  /**
   * The specific, or secondary, reason that this content is abusive (if
   * available). The value is an abuse report reason ID that is a valid
   * secondary reason for the primary reason.
   *
   * @param string $secondaryReasonId
   */
  public function setSecondaryReasonId($secondaryReasonId)
  {
    $this->secondaryReasonId = $secondaryReasonId;
  }
  /**
   * @return string
   */
  public function getSecondaryReasonId()
  {
    return $this->secondaryReasonId;
  }
  /**
   * The ID that YouTube uses to uniquely identify the video.
   *
   * @param string $videoId
   */
  public function setVideoId($videoId)
  {
    $this->videoId = $videoId;
  }
  /**
   * @return string
   */
  public function getVideoId()
  {
    return $this->videoId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoAbuseReport::class, 'Google_Service_YouTube_VideoAbuseReport');
