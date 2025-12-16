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

namespace Google\Service\ShoppingContent;

class Table extends \Google\Collection
{
  protected $collection_key = 'rows';
  protected $columnHeadersType = Headers::class;
  protected $columnHeadersDataType = '';
  /**
   * Name of the table. Required for subtables, ignored for the main table.
   *
   * @var string
   */
  public $name;
  protected $rowHeadersType = Headers::class;
  protected $rowHeadersDataType = '';
  protected $rowsType = Row::class;
  protected $rowsDataType = 'array';

  /**
   * Headers of the table's columns. Optional: if not set then the table has
   * only one dimension.
   *
   * @param Headers $columnHeaders
   */
  public function setColumnHeaders(Headers $columnHeaders)
  {
    $this->columnHeaders = $columnHeaders;
  }
  /**
   * @return Headers
   */
  public function getColumnHeaders()
  {
    return $this->columnHeaders;
  }
  /**
   * Name of the table. Required for subtables, ignored for the main table.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Headers of the table's rows. Required.
   *
   * @param Headers $rowHeaders
   */
  public function setRowHeaders(Headers $rowHeaders)
  {
    $this->rowHeaders = $rowHeaders;
  }
  /**
   * @return Headers
   */
  public function getRowHeaders()
  {
    return $this->rowHeaders;
  }
  /**
   * The list of rows that constitute the table. Must have the same length as
   * `rowHeaders`. Required.
   *
   * @param Row[] $rows
   */
  public function setRows($rows)
  {
    $this->rows = $rows;
  }
  /**
   * @return Row[]
   */
  public function getRows()
  {
    return $this->rows;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Table::class, 'Google_Service_ShoppingContent_Table');
