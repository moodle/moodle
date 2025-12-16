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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DataDiscoverySpecBigQueryPublishingConfig extends \Google\Model
{
  /**
   * Table type unspecified.
   */
  public const TABLE_TYPE_TABLE_TYPE_UNSPECIFIED = 'TABLE_TYPE_UNSPECIFIED';
  /**
   * Default. Discovered tables are published as BigQuery external tables whose
   * data is accessed using the credentials of the user querying the table.
   */
  public const TABLE_TYPE_EXTERNAL = 'EXTERNAL';
  /**
   * Discovered tables are published as BigLake external tables whose data is
   * accessed using the credentials of the associated BigQuery connection.
   */
  public const TABLE_TYPE_BIGLAKE = 'BIGLAKE';
  /**
   * Optional. The BigQuery connection used to create BigLake tables. Must be in
   * the form
   * projects/{project_id}/locations/{location_id}/connections/{connection_id}
   *
   * @var string
   */
  public $connection;
  /**
   * Optional. The location of the BigQuery dataset to publish BigLake external
   * or non-BigLake external tables to. 1. If the Cloud Storage bucket is
   * located in a multi-region bucket, then BigQuery dataset can be in the same
   * multi-region bucket or any single region that is included in the same
   * multi-region bucket. The datascan can be created in any single region that
   * is included in the same multi-region bucket 2. If the Cloud Storage bucket
   * is located in a dual-region bucket, then BigQuery dataset can be located in
   * regions that are included in the dual-region bucket, or in a multi-region
   * that includes the dual-region. The datascan can be created in any single
   * region that is included in the same dual-region bucket. 3. If the Cloud
   * Storage bucket is located in a single region, then BigQuery dataset can be
   * in the same single region or any multi-region bucket that includes the same
   * single region. The datascan will be created in the same single region as
   * the bucket. 4. If the BigQuery dataset is in single region, it must be in
   * the same single region as the datascan.For supported values, refer to
   * https://cloud.google.com/bigquery/docs/locations#supported_locations.
   *
   * @var string
   */
  public $location;
  /**
   * Optional. The project of the BigQuery dataset to publish BigLake external
   * or non-BigLake external tables to. If not specified, the project of the
   * Cloud Storage bucket will be used. The format is
   * "projects/{project_id_or_number}".
   *
   * @var string
   */
  public $project;
  /**
   * Optional. Determines whether to publish discovered tables as BigLake
   * external tables or non-BigLake external tables.
   *
   * @var string
   */
  public $tableType;

  /**
   * Optional. The BigQuery connection used to create BigLake tables. Must be in
   * the form
   * projects/{project_id}/locations/{location_id}/connections/{connection_id}
   *
   * @param string $connection
   */
  public function setConnection($connection)
  {
    $this->connection = $connection;
  }
  /**
   * @return string
   */
  public function getConnection()
  {
    return $this->connection;
  }
  /**
   * Optional. The location of the BigQuery dataset to publish BigLake external
   * or non-BigLake external tables to. 1. If the Cloud Storage bucket is
   * located in a multi-region bucket, then BigQuery dataset can be in the same
   * multi-region bucket or any single region that is included in the same
   * multi-region bucket. The datascan can be created in any single region that
   * is included in the same multi-region bucket 2. If the Cloud Storage bucket
   * is located in a dual-region bucket, then BigQuery dataset can be located in
   * regions that are included in the dual-region bucket, or in a multi-region
   * that includes the dual-region. The datascan can be created in any single
   * region that is included in the same dual-region bucket. 3. If the Cloud
   * Storage bucket is located in a single region, then BigQuery dataset can be
   * in the same single region or any multi-region bucket that includes the same
   * single region. The datascan will be created in the same single region as
   * the bucket. 4. If the BigQuery dataset is in single region, it must be in
   * the same single region as the datascan.For supported values, refer to
   * https://cloud.google.com/bigquery/docs/locations#supported_locations.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Optional. The project of the BigQuery dataset to publish BigLake external
   * or non-BigLake external tables to. If not specified, the project of the
   * Cloud Storage bucket will be used. The format is
   * "projects/{project_id_or_number}".
   *
   * @param string $project
   */
  public function setProject($project)
  {
    $this->project = $project;
  }
  /**
   * @return string
   */
  public function getProject()
  {
    return $this->project;
  }
  /**
   * Optional. Determines whether to publish discovered tables as BigLake
   * external tables or non-BigLake external tables.
   *
   * Accepted values: TABLE_TYPE_UNSPECIFIED, EXTERNAL, BIGLAKE
   *
   * @param self::TABLE_TYPE_* $tableType
   */
  public function setTableType($tableType)
  {
    $this->tableType = $tableType;
  }
  /**
   * @return self::TABLE_TYPE_*
   */
  public function getTableType()
  {
    return $this->tableType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataDiscoverySpecBigQueryPublishingConfig::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataDiscoverySpecBigQueryPublishingConfig');
