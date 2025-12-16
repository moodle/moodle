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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1DatabaseTableSpec extends \Google\Model
{
  /**
   * Default unknown table type.
   */
  public const TYPE_TABLE_TYPE_UNSPECIFIED = 'TABLE_TYPE_UNSPECIFIED';
  /**
   * Native table.
   */
  public const TYPE_NATIVE = 'NATIVE';
  /**
   * External table.
   */
  public const TYPE_EXTERNAL = 'EXTERNAL';
  protected $databaseViewSpecType = GoogleCloudDatacatalogV1DatabaseTableSpecDatabaseViewSpec::class;
  protected $databaseViewSpecDataType = '';
  protected $dataplexTableType = GoogleCloudDatacatalogV1DataplexTableSpec::class;
  protected $dataplexTableDataType = '';
  /**
   * Type of this table.
   *
   * @var string
   */
  public $type;

  /**
   * Spec what applies to tables that are actually views. Not set for "real"
   * tables.
   *
   * @param GoogleCloudDatacatalogV1DatabaseTableSpecDatabaseViewSpec $databaseViewSpec
   */
  public function setDatabaseViewSpec(GoogleCloudDatacatalogV1DatabaseTableSpecDatabaseViewSpec $databaseViewSpec)
  {
    $this->databaseViewSpec = $databaseViewSpec;
  }
  /**
   * @return GoogleCloudDatacatalogV1DatabaseTableSpecDatabaseViewSpec
   */
  public function getDatabaseViewSpec()
  {
    return $this->databaseViewSpec;
  }
  /**
   * Output only. Fields specific to a Dataplex Universal Catalog table and
   * present only in the Dataplex Universal Catalog table entries.
   *
   * @param GoogleCloudDatacatalogV1DataplexTableSpec $dataplexTable
   */
  public function setDataplexTable(GoogleCloudDatacatalogV1DataplexTableSpec $dataplexTable)
  {
    $this->dataplexTable = $dataplexTable;
  }
  /**
   * @return GoogleCloudDatacatalogV1DataplexTableSpec
   */
  public function getDataplexTable()
  {
    return $this->dataplexTable;
  }
  /**
   * Type of this table.
   *
   * Accepted values: TABLE_TYPE_UNSPECIFIED, NATIVE, EXTERNAL
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1DatabaseTableSpec::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1DatabaseTableSpec');
