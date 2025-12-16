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

class StudyMetrics extends \Google\Model
{
  /**
   * Total blob storage bytes for all instances in the study.
   *
   * @var string
   */
  public $blobStorageSizeBytes;
  /**
   * Number of instances in the study.
   *
   * @var string
   */
  public $instanceCount;
  /**
   * Number of series in the study.
   *
   * @var string
   */
  public $seriesCount;
  /**
   * Total structured storage bytes for all instances in the study.
   *
   * @var string
   */
  public $structuredStorageSizeBytes;
  /**
   * The study resource path. For example, `projects/{project_id}/locations/{loc
   * ation_id}/datasets/{dataset_id}/dicomStores/{dicom_store_id}/dicomWeb/studi
   * es/{study_uid}`.
   *
   * @var string
   */
  public $study;

  /**
   * Total blob storage bytes for all instances in the study.
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
   * Number of instances in the study.
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
   * Number of series in the study.
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
   * Total structured storage bytes for all instances in the study.
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
   * The study resource path. For example, `projects/{project_id}/locations/{loc
   * ation_id}/datasets/{dataset_id}/dicomStores/{dicom_store_id}/dicomWeb/studi
   * es/{study_uid}`.
   *
   * @param string $study
   */
  public function setStudy($study)
  {
    $this->study = $study;
  }
  /**
   * @return string
   */
  public function getStudy()
  {
    return $this->study;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StudyMetrics::class, 'Google_Service_CloudHealthcare_StudyMetrics');
