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

class GoogleCloudDataplexV1DataProfileResult extends \Google\Model
{
  protected $catalogPublishingStatusType = GoogleCloudDataplexV1DataScanCatalogPublishingStatus::class;
  protected $catalogPublishingStatusDataType = '';
  protected $postScanActionsResultType = GoogleCloudDataplexV1DataProfileResultPostScanActionsResult::class;
  protected $postScanActionsResultDataType = '';
  protected $profileType = GoogleCloudDataplexV1DataProfileResultProfile::class;
  protected $profileDataType = '';
  /**
   * Output only. The count of rows scanned.
   *
   * @var string
   */
  public $rowCount;
  protected $scannedDataType = GoogleCloudDataplexV1ScannedData::class;
  protected $scannedDataDataType = '';

  /**
   * Output only. The status of publishing the data scan as Dataplex Universal
   * Catalog metadata.
   *
   * @param GoogleCloudDataplexV1DataScanCatalogPublishingStatus $catalogPublishingStatus
   */
  public function setCatalogPublishingStatus(GoogleCloudDataplexV1DataScanCatalogPublishingStatus $catalogPublishingStatus)
  {
    $this->catalogPublishingStatus = $catalogPublishingStatus;
  }
  /**
   * @return GoogleCloudDataplexV1DataScanCatalogPublishingStatus
   */
  public function getCatalogPublishingStatus()
  {
    return $this->catalogPublishingStatus;
  }
  /**
   * Output only. The result of post scan actions.
   *
   * @param GoogleCloudDataplexV1DataProfileResultPostScanActionsResult $postScanActionsResult
   */
  public function setPostScanActionsResult(GoogleCloudDataplexV1DataProfileResultPostScanActionsResult $postScanActionsResult)
  {
    $this->postScanActionsResult = $postScanActionsResult;
  }
  /**
   * @return GoogleCloudDataplexV1DataProfileResultPostScanActionsResult
   */
  public function getPostScanActionsResult()
  {
    return $this->postScanActionsResult;
  }
  /**
   * Output only. The profile information per field.
   *
   * @param GoogleCloudDataplexV1DataProfileResultProfile $profile
   */
  public function setProfile(GoogleCloudDataplexV1DataProfileResultProfile $profile)
  {
    $this->profile = $profile;
  }
  /**
   * @return GoogleCloudDataplexV1DataProfileResultProfile
   */
  public function getProfile()
  {
    return $this->profile;
  }
  /**
   * Output only. The count of rows scanned.
   *
   * @param string $rowCount
   */
  public function setRowCount($rowCount)
  {
    $this->rowCount = $rowCount;
  }
  /**
   * @return string
   */
  public function getRowCount()
  {
    return $this->rowCount;
  }
  /**
   * Output only. The data scanned for this result.
   *
   * @param GoogleCloudDataplexV1ScannedData $scannedData
   */
  public function setScannedData(GoogleCloudDataplexV1ScannedData $scannedData)
  {
    $this->scannedData = $scannedData;
  }
  /**
   * @return GoogleCloudDataplexV1ScannedData
   */
  public function getScannedData()
  {
    return $this->scannedData;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataProfileResult::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataProfileResult');
