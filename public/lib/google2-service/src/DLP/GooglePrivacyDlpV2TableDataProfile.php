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

class GooglePrivacyDlpV2TableDataProfile extends \Google\Collection
{
  /**
   * Unused.
   */
  public const ENCRYPTION_STATUS_ENCRYPTION_STATUS_UNSPECIFIED = 'ENCRYPTION_STATUS_UNSPECIFIED';
  /**
   * Google manages server-side encryption keys on your behalf.
   */
  public const ENCRYPTION_STATUS_ENCRYPTION_GOOGLE_MANAGED = 'ENCRYPTION_GOOGLE_MANAGED';
  /**
   * Customer provides the key.
   */
  public const ENCRYPTION_STATUS_ENCRYPTION_CUSTOMER_MANAGED = 'ENCRYPTION_CUSTOMER_MANAGED';
  /**
   * Unused.
   */
  public const RESOURCE_VISIBILITY_RESOURCE_VISIBILITY_UNSPECIFIED = 'RESOURCE_VISIBILITY_UNSPECIFIED';
  /**
   * Visible to any user.
   */
  public const RESOURCE_VISIBILITY_RESOURCE_VISIBILITY_PUBLIC = 'RESOURCE_VISIBILITY_PUBLIC';
  /**
   * May contain public items. For example, if a Cloud Storage bucket has
   * uniform bucket level access disabled, some objects inside it may be public,
   * but none are known yet.
   */
  public const RESOURCE_VISIBILITY_RESOURCE_VISIBILITY_INCONCLUSIVE = 'RESOURCE_VISIBILITY_INCONCLUSIVE';
  /**
   * Visible only to specific users.
   */
  public const RESOURCE_VISIBILITY_RESOURCE_VISIBILITY_RESTRICTED = 'RESOURCE_VISIBILITY_RESTRICTED';
  /**
   * Unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The profile is currently running. Once a profile has finished it will
   * transition to DONE.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The profile is no longer generating. If profile_status.status.code is 0,
   * the profile succeeded, otherwise, it failed.
   */
  public const STATE_DONE = 'DONE';
  protected $collection_key = 'tags';
  protected $configSnapshotType = GooglePrivacyDlpV2DataProfileConfigSnapshot::class;
  protected $configSnapshotDataType = '';
  /**
   * The time at which the table was created.
   *
   * @var string
   */
  public $createTime;
  protected $dataRiskLevelType = GooglePrivacyDlpV2DataRiskLevel::class;
  protected $dataRiskLevelDataType = '';
  protected $dataSourceTypeType = GooglePrivacyDlpV2DataSourceType::class;
  protected $dataSourceTypeDataType = '';
  /**
   * If the resource is BigQuery, the dataset ID.
   *
   * @var string
   */
  public $datasetId;
  /**
   * If supported, the location where the dataset's data is stored. See
   * https://cloud.google.com/bigquery/docs/locations for supported locations.
   *
   * @var string
   */
  public $datasetLocation;
  /**
   * The Google Cloud project ID that owns the resource.
   *
   * @var string
   */
  public $datasetProjectId;
  protected $domainsType = GooglePrivacyDlpV2Domain::class;
  protected $domainsDataType = 'array';
  /**
   * How the table is encrypted.
   *
   * @var string
   */
  public $encryptionStatus;
  /**
   * Optional. The time when this table expires.
   *
   * @var string
   */
  public $expirationTime;
  /**
   * The number of columns skipped in the table because of an error.
   *
   * @var string
   */
  public $failedColumnCount;
  /**
   * The Cloud Asset Inventory resource that was profiled in order to generate
   * this TableDataProfile.
   * https://cloud.google.com/apis/design/resource_names#full_resource_name
   *
   * @var string
   */
  public $fullResource;
  /**
   * The time when this table was last modified
   *
   * @var string
   */
  public $lastModifiedTime;
  /**
   * The name of the profile.
   *
   * @var string
   */
  public $name;
  protected $otherInfoTypesType = GooglePrivacyDlpV2OtherInfoTypeSummary::class;
  protected $otherInfoTypesDataType = 'array';
  protected $predictedInfoTypesType = GooglePrivacyDlpV2InfoTypeSummary::class;
  protected $predictedInfoTypesDataType = 'array';
  /**
   * The last time the profile was generated.
   *
   * @var string
   */
  public $profileLastGenerated;
  protected $profileStatusType = GooglePrivacyDlpV2ProfileStatus::class;
  protected $profileStatusDataType = '';
  /**
   * The resource name of the project data profile for this table.
   *
   * @var string
   */
  public $projectDataProfile;
  protected $relatedResourcesType = GooglePrivacyDlpV2RelatedResource::class;
  protected $relatedResourcesDataType = 'array';
  /**
   * The labels applied to the resource at the time the profile was generated.
   *
   * @var string[]
   */
  public $resourceLabels;
  /**
   * How broadly a resource has been shared.
   *
   * @var string
   */
  public $resourceVisibility;
  /**
   * Number of rows in the table when the profile was generated. This will not
   * be populated for BigLake tables.
   *
   * @var string
   */
  public $rowCount;
  protected $sampleFindingsTableType = GooglePrivacyDlpV2BigQueryTable::class;
  protected $sampleFindingsTableDataType = '';
  /**
   * The number of columns profiled in the table.
   *
   * @var string
   */
  public $scannedColumnCount;
  protected $sensitivityScoreType = GooglePrivacyDlpV2SensitivityScore::class;
  protected $sensitivityScoreDataType = '';
  /**
   * State of a profile. This will always be set to DONE when the table data
   * profile is written to another service like BigQuery or Pub/Sub.
   *
   * @var string
   */
  public $state;
  /**
   * The table ID.
   *
   * @var string
   */
  public $tableId;
  /**
   * The size of the table when the profile was generated.
   *
   * @var string
   */
  public $tableSizeBytes;
  protected $tagsType = GooglePrivacyDlpV2Tag::class;
  protected $tagsDataType = 'array';

