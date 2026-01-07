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

namespace Google\Service\Bigquery;

class BigtableColumnFamily extends \Google\Collection
{
  protected $collection_key = 'columns';
  protected $columnsType = BigtableColumn::class;
  protected $columnsDataType = 'array';
  /**
   * Optional. The encoding of the values when the type is not STRING.
   * Acceptable encoding values are: TEXT - indicates values are alphanumeric
   * text strings. BINARY - indicates values are encoded using HBase
   * Bytes.toBytes family of functions. This can be overridden for a specific
   * column by listing that column in 'columns' and specifying an encoding for
   * it.
   *
   * @var string
   */
  public $encoding;
  /**
   * Identifier of the column family.
   *
   * @var string
   */
  public $familyId;
  /**
   * Optional. If this is set only the latest version of value are exposed for
   * all columns in this column family. This can be overridden for a specific
   * column by listing that column in 'columns' and specifying a different
   * setting for that column.
   *
   * @var bool
   */
  public $onlyReadLatest;
  /**
   * Optional. The type to convert the value in cells of this column family. The
   * values are expected to be encoded using HBase Bytes.toBytes function when
   * using the BINARY encoding value. Following BigQuery types are allowed
   * (case-sensitive): * BYTES * STRING * INTEGER * FLOAT * BOOLEAN * JSON
   * Default type is BYTES. This can be overridden for a specific column by
   * listing that column in 'columns' and specifying a type for it.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. Lists of columns that should be exposed as individual fields as
   * opposed to a list of (column name, value) pairs. All columns whose
   * qualifier matches a qualifier in this list can be accessed as `.`. Other
   * columns can be accessed as a list through the `.Column` field.
   *
   * @param BigtableColumn[] $columns
   */
  public function setColumns($columns)
  {
    $this->columns = $columns;
  }
  /**
   * @return BigtableColumn[]
   */
  public function getColumns()
  {
    return $this->columns;
  }
  /**
   * Optional. The encoding of the values when the type is not STRING.
   * Acceptable encoding values are: TEXT - indicates values are alphanumeric
   * text strings. BINARY - indicates values are encoded using HBase
   * Bytes.toBytes family of functions. This can be overridden for a specific
   * column by listing that column in 'columns' and specifying an encoding for
   * it.
   *
   * @param string $encoding
   */
  public function setEncoding($encoding)
  {
    $this->encoding = $encoding;
  }
  /**
   * @return string
   */
  public function getEncoding()
  {
    return $this->encoding;
  }
  /**
   * Identifier of the column family.
   *
   * @param string $familyId
   */
  public function setFamilyId($familyId)
  {
    $this->familyId = $familyId;
  }
  /**
   * @return string
   */
  public function getFamilyId()
  {
    return $this->familyId;
  }
  /**
   * Optional. If this is set only the latest version of value are exposed for
   * all columns in this column family. This can be overridden for a specific
   * column by listing that column in 'columns' and specifying a different
   * setting for that column.
   *
   * @param bool $onlyReadLatest
   */
  public function setOnlyReadLatest($onlyReadLatest)
  {
    $this->onlyReadLatest = $onlyReadLatest;
  }
  /**
   * @return bool
   */
  public function getOnlyReadLatest()
  {
    return $this->onlyReadLatest;
  }
  /**
   * Optional. The type to convert the value in cells of this column family. The
   * values are expected to be encoded using HBase Bytes.toBytes function when
   * using the BINARY encoding value. Following BigQuery types are allowed
   * (case-sensitive): * BYTES * STRING * INTEGER * FLOAT * BOOLEAN * JSON
   * Default type is BYTES. This can be overridden for a specific column by
   * listing that column in 'columns' and specifying a type for it.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BigtableColumnFamily::class, 'Google_Service_Bigquery_BigtableColumnFamily');
