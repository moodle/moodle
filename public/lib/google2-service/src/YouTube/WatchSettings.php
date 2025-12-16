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

class WatchSettings extends \Google\Model
{
  /**
   * The text color for the video watch page's branded area.
   *
   * @var string
   */
  public $backgroundColor;
  /**
   * An ID that uniquely identifies a playlist that displays next to the video
   * player.
   *
   * @var string
   */
  public $featuredPlaylistId;
  /**
   * The background color for the video watch page's branded area.
   *
   * @var string
   */
  public $textColor;

  /**
   * The text color for the video watch page's branded area.
   *
   * @param string $backgroundColor
   */
  public function setBackgroundColor($backgroundColor)
  {
    $this->backgroundColor = $backgroundColor;
  }
  /**
   * @return string
   */
  public function getBackgroundColor()
  {
    return $this->backgroundColor;
  }
  /**
   * An ID that uniquely identifies a playlist that displays next to the video
   * player.
   *
   * @param string $featuredPlaylistId
   */
  public function setFeaturedPlaylistId($featuredPlaylistId)
  {
    $this->featuredPlaylistId = $featuredPlaylistId;
  }
  /**
   * @return string
   */
  public function getFeaturedPlaylistId()
  {
    return $this->featuredPlaylistId;
  }
  /**
   * The background color for the video watch page's branded area.
   *
   * @param string $textColor
   */
  public function setTextColor($textColor)
  {
    $this->textColor = $textColor;
  }
  /**
   * @return string
   */
  public function getTextColor()
  {
    return $this->textColor;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WatchSettings::class, 'Google_Service_YouTube_WatchSettings');