  /**
   * The snapshot of the configurations used to generate the profile.
   *
   * @param GooglePrivacyDlpV2DataProfileConfigSnapshot $configSnapshot
   */
  public function setConfigSnapshot(GooglePrivacyDlpV2DataProfileConfigSnapshot $configSnapshot)
  {
    $this->configSnapshot = $configSnapshot;
  }
  /**
   * @return GooglePrivacyDlpV2DataProfileConfigSnapshot
   */
  public function getConfigSnapshot()
  {
    return $this->configSnapshot;
  }
  /**
   * The time at which the table was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The data risk level of this table.
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
   * The resource type that was profiled.
   *
   * @param GooglePrivacyDlpV2DataSourceType $dataSourceType
   */
  public function setDataSourceType(GooglePrivacyDlpV2DataSourceType $dataSourceType)
  {
    $this->dataSourceType = $dataSourceType;
  }
  /**
   * @return GooglePrivacyDlpV2DataSourceType
   */
  public function getDataSourceType()
  {
    return $this->dataSourceType;
  }
  /**
   * If the resource is BigQuery, the dataset ID.
   *
   * @param string $datasetId
   */
  public function setDatasetId($datasetId)
  {
    $this->datasetId = $datasetId;
  }
  /**
   * @return string
   */
  public function getDatasetId()
  {
    return $this->datasetId;
  }
  /**
   * If supported, the location where the dataset's data is stored. See
   * https://cloud.google.com/bigquery/docs/locations for supported locations.
   *
   * @param string $datasetLocation
   */
  public function setDatasetLocation($datasetLocation)
  {
    $this->datasetLocation = $datasetLocation;
  }
  /**
   * @return string
   */
  public function getDatasetLocation()
  {
    return $this->datasetLocation;
  }
  /**
   * The Google Cloud project ID that owns the resource.
   *
   * @param string $datasetProjectId
   */
  public function setDatasetProjectId($datasetProjectId)
  {
    $this->datasetProjectId = $datasetProjectId;
  }
  /**
   * @return string
   */
  public function getDatasetProjectId()
  {
    return $this->datasetProjectId;
  }
  /**
   * Domains associated with the profile.
   *
   * @param GooglePrivacyDlpV2Domain[] $domains
   */
  public function setDomains($domains)
  {
    $this->domains = $domains;
  }
  /**
   * @return GooglePrivacyDlpV2Domain[]
   */
  public function getDomains()
  {
    return $this->domains;
  }
  /**
   * How the table is encrypted.
   *
   * Accepted values: ENCRYPTION_STATUS_UNSPECIFIED, ENCRYPTION_GOOGLE_MANAGED,
   * ENCRYPTION_CUSTOMER_MANAGED
   *
   * @param self::ENCRYPTION_STATUS_* $encryptionStatus
   */
  public function setEncryptionStatus($encryptionStatus)
  {
    $this->encryptionStatus = $encryptionStatus;
  }
  /**
   * @return self::ENCRYPTION_STATUS_*
   */
  public function getEncryptionStatus()
  {
    return $this->encryptionStatus;
  }
  /**
   * Optional. The time when this table expires.
   *
   * @param string $expirationTime
   */
  public function setExpirationTime($expirationTime)
  {
    $this->expirationTime = $expirationTime;
  }
  /**
   * @return string
   */
  public function getExpirationTime()
  {
    return $this->expirationTime;
  }
  /**
   * The number of columns skipped in the table because of an error.
   *
   * @param string $failedColumnCount
   */
  public function setFailedColumnCount($failedColumnCount)
  {
    $this->failedColumnCount = $failedColumnCount;
  }
  /**
   * @return string
   */
  public function getFailedColumnCount()
  {
    return $this->failedColumnCount;
  }
  /**
   * The Cloud Asset Inventory resource that was profiled in order to generate
   * this TableDataProfile.
   * https://cloud.google.com/apis/design/resource_names#full_resource_name
   *
   * @param string $fullResource
   */
  public function setFullResource($fullResource)
  {
    $this->fullResource = $fullResource;
  }
  /**
   * @return string
   */
  public function getFullResource()
  {
    return $this->fullResource;
  }
  /**
   * The time when this table was last modified
   *
   * @param string $lastModifiedTime
   */
  public function setLastModifiedTime($lastModifiedTime)
  {
    $this->lastModifiedTime = $lastModifiedTime;
  }
  /**
   * @return string
   */
  public function getLastModifiedTime()
  {
    return $this->lastModifiedTime;
  }
  /**
   * The name of the profile.
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
   * Other infoTypes found in this table's data.
   *
   * @param GooglePrivacyDlpV2OtherInfoTypeSummary[] $otherInfoTypes
   */
  public function setOtherInfoTypes($otherInfoTypes)
  {
    $this->otherInfoTypes = $otherInfoTypes;
  }
  /**
   * @return GooglePrivacyDlpV2OtherInfoTypeSummary[]
   */
  public function getOtherInfoTypes()
  {
    return $this->otherInfoTypes;
  }
  /**
   * The infoTypes predicted from this table's data.
   *
   * @param GooglePrivacyDlpV2InfoTypeSummary[] $predictedInfoTypes
   */
  public function setPredictedInfoTypes($predictedInfoTypes)
  {
    $this->predictedInfoTypes = $predictedInfoTypes;
  }
  /**
   * @return GooglePrivacyDlpV2InfoTypeSummary[]
   */
  public function getPredictedInfoTypes()
  {
    return $this->predictedInfoTypes;
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
   * Success or error status from the most recent profile generation attempt.
   * May be empty if the profile is still being generated.
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
   * The resource name of the project data profile for this table.
   *
   * @param string $projectDataProfile
   */
  public function setProjectDataProfile($projectDataProfile)
  {
    $this->projectDataProfile = $projectDataProfile;
  }
  /**
   * @return string
   */
  public function getProjectDataProfile()
  {
    return $this->projectDataProfile;
  }
  /**
   * Resources related to this profile.
   *
   * @param GooglePrivacyDlpV2RelatedResource[] $relatedResources
   */
  public function setRelatedResources($relatedResources)
  {
    $this->relatedResources = $relatedResources;
  }
  /**
   * @return GooglePrivacyDlpV2RelatedResource[]
   */
  public function getRelatedResources()
  {
    return $this->relatedResources;
  }
  /**
   * The labels applied to the resource at the time the profile was generated.
   *
   * @param string[] $resourceLabels
   */
  public function setResourceLabels($resourceLabels)
  {
    $this->resourceLabels = $resourceLabels;
  }
  /**
   * @return string[]
   */
  public function getResourceLabels()
  {
    return $this->resourceLabels;
  }
  /**
   * How broadly a resource has been shared.
   *
   * Accepted values: RESOURCE_VISIBILITY_UNSPECIFIED,
   * RESOURCE_VISIBILITY_PUBLIC, RESOURCE_VISIBILITY_INCONCLUSIVE,
   * RESOURCE_VISIBILITY_RESTRICTED
   *
   * @param self::RESOURCE_VISIBILITY_* $resourceVisibility
   */
  public function setResourceVisibility($resourceVisibility)
  {
    $this->resourceVisibility = $resourceVisibility;
  }
  /**
   * @return self::RESOURCE_VISIBILITY_*
   */
  public function getResourceVisibility()
  {
    return $this->resourceVisibility;
  }
  /**
   * Number of rows in the table when the profile was generated. This will not
   * be populated for BigLake tables.
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
   * The BigQuery table to which the sample findings are written.
   *
   * @param GooglePrivacyDlpV2BigQueryTable $sampleFindingsTable
   */
  public function setSampleFindingsTable(GooglePrivacyDlpV2BigQueryTable $sampleFindingsTable)
  {
    $this->sampleFindingsTable = $sampleFindingsTable;
  }
  /**
   * @return GooglePrivacyDlpV2BigQueryTable
   */
  public function getSampleFindingsTable()
  {
    return $this->sampleFindingsTable;
  }
  /**
   * The number of columns profiled in the table.
   *
   * @param string $scannedColumnCount
   */
  public function setScannedColumnCount($scannedColumnCount)
  {
    $this->scannedColumnCount = $scannedColumnCount;
  }
  /**
   * @return string
   */
  public function getScannedColumnCount()
  {
    return $this->scannedColumnCount;
  }
  /**
   * The sensitivity score of this table.
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
   * State of a profile. This will always be set to DONE when the table data
   * profile is written to another service like BigQuery or Pub/Sub.
   *
   * Accepted values: STATE_UNSPECIFIED, RUNNING, DONE
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * The table ID.
   *
   * @param string $tableId
   */
  public function setTableId($tableId)
  {
    $this->tableId = $tableId;
  }
  /**
   * @return string
   */
  public function getTableId()
  {
    return $this->tableId;
  }
  /**
   * The size of the table when the profile was generated.
   *
   * @param string $tableSizeBytes
   */
  public function setTableSizeBytes($tableSizeBytes)
  {
    $this->tableSizeBytes = $tableSizeBytes;
  }
  /**
   * @return string
   */
  public function getTableSizeBytes()
  {
    return $this->tableSizeBytes;
  }
  /**
   * The tags attached to the table, including any tags attached during
   * profiling. Because tags are attached to Cloud SQL instances rather than
   * Cloud SQL tables, this field is empty for Cloud SQL table profiles.
   *
   * @param GooglePrivacyDlpV2Tag[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return GooglePrivacyDlpV2Tag[]
   */
  public function getTags()
  {
    return $this->tags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2TableDataProfile::class, 'Google_Service_DLP_GooglePrivacyDlpV2TableDataProfile');
