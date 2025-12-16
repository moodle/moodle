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

namespace Google\Service\Games;

class SnapshotImage extends \Google\Model
{
  protected $internal_gapi_mappings = [
        "mimeType" => "mime_type",
  ];
  /**
   * The height of the image.
   *
   * @var int
   */
  public $height;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#snapshotImage`.
   *
   * @var string
   */
  public $kind;
  /**
   * The MIME type of the image.
   *
   * @var string
   */
  public $mimeType;
  /**
   * The URL of the image. This URL may be invalidated at any time and should
   * not be cached.
   *
   * @var string
   */
  public $url;
  /**
   * The width of the image.
   *
   * @var int
   */
  public $width;

  /**
   * The height of the image.
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
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#snapshotImage`.
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
   * The MIME type of the image.
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
  /**
   * The URL of the image. This URL may be invalidated at any time and should
   * not be cached.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
  /**
   * The width of the image.
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
class_alias(SnapshotImage::class, 'Google_Service_Games_SnapshotImage');
