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

namespace Google\Service\CloudHealthcare;

class SeriesMetrics extends \Google\Model
{
  /**
   * Total blob storage bytes for all instances in the series.
   *
   * @var string
   */
  public $blobStorageSizeBytes;
  /**
   * Number of instances in the series.
   *
   * @var string
   */
  public $instanceCount;
  /**
   * The series resource path. For example, `projects/{project_id}/locations/{lo
   * cation_id}/datasets/{dataset_id}/dicomStores/{dicom_store_id}/dicomWeb/stud
   * ies/{study_uid}/series/{series_uid}`.
   *
   * @var string
   */
  public $series;
  /**
   * Total structured storage bytes for all instances in the series.
   *
   * @var string
   */
  public $structuredStorageSizeBytes;

  /**
   * Total blob storage bytes for all instances in the series.
   *
   * @param string $blobStorageSizeBytes
   */
  public function setBlobStorageSizeBytes($blobStorageSizeBytes)
  {
    $this->blobStorageSizeBytes = $blobStorageSizeBytes;
  }
  /**
   * @return string
   */
  public function getBlobStorageSizeBytes()
  {
    return $this->blobStorageSizeBytes;
  }
  /**
   * Number of instances in the series.
   *
   * @param string $instanceCount
   */
  public function setInstanceCount($instanceCount)
  {
    $this->instanceCount = $instanceCount;
  }
  /**
   * @return string
   */
  public function getInstanceCount()
  {
    return $this->instanceCount;
  }
  /**
   * The series resource path. For example, `projects/{project_id}/locations/{lo
   * cation_id}/datasets/{dataset_id}/dicomStores/{dicom_store_id}/dicomWeb/stud
   * ies/{study_uid}/series/{series_uid}`.
   *
   * @param string $series
   */
  public function setSeries($series)
  {
    $this->series = $series;
  }
  /**
   * @return string
   */
  public function getSeries()
  {
    return $this->series;
  }
  /**
   * Total structured storage bytes for all instances in the series.
   *
   * @param string $structuredStorageSizeBytes
   */
  public function setStructuredStorageSizeBytes($structuredStorageSizeBytes)
  {
    $this->structuredStorageSizeBytes = $structuredStorageSizeBytes;
  }
  /**
   * @return string
   */
  public function getStructuredStorageSizeBytes()
  {
    return $this->structuredStorageSizeBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SeriesMetrics::class, 'Google_Service_CloudHealthcare_SeriesMetrics');
