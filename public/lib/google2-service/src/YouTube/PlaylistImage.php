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

class PlaylistImage extends \Google\Model
{
  /**
   * Identifies this resource (playlist id and image type).
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "youtube#playlistImages".
   *
   * @var string
   */
  public $kind;
  protected $snippetType = PlaylistImageSnippet::class;
  protected $snippetDataType = '';

  /**
   * Identifies this resource (playlist id and image type).
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
   * Identifies what kind of resource this is. Value: the fixed string
   * "youtube#playlistImages".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * @param PlaylistImageSnippet $snippet
   */
  public function setSnippet(PlaylistImageSnippet $snippet)
  {
    $this->snippet = $snippet;
  }
  /**
   * @return PlaylistImageSnippet
   */
  public function getSnippet()
  {
    return $this->snippet;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlaylistImage::class, 'Google_Service_YouTube_PlaylistImage');
