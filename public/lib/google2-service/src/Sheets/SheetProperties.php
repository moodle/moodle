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

class SheetProperties extends \Google\Model
{
  /**
   * Default value, do not use.
   */
  public const SHEET_TYPE_SHEET_TYPE_UNSPECIFIED = 'SHEET_TYPE_UNSPECIFIED';
  /**
   * The sheet is a grid.
   */
  public const SHEET_TYPE_GRID = 'GRID';
  /**
   * The sheet has no grid and instead has an object like a chart or image.
   */
  public const SHEET_TYPE_OBJECT = 'OBJECT';
  /**
   * The sheet connects with an external DataSource and shows the preview of
   * data.
   */
  public const SHEET_TYPE_DATA_SOURCE = 'DATA_SOURCE';
  protected $dataSourceSheetPropertiesType = DataSourceSheetProperties::class;
  protected $dataSourceSheetPropertiesDataType = '';
  protected $gridPropertiesType = GridProperties::class;
  protected $gridPropertiesDataType = '';
  /**
   * True if the sheet is hidden in the UI, false if it's visible.
   *
   * @var bool
   */
  public $hidden;
  /**
   * The index of the sheet within the spreadsheet. When adding or updating
   * sheet properties, if this field is excluded then the sheet is added or
   * moved to the end of the sheet list. When updating sheet indices or
   * inserting sheets, movement is considered in "before the move" indexes. For
   * example, if there were three sheets (S1, S2, S3) in order to move S1 ahead
   * of S2 the index would have to be set to 2. A sheet index update request is
   * ignored if the requested index is identical to the sheets current index or
   * if the requested new index is equal to the current sheet index + 1.
   *
   * @var int
   */
  public $index;
  /**
   * True if the sheet is an RTL sheet instead of an LTR sheet.
   *
   * @var bool
   */
  public $rightToLeft;
  /**
   * The ID of the sheet. Must be non-negative. This field cannot be changed
   * once set.
   *
   * @var int
   */
  public $sheetId;
  /**
   * The type of sheet. Defaults to GRID. This field cannot be changed once set.
   *
   * @var string
   */
  public $sheetType;
  protected $tabColorType = Color::class;
  protected $tabColorDataType = '';
  protected $tabColorStyleType = ColorStyle::class;
  protected $tabColorStyleDataType = '';
  /**
   * The name of the sheet.
   *
   * @var string
   */
  public $title;

  /**
   * Output only. If present, the field contains DATA_SOURCE sheet specific
   * properties.
   *
   * @param DataSourceSheetProperties $dataSourceSheetProperties
   */
  public function setDataSourceSheetProperties(DataSourceSheetProperties $dataSourceSheetProperties)
  {
    $this->dataSourceSheetProperties = $dataSourceSheetProperties;
  }
  /**
   * @return DataSourceSheetProperties
   */
  public function getDataSourceSheetProperties()
  {
    return $this->dataSourceSheetProperties;
  }
  /**
   * Additional properties of the sheet if this sheet is a grid. (If the sheet
   * is an object sheet, containing a chart or image, then this field will be
   * absent.) When writing it is an error to set any grid properties on non-grid
   * sheets. If this sheet is a DATA_SOURCE sheet, this field is output only but
   * contains the properties that reflect how a data source sheet is rendered in
   * the UI, e.g. row_count.
   *
   * @param GridProperties $gridProperties
   */
  public function setGridProperties(GridProperties $gridProperties)
  {
    $this->gridProperties = $gridProperties;
  }
  /**
   * @return GridProperties
   */
  public function getGridProperties()
  {
    return $this->gridProperties;
  }
  /**
   * True if the sheet is hidden in the UI, false if it's visible.
   *
   * @param bool $hidden
   */
  public function setHidden($hidden)
  {
    $this->hidden = $hidden;
  }
  /**
   * @return bool
   */
  public function getHidden()
  {
    return $this->hidden;
  }
  /**
   * The index of the sheet within the spreadsheet. When adding or updating
   * sheet properties, if this field is excluded then the sheet is added or
   * moved to the end of the sheet list. When updating sheet indices or
   * inserting sheets, movement is considered in "before the move" indexes. For
   * example, if there were three sheets (S1, S2, S3) in order to move S1 ahead
   * of S2 the index would have to be set to 2. A sheet index update request is
   * ignored if the requested index is identical to the sheets current index or
   * if the requested new index is equal to the current sheet index + 1.
   *
   * @param int $index
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }
  /**
   * @return int
   */
  public function getIndex()
  {
    return $this->index;
  }
  /**
   * True if the sheet is an RTL sheet instead of an LTR sheet.
   *
   * @param bool $rightToLeft
   */
  public function setRightToLeft($rightToLeft)
  {
    $this->rightToLeft = $rightToLeft;
  }
  /**
   * @return bool
   */
  public function getRightToLeft()
  {
    return $this->rightToLeft;
  }
  /**
   * The ID of the sheet. Must be non-negative. This field cannot be changed
   * once set.
   *
   * @param int $sheetId
   */
  public function setSheetId($sheetId)
  {
    $this->sheetId = $sheetId;
  }
  /**
   * @return int
   */
  public function getSheetId()
  {
    return $this->sheetId;
  }
  /**
   * The type of sheet. Defaults to GRID. This field cannot be changed once set.
   *
   * Accepted values: SHEET_TYPE_UNSPECIFIED, GRID, OBJECT, DATA_SOURCE
   *
   * @param self::SHEET_TYPE_* $sheetType
   */
  public function setSheetType($sheetType)
  {
    $this->sheetType = $sheetType;
  }
  /**
   * @return self::SHEET_TYPE_*
   */
  public function getSheetType()
  {
    return $this->sheetType;
  }
  /**
   * The color of the tab in the UI. Deprecated: Use tab_color_style.
   *
   * @deprecated
   * @param Color $tabColor
   */
  public function setTabColor(Color $tabColor)
  {
    $this->tabColor = $tabColor;
  }
  /**
   * @deprecated
   * @return Color
   */
  public function getTabColor()
  {
    return $this->tabColor;
  }
  /**
   * The color of the tab in the UI. If tab_color is also set, this field takes
   * precedence.
   *
   * @param ColorStyle $tabColorStyle
   */
  public function setTabColorStyle(ColorStyle $tabColorStyle)
  {
    $this->tabColorStyle = $tabColorStyle;
  }
  /**
   * @return ColorStyle
   */
  public function getTabColorStyle()
  {
    return $this->tabColorStyle;
  }
  /**
   * The name of the sheet.
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
class_alias(SheetProperties::class, 'Google_Service_Sheets_SheetProperties');
