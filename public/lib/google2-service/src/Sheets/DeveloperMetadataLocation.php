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

class DeveloperMetadataLocation extends \Google\Model
{
  /**
   * Default value.
   */
  public const LOCATION_TYPE_DEVELOPER_METADATA_LOCATION_TYPE_UNSPECIFIED = 'DEVELOPER_METADATA_LOCATION_TYPE_UNSPECIFIED';
  /**
   * Developer metadata associated on an entire row dimension.
   */
  public const LOCATION_TYPE_ROW = 'ROW';
  /**
   * Developer metadata associated on an entire column dimension.
   */
  public const LOCATION_TYPE_COLUMN = 'COLUMN';
  /**
   * Developer metadata associated on an entire sheet.
   */
  public const LOCATION_TYPE_SHEET = 'SHEET';
  /**
   * Developer metadata associated on the entire spreadsheet.
   */
  public const LOCATION_TYPE_SPREADSHEET = 'SPREADSHEET';
  protected $dimensionRangeType = DimensionRange::class;
  protected $dimensionRangeDataType = '';
  /**
   * The type of location this object represents. This field is read-only.
   *
   * @var string
   */
  public $locationType;
  /**
   * The ID of the sheet when metadata is associated with an entire sheet.
   *
   * @var int
   */
  public $sheetId;
  /**
   * True when metadata is associated with an entire spreadsheet.
   *
   * @var bool
   */
  public $spreadsheet;

  /**
   * Represents the row or column when metadata is associated with a dimension.
   * The specified DimensionRange must represent a single row or column; it
   * cannot be unbounded or span multiple rows or columns.
   *
   * @param DimensionRange $dimensionRange
   */
  public function setDimensionRange(DimensionRange $dimensionRange)
  {
    $this->dimensionRange = $dimensionRange;
  }
  /**
   * @return DimensionRange
   */
  public function getDimensionRange()
  {
    return $this->dimensionRange;
  }
  /**
   * The type of location this object represents. This field is read-only.
   *
   * Accepted values: DEVELOPER_METADATA_LOCATION_TYPE_UNSPECIFIED, ROW, COLUMN,
   * SHEET, SPREADSHEET
   *
   * @param self::LOCATION_TYPE_* $locationType
   */
  public function setLocationType($locationType)
  {
    $this->locationType = $locationType;
  }
  /**
   * @return self::LOCATION_TYPE_*
   */
  public function getLocationType()
  {
    return $this->locationType;
  }
  /**
   * The ID of the sheet when metadata is associated with an entire sheet.
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
   * True when metadata is associated with an entire spreadsheet.
   *
   * @param bool $spreadsheet
   */
  public function setSpreadsheet($spreadsheet)
  {
    $this->spreadsheet = $spreadsheet;
  }
  /**
   * @return bool
   */
  public function getSpreadsheet()
  {
    return $this->spreadsheet;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeveloperMetadataLocation::class, 'Google_Service_Sheets_DeveloperMetadataLocation');
