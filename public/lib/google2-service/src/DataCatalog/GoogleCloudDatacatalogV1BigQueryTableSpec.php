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

class GoogleCloudDatacatalogV1BigQueryTableSpec extends \Google\Model
{
  /**
   * Default unknown type.
   */
  public const TABLE_SOURCE_TYPE_TABLE_SOURCE_TYPE_UNSPECIFIED = 'TABLE_SOURCE_TYPE_UNSPECIFIED';
  /**
   * Table view.
   */
  public const TABLE_SOURCE_TYPE_BIGQUERY_VIEW = 'BIGQUERY_VIEW';
  /**
   * BigQuery native table.
   */
  public const TABLE_SOURCE_TYPE_BIGQUERY_TABLE = 'BIGQUERY_TABLE';
  /**
   * BigQuery materialized view.
   */
  public const TABLE_SOURCE_TYPE_BIGQUERY_MATERIALIZED_VIEW = 'BIGQUERY_MATERIALIZED_VIEW';
  /**
   * Output only. The table source type.
   *
   * @var string
   */
  public $tableSourceType;
  protected $tableSpecType = GoogleCloudDatacatalogV1TableSpec::class;
  protected $tableSpecDataType = '';
  protected $viewSpecType = GoogleCloudDatacatalogV1ViewSpec::class;
  protected $viewSpecDataType = '';

  /**
   * Output only. The table source type.
   *
   * Accepted values: TABLE_SOURCE_TYPE_UNSPECIFIED, BIGQUERY_VIEW,
   * BIGQUERY_TABLE, BIGQUERY_MATERIALIZED_VIEW
   *
   * @param self::TABLE_SOURCE_TYPE_* $tableSourceType
   */
  public function setTableSourceType($tableSourceType)
  {
    $this->tableSourceType = $tableSourceType;
  }
  /**
   * @return self::TABLE_SOURCE_TYPE_*
   */
  public function getTableSourceType()
  {
    return $this->tableSourceType;
  }
  /**
   * Specification of a BigQuery table. Populated only if the
   * `table_source_type` is `BIGQUERY_TABLE`.
   *
   * @param GoogleCloudDatacatalogV1TableSpec $tableSpec
   */
  public function setTableSpec(GoogleCloudDatacatalogV1TableSpec $tableSpec)
  {
    $this->tableSpec = $tableSpec;
  }
  /**
   * @return GoogleCloudDatacatalogV1TableSpec
   */
  public function getTableSpec()
  {
    return $this->tableSpec;
  }
  /**
   * Table view specification. Populated only if the `table_source_type` is
   * `BIGQUERY_VIEW`.
   *
   * @param GoogleCloudDatacatalogV1ViewSpec $viewSpec
   */
  public function setViewSpec(GoogleCloudDatacatalogV1ViewSpec $viewSpec)
  {
    $this->viewSpec = $viewSpec;
  }
  /**
   * @return GoogleCloudDatacatalogV1ViewSpec
   */
  public function getViewSpec()
  {
    return $this->viewSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1BigQueryTableSpec::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1BigQueryTableSpec');
