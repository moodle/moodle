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

class FilterCriteria extends \Google\Collection
{
  protected $collection_key = 'hiddenValues';
  protected $conditionType = BooleanCondition::class;
  protected $conditionDataType = '';
  /**
   * Values that should be hidden.
   *
   * @var string[]
   */
  public $hiddenValues;
  protected $visibleBackgroundColorType = Color::class;
  protected $visibleBackgroundColorDataType = '';
  protected $visibleBackgroundColorStyleType = ColorStyle::class;
  protected $visibleBackgroundColorStyleDataType = '';
  protected $visibleForegroundColorType = Color::class;
  protected $visibleForegroundColorDataType = '';
  protected $visibleForegroundColorStyleType = ColorStyle::class;
  protected $visibleForegroundColorStyleDataType = '';

  /**
   * A condition that must be true for values to be shown. (This does not
   * override hidden_values -- if a value is listed there, it will still be
   * hidden.)
   *
   * @param BooleanCondition $condition
   */
  public function setCondition(BooleanCondition $condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return BooleanCondition
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * Values that should be hidden.
   *
   * @param string[] $hiddenValues
   */
  public function setHiddenValues($hiddenValues)
  {
    $this->hiddenValues = $hiddenValues;
  }
  /**
   * @return string[]
   */
  public function getHiddenValues()
  {
    return $this->hiddenValues;
  }
  /**
   * The background fill color to filter by; only cells with this fill color are
   * shown. Mutually exclusive with visible_foreground_color. Deprecated: Use
   * visible_background_color_style.
   *
   * @deprecated
   * @param Color $visibleBackgroundColor
   */
  public function setVisibleBackgroundColor(Color $visibleBackgroundColor)
  {
    $this->visibleBackgroundColor = $visibleBackgroundColor;
  }
  /**
   * @deprecated
   * @return Color
   */
  public function getVisibleBackgroundColor()
  {
    return $this->visibleBackgroundColor;
  }
  /**
   * The background fill color to filter by; only cells with this fill color are
   * shown. This field is mutually exclusive with visible_foreground_color, and
   * must be set to an RGB-type color. If visible_background_color is also set,
   * this field takes precedence.
   *
   * @param ColorStyle $visibleBackgroundColorStyle
   */
  public function setVisibleBackgroundColorStyle(ColorStyle $visibleBackgroundColorStyle)
  {
    $this->visibleBackgroundColorStyle = $visibleBackgroundColorStyle;
  }
  /**
   * @return ColorStyle
   */
  public function getVisibleBackgroundColorStyle()
  {
    return $this->visibleBackgroundColorStyle;
  }
  /**
   * The foreground color to filter by; only cells with this foreground color
   * are shown. Mutually exclusive with visible_background_color. Deprecated:
   * Use visible_foreground_color_style.
   *
   * @deprecated
   * @param Color $visibleForegroundColor
   */
  public function setVisibleForegroundColor(Color $visibleForegroundColor)
  {
    $this->visibleForegroundColor = $visibleForegroundColor;
  }
  /**
   * @deprecated
   * @return Color
   */
  public function getVisibleForegroundColor()
  {
    return $this->visibleForegroundColor;
  }
  /**
   * The foreground color to filter by; only cells with this foreground color
   * are shown. This field is mutually exclusive with visible_background_color,
   * and must be set to an RGB-type color. If visible_foreground_color is also
   * set, this field takes precedence.
   *
   * @param ColorStyle $visibleForegroundColorStyle
   */
  public function setVisibleForegroundColorStyle(ColorStyle $visibleForegroundColorStyle)
  {
    $this->visibleForegroundColorStyle = $visibleForegroundColorStyle;
  }
  /**
   * @return ColorStyle
   */
  public function getVisibleForegroundColorStyle()
  {
    return $this->visibleForegroundColorStyle;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FilterCriteria::class, 'Google_Service_Sheets_FilterCriteria');
