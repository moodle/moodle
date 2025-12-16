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

namespace Google\Service\Slides;

class StretchedPictureFill extends \Google\Model
{
  /**
   * Reading the content_url: An URL to a picture with a default lifetime of 30
   * minutes. This URL is tagged with the account of the requester. Anyone with
   * the URL effectively accesses the picture as the original requester. Access
   * to the picture may be lost if the presentation's sharing settings change.
   * Writing the content_url: The picture is fetched once at insertion time and
   * a copy is stored for display inside the presentation. Pictures must be less
   * than 50MB in size, cannot exceed 25 megapixels, and must be in one of PNG,
   * JPEG, or GIF format. The provided URL can be at most 2 kB in length.
   *
   * @var string
   */
  public $contentUrl;
  protected $sizeType = Size::class;
  protected $sizeDataType = '';

  /**
   * Reading the content_url: An URL to a picture with a default lifetime of 30
   * minutes. This URL is tagged with the account of the requester. Anyone with
   * the URL effectively accesses the picture as the original requester. Access
   * to the picture may be lost if the presentation's sharing settings change.
   * Writing the content_url: The picture is fetched once at insertion time and
   * a copy is stored for display inside the presentation. Pictures must be less
   * than 50MB in size, cannot exceed 25 megapixels, and must be in one of PNG,
   * JPEG, or GIF format. The provided URL can be at most 2 kB in length.
   *
   * @param string $contentUrl
   */
  public function setContentUrl($contentUrl)
  {
    $this->contentUrl = $contentUrl;
  }
  /**
   * @return string
   */
  public function getContentUrl()
  {
    return $this->contentUrl;
  }
  /**
   * The original size of the picture fill. This field is read-only.
   *
   * @param Size $size
   */
  public function setSize(Size $size)
  {
    $this->size = $size;
  }
  /**
   * @return Size
   */
  public function getSize()
  {
    return $this->size;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StretchedPictureFill::class, 'Google_Service_Slides_StretchedPictureFill');
