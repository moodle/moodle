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

namespace Google\Service\Sheets;

class Border extends \Google\Model
{
  /**
   * The style is not specified. Do not use this.
   */
  public const STYLE_STYLE_UNSPECIFIED = 'STYLE_UNSPECIFIED';
  /**
   * The border is dotted.
   */
  public const STYLE_DOTTED = 'DOTTED';
  /**
   * The border is dashed.
   */
  public const STYLE_DASHED = 'DASHED';
  /**
   * The border is a thin solid line.
   */
  public const STYLE_SOLID = 'SOLID';
  /**
   * The border is a medium solid line.
   */
  public const STYLE_SOLID_MEDIUM = 'SOLID_MEDIUM';
  /**
   * The border is a thick solid line.
   */
  public const STYLE_SOLID_THICK = 'SOLID_THICK';
  /**
   * No border. Used only when updating a border in order to erase it.
   */
  public const STYLE_NONE = 'NONE';
  /**
   * The border is two solid lines.
   */
  public const STYLE_DOUBLE = 'DOUBLE';
  protected $colorType = Color::class;
  protected $colorDataType = '';
  protected $colorStyleType = ColorStyle::class;
  protected $colorStyleDataType = '';
  /**
   * The style of the border.
   *
   * @var string
   */
  public $style;
  /**
   * The width of the border, in pixels. Deprecated; the width is determined by
   * the "style" field.
   *
   * @deprecated
   * @var int
   */
  public $width;

  /**
   * The color of the border. Deprecated: Use color_style.
   *
   * @deprecated
   * @param Color $color
   */
  public function setColor(Color $color)
  {
    $this->color = $color;
  }
  /**
   * @deprecated
   * @return Color
   */
  public function getColor()
  {
    return $this->color;
  }
  /**
   * The color of the border. If color is also set, this field takes precedence.
   *
   * @param ColorStyle $colorStyle
   */
  public function setColorStyle(ColorStyle $colorStyle)
  {
    $this->colorStyle = $colorStyle;
  }
  /**
   * @return ColorStyle
   */
  public function getColorStyle()
  {
    return $this->colorStyle;
  }
  /**
   * The style of the border.
   *
   * Accepted values: STYLE_UNSPECIFIED, DOTTED, DASHED, SOLID, SOLID_MEDIUM,
   * SOLID_THICK, NONE, DOUBLE
   *
   * @param self::STYLE_* $style
   */
  public function setStyle($style)
  {
    $this->style = $style;
  }
  /**
   * @return self::STYLE_*
   */
  public function getStyle()
  {
    return $this->style;
  }
  /**
   * The width of the border, in pixels. Deprecated; the width is determined by
   * the "style" field.
   *
   * @deprecated
   * @param int $width
   */
  public function setWidth($width)
  {
    $this->width = $width;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getWidth()
  {
    return $this->width;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Border::class, 'Google_Service_Sheets_Border');
