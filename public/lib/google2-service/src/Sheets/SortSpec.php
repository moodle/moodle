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

class SortSpec extends \Google\Model
{
  /**
   * Default value, do not use this.
   */
  public const SORT_ORDER_SORT_ORDER_UNSPECIFIED = 'SORT_ORDER_UNSPECIFIED';
  /**
   * Sort ascending.
   */
  public const SORT_ORDER_ASCENDING = 'ASCENDING';
  /**
   * Sort descending.
   */
  public const SORT_ORDER_DESCENDING = 'DESCENDING';
  protected $backgroundColorType = Color::class;
  protected $backgroundColorDataType = '';
  protected $backgroundColorStyleType = ColorStyle::class;
  protected $backgroundColorStyleDataType = '';
  protected $dataSourceColumnReferenceType = DataSourceColumnReference::class;
  protected $dataSourceColumnReferenceDataType = '';
  /**
   * The dimension the sort should be applied to.
   *
   * @var int
   */
  public $dimensionIndex;
  protected $foregroundColorType = Color::class;
  protected $foregroundColorDataType = '';
  protected $foregroundColorStyleType = ColorStyle::class;
  protected $foregroundColorStyleDataType = '';
  /**
   * The order data should be sorted.
   *
   * @var string
   */
  public $sortOrder;

  /**
   * The background fill color to sort by; cells with this fill color are sorted
   * to the top. Mutually exclusive with foreground_color. Deprecated: Use
   * background_color_style.
   *
   * @deprecated
   * @param Color $backgroundColor
   */
  public function setBackgroundColor(Color $backgroundColor)
  {
    $this->backgroundColor = $backgroundColor;
  }
  /**
   * @deprecated
   * @return Color
   */
  public function getBackgroundColor()
  {
    return $this->backgroundColor;
  }
  /**
   * The background fill color to sort by; cells with this fill color are sorted
   * to the top. Mutually exclusive with foreground_color, and must be an RGB-
   * type color. If background_color is also set, this field takes precedence.
   *
   * @param ColorStyle $backgroundColorStyle
   */
  public function setBackgroundColorStyle(ColorStyle $backgroundColorStyle)
  {
    $this->backgroundColorStyle = $backgroundColorStyle;
  }
  /**
   * @return ColorStyle
   */
  public function getBackgroundColorStyle()
  {
    return $this->backgroundColorStyle;
  }
  /**
   * Reference to a data source column.
   *
   * @param DataSourceColumnReference $dataSourceColumnReference
   */
  public function setDataSourceColumnReference(DataSourceColumnReference $dataSourceColumnReference)
  {
    $this->dataSourceColumnReference = $dataSourceColumnReference;
  }
  /**
   * @return DataSourceColumnReference
   */
  public function getDataSourceColumnReference()
  {
    return $this->dataSourceColumnReference;
  }
  /**
   * The dimension the sort should be applied to.
   *
   * @param int $dimensionIndex
   */
  public function setDimensionIndex($dimensionIndex)
  {
    $this->dimensionIndex = $dimensionIndex;
  }
  /**
   * @return int
   */
  public function getDimensionIndex()
  {
    return $this->dimensionIndex;
  }
  /**
   * The foreground color to sort by; cells with this foreground color are
   * sorted to the top. Mutually exclusive with background_color. Deprecated:
   * Use foreground_color_style.
   *
   * @deprecated
   * @param Color $foregroundColor
   */
  public function setForegroundColor(Color $foregroundColor)
  {
    $this->foregroundColor = $foregroundColor;
  }
  /**
   * @deprecated
   * @return Color
   */
  public function getForegroundColor()
  {
    return $this->foregroundColor;
  }
  /**
   * The foreground color to sort by; cells with this foreground color are
   * sorted to the top. Mutually exclusive with background_color, and must be an
   * RGB-type color. If foreground_color is also set, this field takes
   * precedence.
   *
   * @param ColorStyle $foregroundColorStyle
   */
  public function setForegroundColorStyle(ColorStyle $foregroundColorStyle)
  {
    $this->foregroundColorStyle = $foregroundColorStyle;
  }
  /**
   * @return ColorStyle
   */
  public function getForegroundColorStyle()
  {
    return $this->foregroundColorStyle;
  }
  /**
   * The order data should be sorted.
   *
   * Accepted values: SORT_ORDER_UNSPECIFIED, ASCENDING, DESCENDING
   *
   * @param self::SORT_ORDER_* $sortOrder
   */
  public function setSortOrder($sortOrder)
  {
    $this->sortOrder = $sortOrder;
  }
  /**
   * @return self::SORT_ORDER_*
   */
  public function getSortOrder()
  {
    return $this->sortOrder;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SortSpec::class, 'Google_Service_Sheets_SortSpec');
