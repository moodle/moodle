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

namespace Google\Service\Docs;

class WeightedFontFamily extends \Google\Model
{
  /**
   * The font family of the text. The font family can be any font from the Font
   * menu in Docs or from [Google Fonts] (https://fonts.google.com/). If the
   * font name is unrecognized, the text is rendered in `Arial`.
   *
   * @var string
   */
  public $fontFamily;
  /**
   * The weight of the font. This field can have any value that's a multiple of
   * `100` between `100` and `900`, inclusive. This range corresponds to the
   * numerical values described in the CSS 2.1 Specification, [section
   * 15.6](https://www.w3.org/TR/CSS21/fonts.html#font-boldness), with non-
   * numerical values disallowed. The default value is `400` ("normal"). The
   * font weight makes up just one component of the rendered font weight. A
   * combination of the `weight` and the text style's resolved `bold` value
   * determine the rendered weight, after accounting for inheritance: * If the
   * text is bold and the weight is less than `400`, the rendered weight is 400.
   * * If the text is bold and the weight is greater than or equal to `400` but
   * is less than `700`, the rendered weight is `700`. * If the weight is
   * greater than or equal to `700`, the rendered weight is equal to the weight.
   * * If the text is not bold, the rendered weight is equal to the weight.
   *
   * @var int
   */
  public $weight;

  /**
   * The font family of the text. The font family can be any font from the Font
   * menu in Docs or from [Google Fonts] (https://fonts.google.com/). If the
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
   * The weight of the font. This field can have any value that's a multiple of
   * `100` between `100` and `900`, inclusive. This range corresponds to the
   * numerical values described in the CSS 2.1 Specification, [section
   * 15.6](https://www.w3.org/TR/CSS21/fonts.html#font-boldness), with non-
   * numerical values disallowed. The default value is `400` ("normal"). The
   * font weight makes up just one component of the rendered font weight. A
   * combination of the `weight` and the text style's resolved `bold` value
   * determine the rendered weight, after accounting for inheritance: * If the
   * text is bold and the weight is less than `400`, the rendered weight is 400.
   * * If the text is bold and the weight is greater than or equal to `400` but
   * is less than `700`, the rendered weight is `700`. * If the weight is
   * greater than or equal to `700`, the rendered weight is equal to the weight.
   * * If the text is not bold, the rendered weight is equal to the weight.
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
class_alias(WeightedFontFamily::class, 'Google_Service_Docs_WeightedFontFamily');
