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

class SolidFill extends \Google\Model
{
  /**
   * The fraction of this `color` that should be applied to the pixel. That is,
   * the final pixel color is defined by the equation: pixel color = alpha *
   * (color) + (1.0 - alpha) * (background color) This means that a value of 1.0
   * corresponds to a solid color, whereas a value of 0.0 corresponds to a
   * completely transparent color.
   *
   * @var float
   */
  public $alpha;
  protected $colorType = OpaqueColor::class;
  protected $colorDataType = '';

  /**
   * The fraction of this `color` that should be applied to the pixel. That is,
   * the final pixel color is defined by the equation: pixel color = alpha *
   * (color) + (1.0 - alpha) * (background color) This means that a value of 1.0
   * corresponds to a solid color, whereas a value of 0.0 corresponds to a
   * completely transparent color.
   *
   * @param float $alpha
   */
  public function setAlpha($alpha)
  {
    $this->alpha = $alpha;
  }
  /**
   * @return float
   */
  public function getAlpha()
  {
    return $this->alpha;
  }
  /**
   * The color value of the solid fill.
   *
   * @param OpaqueColor $color
   */
  public function setColor(OpaqueColor $color)
  {
    $this->color = $color;
  }
  /**
   * @return OpaqueColor
   */
  public function getColor()
  {
    return $this->color;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SolidFill::class, 'Google_Service_Slides_SolidFill');
