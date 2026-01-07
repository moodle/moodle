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

namespace Google\Service\Datastore;

class GoogleDatastoreAdminV1ExportEntitiesMetadata extends \Google\Model
{
  protected $commonType = GoogleDatastoreAdminV1CommonMetadata::class;
  protected $commonDataType = '';
  protected $entityFilterType = GoogleDatastoreAdminV1EntityFilter::class;
  protected $entityFilterDataType = '';
  /**
   * Location for the export metadata and data files. This will be the same
   * value as the
   * google.datastore.admin.v1.ExportEntitiesRequest.output_url_prefix field.
   * The final output location is provided in
   * google.datastore.admin.v1.ExportEntitiesResponse.output_url.
   *
   * @var string
   */
  public $outputUrlPrefix;
  protected $progressBytesType = GoogleDatastoreAdminV1Progress::class;
  protected $progressBytesDataType = '';
  protected $progressEntitiesType = GoogleDatastoreAdminV1Progress::class;
  protected $progressEntitiesDataType = '';

  /**
   * Metadata common to all Datastore Admin operations.
   *
   * @param GoogleDatastoreAdminV1CommonMetadata $common
   */
  public function setCommon(GoogleDatastoreAdminV1CommonMetadata $common)
  {
    $this->common = $common;
  }
  /**
   * @return GoogleDatastoreAdminV1CommonMetadata
   */
  public function getCommon()
  {
    return $this->common;
  }
  /**
   * Description of which entities are being exported.
   *
   * @param GoogleDatastoreAdminV1EntityFilter $entityFilter
   */
  public function setEntityFilter(GoogleDatastoreAdminV1EntityFilter $entityFilter)
  {
    $this->entityFilter = $entityFilter;
  }
  /**
   * @return GoogleDatastoreAdminV1EntityFilter
   */
  public function getEntityFilter()
  {
    return $this->entityFilter;
  }
  /**
   * Location for the export metadata and data files. This will be the same
   * value as the
   * google.datastore.admin.v1.ExportEntitiesRequest.output_url_prefix field.
   * The final output location is provided in
   * google.datastore.admin.v1.ExportEntitiesResponse.output_url.
   *
   * @param string $outputUrlPrefix
   */
  public function setOutputUrlPrefix($outputUrlPrefix)
  {
    $this->outputUrlPrefix = $outputUrlPrefix;
  }
  /**
   * @return string
   */
  public function getOutputUrlPrefix()
  {
    return $this->outputUrlPrefix;
  }
  /**
   * An estimate of the number of bytes processed.
   *
   * @param GoogleDatastoreAdminV1Progress $progressBytes
   */
  public function setProgressBytes(GoogleDatastoreAdminV1Progress $progressBytes)
  {
    $this->progressBytes = $progressBytes;
  }
  /**
   * @return GoogleDatastoreAdminV1Progress
   */
  public function getProgressBytes()
  {
    return $this->progressBytes;
  }
  /**
   * An estimate of the number of entities processed.
   *
   * @param GoogleDatastoreAdminV1Progress $progressEntities
   */
  public function setProgressEntities(GoogleDatastoreAdminV1Progress $progressEntities)
  {
    $this->progressEntities = $progressEntities;
  }
  /**
   * @return GoogleDatastoreAdminV1Progress
   */
  public function getProgressEntities()
  {
    return $this->progressEntities;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDatastoreAdminV1ExportEntitiesMetadata::class, 'Google_Service_Datastore_GoogleDatastoreAdminV1ExportEntitiesMetadata');
