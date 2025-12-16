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

class UpdateTableCellStyleRequest extends \Google\Model
{
  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `tableCellStyle` is implied and should not be specified. A single
   * `"*"` can be used as short-hand for listing every field. For example to
   * update the table cell background color, set `fields` to
   * `"backgroundColor"`. To reset a property to its default value, include its
   * field name in the field mask but leave the field itself unset.
   *
   * @var string
   */
  public $fields;
  protected $tableCellStyleType = TableCellStyle::class;
  protected $tableCellStyleDataType = '';
  protected $tableRangeType = TableRange::class;
  protected $tableRangeDataType = '';
  protected $tableStartLocationType = Location::class;
  protected $tableStartLocationDataType = '';

  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `tableCellStyle` is implied and should not be specified. A single
   * `"*"` can be used as short-hand for listing every field. For example to
   * update the table cell background color, set `fields` to
   * `"backgroundColor"`. To reset a property to its default value, include its
   * field name in the field mask but leave the field itself unset.
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
   * The style to set on the table cells. When updating borders, if a cell
   * shares a border with an adjacent cell, the corresponding border property of
   * the adjacent cell is updated as well. Borders that are merged and invisible
   * are not updated. Since updating a border shared by adjacent cells in the
   * same request can cause conflicting border updates, border updates are
   * applied in the following order: - `border_right` - `border_left` -
   * `border_bottom` - `border_top`
   *
   * @param TableCellStyle $tableCellStyle
   */
  public function setTableCellStyle(TableCellStyle $tableCellStyle)
  {
    $this->tableCellStyle = $tableCellStyle;
  }
  /**
   * @return TableCellStyle
   */
  public function getTableCellStyle()
  {
    return $this->tableCellStyle;
  }
  /**
   * The table range representing the subset of the table to which the updates
   * are applied.
   *
   * @param TableRange $tableRange
   */
  public function setTableRange(TableRange $tableRange)
  {
    $this->tableRange = $tableRange;
  }
  /**
   * @return TableRange
   */
  public function getTableRange()
  {
    return $this->tableRange;
  }
  /**
   * The location where the table starts in the document. When specified, the
   * updates are applied to all the cells in the table.
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
class_alias(UpdateTableCellStyleRequest::class, 'Google_Service_Docs_UpdateTableCellStyleRequest');
