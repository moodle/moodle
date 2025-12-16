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

class Recolor extends \Google\Collection
{
  /**
   * No recolor effect. The default value.
   */
  public const NAME_NONE = 'NONE';
  /**
   * A recolor effect that lightens the image using the page's first available
   * color from its color scheme.
   */
  public const NAME_LIGHT1 = 'LIGHT1';
  /**
   * A recolor effect that lightens the image using the page's second available
   * color from its color scheme.
   */
  public const NAME_LIGHT2 = 'LIGHT2';
  /**
   * A recolor effect that lightens the image using the page's third available
   * color from its color scheme.
   */
  public const NAME_LIGHT3 = 'LIGHT3';
  /**
   * A recolor effect that lightens the image using the page's fourth available
   * color from its color scheme.
   */
  public const NAME_LIGHT4 = 'LIGHT4';
  /**
   * A recolor effect that lightens the image using the page's fifth available
   * color from its color scheme.
   */
  public const NAME_LIGHT5 = 'LIGHT5';
  /**
   * A recolor effect that lightens the image using the page's sixth available
   * color from its color scheme.
   */
  public const NAME_LIGHT6 = 'LIGHT6';
  /**
   * A recolor effect that lightens the image using the page's seventh available
   * color from its color scheme.
   */
  public const NAME_LIGHT7 = 'LIGHT7';
  /**
   * A recolor effect that lightens the image using the page's eighth available
   * color from its color scheme.
   */
  public const NAME_LIGHT8 = 'LIGHT8';
  /**
   * A recolor effect that lightens the image using the page's ninth available
   * color from its color scheme.
   */
  public const NAME_LIGHT9 = 'LIGHT9';
  /**
   * A recolor effect that lightens the image using the page's tenth available
   * color from its color scheme.
   */
  public const NAME_LIGHT10 = 'LIGHT10';
  /**
   * A recolor effect that darkens the image using the page's first available
   * color from its color scheme.
   */
  public const NAME_DARK1 = 'DARK1';
  /**
   * A recolor effect that darkens the image using the page's second available
   * color from its color scheme.
   */
  public const NAME_DARK2 = 'DARK2';
  /**
   * A recolor effect that darkens the image using the page's third available
   * color from its color scheme.
   */
  public const NAME_DARK3 = 'DARK3';
  /**
   * A recolor effect that darkens the image using the page's fourth available
   * color from its color scheme.
   */
  public const NAME_DARK4 = 'DARK4';
  /**
   * A recolor effect that darkens the image using the page's fifth available
   * color from its color scheme.
   */
  public const NAME_DARK5 = 'DARK5';
  /**
   * A recolor effect that darkens the image using the page's sixth available
   * color from its color scheme.
   */
  public const NAME_DARK6 = 'DARK6';
  /**
   * A recolor effect that darkens the image using the page's seventh available
   * color from its color scheme.
   */
  public const NAME_DARK7 = 'DARK7';
  /**
   * A recolor effect that darkens the image using the page's eighth available
   * color from its color scheme.
   */
  public const NAME_DARK8 = 'DARK8';
  /**
   * A recolor effect that darkens the image using the page's ninth available
   * color from its color scheme.
   */
  public const NAME_DARK9 = 'DARK9';
  /**
   * A recolor effect that darkens the image using the page's tenth available
   * color from its color scheme.
   */
  public const NAME_DARK10 = 'DARK10';
  /**
   * A recolor effect that recolors the image to grayscale.
   */
  public const NAME_GRAYSCALE = 'GRAYSCALE';
  /**
   * A recolor effect that recolors the image to negative grayscale.
   */
  public const NAME_NEGATIVE = 'NEGATIVE';
  /**
   * A recolor effect that recolors the image using the sepia color.
   */
  public const NAME_SEPIA = 'SEPIA';
  /**
   * Custom recolor effect. Refer to `recolor_stops` for the concrete gradient.
   */
  public const NAME_CUSTOM = 'CUSTOM';
  protected $collection_key = 'recolorStops';
  /**
   * The name of the recolor effect. The name is determined from the
   * `recolor_stops` by matching the gradient against the colors in the page's
   * current color scheme. This property is read-only.
   *
   * @var string
   */
  public $name;
  protected $recolorStopsType = ColorStop::class;
  protected $recolorStopsDataType = 'array';

  /**
   * The name of the recolor effect. The name is determined from the
   * `recolor_stops` by matching the gradient against the colors in the page's
   * current color scheme. This property is read-only.
   *
   * Accepted values: NONE, LIGHT1, LIGHT2, LIGHT3, LIGHT4, LIGHT5, LIGHT6,
   * LIGHT7, LIGHT8, LIGHT9, LIGHT10, DARK1, DARK2, DARK3, DARK4, DARK5, DARK6,
   * DARK7, DARK8, DARK9, DARK10, GRAYSCALE, NEGATIVE, SEPIA, CUSTOM
   *
   * @param self::NAME_* $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return self::NAME_*
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The recolor effect is represented by a gradient, which is a list of color
   * stops. The colors in the gradient will replace the corresponding colors at
   * the same position in the color palette and apply to the image. This
   * property is read-only.
   *
   * @param ColorStop[] $recolorStops
   */
  public function setRecolorStops($recolorStops)
  {
    $this->recolorStops = $recolorStops;
  }
  /**
   * @return ColorStop[]
   */
  public function getRecolorStops()
  {
    return $this->recolorStops;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Recolor::class, 'Google_Service_Slides_Recolor');
