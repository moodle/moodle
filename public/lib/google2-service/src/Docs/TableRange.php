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

namespace Google\Service\Docs;

class TableRange extends \Google\Model
{
  /**
   * The column span of the table range.
   *
   * @var int
   */
  public $columnSpan;
  /**
   * The row span of the table range.
   *
   * @var int
   */
  public $rowSpan;
  protected $tableCellLocationType = TableCellLocation::class;
  protected $tableCellLocationDataType = '';

  /**
   * The column span of the table range.
   *
   * @param int $columnSpan
   */
  public function setColumnSpan($columnSpan)
  {
    $this->columnSpan = $columnSpan;
  }
  /**
   * @return int
   */
  public function getColumnSpan()
  {
    return $this->columnSpan;
  }
  /**
   * The row span of the table range.
   *
   * @param int $rowSpan
   */
  public function setRowSpan($rowSpan)
  {
    $this->rowSpan = $rowSpan;
  }
  /**
   * @return int
   */
  public function getRowSpan()
  {
    return $this->rowSpan;
  }
  /**
   * The cell location where the table range starts.
   *
   * @param TableCellLocation $tableCellLocation
   */
  public function setTableCellLocation(TableCellLocation $tableCellLocation)
  {
    $this->tableCellLocation = $tableCellLocation;
  }
  /**
   * @return TableCellLocation
   */
  public function getTableCellLocation()
  {
    return $this->tableCellLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TableRange::class, 'Google_Service_Docs_TableRange');
