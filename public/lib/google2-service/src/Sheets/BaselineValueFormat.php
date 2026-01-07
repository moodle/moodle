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

class BaselineValueFormat extends \Google\Model
{
  /**
   * Default value, do not use.
   */
  public const COMPARISON_TYPE_COMPARISON_TYPE_UNDEFINED = 'COMPARISON_TYPE_UNDEFINED';
  /**
   * Use absolute difference between key and baseline value.
   */
  public const COMPARISON_TYPE_ABSOLUTE_DIFFERENCE = 'ABSOLUTE_DIFFERENCE';
  /**
   * Use percentage difference between key and baseline value.
   */
  public const COMPARISON_TYPE_PERCENTAGE_DIFFERENCE = 'PERCENTAGE_DIFFERENCE';
  /**
   * The comparison type of key value with baseline value.
   *
   * @var string
   */
  public $comparisonType;
  /**
   * Description which is appended after the baseline value. This field is
   * optional.
   *
   * @var string
   */
  public $description;
  protected $negativeColorType = Color::class;
  protected $negativeColorDataType = '';
  protected $negativeColorStyleType = ColorStyle::class;
  protected $negativeColorStyleDataType = '';
  protected $positionType = TextPosition::class;
  protected $positionDataType = '';
  protected $positiveColorType = Color::class;
  protected $positiveColorDataType = '';
  protected $positiveColorStyleType = ColorStyle::class;
  protected $positiveColorStyleDataType = '';
  protected $textFormatType = TextFormat::class;
  protected $textFormatDataType = '';

  /**
   * The comparison type of key value with baseline value.
   *
   * Accepted values: COMPARISON_TYPE_UNDEFINED, ABSOLUTE_DIFFERENCE,
   * PERCENTAGE_DIFFERENCE
   *
   * @param self::COMPARISON_TYPE_* $comparisonType
   */
  public function setComparisonType($comparisonType)
  {
    $this->comparisonType = $comparisonType;
  }
  /**
   * @return self::COMPARISON_TYPE_*
   */
  public function getComparisonType()
  {
    return $this->comparisonType;
  }
  /**
   * Description which is appended after the baseline value. This field is
   * optional.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Color to be used, in case baseline value represents a negative change for
   * key value. This field is optional. Deprecated: Use negative_color_style.
   *
   * @deprecated
   * @param Color $negativeColor
   */
  public function setNegativeColor(Color $negativeColor)
  {
    $this->negativeColor = $negativeColor;
  }
  /**
   * @deprecated
   * @return Color
   */
  public function getNegativeColor()
  {
    return $this->negativeColor;
  }
  /**
   * Color to be used, in case baseline value represents a negative change for
   * key value. This field is optional. If negative_color is also set, this
   * field takes precedence.
   *
   * @param ColorStyle $negativeColorStyle
   */
  public function setNegativeColorStyle(ColorStyle $negativeColorStyle)
  {
    $this->negativeColorStyle = $negativeColorStyle;
  }
  /**
   * @return ColorStyle
   */
  public function getNegativeColorStyle()
  {
    return $this->negativeColorStyle;
  }
  /**
   * Specifies the horizontal text positioning of baseline value. This field is
   * optional. If not specified, default positioning is used.
   *
   * @param TextPosition $position
   */
  public function setPosition(TextPosition $position)
  {
    $this->position = $position;
  }
  /**
   * @return TextPosition
   */
  public function getPosition()
  {
    return $this->position;
  }
  /**
   * Color to be used, in case baseline value represents a positive change for
   * key value. This field is optional. Deprecated: Use positive_color_style.
   *
   * @deprecated
   * @param Color $positiveColor
   */
  public function setPositiveColor(Color $positiveColor)
  {
    $this->positiveColor = $positiveColor;
  }
  /**
   * @deprecated
   * @return Color
   */
  public function getPositiveColor()
  {
    return $this->positiveColor;
  }
  /**
   * Color to be used, in case baseline value represents a positive change for
   * key value. This field is optional. If positive_color is also set, this
   * field takes precedence.
   *
   * @param ColorStyle $positiveColorStyle
   */
  public function setPositiveColorStyle(ColorStyle $positiveColorStyle)
  {
    $this->positiveColorStyle = $positiveColorStyle;
  }
  /**
   * @return ColorStyle
   */
  public function getPositiveColorStyle()
  {
    return $this->positiveColorStyle;
  }
  /**
   * Text formatting options for baseline value. The link field is not
   * supported.
   *
   * @param TextFormat $textFormat
   */
  public function setTextFormat(TextFormat $textFormat)
  {
    $this->textFormat = $textFormat;
  }
  /**
   * @return TextFormat
   */
  public function getTextFormat()
  {
    return $this->textFormat;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BaselineValueFormat::class, 'Google_Service_Sheets_BaselineValueFormat');
