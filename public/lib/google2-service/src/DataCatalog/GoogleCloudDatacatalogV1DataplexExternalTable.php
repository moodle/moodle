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

class GoogleCloudDatacatalogV1DataplexExternalTable extends \Google\Model
{
  /**
   * Default unknown system.
   */
  public const SYSTEM_INTEGRATED_SYSTEM_UNSPECIFIED = 'INTEGRATED_SYSTEM_UNSPECIFIED';
  /**
   * BigQuery.
   */
  public const SYSTEM_BIGQUERY = 'BIGQUERY';
  /**
   * Cloud Pub/Sub.
   */
  public const SYSTEM_CLOUD_PUBSUB = 'CLOUD_PUBSUB';
  /**
   * Dataproc Metastore.
   */
  public const SYSTEM_DATAPROC_METASTORE = 'DATAPROC_METASTORE';
  /**
   * Dataplex Universal Catalog.
   */
  public const SYSTEM_DATAPLEX = 'DATAPLEX';
  /**
   * Cloud Spanner
   */
  public const SYSTEM_CLOUD_SPANNER = 'CLOUD_SPANNER';
  /**
   * Cloud Bigtable
   */
  public const SYSTEM_CLOUD_BIGTABLE = 'CLOUD_BIGTABLE';
  /**
   * Cloud Sql
   */
  public const SYSTEM_CLOUD_SQL = 'CLOUD_SQL';
  /**
   * Looker
   */
  public const SYSTEM_LOOKER = 'LOOKER';
  /**
   * Vertex AI
   */
  public const SYSTEM_VERTEX_AI = 'VERTEX_AI';
  /**
   * Name of the Data Catalog entry representing the external table.
   *
   * @var string
   */
  public $dataCatalogEntry;
  /**
   * Fully qualified name (FQN) of the external table.
   *
   * @var string
   */
  public $fullyQualifiedName;
  /**
   * Google Cloud resource name of the external table.
   *
   * @var string
   */
  public $googleCloudResource;
  /**
   * Service in which the external table is registered.
   *
   * @var string
   */
  public $system;

  /**
   * Name of the Data Catalog entry representing the external table.
   *
   * @param string $dataCatalogEntry
   */
  public function setDataCatalogEntry($dataCatalogEntry)
  {
    $this->dataCatalogEntry = $dataCatalogEntry;
  }
  /**
   * @return string
   */
  public function getDataCatalogEntry()
  {
    return $this->dataCatalogEntry;
  }
  /**
   * Fully qualified name (FQN) of the external table.
   *
   * @param string $fullyQualifiedName
   */
  public function setFullyQualifiedName($fullyQualifiedName)
  {
    $this->fullyQualifiedName = $fullyQualifiedName;
  }
  /**
   * @return string
   */
  public function getFullyQualifiedName()
  {
    return $this->fullyQualifiedName;
  }
  /**
   * Google Cloud resource name of the external table.
   *
   * @param string $googleCloudResource
   */
  public function setGoogleCloudResource($googleCloudResource)
  {
    $this->googleCloudResource = $googleCloudResource;
  }
  /**
   * @return string
   */
  public function getGoogleCloudResource()
  {
    return $this->googleCloudResource;
  }
  /**
   * Service in which the external table is registered.
   *
   * Accepted values: INTEGRATED_SYSTEM_UNSPECIFIED, BIGQUERY, CLOUD_PUBSUB,
   * DATAPROC_METASTORE, DATAPLEX, CLOUD_SPANNER, CLOUD_BIGTABLE, CLOUD_SQL,
   * LOOKER, VERTEX_AI
   *
   * @param self::SYSTEM_* $system
   */
  public function setSystem($system)
  {
    $this->system = $system;
  }
  /**
   * @return self::SYSTEM_*
   */
  public function getSystem()
  {
    return $this->system;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1DataplexExternalTable::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1DataplexExternalTable');
