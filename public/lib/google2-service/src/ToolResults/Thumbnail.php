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

namespace Google\Service\ToolResults;

class Thumbnail extends \Google\Model
{
  /**
   * The thumbnail's content type, i.e. "image/png". Always set.
   *
   * @var string
   */
  public $contentType;
  /**
   * The thumbnail file itself. That is, the bytes here are precisely the bytes
   * that make up the thumbnail file; they can be served as an image as-is (with
   * the appropriate content type.) Always set.
   *
   * @var string
   */
  public $data;
  /**
   * The height of the thumbnail, in pixels. Always set.
   *
   * @var int
   */
  public $heightPx;
  /**
   * The width of the thumbnail, in pixels. Always set.
   *
   * @var int
   */
  public $widthPx;

  /**
   * The thumbnail's content type, i.e. "image/png". Always set.
   *
   * @param string $contentType
   */
  public function setContentType($contentType)
  {
    $this->contentType = $contentType;
  }
  /**
   * @return string
   */
  public function getContentType()
  {
    return $this->contentType;
  }
  /**
   * The thumbnail file itself. That is, the bytes here are precisely the bytes
   * that make up the thumbnail file; they can be served as an image as-is (with
   * the appropriate content type.) Always set.
   *
   * @param string $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return string
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * The height of the thumbnail, in pixels. Always set.
   *
   * @param int $heightPx
   */
  public function setHeightPx($heightPx)
  {
    $this->heightPx = $heightPx;
  }
  /**
   * @return int
   */
  public function getHeightPx()
  {
    return $this->heightPx;
  }
  /**
   * The width of the thumbnail, in pixels. Always set.
   *
   * @param int $widthPx
   */
  public function setWidthPx($widthPx)
  {
    $this->widthPx = $widthPx;
  }
  /**
   * @return int
   */
  public function getWidthPx()
  {
    return $this->widthPx;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Thumbnail::class, 'Google_Service_ToolResults_Thumbnail');
