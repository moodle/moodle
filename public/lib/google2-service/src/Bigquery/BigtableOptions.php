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

class BigtableOptions extends \Google\Collection
{
  protected $collection_key = 'columnFamilies';
  protected $columnFamiliesType = BigtableColumnFamily::class;
  protected $columnFamiliesDataType = 'array';
  /**
   * Optional. If field is true, then the column families that are not specified
   * in columnFamilies list are not exposed in the table schema. Otherwise, they
   * are read with BYTES type values. The default value is false.
   *
   * @var bool
   */
  public $ignoreUnspecifiedColumnFamilies;
  /**
   * Optional. If field is true, then each column family will be read as a
   * single JSON column. Otherwise they are read as a repeated cell structure
   * containing timestamp/value tuples. The default value is false.
   *
   * @var bool
   */
  public $outputColumnFamiliesAsJson;
  /**
   * Optional. If field is true, then the rowkey column families will be read
   * and converted to string. Otherwise they are read with BYTES type values and
   * users need to manually cast them with CAST if necessary. The default value
   * is false.
   *
   * @var bool
   */
  public $readRowkeyAsString;

  /**
   * Optional. List of column families to expose in the table schema along with
   * their types. This list restricts the column families that can be referenced
   * in queries and specifies their value types. You can use this list to do
   * type conversions - see the 'type' field for more details. If you leave this
   * list empty, all column families are present in the table schema and their
   * values are read as BYTES. During a query only the column families
   * referenced in that query are read from Bigtable.
   *
   * @param BigtableColumnFamily[] $columnFamilies
   */
  public function setColumnFamilies($columnFamilies)
  {
    $this->columnFamilies = $columnFamilies;
  }
  /**
   * @return BigtableColumnFamily[]
   */
  public function getColumnFamilies()
  {
    return $this->columnFamilies;
  }
  /**
   * Optional. If field is true, then the column families that are not specified
   * in columnFamilies list are not exposed in the table schema. Otherwise, they
   * are read with BYTES type values. The default value is false.
   *
   * @param bool $ignoreUnspecifiedColumnFamilies
   */
  public function setIgnoreUnspecifiedColumnFamilies($ignoreUnspecifiedColumnFamilies)
  {
    $this->ignoreUnspecifiedColumnFamilies = $ignoreUnspecifiedColumnFamilies;
  }
  /**
   * @return bool
   */
  public function getIgnoreUnspecifiedColumnFamilies()
  {
    return $this->ignoreUnspecifiedColumnFamilies;
  }
  /**
   * Optional. If field is true, then each column family will be read as a
   * single JSON column. Otherwise they are read as a repeated cell structure
   * containing timestamp/value tuples. The default value is false.
   *
   * @param bool $outputColumnFamiliesAsJson
   */
  public function setOutputColumnFamiliesAsJson($outputColumnFamiliesAsJson)
  {
    $this->outputColumnFamiliesAsJson = $outputColumnFamiliesAsJson;
  }
  /**
   * @return bool
   */
  public function getOutputColumnFamiliesAsJson()
  {
    return $this->outputColumnFamiliesAsJson;
  }
  /**
   * Optional. If field is true, then the rowkey column families will be read
   * and converted to string. Otherwise they are read with BYTES type values and
   * users need to manually cast them with CAST if necessary. The default value
   * is false.
   *
   * @param bool $readRowkeyAsString
   */
  public function setReadRowkeyAsString($readRowkeyAsString)
  {
    $this->readRowkeyAsString = $readRowkeyAsString;
  }
  /**
   * @return bool
   */
  public function getReadRowkeyAsString()
  {
    return $this->readRowkeyAsString;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BigtableOptions::class, 'Google_Service_Bigquery_BigtableOptions');
