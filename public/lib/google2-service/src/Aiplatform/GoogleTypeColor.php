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

namespace Google\Service\Aiplatform;

class GoogleTypeColor extends \Google\Model
{
  /**
   * The fraction of this color that should be applied to the pixel. That is,
   * the final pixel color is defined by the equation: `pixel color = alpha *
   * (this color) + (1.0 - alpha) * (background color)` This means that a value
   * of 1.0 corresponds to a solid color, whereas a value of 0.0 corresponds to
   * a completely transparent color. This uses a wrapper message rather than a
   * simple float scalar so that it is possible to distinguish between a default
   * value and the value being unset. If omitted, this color object is rendered
   * as a solid color (as if the alpha value had been explicitly given a value
   * of 1.0).
   *
   * @var float
   */
  public $alpha;
  /**
   * The amount of blue in the color as a value in the interval [0, 1].
   *
   * @var float
   */
  public $blue;
  /**
   * The amount of green in the color as a value in the interval [0, 1].
   *
   * @var float
   */
  public $green;
  /**
   * The amount of red in the color as a value in the interval [0, 1].
   *
   * @var float
   */
  public $red;

  /**
   * The fraction of this color that should be applied to the pixel. That is,
   * the final pixel color is defined by the equation: `pixel color = alpha *
   * (this color) + (1.0 - alpha) * (background color)` This means that a value
   * of 1.0 corresponds to a solid color, whereas a value of 0.0 corresponds to
   * a completely transparent color. This uses a wrapper message rather than a
   * simple float scalar so that it is possible to distinguish between a default
   * value and the value being unset. If omitted, this color object is rendered
   * as a solid color (as if the alpha value had been explicitly given a value
   * of 1.0).
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
   * The amount of blue in the color as a value in the interval [0, 1].
   *
   * @param float $blue
   */
  public function setBlue($blue)
  {
    $this->blue = $blue;
  }
  /**
   * @return float
   */
  public function getBlue()
  {
    return $this->blue;
  }
  /**
   * The amount of green in the color as a value in the interval [0, 1].
   *
   * @param float $green
   */
  public function setGreen($green)
  {
    $this->green = $green;
  }
  /**
   * @return float
   */
  public function getGreen()
  {
    return $this->green;
  }
  /**
   * The amount of red in the color as a value in the interval [0, 1].
   *
   * @param float $red
   */
  public function setRed($red)
  {
    $this->red = $red;
  }
  /**
   * @return float
   */
  public function getRed()
  {
    return $this->red;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleTypeColor::class, 'Google_Service_Aiplatform_GoogleTypeColor');
