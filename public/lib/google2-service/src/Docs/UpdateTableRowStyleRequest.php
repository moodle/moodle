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

class UpdateTableRowStyleRequest extends \Google\Collection
{
  protected $collection_key = 'rowIndices';
  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `tableRowStyle` is implied and should not be specified. A single
   * `"*"` can be used as short-hand for listing every field. For example to
   * update the minimum row height, set `fields` to `"min_row_height"`.
   *
   * @var string
   */
  public $fields;
  /**
   * The list of zero-based row indices whose style should be updated. If no
   * indices are specified, all rows will be updated.
   *
   * @var int[]
   */
  public $rowIndices;
  protected $tableRowStyleType = TableRowStyle::class;
  protected $tableRowStyleDataType = '';
  protected $tableStartLocationType = Location::class;
  protected $tableStartLocationDataType = '';

  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `tableRowStyle` is implied and should not be specified. A single
   * `"*"` can be used as short-hand for listing every field. For example to
   * update the minimum row height, set `fields` to `"min_row_height"`.
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
   * The list of zero-based row indices whose style should be updated. If no
   * indices are specified, all rows will be updated.
   *
   * @param int[] $rowIndices
   */
  public function setRowIndices($rowIndices)
  {
    $this->rowIndices = $rowIndices;
  }
  /**
   * @return int[]
   */
  public function getRowIndices()
  {
    return $this->rowIndices;
  }
  /**
   * The styles to be set on the rows.
   *
   * @param TableRowStyle $tableRowStyle
   */
  public function setTableRowStyle(TableRowStyle $tableRowStyle)
  {
    $this->tableRowStyle = $tableRowStyle;
  }
  /**
   * @return TableRowStyle
   */
  public function getTableRowStyle()
  {
    return $this->tableRowStyle;
  }
  /**
   * The location where the table starts in the document.
   *
   * @param Location $tableStartLocation
   */
  public function setTableStartLocation(Location $tableStartLocation)
  {
    $this->tableStartLocation = $tableStartLocation;
  }
  /**
   * @return Location
   */
  public function getTableStartLocation()
  {
    return $this->tableStartLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateTableRowStyleRequest::class, 'Google_Service_Docs_UpdateTableRowStyleRequest');
