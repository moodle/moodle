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

class SlicerSpec extends \Google\Model
{
  /**
   * The horizontal alignment is not specified. Do not use this.
   */
  public const HORIZONTAL_ALIGNMENT_HORIZONTAL_ALIGN_UNSPECIFIED = 'HORIZONTAL_ALIGN_UNSPECIFIED';
  /**
   * The text is explicitly aligned to the left of the cell.
   */
  public const HORIZONTAL_ALIGNMENT_LEFT = 'LEFT';
  /**
   * The text is explicitly aligned to the center of the cell.
   */
  public const HORIZONTAL_ALIGNMENT_CENTER = 'CENTER';
  /**
   * The text is explicitly aligned to the right of the cell.
   */
  public const HORIZONTAL_ALIGNMENT_RIGHT = 'RIGHT';
  /**
   * True if the filter should apply to pivot tables. If not set, default to
   * `True`.
   *
   * @var bool
   */
  public $applyToPivotTables;
  protected $backgroundColorType = Color::class;
  protected $backgroundColorDataType = '';
  protected $backgroundColorStyleType = ColorStyle::class;
  protected $backgroundColorStyleDataType = '';
  /**
   * The zero-based column index in the data table on which the filter is
   * applied to.
   *
   * @var int
   */
  public $columnIndex;
  protected $dataRangeType = GridRange::class;
  protected $dataRangeDataType = '';
  protected $filterCriteriaType = FilterCriteria::class;
  protected $filterCriteriaDataType = '';
  /**
   * The horizontal alignment of title in the slicer. If unspecified, defaults
   * to `LEFT`
   *
   * @var string
   */
  public $horizontalAlignment;
  protected $textFormatType = TextFormat::class;
  protected $textFormatDataType = '';
  /**
   * The title of the slicer.
   *
   * @var string
   */
  public $title;

  /**
   * True if the filter should apply to pivot tables. If not set, default to
   * `True`.
   *
   * @param bool $applyToPivotTables
   */
  public function setApplyToPivotTables($applyToPivotTables)
  {
    $this->applyToPivotTables = $applyToPivotTables;
  }
  /**
   * @return bool
   */
  public function getApplyToPivotTables()
  {
    return $this->applyToPivotTables;
  }
  /**
   * The background color of the slicer. Deprecated: Use background_color_style.
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
   * The background color of the slicer. If background_color is also set, this
   * field takes precedence.
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
   * The zero-based column index in the data table on which the filter is
   * applied to.
   *
   * @param int $columnIndex
   */
  public function setColumnIndex($columnIndex)
  {
    $this->columnIndex = $columnIndex;
  }
  /**
   * @return int
   */
  public function getColumnIndex()
  {
    return $this->columnIndex;
  }
  /**
   * The data range of the slicer.
   *
   * @param GridRange $dataRange
   */
  public function setDataRange(GridRange $dataRange)
  {
    $this->dataRange = $dataRange;
  }
  /**
   * @return GridRange
   */
  public function getDataRange()
  {
    return $this->dataRange;
  }
  /**
   * The filtering criteria of the slicer.
   *
   * @param FilterCriteria $filterCriteria
   */
  public function setFilterCriteria(FilterCriteria $filterCriteria)
  {
    $this->filterCriteria = $filterCriteria;
  }
  /**
   * @return FilterCriteria
   */
  public function getFilterCriteria()
  {
    return $this->filterCriteria;
  }
  /**
   * The horizontal alignment of title in the slicer. If unspecified, defaults
   * to `LEFT`
   *
   * Accepted values: HORIZONTAL_ALIGN_UNSPECIFIED, LEFT, CENTER, RIGHT
   *
   * @param self::HORIZONTAL_ALIGNMENT_* $horizontalAlignment
   */
  public function setHorizontalAlignment($horizontalAlignment)
  {
    $this->horizontalAlignment = $horizontalAlignment;
  }
  /**
   * @return self::HORIZONTAL_ALIGNMENT_*
   */
  public function getHorizontalAlignment()
  {
    return $this->horizontalAlignment;
  }
  /**
   * The text format of title in the slicer. The link field is not supported.
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
  /**
   * The title of the slicer.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SlicerSpec::class, 'Google_Service_Sheets_SlicerSpec');
