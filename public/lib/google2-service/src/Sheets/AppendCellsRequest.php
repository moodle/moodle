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

class AppendCellsRequest extends \Google\Collection
{
  protected $collection_key = 'rows';
  /**
   * The fields of CellData that should be updated. At least one field must be
   * specified. The root is the CellData; 'row.values.' should not be specified.
   * A single `"*"` can be used as short-hand for listing every field.
   *
   * @var string
   */
  public $fields;
  protected $rowsType = RowData::class;
  protected $rowsDataType = 'array';
  /**
   * The sheet ID to append the data to.
   *
   * @var int
   */
  public $sheetId;
  /**
   * The ID of the table to append data to. The data will be only appended to
   * the table body. This field also takes precedence over the `sheet_id` field.
   *
   * @var string
   */
  public $tableId;

  /**
   * The fields of CellData that should be updated. At least one field must be
   * specified. The root is the CellData; 'row.values.' should not be specified.
   * A single `"*"` can be used as short-hand for listing every field.
   *
   * @param string $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return string
   */
  public function getFields()
  {
    return $this->fields;
  }
  /**
   * The data to append.
   *
   * @param RowData[] $rows
   */
  public function setRows($rows)
  {
    $this->rows = $rows;
  }
  /**
   * @return RowData[]
   */
  public function getRows()
  {
    return $this->rows;
  }
  /**
   * The sheet ID to append the data to.
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
   * The ID of the table to append data to. The data will be only appended to
   * the table body. This field also takes precedence over the `sheet_id` field.
   *
   * @param string $tableId
   */
  public function setTableId($tableId)
  {
    $this->tableId = $tableId;
  }
  /**
   * @return string
   */
  public function getTableId()
  {
    return $this->tableId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppendCellsRequest::class, 'Google_Service_Sheets_AppendCellsRequest');
