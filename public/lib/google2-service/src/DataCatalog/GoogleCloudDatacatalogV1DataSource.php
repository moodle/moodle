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

class GoogleCloudDatacatalogV1DataSource extends \Google\Model
{
  /**
   * Default unknown service.
   */
  public const SERVICE_SERVICE_UNSPECIFIED = 'SERVICE_UNSPECIFIED';
  /**
   * Google Cloud Storage service.
   */
  public const SERVICE_CLOUD_STORAGE = 'CLOUD_STORAGE';
  /**
   * BigQuery service.
   */
  public const SERVICE_BIGQUERY = 'BIGQUERY';
  /**
   * Full name of a resource as defined by the service. For example: `//bigquery
   * .googleapis.com/projects/{PROJECT_ID}/locations/{LOCATION}/datasets/{DATASE
   * T_ID}/tables/{TABLE_ID}`
   *
   * @var string
   */
  public $resource;
  /**
   * Service that physically stores the data.
   *
   * @var string
   */
  public $service;
  /**
   * Output only. Data Catalog entry name, if applicable.
   *
   * @var string
   */
  public $sourceEntry;
  protected $storagePropertiesType = GoogleCloudDatacatalogV1StorageProperties::class;
  protected $storagePropertiesDataType = '';

  /**
   * Full name of a resource as defined by the service. For example: `//bigquery
   * .googleapis.com/projects/{PROJECT_ID}/locations/{LOCATION}/datasets/{DATASE
   * T_ID}/tables/{TABLE_ID}`
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * Service that physically stores the data.
   *
   * Accepted values: SERVICE_UNSPECIFIED, CLOUD_STORAGE, BIGQUERY
   *
   * @param self::SERVICE_* $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return self::SERVICE_*
   */
  public function getService()
  {
    return $this->service;
  }
  /**
   * Output only. Data Catalog entry name, if applicable.
   *
   * @param string $sourceEntry
   */
  public function setSourceEntry($sourceEntry)
  {
    $this->sourceEntry = $sourceEntry;
  }
  /**
   * @return string
   */
  public function getSourceEntry()
  {
    return $this->sourceEntry;
  }
  /**
   * Detailed properties of the underlying storage.
   *
   * @param GoogleCloudDatacatalogV1StorageProperties $storageProperties
   */
  public function setStorageProperties(GoogleCloudDatacatalogV1StorageProperties $storageProperties)
  {
    $this->storageProperties = $storageProperties;
  }
  /**
   * @return GoogleCloudDatacatalogV1StorageProperties
   */
  public function getStorageProperties()
  {
    return $this->storageProperties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1DataSource::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1DataSource');
