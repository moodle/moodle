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

class UpdateTableColumnPropertiesRequest extends \Google\Collection
{
  protected $collection_key = 'columnIndices';
  /**
   * The list of zero-based column indices whose property should be updated. If
   * no indices are specified, all columns will be updated.
   *
   * @var int[]
   */
  public $columnIndices;
  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `tableColumnProperties` is implied and should not be specified. A
   * single `"*"` can be used as short-hand for listing every field. For example
   * to update the column width, set `fields` to `"width"`.
   *
   * @var string
   */
  public $fields;
  protected $tableColumnPropertiesType = TableColumnProperties::class;
  protected $tableColumnPropertiesDataType = '';
  protected $tableStartLocationType = Location::class;
  protected $tableStartLocationDataType = '';

  /**
   * The list of zero-based column indices whose property should be updated. If
   * no indices are specified, all columns will be updated.
   *
   * @param int[] $columnIndices
   */
  public function setColumnIndices($columnIndices)
  {
    $this->columnIndices = $columnIndices;
  }
  /**
   * @return int[]
   */
  public function getColumnIndices()
  {
    return $this->columnIndices;
  }
  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `tableColumnProperties` is implied and should not be specified. A
   * single `"*"` can be used as short-hand for listing every field. For example
   * to update the column width, set `fields` to `"width"`.
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
   * The table column properties to update. If the value of
   * `table_column_properties#width` is less than 5 points (5/72 inch), a 400
   * bad request error is returned.
   *
   * @param TableColumnProperties $tableColumnProperties
   */
  public function setTableColumnProperties(TableColumnProperties $tableColumnProperties)
  {
    $this->tableColumnProperties = $tableColumnProperties;
  }
  /**
   * @return TableColumnProperties
   */
  public function getTableColumnProperties()
  {
    return $this->tableColumnProperties;
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
class_alias(UpdateTableColumnPropertiesRequest::class, 'Google_Service_Docs_UpdateTableColumnPropertiesRequest');
