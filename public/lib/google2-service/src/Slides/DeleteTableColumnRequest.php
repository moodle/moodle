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

namespace Google\Service\Slides;

class DeleteTableColumnRequest extends \Google\Model
{
  protected $cellLocationType = TableCellLocation::class;
  protected $cellLocationDataType = '';
  /**
   * The table to delete columns from.
   *
   * @var string
   */
  public $tableObjectId;

  /**
   * The reference table cell location from which a column will be deleted. The
   * column this cell spans will be deleted. If this is a merged cell, multiple
   * columns will be deleted. If no columns remain in the table after this
   * deletion, the whole table is deleted.
   *
   * @param TableCellLocation $cellLocation
   */
  public function setCellLocation(TableCellLocation $cellLocation)
  {
    $this->cellLocation = $cellLocation;
  }
  /**
   * @return TableCellLocation
   */
  public function getCellLocation()
  {
    return $this->cellLocation;
  }
  /**
   * The table to delete columns from.
   *
   * @param string $tableObjectId
   */
  public function setTableObjectId($tableObjectId)
  {
    $this->tableObjectId = $tableObjectId;
  }
  /**
   * @return string
   */
  public function getTableObjectId()
  {
    return $this->tableObjectId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeleteTableColumnRequest::class, 'Google_Service_Slides_DeleteTableColumnRequest');
