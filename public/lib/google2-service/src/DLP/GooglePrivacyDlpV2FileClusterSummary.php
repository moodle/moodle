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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2FileClusterSummary extends \Google\Collection
{
  protected $collection_key = 'fileStoreInfoTypeSummaries';
  protected $dataRiskLevelType = GooglePrivacyDlpV2DataRiskLevel::class;
  protected $dataRiskLevelDataType = '';
  protected $errorsType = GooglePrivacyDlpV2Error::class;
  protected $errorsDataType = 'array';
  protected $fileClusterTypeType = GooglePrivacyDlpV2FileClusterType::class;
  protected $fileClusterTypeDataType = '';
  protected $fileExtensionsScannedType = GooglePrivacyDlpV2FileExtensionInfo::class;
  protected $fileExtensionsScannedDataType = 'array';
  protected $fileExtensionsSeenType = GooglePrivacyDlpV2FileExtensionInfo::class;
  protected $fileExtensionsSeenDataType = 'array';
  protected $fileStoreInfoTypeSummariesType = GooglePrivacyDlpV2FileStoreInfoTypeSummary::class;
  protected $fileStoreInfoTypeSummariesDataType = 'array';
  /**
   * True if no files exist in this cluster. If the file store had more files
   * than could be listed, this will be false even if no files for this cluster
   * were seen and file_extensions_seen is empty.
   *
   * @var bool
   */
  public $noFilesExist;
  protected $sensitivityScoreType = GooglePrivacyDlpV2SensitivityScore::class;
  protected $sensitivityScoreDataType = '';

  /**
   * The data risk level of this cluster. RISK_LOW if nothing has been scanned.
   *
   * @param GooglePrivacyDlpV2DataRiskLevel $dataRiskLevel
   */
  public function setDataRiskLevel(GooglePrivacyDlpV2DataRiskLevel $dataRiskLevel)
  {
    $this->dataRiskLevel = $dataRiskLevel;
  }
  /**
   * @return GooglePrivacyDlpV2DataRiskLevel
   */
  public function getDataRiskLevel()
  {
    return $this->dataRiskLevel;
  }
  /**
   * A list of errors detected while scanning this cluster. The list is
   * truncated to 10 per cluster.
   *
   * @param GooglePrivacyDlpV2Error[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return GooglePrivacyDlpV2Error[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * The file cluster type.
   *
   * @param GooglePrivacyDlpV2FileClusterType $fileClusterType
   */
  public function setFileClusterType(GooglePrivacyDlpV2FileClusterType $fileClusterType)
  {
    $this->fileClusterType = $fileClusterType;
  }
  /**
   * @return GooglePrivacyDlpV2FileClusterType
   */
  public function getFileClusterType()
  {
    return $this->fileClusterType;
  }
  /**
   * A sample of file types scanned in this cluster. Empty if no files were
   * scanned. File extensions can be derived from the file name or the file
   * content.
   *
   * @param GooglePrivacyDlpV2FileExtensionInfo[] $fileExtensionsScanned
   */
  public function setFileExtensionsScanned($fileExtensionsScanned)
  {
    $this->fileExtensionsScanned = $fileExtensionsScanned;
  }
  /**
   * @return GooglePrivacyDlpV2FileExtensionInfo[]
   */
  public function getFileExtensionsScanned()
  {
    return $this->fileExtensionsScanned;
  }
  /**
   * A sample of file types seen in this cluster. Empty if no files were seen.
   * File extensions can be derived from the file name or the file content.
   *
   * @param GooglePrivacyDlpV2FileExtensionInfo[] $fileExtensionsSeen
   */
  public function setFileExtensionsSeen($fileExtensionsSeen)
  {
    $this->fileExtensionsSeen = $fileExtensionsSeen;
  }
  /**
   * @return GooglePrivacyDlpV2FileExtensionInfo[]
   */
  public function getFileExtensionsSeen()
  {
    return $this->fileExtensionsSeen;
  }
  /**
   * InfoTypes detected in this cluster.
   *
   * @param GooglePrivacyDlpV2FileStoreInfoTypeSummary[] $fileStoreInfoTypeSummaries
   */
  public function setFileStoreInfoTypeSummaries($fileStoreInfoTypeSummaries)
  {
    $this->fileStoreInfoTypeSummaries = $fileStoreInfoTypeSummaries;
  }
  /**
   * @return GooglePrivacyDlpV2FileStoreInfoTypeSummary[]
   */
  public function getFileStoreInfoTypeSummaries()
  {
    return $this->fileStoreInfoTypeSummaries;
  }
  /**
   * True if no files exist in this cluster. If the file store had more files
   * than could be listed, this will be false even if no files for this cluster
   * were seen and file_extensions_seen is empty.
   *
   * @param bool $noFilesExist
   */
  public function setNoFilesExist($noFilesExist)
  {
    $this->noFilesExist = $noFilesExist;
  }
  /**
   * @return bool
   */
  public function getNoFilesExist()
  {
    return $this->noFilesExist;
  }
  /**
   * The sensitivity score of this cluster. The score will be SENSITIVITY_LOW if
   * nothing has been scanned.
   *
   * @param GooglePrivacyDlpV2SensitivityScore $sensitivityScore
   */
  public function setSensitivityScore(GooglePrivacyDlpV2SensitivityScore $sensitivityScore)
  {
    $this->sensitivityScore = $sensitivityScore;
  }
  /**
   * @return GooglePrivacyDlpV2SensitivityScore
   */
  public function getSensitivityScore()
  {
    return $this->sensitivityScore;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2FileClusterSummary::class, 'Google_Service_DLP_GooglePrivacyDlpV2FileClusterSummary');
