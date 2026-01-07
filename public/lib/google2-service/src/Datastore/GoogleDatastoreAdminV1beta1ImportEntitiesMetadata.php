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

class GoogleDatastoreAdminV1beta1ImportEntitiesMetadata extends \Google\Model
{
  protected $commonType = GoogleDatastoreAdminV1beta1CommonMetadata::class;
  protected $commonDataType = '';
  protected $entityFilterType = GoogleDatastoreAdminV1beta1EntityFilter::class;
  protected $entityFilterDataType = '';
  /**
   * The location of the import metadata file. This will be the same value as
   * the google.datastore.admin.v1beta1.ExportEntitiesResponse.output_url field.
   *
   * @var string
   */
  public $inputUrl;
  protected $progressBytesType = GoogleDatastoreAdminV1beta1Progress::class;
  protected $progressBytesDataType = '';
  protected $progressEntitiesType = GoogleDatastoreAdminV1beta1Progress::class;
  protected $progressEntitiesDataType = '';

  /**
   * Metadata common to all Datastore Admin operations.
   *
   * @param GoogleDatastoreAdminV1beta1CommonMetadata $common
   */
  public function setCommon(GoogleDatastoreAdminV1beta1CommonMetadata $common)
  {
    $this->common = $common;
  }
  /**
   * @return GoogleDatastoreAdminV1beta1CommonMetadata
   */
  public function getCommon()
  {
    return $this->common;
  }
  /**
   * Description of which entities are being imported.
   *
   * @param GoogleDatastoreAdminV1beta1EntityFilter $entityFilter
   */
  public function setEntityFilter(GoogleDatastoreAdminV1beta1EntityFilter $entityFilter)
  {
    $this->entityFilter = $entityFilter;
  }
  /**
   * @return GoogleDatastoreAdminV1beta1EntityFilter
   */
  public function getEntityFilter()
  {
    return $this->entityFilter;
  }
  /**
   * The location of the import metadata file. This will be the same value as
   * the google.datastore.admin.v1beta1.ExportEntitiesResponse.output_url field.
   *
   * @param string $inputUrl
   */
  public function setInputUrl($inputUrl)
  {
    $this->inputUrl = $inputUrl;
  }
  /**
   * @return string
   */
  public function getInputUrl()
  {
    return $this->inputUrl;
  }
  /**
   * An estimate of the number of bytes processed.
   *
   * @param GoogleDatastoreAdminV1beta1Progress $progressBytes
   */
  public function setProgressBytes(GoogleDatastoreAdminV1beta1Progress $progressBytes)
  {
    $this->progressBytes = $progressBytes;
  }
  /**
   * @return GoogleDatastoreAdminV1beta1Progress
   */
  public function getProgressBytes()
  {
    return $this->progressBytes;
  }
  /**
   * An estimate of the number of entities processed.
   *
   * @param GoogleDatastoreAdminV1beta1Progress $progressEntities
   */
  public function setProgressEntities(GoogleDatastoreAdminV1beta1Progress $progressEntities)
  {
    $this->progressEntities = $progressEntities;
  }
  /**
   * @return GoogleDatastoreAdminV1beta1Progress
   */
  public function getProgressEntities()
  {
    return $this->progressEntities;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDatastoreAdminV1beta1ImportEntitiesMetadata::class, 'Google_Service_Datastore_GoogleDatastoreAdminV1beta1ImportEntitiesMetadata');
