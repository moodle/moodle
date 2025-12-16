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

class VideoStatistics extends \Google\Model
{
  /**
   * The number of comments for the video.
   *
   * @var string
   */
  public $commentCount;
  /**
   * The number of users who have indicated that they disliked the video by
   * giving it a negative rating.
   *
   * @var string
   */
  public $dislikeCount;
  /**
   * The number of users who currently have the video marked as a favorite
   * video.
   *
   * @deprecated
   * @var string
   */
  public $favoriteCount;
  /**
   * The number of users who have indicated that they liked the video by giving
   * it a positive rating.
   *
   * @var string
   */
  public $likeCount;
  /**
   * The number of times the video has been viewed.
   *
   * @var string
   */
  public $viewCount;

  /**
   * The number of comments for the video.
   *
   * @param string $commentCount
   */
  public function setCommentCount($commentCount)
  {
    $this->commentCount = $commentCount;
  }
  /**
   * @return string
   */
  public function getCommentCount()
  {
    return $this->commentCount;
  }
  /**
   * The number of users who have indicated that they disliked the video by
   * giving it a negative rating.
   *
   * @param string $dislikeCount
   */
  public function setDislikeCount($dislikeCount)
  {
    $this->dislikeCount = $dislikeCount;
  }
  /**
   * @return string
   */
  public function getDislikeCount()
  {
    return $this->dislikeCount;
  }
  /**
   * The number of users who currently have the video marked as a favorite
   * video.
   *
   * @deprecated
   * @param string $favoriteCount
   */
  public function setFavoriteCount($favoriteCount)
  {
    $this->favoriteCount = $favoriteCount;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getFavoriteCount()
  {
    return $this->favoriteCount;
  }
  /**
   * The number of users who have indicated that they liked the video by giving
   * it a positive rating.
   *
   * @param string $likeCount
   */
  public function setLikeCount($likeCount)
  {
    $this->likeCount = $likeCount;
  }
  /**
   * @return string
   */
  public function getLikeCount()
  {
    return $this->likeCount;
  }
  /**
   * The number of times the video has been viewed.
   *
   * @param string $viewCount
   */
  public function setViewCount($viewCount)
  {
    $this->viewCount = $viewCount;
  }
  /**
   * @return string
   */
  public function getViewCount()
  {
    return $this->viewCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoStatistics::class, 'Google_Service_YouTube_VideoStatistics');
