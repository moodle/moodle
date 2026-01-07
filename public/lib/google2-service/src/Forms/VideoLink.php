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

namespace Google\Service\Forms;

class VideoLink extends \Google\Model
{
  /**
   * Required. The display text for the link.
   *
   * @var string
   */
  public $displayText;
  /**
   * The URI of a YouTube video.
   *
   * @var string
   */
  public $youtubeUri;

  /**
   * Required. The display text for the link.
   *
   * @param string $displayText
   */
  public function setDisplayText($displayText)
  {
    $this->displayText = $displayText;
  }
  /**
   * @return string
   */
  public function getDisplayText()
  {
    return $this->displayText;
  }
  /**
   * The URI of a YouTube video.
   *
   * @param string $youtubeUri
   */
  public function setYoutubeUri($youtubeUri)
  {
    $this->youtubeUri = $youtubeUri;
  }
  /**
   * @return string
   */
  public function getYoutubeUri()
  {
    return $this->youtubeUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoLink::class, 'Google_Service_Forms_VideoLink');
