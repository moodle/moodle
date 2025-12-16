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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0CommonImageDimension extends \Google\Model
{
  /**
   * Height of the image.
   *
   * @var string
   */
  public $heightPixels;
  /**
   * A URL that returns the image with this height and width.
   *
   * @var string
   */
  public $url;
  /**
   * Width of the image.
   *
   * @var string
   */
  public $widthPixels;

  /**
   * Height of the image.
   *
   * @param string $heightPixels
   */
  public function setHeightPixels($heightPixels)
  {
    $this->heightPixels = $heightPixels;
  }
  /**
   * @return string
   */
  public function getHeightPixels()
  {
    return $this->heightPixels;
  }
  /**
   * A URL that returns the image with this height and width.
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
   * Width of the image.
   *
   * @param string $widthPixels
   */
  public function setWidthPixels($widthPixels)
  {
    $this->widthPixels = $widthPixels;
  }
  /**
   * @return string
   */
  public function getWidthPixels()
  {
    return $this->widthPixels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0CommonImageDimension::class, 'Google_Service_SA360_GoogleAdsSearchads360V0CommonImageDimension');
