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

namespace Google\Service\HangoutsChat;

class Image extends \Google\Model
{
  /**
   * The aspect ratio of this image (width and height). This field lets you
   * reserve the right height for the image while waiting for it to load. It's
   * not meant to override the built-in aspect ratio of the image. If unset, the
   * server fills it by prefetching the image.
   *
   * @var 
   */
  public $aspectRatio;
  /**
   * The URL of the image.
   *
   * @var string
   */
  public $imageUrl;
  protected $onClickType = OnClick::class;
  protected $onClickDataType = '';

  public function setAspectRatio($aspectRatio)
  {
    $this->aspectRatio = $aspectRatio;
  }
  public function getAspectRatio()
  {
    return $this->aspectRatio;
  }
  /**
   * The URL of the image.
   *
   * @param string $imageUrl
   */
  public function setImageUrl($imageUrl)
  {
    $this->imageUrl = $imageUrl;
  }
  /**
   * @return string
   */
  public function getImageUrl()
  {
    return $this->imageUrl;
  }
  /**
   * The `onclick` action.
   *
   * @param OnClick $onClick
   */
  public function setOnClick(OnClick $onClick)
  {
    $this->onClick = $onClick;
  }
  /**
   * @return OnClick
   */
  public function getOnClick()
  {
    return $this->onClick;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Image::class, 'Google_Service_HangoutsChat_Image');
