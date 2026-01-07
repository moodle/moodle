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

class WeightedFontFamily extends \Google\Model
{
  /**
   * The font family of the text. The font family can be any font from the Font
   * menu in Slides or from [Google Fonts] (https://fonts.google.com/). If the
   * font name is unrecognized, the text is rendered in `Arial`.
   *
   * @var string
   */
  public $fontFamily;
  /**
   * The rendered weight of the text. This field can have any value that is a
   * multiple of `100` between `100` and `900`, inclusive. This range
   * corresponds to the numerical values described in the CSS 2.1 Specification,
   * [section 15.6](https://www.w3.org/TR/CSS21/fonts.html#font-boldness), with
   * non-numerical values disallowed. Weights greater than or equal to `700` are
   * considered bold, and weights less than `700`are not bold. The default value
   * is `400` ("normal").
   *
   * @var int
   */
  public $weight;

  /**
   * The font family of the text. The font family can be any font from the Font
   * menu in Slides or from [Google Fonts] (https://fonts.google.com/). If the
   * font name is unrecognized, the text is rendered in `Arial`.
   *
   * @param string $fontFamily
   */
  public function setFontFamily($fontFamily)
  {
    $this->fontFamily = $fontFamily;
  }
  /**
   * @return string
   */
  public function getFontFamily()
  {
    return $this->fontFamily;
  }
  /**
   * The rendered weight of the text. This field can have any value that is a
   * multiple of `100` between `100` and `900`, inclusive. This range
   * corresponds to the numerical values described in the CSS 2.1 Specification,
   * [section 15.6](https://www.w3.org/TR/CSS21/fonts.html#font-boldness), with
   * non-numerical values disallowed. Weights greater than or equal to `700` are
   * considered bold, and weights less than `700`are not bold. The default value
   * is `400` ("normal").
   *
   * @param int $weight
   */
  public function setWeight($weight)
  {
    $this->weight = $weight;
  }
  /**
   * @return int
   */
  public function getWeight()
  {
    return $this->weight;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WeightedFontFamily::class, 'Google_Service_Slides_WeightedFontFamily');
