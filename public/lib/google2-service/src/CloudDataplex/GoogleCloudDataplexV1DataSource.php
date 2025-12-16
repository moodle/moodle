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

class GoogleCloudDataplexV1DataSource extends \Google\Model
{
  /**
   * Immutable. The Dataplex Universal Catalog entity that represents the data
   * source (e.g. BigQuery table) for DataScan, of the form: projects/{project_n
   * umber}/locations/{location_id}/lakes/{lake_id}/zones/{zone_id}/entities/{en
   * tity_id}.
   *
   * @var string
   */
  public $entity;
  /**
   * Immutable. The service-qualified full resource name of the cloud resource
   * for a DataScan job to scan against. The field could either be: Cloud
   * Storage bucket for DataDiscoveryScan Format:
   * //storage.googleapis.com/projects/PROJECT_ID/buckets/BUCKET_ID or BigQuery
   * table of type "TABLE" for
   * DataProfileScan/DataQualityScan/DataDocumentationScan Format: //bigquery.go
   * ogleapis.com/projects/PROJECT_ID/datasets/DATASET_ID/tables/TABLE_ID
   *
   * @var string
   */
  public $resource;

  /**
   * Immutable. The Dataplex Universal Catalog entity that represents the data
   * source (e.g. BigQuery table) for DataScan, of the form: projects/{project_n
   * umber}/locations/{location_id}/lakes/{lake_id}/zones/{zone_id}/entities/{en
   * tity_id}.
   *
   * @param string $entity
   */
  public function setEntity($entity)
  {
    $this->entity = $entity;
  }
  /**
   * @return string
   */
  public function getEntity()
  {
    return $this->entity;
  }
  /**
   * Immutable. The service-qualified full resource name of the cloud resource
   * for a DataScan job to scan against. The field could either be: Cloud
   * Storage bucket for DataDiscoveryScan Format:
   * //storage.googleapis.com/projects/PROJECT_ID/buckets/BUCKET_ID or BigQuery
   * table of type "TABLE" for
   * DataProfileScan/DataQualityScan/DataDocumentationScan Format: //bigquery.go
   * ogleapis.com/projects/PROJECT_ID/datasets/DATASET_ID/tables/TABLE_ID
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataSource::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataSource');
