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

class PlaylistImageSnippet extends \Google\Model
{
  /**
   * The main image that will be used for this playlist.
   */
  public const TYPE_hero = 'hero';
  /**
   * The image height.
   *
   * @var int
   */
  public $height;
  /**
   * The Playlist ID of the playlist this image is associated with.
   *
   * @var string
   */
  public $playlistId;
  /**
   * The image type.
   *
   * @var string
   */
  public $type;
  /**
   * The image width.
   *
   * @var int
   */
  public $width;

  /**
   * The image height.
   *
   * @param int $height
   */
  public function setHeight($height)
  {
    $this->height = $height;
  }
  /**
   * @return int
   */
  public function getHeight()
  {
    return $this->height;
  }
  /**
   * The Playlist ID of the playlist this image is associated with.
   *
   * @param string $playlistId
   */
  public function setPlaylistId($playlistId)
  {
    $this->playlistId = $playlistId;
  }
  /**
   * @return string
   */
  public function getPlaylistId()
  {
    return $this->playlistId;
  }
  /**
   * The image type.
   *
   * Accepted values: hero
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * The image width.
   *
   * @param int $width
   */
  public function setWidth($width)
  {
    $this->width = $width;
  }
  /**
   * @return int
   */
  public function getWidth()
  {
    return $this->width;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlaylistImageSnippet::class, 'Google_Service_YouTube_PlaylistImageSnippet');
