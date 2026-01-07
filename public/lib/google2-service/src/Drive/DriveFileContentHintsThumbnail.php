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

namespace Google\Service\Drive;

class DriveFileContentHintsThumbnail extends \Google\Model
{
  /**
   * The thumbnail data encoded with URL-safe Base64 ([RFC 4648 section
   * 5](https://datatracker.ietf.org/doc/html/rfc4648#section-5)).
   *
   * @var string
   */
  public $image;
  /**
   * The MIME type of the thumbnail.
   *
   * @var string
   */
  public $mimeType;

  /**
   * The thumbnail data encoded with URL-safe Base64 ([RFC 4648 section
   * 5](https://datatracker.ietf.org/doc/html/rfc4648#section-5)).
   *
   * @param string $image
   */
  public function setImage($image)
  {
    $this->image = $image;
  }
  /**
   * @return string
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * The MIME type of the thumbnail.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DriveFileContentHintsThumbnail::class, 'Google_Service_Drive_DriveFileContentHintsThumbnail');
