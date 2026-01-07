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

namespace Google\Service\Walletobjects;

class ImageModuleData extends \Google\Model
{
  /**
   * The ID associated with an image module. This field is here to enable ease
   * of management of image modules.
   *
   * @var string
   */
  public $id;
  protected $mainImageType = Image::class;
  protected $mainImageDataType = '';

  /**
   * The ID associated with an image module. This field is here to enable ease
   * of management of image modules.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * A 100% width image.
   *
   * @param Image $mainImage
   */
  public function setMainImage(Image $mainImage)
  {
    $this->mainImage = $mainImage;
  }
  /**
   * @return Image
   */
  public function getMainImage()
  {
    return $this->mainImage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImageModuleData::class, 'Google_Service_Walletobjects_ImageModuleData');
