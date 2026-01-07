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

class GoogleDatastoreAdminV1ExportEntitiesRequest extends \Google\Model
{
  protected $entityFilterType = GoogleDatastoreAdminV1EntityFilter::class;
  protected $entityFilterDataType = '';
  /**
   * Client-assigned labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. Location for the export metadata and data files. The full
   * resource URL of the external storage location. Currently, only Google Cloud
   * Storage is supported. So output_url_prefix should be of the form:
   * `gs://BUCKET_NAME[/NAMESPACE_PATH]`, where `BUCKET_NAME` is the name of the
   * Cloud Storage bucket and `NAMESPACE_PATH` is an optional Cloud Storage
   * namespace path (this is not a Cloud Datastore namespace). For more
   * information about Cloud Storage namespace paths, see [Object name
   * considerations](https://cloud.google.com/storage/docs/naming#object-
   * considerations). The resulting files will be nested deeper than the
   * specified URL prefix. The final output URL will be provided in the
   * google.datastore.admin.v1.ExportEntitiesResponse.output_url field. That
   * value should be used for subsequent ImportEntities operations. By nesting
   * the data files deeper, the same Cloud Storage bucket can be used in
   * multiple ExportEntities operations without conflict.
   *
   * @var string
   */
  public $outputUrlPrefix;

  /**
   * Description of what data from the project is included in the export.
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
  /**
   * Required. Location for the export metadata and data files. The full
   * resource URL of the external storage location. Currently, only Google Cloud
   * Storage is supported. So output_url_prefix should be of the form:
   * `gs://BUCKET_NAME[/NAMESPACE_PATH]`, where `BUCKET_NAME` is the name of the
   * Cloud Storage bucket and `NAMESPACE_PATH` is an optional Cloud Storage
   * namespace path (this is not a Cloud Datastore namespace). For more
   * information about Cloud Storage namespace paths, see [Object name
   * considerations](https://cloud.google.com/storage/docs/naming#object-
   * considerations). The resulting files will be nested deeper than the
   * specified URL prefix. The final output URL will be provided in the
   * google.datastore.admin.v1.ExportEntitiesResponse.output_url field. That
   * value should be used for subsequent ImportEntities operations. By nesting
   * the data files deeper, the same Cloud Storage bucket can be used in
   * multiple ExportEntities operations without conflict.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDatastoreAdminV1ExportEntitiesRequest::class, 'Google_Service_Datastore_GoogleDatastoreAdminV1ExportEntitiesRequest');
