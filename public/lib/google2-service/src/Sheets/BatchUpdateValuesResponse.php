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

class BatchUpdateValuesResponse extends \Google\Collection
{
  protected $collection_key = 'responses';
  protected $responsesType = UpdateValuesResponse::class;
  protected $responsesDataType = 'array';
  /**
   * The spreadsheet the updates were applied to.
   *
   * @var string
   */
  public $spreadsheetId;
  /**
   * The total number of cells updated.
   *
   * @var int
   */
  public $totalUpdatedCells;
  /**
   * The total number of columns where at least one cell in the column was
   * updated.
   *
   * @var int
   */
  public $totalUpdatedColumns;
  /**
   * The total number of rows where at least one cell in the row was updated.
   *
   * @var int
   */
  public $totalUpdatedRows;
  /**
   * The total number of sheets where at least one cell in the sheet was
   * updated.
   *
   * @var int
   */
  public $totalUpdatedSheets;

  /**
   * One UpdateValuesResponse per requested range, in the same order as the
   * requests appeared.
   *
   * @param UpdateValuesResponse[] $responses
   */
  public function setResponses($responses)
  {
    $this->responses = $responses;
  }
  /**
   * @return UpdateValuesResponse[]
   */
  public function getResponses()
  {
    return $this->responses;
  }
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
   * The total number of cells updated.
   *
   * @param int $totalUpdatedCells
   */
  public function setTotalUpdatedCells($totalUpdatedCells)
  {
    $this->totalUpdatedCells = $totalUpdatedCells;
  }
  /**
   * @return int
   */
  public function getTotalUpdatedCells()
  {
    return $this->totalUpdatedCells;
  }
  /**
   * The total number of columns where at least one cell in the column was
   * updated.
   *
   * @param int $totalUpdatedColumns
   */
  public function setTotalUpdatedColumns($totalUpdatedColumns)
  {
    $this->totalUpdatedColumns = $totalUpdatedColumns;
  }
  /**
   * @return int
   */
  public function getTotalUpdatedColumns()
  {
    return $this->totalUpdatedColumns;
  }
  /**
   * The total number of rows where at least one cell in the row was updated.
   *
   * @param int $totalUpdatedRows
   */
  public function setTotalUpdatedRows($totalUpdatedRows)
  {
    $this->totalUpdatedRows = $totalUpdatedRows;
  }
  /**
   * @return int
   */
  public function getTotalUpdatedRows()
  {
    return $this->totalUpdatedRows;
  }
  /**
   * The total number of sheets where at least one cell in the sheet was
   * updated.
   *
   * @param int $totalUpdatedSheets
   */
  public function setTotalUpdatedSheets($totalUpdatedSheets)
  {
    $this->totalUpdatedSheets = $totalUpdatedSheets;
  }
  /**
   * @return int
   */
  public function getTotalUpdatedSheets()
  {
    return $this->totalUpdatedSheets;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchUpdateValuesResponse::class, 'Google_Service_Sheets_BatchUpdateValuesResponse');
