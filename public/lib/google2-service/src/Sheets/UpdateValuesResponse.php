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

class UpdateValuesResponse extends \Google\Model
{
  /**
   * The spreadsheet the updates were applied to.
   *
   * @var string
   */
  public $spreadsheetId;
  /**
   * The number of cells updated.
   *
   * @var int
   */
  public $updatedCells;
  /**
   * The number of columns where at least one cell in the column was updated.
   *
   * @var int
   */
  public $updatedColumns;
  protected $updatedDataType = ValueRange::class;
  protected $updatedDataDataType = '';
  /**
   * The range (in A1 notation) that updates were applied to.
   *
   * @var string
   */
  public $updatedRange;
  /**
   * The number of rows where at least one cell in the row was updated.
   *
   * @var int
   */
  public $updatedRows;

  /**
   * The spreadsheet the updates were applied to.
   *
   * @param string $spreadsheetId
   */
  public function setSpreadsheetId($spreadsheetId)
  {
    $this->spreadsheetId = $spreadsheetId;
  }
  /**
   * @return string
   */
  public function getSpreadsheetId()
  {
    return $this->spreadsheetId;
  }
  /**
   * The number of cells updated.
   *
   * @param int $updatedCells
   */
  public function setUpdatedCells($updatedCells)
  {
    $this->updatedCells = $updatedCells;
  }
  /**
   * @return int
   */
  public function getUpdatedCells()
  {
    return $this->updatedCells;
  }
  /**
   * The number of columns where at least one cell in the column was updated.
   *
   * @param int $updatedColumns
   */
  public function setUpdatedColumns($updatedColumns)
  {
    $this->updatedColumns = $updatedColumns;
  }
  /**
   * @return int
   */
  public function getUpdatedColumns()
  {
    return $this->updatedColumns;
  }
  /**
   * The values of the cells after updates were applied. This is only included
   * if the request's `includeValuesInResponse` field was `true`.
   *
   * @param ValueRange $updatedData
   */
  public function setUpdatedData(ValueRange $updatedData)
  {
    $this->updatedData = $updatedData;
  }
  /**
   * @return ValueRange
   */
  public function getUpdatedData()
  {
    return $this->updatedData;
  }
  /**
   * The range (in A1 notation) that updates were applied to.
   *
   * @param string $updatedRange
   */
  public function setUpdatedRange($updatedRange)
  {
    $this->updatedRange = $updatedRange;
  }
  /**
   * @return string
   */
  public function getUpdatedRange()
  {
    return $this->updatedRange;
  }
  /**
   * The number of rows where at least one cell in the row was updated.
   *
   * @param int $updatedRows
   */
  public function setUpdatedRows($updatedRows)
  {
    $this->updatedRows = $updatedRows;
  }
  /**
   * @return int
   */
  public function getUpdatedRows()
  {
    return $this->updatedRows;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateValuesResponse::class, 'Google_Service_Sheets_UpdateValuesResponse');
