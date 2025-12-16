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

class GoogleCloudDatacatalogV1ImportEntriesRequest extends \Google\Model
{
  /**
   * Path to a Cloud Storage bucket that contains a dump ready for ingestion.
   *
   * @var string
   */
  public $gcsBucketPath;
  /**
   * Optional. (Optional) Dataplex Universal Catalog task job id, if specified
   * will be used as part of ImportEntries LRO ID
   *
   * @var string
   */
  public $jobId;

  /**
   * Path to a Cloud Storage bucket that contains a dump ready for ingestion.
   *
   * @param string $gcsBucketPath
   */
  public function setGcsBucketPath($gcsBucketPath)
  {
    $this->gcsBucketPath = $gcsBucketPath;
  }
  /**
   * @return string
   */
  public function getGcsBucketPath()
  {
    return $this->gcsBucketPath;
  }
  /**
   * Optional. (Optional) Dataplex Universal Catalog task job id, if specified
   * will be used as part of ImportEntries LRO ID
   *
   * @param string $jobId
   */
  public function setJobId($jobId)
  {
    $this->jobId = $jobId;
  }
  /**
   * @return string
   */
  public function getJobId()
  {
    return $this->jobId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1ImportEntriesRequest::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1ImportEntriesRequest');
