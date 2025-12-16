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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1BulkDownloadFeedbackLabelsMetadataDownloadStats extends \Google\Collection
{
  protected $collection_key = 'fileNames';
  /**
   * Output only. Full name of the files written to Cloud storage.
   *
   * @var string[]
   */
  public $fileNames;
  /**
   * The number of objects processed during the download operation.
   *
   * @var int
   */
  public $processedObjectCount;
  /**
   * The number of new feedback labels downloaded during this operation.
   * Different from "processed" because some labels might not be downloaded
   * because an error.
   *
   * @var int
   */
  public $successfulDownloadCount;
  /**
   * Total number of files written to the provided Cloud Storage bucket.
   *
   * @var int
   */
  public $totalFilesWritten;

  /**
   * Output only. Full name of the files written to Cloud storage.
   *
   * @param string[] $fileNames
   */
  public function setFileNames($fileNames)
  {
    $this->fileNames = $fileNames;
  }
  /**
   * @return string[]
   */
  public function getFileNames()
  {
    return $this->fileNames;
  }
  /**
   * The number of objects processed during the download operation.
   *
   * @param int $processedObjectCount
   */
  public function setProcessedObjectCount($processedObjectCount)
  {
    $this->processedObjectCount = $processedObjectCount;
  }
  /**
   * @return int
   */
  public function getProcessedObjectCount()
  {
    return $this->processedObjectCount;
  }
  /**
   * The number of new feedback labels downloaded during this operation.
   * Different from "processed" because some labels might not be downloaded
   * because an error.
   *
   * @param int $successfulDownloadCount
   */
  public function setSuccessfulDownloadCount($successfulDownloadCount)
  {
    $this->successfulDownloadCount = $successfulDownloadCount;
  }
  /**
   * @return int
   */
  public function getSuccessfulDownloadCount()
  {
    return $this->successfulDownloadCount;
  }
  /**
   * Total number of files written to the provided Cloud Storage bucket.
   *
   * @param int $totalFilesWritten
   */
  public function setTotalFilesWritten($totalFilesWritten)
  {
    $this->totalFilesWritten = $totalFilesWritten;
  }
  /**
   * @return int
   */
  public function getTotalFilesWritten()
  {
    return $this->totalFilesWritten;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1BulkDownloadFeedbackLabelsMetadataDownloadStats::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1BulkDownloadFeedbackLabelsMetadataDownloadStats');
