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

class DicomStoreMetrics extends \Google\Model
{
  /**
   * Total blob storage bytes for all instances in the store.
   *
   * @var string
   */
  public $blobStorageSizeBytes;
  /**
   * Number of instances in the store.
   *
   * @var string
   */
  public $instanceCount;
  /**
   * Resource name of the DICOM store, of the form `projects/{project_id}/locati
   * ons/{location_id}/datasets/{dataset_id}/dicomStores/{dicom_store_id}`.
   *
   * @var string
   */
  public $name;
  /**
   * Number of series in the store.
   *
   * @var string
   */
  public $seriesCount;
  /**
   * Total structured storage bytes for all instances in the store.
   *
   * @var string
   */
  public $structuredStorageSizeBytes;
  /**
   * Number of studies in the store.
   *
   * @var string
   */
  public $studyCount;

  /**
   * Total blob storage bytes for all instances in the store.
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
   * Number of instances in the store.
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
   * Resource name of the DICOM store, of the form `projects/{project_id}/locati
   * ons/{location_id}/datasets/{dataset_id}/dicomStores/{dicom_store_id}`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Number of series in the store.
   *
   * @param string $seriesCount
   */
  public function setSeriesCount($seriesCount)
  {
    $this->seriesCount = $seriesCount;
  }
  /**
   * @return string
   */
  public function getSeriesCount()
  {
    return $this->seriesCount;
  }
  /**
   * Total structured storage bytes for all instances in the store.
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
  /**
   * Number of studies in the store.
   *
   * @param string $studyCount
   */
  public function setStudyCount($studyCount)
  {
    $this->studyCount = $studyCount;
  }
  /**
   * @return string
   */
  public function getStudyCount()
  {
    return $this->studyCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DicomStoreMetrics::class, 'Google_Service_CloudHealthcare_DicomStoreMetrics');
