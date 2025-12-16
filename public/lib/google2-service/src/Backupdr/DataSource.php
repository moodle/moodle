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

namespace Google\Service\Backupdr;

class DataSource extends \Google\Model
{
  /**
   * The possible states of backup configuration. Status not set.
   */
  public const CONFIG_STATE_BACKUP_CONFIG_STATE_UNSPECIFIED = 'BACKUP_CONFIG_STATE_UNSPECIFIED';
  /**
   * The data source is actively protected (i.e. there is a
   * BackupPlanAssociation or Appliance SLA pointing to it)
   */
  public const CONFIG_STATE_ACTIVE = 'ACTIVE';
  /**
   * The data source is no longer protected (but may have backups under it)
   */
  public const CONFIG_STATE_PASSIVE = 'PASSIVE';
  /**
   * State not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The data source is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The data source has been created and is fully usable.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The data source is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The data source is experiencing an issue and might be unusable.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * Output only. This field is set to true if the backup is blocked by vault
   * access restriction.
   *
   * @var bool
   */
  public $backupBlockedByVaultAccessRestriction;
  protected $backupConfigInfoType = BackupConfigInfo::class;
  protected $backupConfigInfoDataType = '';
  /**
   * Number of backups in the data source.
   *
   * @var string
   */
  public $backupCount;
  /**
   * Output only. The backup configuration state.
   *
   * @var string
   */
  public $configState;
  /**
   * Output only. The time when the instance was created.
   *
   * @var string
   */
  public $createTime;
  protected $dataSourceBackupApplianceApplicationType = DataSourceBackupApplianceApplication::class;
  protected $dataSourceBackupApplianceApplicationDataType = '';
  protected $dataSourceGcpResourceType = DataSourceGcpResource::class;
  protected $dataSourceGcpResourceDataType = '';
  /**
   * Server specified ETag for the ManagementServer resource to prevent
   * simultaneous updates from overwiting each other.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. Resource labels to represent user provided metadata. No labels
   * currently defined:
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Identifier. Name of the datasource to create. It must have the
   * format`"projects/{project}/locations/{location}/backupVaults/{backupvault}/
   * dataSources/{datasource}"`. `{datasource}` cannot be changed after
   * creation. It must be between 3-63 characters long and must be unique within
   * the backup vault.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The DataSource resource instance state.
   *
   * @var string
   */
  public $state;
  /**
   * The number of bytes (metadata and data) stored in this datasource.
   *
   * @var string
   */
  public $totalStoredBytes;
  /**
   * Output only. The time when the instance was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. This field is set to true if the backup is blocked by vault
   * access restriction.
   *
   * @param bool $backupBlockedByVaultAccessRestriction
   */
  public function setBackupBlockedByVaultAccessRestriction($backupBlockedByVaultAccessRestriction)
  {
    $this->backupBlockedByVaultAccessRestriction = $backupBlockedByVaultAccessRestriction;
  }
  /**
   * @return bool
   */
  public function getBackupBlockedByVaultAccessRestriction()
  {
    return $this->backupBlockedByVaultAccessRestriction;
  }
  /**
   * Output only. Details of how the resource is configured for backup.
   *
   * @param BackupConfigInfo $backupConfigInfo
   */
  public function setBackupConfigInfo(BackupConfigInfo $backupConfigInfo)
  {
    $this->backupConfigInfo = $backupConfigInfo;
  }
  /**
   * @return BackupConfigInfo
   */
  public function getBackupConfigInfo()
  {
    return $this->backupConfigInfo;
  }
  /**
   * Number of backups in the data source.
   *
   * @param string $backupCount
   */
  public function setBackupCount($backupCount)
  {
    $this->backupCount = $backupCount;
  }
  /**
   * @return string
   */
  public function getBackupCount()
  {
    return $this->backupCount;
  }
  /**
   * Output only. The backup configuration state.
   *
   * Accepted values: BACKUP_CONFIG_STATE_UNSPECIFIED, ACTIVE, PASSIVE
   *
   * @param self::CONFIG_STATE_* $configState
   */
  public function setConfigState($configState)
  {
    $this->configState = $configState;
  }
  /**
   * @return self::CONFIG_STATE_*
   */
  public function getConfigState()
  {
    return $this->configState;
  }
  /**
   * Output only. The time when the instance was created.
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
   * The backed up resource is a backup appliance application.
   *
   * @param DataSourceBackupApplianceApplication $dataSourceBackupApplianceApplication
   */
  public function setDataSourceBackupApplianceApplication(DataSourceBackupApplianceApplication $dataSourceBackupApplianceApplication)
  {
    $this->dataSourceBackupApplianceApplication = $dataSourceBackupApplianceApplication;
  }
  /**
   * @return DataSourceBackupApplianceApplication
   */
  public function getDataSourceBackupApplianceApplication()
  {
    return $this->dataSourceBackupApplianceApplication;
  }
  /**
   * The backed up resource is a Google Cloud resource. The word 'DataSource'
   * was included in the names to indicate that this is the representation of
   * the Google Cloud resource used within the DataSource object.
   *
   * @param DataSourceGcpResource $dataSourceGcpResource
   */
  public function setDataSourceGcpResource(DataSourceGcpResource $dataSourceGcpResource)
  {
    $this->dataSourceGcpResource = $dataSourceGcpResource;
  }
  /**
   * @return DataSourceGcpResource
   */
  public function getDataSourceGcpResource()
  {
    return $this->dataSourceGcpResource;
  }
  /**
   * Server specified ETag for the ManagementServer resource to prevent
   * simultaneous updates from overwiting each other.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. Resource labels to represent user provided metadata. No labels
   * currently defined:
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. Identifier. Name of the datasource to create. It must have the
   * format`"projects/{project}/locations/{location}/backupVaults/{backupvault}/
   * dataSources/{datasource}"`. `{datasource}` cannot be changed after
   * creation. It must be between 3-63 characters long and must be unique within
   * the backup vault.
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
   * Output only. The DataSource resource instance state.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING, ERROR
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
   * The number of bytes (metadata and data) stored in this datasource.
   *
   * @param string $totalStoredBytes
   */
  public function setTotalStoredBytes($totalStoredBytes)
  {
    $this->totalStoredBytes = $totalStoredBytes;
  }
  /**
   * @return string
   */
  public function getTotalStoredBytes()
  {
    return $this->totalStoredBytes;
  }
  /**
   * Output only. The time when the instance was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataSource::class, 'Google_Service_Backupdr_DataSource');
