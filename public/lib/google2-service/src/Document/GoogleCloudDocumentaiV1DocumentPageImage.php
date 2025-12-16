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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1DocumentPageImage extends \Google\Model
{
  /**
   * Raw byte content of the image.
   *
   * @var string
   */
  public $content;
  /**
   * Height of the image in pixels.
   *
   * @var int
   */
  public $height;
  /**
   * Encoding [media type (MIME type)](https://www.iana.org/assignments/media-
   * types/media-types.xhtml) for the image.
   *
   * @var string
   */
  public $mimeType;
  /**
   * Width of the image in pixels.
   *
   * @var int
   */
  public $width;

  /**
   * Raw byte content of the image.
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * Height of the image in pixels.
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
   * Encoding [media type (MIME type)](https://www.iana.org/assignments/media-
   * types/media-types.xhtml) for the image.
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
   * Width of the image in pixels.
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
class_alias(GoogleCloudDocumentaiV1DocumentPageImage::class, 'Google_Service_Document_GoogleCloudDocumentaiV1DocumentPageImage');
