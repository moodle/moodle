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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2Image extends \Google\Model
{
  /**
   * Height of the image in number of pixels. This field must be nonnegative.
   * Otherwise, an INVALID_ARGUMENT error is returned.
   *
   * @var int
   */
  public $height;
  /**
   * Required. URI of the image. This field must be a valid UTF-8 encoded URI
   * with a length limit of 5,000 characters. Otherwise, an INVALID_ARGUMENT
   * error is returned. Google Merchant Center property
   * [image_link](https://support.google.com/merchants/answer/6324350).
   * Schema.org property [Product.image](https://schema.org/image).
   *
   * @var string
   */
  public $uri;
  /**
   * Width of the image in number of pixels. This field must be nonnegative.
   * Otherwise, an INVALID_ARGUMENT error is returned.
   *
   * @var int
   */
  public $width;

  /**
   * Height of the image in number of pixels. This field must be nonnegative.
   * Otherwise, an INVALID_ARGUMENT error is returned.
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
   * Required. URI of the image. This field must be a valid UTF-8 encoded URI
   * with a length limit of 5,000 characters. Otherwise, an INVALID_ARGUMENT
   * error is returned. Google Merchant Center property
   * [image_link](https://support.google.com/merchants/answer/6324350).
   * Schema.org property [Product.image](https://schema.org/image).
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
  /**
   * Width of the image in number of pixels. This field must be nonnegative.
   * Otherwise, an INVALID_ARGUMENT error is returned.
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
class_alias(GoogleCloudRetailV2Image::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2Image');
