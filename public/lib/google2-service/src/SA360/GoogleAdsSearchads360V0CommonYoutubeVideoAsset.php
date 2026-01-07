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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0CommonYoutubeVideoAsset extends \Google\Model
{
  /**
   * YouTube video id. This is the 11 character string value used in the YouTube
   * video URL.
   *
   * @var string
   */
  public $youtubeVideoId;
  /**
   * YouTube video title.
   *
   * @var string
   */
  public $youtubeVideoTitle;

  /**
   * YouTube video id. This is the 11 character string value used in the YouTube
   * video URL.
   *
   * @param string $youtubeVideoId
   */
  public function setYoutubeVideoId($youtubeVideoId)
  {
    $this->youtubeVideoId = $youtubeVideoId;
  }
  /**
   * @return string
   */
  public function getYoutubeVideoId()
  {
    return $this->youtubeVideoId;
  }
  /**
   * YouTube video title.
   *
   * @param string $youtubeVideoTitle
   */
  public function setYoutubeVideoTitle($youtubeVideoTitle)
  {
    $this->youtubeVideoTitle = $youtubeVideoTitle;
  }
  /**
   * @return string
   */
  public function getYoutubeVideoTitle()
  {
    return $this->youtubeVideoTitle;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0CommonYoutubeVideoAsset::class, 'Google_Service_SA360_GoogleAdsSearchads360V0CommonYoutubeVideoAsset');
