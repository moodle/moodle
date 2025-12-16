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

class GoogleCloudRetailV2ColorInfo extends \Google\Collection
{
  protected $collection_key = 'colors';
  /**
   * The standard color families. Strongly recommended to use the following
   * standard color groups: "Red", "Pink", "Orange", "Yellow", "Purple",
   * "Green", "Cyan", "Blue", "Brown", "White", "Gray", "Black" and "Mixed".
   * Normally it is expected to have only 1 color family. May consider using
   * single "Mixed" instead of multiple values. A maximum of 5 values are
   * allowed. Each value must be a UTF-8 encoded string with a length limit of
   * 128 characters. Otherwise, an INVALID_ARGUMENT error is returned. Google
   * Merchant Center property
   * [color](https://support.google.com/merchants/answer/6324487). Schema.org
   * property [Product.color](https://schema.org/color). The colorFamilies field
   * as a system attribute is not a required field but strongly recommended to
   * be specified. Google Search models treat this field as more important than
   * a custom product attribute when specified.
   *
   * @var string[]
   */
  public $colorFamilies;
  /**
   * The color display names, which may be different from standard color family
   * names, such as the color aliases used in the website frontend. Normally it
   * is expected to have only 1 color. May consider using single "Mixed" instead
   * of multiple values. A maximum of 75 colors are allowed. Each value must be
   * a UTF-8 encoded string with a length limit of 128 characters. Otherwise, an
   * INVALID_ARGUMENT error is returned. Google Merchant Center property
   * [color](https://support.google.com/merchants/answer/6324487). Schema.org
   * property [Product.color](https://schema.org/color).
   *
   * @var string[]
   */
  public $colors;

  /**
   * The standard color families. Strongly recommended to use the following
   * standard color groups: "Red", "Pink", "Orange", "Yellow", "Purple",
   * "Green", "Cyan", "Blue", "Brown", "White", "Gray", "Black" and "Mixed".
   * Normally it is expected to have only 1 color family. May consider using
   * single "Mixed" instead of multiple values. A maximum of 5 values are
   * allowed. Each value must be a UTF-8 encoded string with a length limit of
   * 128 characters. Otherwise, an INVALID_ARGUMENT error is returned. Google
   * Merchant Center property
   * [color](https://support.google.com/merchants/answer/6324487). Schema.org
   * property [Product.color](https://schema.org/color). The colorFamilies field
   * as a system attribute is not a required field but strongly recommended to
   * be specified. Google Search models treat this field as more important than
   * a custom product attribute when specified.
   *
   * @param string[] $colorFamilies
   */
  public function setColorFamilies($colorFamilies)
  {
    $this->colorFamilies = $colorFamilies;
  }
  /**
   * @return string[]
   */
  public function getColorFamilies()
  {
    return $this->colorFamilies;
  }
  /**
   * The color display names, which may be different from standard color family
   * names, such as the color aliases used in the website frontend. Normally it
   * is expected to have only 1 color. May consider using single "Mixed" instead
   * of multiple values. A maximum of 75 colors are allowed. Each value must be
   * a UTF-8 encoded string with a length limit of 128 characters. Otherwise, an
   * INVALID_ARGUMENT error is returned. Google Merchant Center property
   * [color](https://support.google.com/merchants/answer/6324487). Schema.org
   * property [Product.color](https://schema.org/color).
   *
   * @param string[] $colors
   */
  public function setColors($colors)
  {
    $this->colors = $colors;
  }
  /**
   * @return string[]
   */
  public function getColors()
  {
    return $this->colors;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2ColorInfo::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2ColorInfo');
