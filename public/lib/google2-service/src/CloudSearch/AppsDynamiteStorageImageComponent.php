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

namespace Google\Service\CloudSearch;

class AppsDynamiteStorageImageComponent extends \Google\Model
{
  /**
   * @var string
   */
  public $altText;
  protected $borderStyleType = AppsDynamiteStorageBorderStyle::class;
  protected $borderStyleDataType = '';
  protected $cropStyleType = AppsDynamiteStorageImageCropStyle::class;
  protected $cropStyleDataType = '';
  /**
   * @var string
   */
  public $imageUri;

  /**
   * @param string
   */
  public function setAltText($altText)
  {
    $this->altText = $altText;
  }
  /**
   * @return string
   */
  public function getAltText()
  {
    return $this->altText;
  }
  /**
   * @param AppsDynamiteStorageBorderStyle
   */
  public function setBorderStyle(AppsDynamiteStorageBorderStyle $borderStyle)
  {
    $this->borderStyle = $borderStyle;
  }
  /**
   * @return AppsDynamiteStorageBorderStyle
   */
  public function getBorderStyle()
  {
    return $this->borderStyle;
  }
  /**
   * @param AppsDynamiteStorageImageCropStyle
   */
  public function setCropStyle(AppsDynamiteStorageImageCropStyle $cropStyle)
  {
    $this->cropStyle = $cropStyle;
  }
  /**
   * @return AppsDynamiteStorageImageCropStyle
   */
  public function getCropStyle()
  {
    return $this->cropStyle;
  }
  /**
   * @param string
   */
  public function setImageUri($imageUri)
  {
    $this->imageUri = $imageUri;
  }
  /**
   * @return string
   */
  public function getImageUri()
  {
    return $this->imageUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppsDynamiteStorageImageComponent::class, 'Google_Service_CloudSearch_AppsDynamiteStorageImageComponent');
