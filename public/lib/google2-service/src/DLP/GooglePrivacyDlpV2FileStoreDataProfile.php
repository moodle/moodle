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

class GooglePrivacyDlpV2FileStoreDataProfile extends \Google\Collection
{
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
   * The time the file store was first created.
   *
   * @var string
   */
  public $createTime;
  protected $dataRiskLevelType = GooglePrivacyDlpV2DataRiskLevel::class;
  protected $dataRiskLevelDataType = '';
  protected $dataSourceTypeType = GooglePrivacyDlpV2DataSourceType::class;
  protected $dataSourceTypeDataType = '';
  /**
   * For resources that have multiple storage locations, these are those
   * regions. For Cloud Storage this is the list of regions chosen for dual-
   * region storage. `file_store_location` will normally be the corresponding
   * multi-region for the list of individual locations. The first region is
   * always picked as the processing and storage location for the data profile.
   *
   * @var string[]
   */
  public $dataStorageLocations;
  protected $domainsType = GooglePrivacyDlpV2Domain::class;
  protected $domainsDataType = 'array';
  protected $fileClusterSummariesType = GooglePrivacyDlpV2FileClusterSummary::class;
  protected $fileClusterSummariesDataType = 'array';
  protected $fileStoreInfoTypeSummariesType = GooglePrivacyDlpV2FileStoreInfoTypeSummary::class;
  protected $fileStoreInfoTypeSummariesDataType = 'array';
  /**
   * The file store does not have any files. If the profiling operation failed,
   * this is false.
   *
   * @var bool
   */
  public $fileStoreIsEmpty;
  /**
   * The location of the file store. * Cloud Storage:
   * https://cloud.google.com/storage/docs/locations#available-locations *
   * Amazon S3:
   * https://docs.aws.amazon.com/general/latest/gr/rande.html#regional-endpoints
   *
   * @var string
   */
  public $fileStoreLocation;
  /**
   * The file store path. * Cloud Storage: `gs://{bucket}` * Amazon S3:
   * `s3://{bucket}` * Vertex AI dataset:
   * `projects/{project_number}/locations/{location}/datasets/{dataset_id}`
   *
   * @var string
   */
  public $fileStorePath;
  /**
   * The resource name of the resource profiled.
   * https://cloud.google.com/apis/design/resource_names#full_resource_name
   * Example format of an S3 bucket full resource name: `//cloudasset.googleapis
   * .com/organizations/{org_id}/otherCloudConnections/aws/arn:aws:s3:::{bucket_
   * name}`
   *
   * @var string
   */
  public $fullResource;
  /**
   * The time the file store was last modified.
   *
   * @var string
   */
  public $lastModifiedTime;
  /**
   * The location type of the file store (region, dual-region, multi-region,
   * etc). If dual-region, expect data_storage_locations to be populated.
   *
   * @var string
   */
  public $locationType;
  /**
   * The name of the profile.
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
   * The resource name of the project data profile for this file store.
   *
   * @var string
   */
  public $projectDataProfile;
  /**
   * The Google Cloud project ID that owns the resource. For Amazon S3 buckets,
   * this is the AWS Account Id.
   *
   * @var string
   */
  public $projectId;
  protected $relatedResourcesType = GooglePrivacyDlpV2RelatedResource::class;
  protected $relatedResourcesDataType = 'array';
  protected $resourceAttributesType = GooglePrivacyDlpV2Value::class;
  protected $resourceAttributesDataType = 'map';
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
  protected $sampleFindingsTableType = GooglePrivacyDlpV2BigQueryTable::class;
  protected $sampleFindingsTableDataType = '';
  protected $sensitivityScoreType = GooglePrivacyDlpV2SensitivityScore::class;
  protected $sensitivityScoreDataType = '';
  /**
   * State of a profile.
   *
   * @var string
   */
  public $state;
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
   * The time the file store was first created.
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
   * The data risk level of this resource.
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
   * For resources that have multiple storage locations, these are those
   * regions. For Cloud Storage this is the list of regions chosen for dual-
   * region storage. `file_store_location` will normally be the corresponding
   * multi-region for the list of individual locations. The first region is
   * always picked as the processing and storage location for the data profile.
   *
   * @param string[] $dataStorageLocations
   */
  public function setDataStorageLocations($dataStorageLocations)
  {
    $this->dataStorageLocations = $dataStorageLocations;
  }
  /**
   * @return string[]
   */
  public function getDataStorageLocations()
  {
    return $this->dataStorageLocations;
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
   * FileClusterSummary per each cluster.
   *
   * @param GooglePrivacyDlpV2FileClusterSummary[] $fileClusterSummaries
   */
  public function setFileClusterSummaries($fileClusterSummaries)
  {
    $this->fileClusterSummaries = $fileClusterSummaries;
  }
  /**
   * @return GooglePrivacyDlpV2FileClusterSummary[]
   */
  public function getFileClusterSummaries()
  {
    return $this->fileClusterSummaries;
  }
  /**
   * InfoTypes detected in this file store.
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
   * The file store does not have any files. If the profiling operation failed,
   * this is false.
   *
   * @param bool $fileStoreIsEmpty
   */
  public function setFileStoreIsEmpty($fileStoreIsEmpty)
  {
    $this->fileStoreIsEmpty = $fileStoreIsEmpty;
  }
  /**
   * @return bool
   */
  public function getFileStoreIsEmpty()
  {
    return $this->fileStoreIsEmpty;
  }
  /**
   * The location of the file store. * Cloud Storage:
   * https://cloud.google.com/storage/docs/locations#available-locations *
   * Amazon S3:
   * https://docs.aws.amazon.com/general/latest/gr/rande.html#regional-endpoints
   *
   * @param string $fileStoreLocation
   */
  public function setFileStoreLocation($fileStoreLocation)
  {
    $this->fileStoreLocation = $fileStoreLocation;
  }
  /**
   * @return string
   */
  public function getFileStoreLocation()
  {
    return $this->fileStoreLocation;
  }
  /**
   * The file store path. * Cloud Storage: `gs://{bucket}` * Amazon S3:
   * `s3://{bucket}` * Vertex AI dataset:
   * `projects/{project_number}/locations/{location}/datasets/{dataset_id}`
   *
   * @param string $fileStorePath
   */
  public function setFileStorePath($fileStorePath)
  {
    $this->fileStorePath = $fileStorePath;
  }
  /**
   * @return string
   */
  public function getFileStorePath()
  {
    return $this->fileStorePath;
  }
  /**
   * The resource name of the resource profiled.
   * https://cloud.google.com/apis/design/resource_names#full_resource_name
   * Example format of an S3 bucket full resource name: `//cloudasset.googleapis
   * .com/organizations/{org_id}/otherCloudConnections/aws/arn:aws:s3:::{bucket_
   * name}`
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
   * The time the file store was last modified.
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
   * The location type of the file store (region, dual-region, multi-region,
   * etc). If dual-region, expect data_storage_locations to be populated.
   *
   * @param string $locationType
   */
  public function setLocationType($locationType)
  {
    $this->locationType = $locationType;
  }
  /**
   * @return string
   */
  public function getLocationType()
  {
    return $this->locationType;
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
   * The resource name of the project data profile for this file store.
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
   * The Google Cloud project ID that owns the resource. For Amazon S3 buckets,
   * this is the AWS Account Id.
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
   * Attributes of the resource being profiled. Currently used attributes: *
   * customer_managed_encryption: boolean - true: the resource is encrypted with
   * a customer-managed key. - false: the resource is encrypted with a provider-
   * managed key.
   *
   * @param GooglePrivacyDlpV2Value[] $resourceAttributes
   */
  public function setResourceAttributes($resourceAttributes)
  {
    $this->resourceAttributes = $resourceAttributes;
  }
  /**
   * @return GooglePrivacyDlpV2Value[]
   */
  public function getResourceAttributes()
  {
    return $this->resourceAttributes;
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
   * The sensitivity score of this resource.
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
   * State of a profile.
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
   * The tags attached to the resource, including any tags attached during
   * profiling.
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
class_alias(GooglePrivacyDlpV2FileStoreDataProfile::class, 'Google_Service_DLP_GooglePrivacyDlpV2FileStoreDataProfile');
