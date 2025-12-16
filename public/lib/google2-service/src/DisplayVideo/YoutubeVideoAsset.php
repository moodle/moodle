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

class YoutubeVideoAsset extends \Google\Model
{
  /**
   * Required. The YouTube video id of the asset. This is the 11 char string
   * value used in the YouTube video URL.
   *
   * @var string
   */
  public $youtubeVideoId;

  /**
   * Required. The YouTube video id of the asset. This is the 11 char string
   * value used in the YouTube video URL.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(YoutubeVideoAsset::class, 'Google_Service_DisplayVideo_YoutubeVideoAsset');
