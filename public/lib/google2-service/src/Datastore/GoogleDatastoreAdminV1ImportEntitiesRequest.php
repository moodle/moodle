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

class GoogleDatastoreAdminV1ImportEntitiesRequest extends \Google\Model
{
  protected $entityFilterType = GoogleDatastoreAdminV1EntityFilter::class;
  protected $entityFilterDataType = '';
  /**
   * Required. The full resource URL of the external storage location.
   * Currently, only Google Cloud Storage is supported. So input_url should be
   * of the form:
   * `gs://BUCKET_NAME[/NAMESPACE_PATH]/OVERALL_EXPORT_METADATA_FILE`, where
   * `BUCKET_NAME` is the name of the Cloud Storage bucket, `NAMESPACE_PATH` is
   * an optional Cloud Storage namespace path (this is not a Cloud Datastore
   * namespace), and `OVERALL_EXPORT_METADATA_FILE` is the metadata file written
   * by the ExportEntities operation. For more information about Cloud Storage
   * namespace paths, see [Object name
   * considerations](https://cloud.google.com/storage/docs/naming#object-
   * considerations). For more information, see
   * google.datastore.admin.v1.ExportEntitiesResponse.output_url.
   *
   * @var string
   */
  public $inputUrl;
  /**
   * Client-assigned labels.
   *
   * @var string[]
   */
  public $labels;

  /**
   * Optionally specify which kinds/namespaces are to be imported. If provided,
   * the list must be a subset of the EntityFilter used in creating the export,
   * otherwise a FAILED_PRECONDITION error will be returned. If no filter is
   * specified then all entities from the export are imported.
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
   * Required. The full resource URL of the external storage location.
   * Currently, only Google Cloud Storage is supported. So input_url should be
   * of the form:
   * `gs://BUCKET_NAME[/NAMESPACE_PATH]/OVERALL_EXPORT_METADATA_FILE`, where
   * `BUCKET_NAME` is the name of the Cloud Storage bucket, `NAMESPACE_PATH` is
   * an optional Cloud Storage namespace path (this is not a Cloud Datastore
   * namespace), and `OVERALL_EXPORT_METADATA_FILE` is the metadata file written
   * by the ExportEntities operation. For more information about Cloud Storage
   * namespace paths, see [Object name
   * considerations](https://cloud.google.com/storage/docs/naming#object-
   * considerations). For more information, see
   * google.datastore.admin.v1.ExportEntitiesResponse.output_url.
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
   * Client-assigned labels.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDatastoreAdminV1ImportEntitiesRequest::class, 'Google_Service_Datastore_GoogleDatastoreAdminV1ImportEntitiesRequest');
