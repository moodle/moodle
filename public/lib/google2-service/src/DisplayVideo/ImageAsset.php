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

class ImageAsset extends \Google\Model
{
  /**
   * File size of the image asset in bytes.
   *
   * @var string
   */
  public $fileSize;
  protected $fullSizeType = Dimensions::class;
  protected $fullSizeDataType = '';
  /**
   * MIME type of the image asset.
   *
   * @var string
   */
  public $mimeType;

  /**
   * File size of the image asset in bytes.
   *
   * @param string $fileSize
   */
  public function setFileSize($fileSize)
  {
    $this->fileSize = $fileSize;
  }
  /**
   * @return string
   */
  public function getFileSize()
  {
    return $this->fileSize;
  }
  /**
   * Metadata for this image at its original size.
   *
   * @param Dimensions $fullSize
   */
  public function setFullSize(Dimensions $fullSize)
  {
    $this->fullSize = $fullSize;
  }
  /**
   * @return Dimensions
   */
  public function getFullSize()
  {
    return $this->fullSize;
  }
  /**
   * MIME type of the image asset.
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
class_alias(ImageAsset::class, 'Google_Service_DisplayVideo_ImageAsset');
