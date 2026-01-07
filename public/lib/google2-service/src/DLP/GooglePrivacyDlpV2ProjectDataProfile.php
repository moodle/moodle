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

class GooglePrivacyDlpV2ProjectDataProfile extends \Google\Model
{
  protected $dataRiskLevelType = GooglePrivacyDlpV2DataRiskLevel::class;
  protected $dataRiskLevelDataType = '';
  /**
   * The number of file store data profiles generated for this project.
   *
   * @var string
   */
  public $fileStoreDataProfileCount;
  /**
   * The resource name of the profile.
   *
   * @var string
   */
  public $name;
  /**
   * The last time the profile was generated.
   *
   * @var string
   */
  public $profileLastGenerated;
  protected $profileStatusType = GooglePrivacyDlpV2ProfileStatus::class;
  protected $profileStatusDataType = '';
  /**
   * Project ID or account that was profiled.
   *
   * @var string
   */
  public $projectId;
  protected $sensitivityScoreType = GooglePrivacyDlpV2SensitivityScore::class;
  protected $sensitivityScoreDataType = '';
  /**
   * The number of table data profiles generated for this project.
   *
   * @var string
   */
  public $tableDataProfileCount;

  /**
   * The data risk level of this project.
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
   * The number of file store data profiles generated for this project.
   *
   * @param string $fileStoreDataProfileCount
   */
  public function setFileStoreDataProfileCount($fileStoreDataProfileCount)
  {
    $this->fileStoreDataProfileCount = $fileStoreDataProfileCount;
  }
  /**
   * @return string
   */
  public function getFileStoreDataProfileCount()
  {
    return $this->fileStoreDataProfileCount;
  }
  /**
   * The resource name of the profile.
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
   * The last time the profile was generated.
   *
   * @param string $profileLastGenerated
   */
  public function setProfileLastGenerated($profileLastGenerated)
  {
    $this->profileLastGenerated = $profileLastGenerated;
  }
  /**
   * @return string
   */
  public function getProfileLastGenerated()
  {
    return $this->profileLastGenerated;
  }
  /**
   * Success or error status of the last attempt to profile the project.
   *
   * @param GooglePrivacyDlpV2ProfileStatus $profileStatus
   */
  public function setProfileStatus(GooglePrivacyDlpV2ProfileStatus $profileStatus)
  {
    $this->profileStatus = $profileStatus;
  }
  /**
   * @return GooglePrivacyDlpV2ProfileStatus
   */
  public function getProfileStatus()
  {
    return $this->profileStatus;
  }
  /**
   * Project ID or account that was profiled.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * The sensitivity score of this project.
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
  /**
   * The number of table data profiles generated for this project.
   *
   * @param string $tableDataProfileCount
   */
  public function setTableDataProfileCount($tableDataProfileCount)
  {
    $this->tableDataProfileCount = $tableDataProfileCount;
  }
  /**
   * @return string
   */
  public function getTableDataProfileCount()
  {
    return $this->tableDataProfileCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2ProjectDataProfile::class, 'Google_Service_DLP_GooglePrivacyDlpV2ProjectDataProfile');
