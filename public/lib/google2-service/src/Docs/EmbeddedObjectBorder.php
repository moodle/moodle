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

class EmbeddedObjectBorder extends \Google\Model
{
  /**
   * Unspecified dash style.
   */
  public const DASH_STYLE_DASH_STYLE_UNSPECIFIED = 'DASH_STYLE_UNSPECIFIED';
  /**
   * Solid line. Corresponds to ECMA-376 ST_PresetLineDashVal value 'solid'.
   * This is the default dash style.
   */
  public const DASH_STYLE_SOLID = 'SOLID';
  /**
   * Dotted line. Corresponds to ECMA-376 ST_PresetLineDashVal value 'dot'.
   */
  public const DASH_STYLE_DOT = 'DOT';
  /**
   * Dashed line. Corresponds to ECMA-376 ST_PresetLineDashVal value 'dash'.
   */
  public const DASH_STYLE_DASH = 'DASH';
  /**
   * If a property's state is RENDERED, then the element has the corresponding
   * property when rendered in the document. This is the default value.
   */
  public const PROPERTY_STATE_RENDERED = 'RENDERED';
  /**
   * If a property's state is NOT_RENDERED, then the element does not have the
   * corresponding property when rendered in the document.
   */
  public const PROPERTY_STATE_NOT_RENDERED = 'NOT_RENDERED';
  protected $colorType = OptionalColor::class;
  protected $colorDataType = '';
  /**
   * The dash style of the border.
   *
   * @var string
   */
  public $dashStyle;
  /**
   * The property state of the border property.
   *
   * @var string
   */
  public $propertyState;
  protected $widthType = Dimension::class;
  protected $widthDataType = '';

  /**
   * The color of the border.
   *
   * @param OptionalColor $color
   */
  public function setColor(OptionalColor $color)
  {
    $this->color = $color;
  }
  /**
   * @return OptionalColor
   */
  public function getColor()
  {
    return $this->color;
  }
  /**
   * The dash style of the border.
   *
   * Accepted values: DASH_STYLE_UNSPECIFIED, SOLID, DOT, DASH
   *
   * @param self::DASH_STYLE_* $dashStyle
   */
  public function setDashStyle($dashStyle)
  {
    $this->dashStyle = $dashStyle;
  }
  /**
   * @return self::DASH_STYLE_*
   */
  public function getDashStyle()
  {
    return $this->dashStyle;
  }
  /**
   * The property state of the border property.
   *
   * Accepted values: RENDERED, NOT_RENDERED
   *
   * @param self::PROPERTY_STATE_* $propertyState
   */
  public function setPropertyState($propertyState)
  {
    $this->propertyState = $propertyState;
  }
  /**
   * @return self::PROPERTY_STATE_*
   */
  public function getPropertyState()
  {
    return $this->propertyState;
  }
  /**
   * The width of the border.
   *
   * @param Dimension $width
   */
  public function setWidth(Dimension $width)
  {
    $this->width = $width;
  }
  /**
   * @return Dimension
   */
  public function getWidth()
  {
    return $this->width;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EmbeddedObjectBorder::class, 'Google_Service_Docs_EmbeddedObjectBorder');
