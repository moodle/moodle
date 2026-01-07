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

class DataSourceReference extends \Google\Model
{
  /**
   * The possible states of backup configuration. Status not set.
   */
  public const DATA_SOURCE_BACKUP_CONFIG_STATE_BACKUP_CONFIG_STATE_UNSPECIFIED = 'BACKUP_CONFIG_STATE_UNSPECIFIED';
  /**
   * The data source is actively protected (i.e. there is a
   * BackupPlanAssociation or Appliance SLA pointing to it)
   */
  public const DATA_SOURCE_BACKUP_CONFIG_STATE_ACTIVE = 'ACTIVE';
  /**
   * The data source is no longer protected (but may have backups under it)
   */
  public const DATA_SOURCE_BACKUP_CONFIG_STATE_PASSIVE = 'PASSIVE';
  /**
   * Output only. The time when the DataSourceReference was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The resource name of the DataSource. Format: projects/{project
   * }/locations/{location}/backupVaults/{backupVault}/dataSources/{dataSource}
   *
   * @var string
   */
  public $dataSource;
  protected $dataSourceBackupConfigInfoType = DataSourceBackupConfigInfo::class;
  protected $dataSourceBackupConfigInfoDataType = '';
  /**
   * Output only. The backup configuration state of the DataSource.
   *
   * @var string
   */
  public $dataSourceBackupConfigState;
  /**
   * Output only. Number of backups in the DataSource.
   *
   * @var string
   */
  public $dataSourceBackupCount;
  protected $dataSourceGcpResourceInfoType = DataSourceGcpResourceInfo::class;
  protected $dataSourceGcpResourceInfoDataType = '';
  /**
   * Identifier. The resource name of the DataSourceReference. Format: projects/
   * {project}/locations/{location}/dataSourceReferences/{data_source_reference}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Total size of the storage used by all backup resources for the
   * referenced datasource.
   *
   * @var string
   */
  public $totalStoredBytes;

  /**
   * Output only. The time when the DataSourceReference was created.
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
   * Output only. The resource name of the DataSource. Format: projects/{project
   * }/locations/{location}/backupVaults/{backupVault}/dataSources/{dataSource}
   *
   * @param string $dataSource
   */
  public function setDataSource($dataSource)
  {
    $this->dataSource = $dataSource;
  }
  /**
   * @return string
   */
  public function getDataSource()
  {
    return $this->dataSource;
  }
  /**
   * Output only. Information of backup configuration on the DataSource.
   *
   * @param DataSourceBackupConfigInfo $dataSourceBackupConfigInfo
   */
  public function setDataSourceBackupConfigInfo(DataSourceBackupConfigInfo $dataSourceBackupConfigInfo)
  {
    $this->dataSourceBackupConfigInfo = $dataSourceBackupConfigInfo;
  }
  /**
   * @return DataSourceBackupConfigInfo
   */
  public function getDataSourceBackupConfigInfo()
  {
    return $this->dataSourceBackupConfigInfo;
  }
  /**
   * Output only. The backup configuration state of the DataSource.
   *
   * Accepted values: BACKUP_CONFIG_STATE_UNSPECIFIED, ACTIVE, PASSIVE
   *
   * @param self::DATA_SOURCE_BACKUP_CONFIG_STATE_* $dataSourceBackupConfigState
   */
  public function setDataSourceBackupConfigState($dataSourceBackupConfigState)
  {
    $this->dataSourceBackupConfigState = $dataSourceBackupConfigState;
  }
  /**
   * @return self::DATA_SOURCE_BACKUP_CONFIG_STATE_*
   */
  public function getDataSourceBackupConfigState()
  {
    return $this->dataSourceBackupConfigState;
  }
  /**
   * Output only. Number of backups in the DataSource.
   *
   * @param string $dataSourceBackupCount
   */
  public function setDataSourceBackupCount($dataSourceBackupCount)
  {
    $this->dataSourceBackupCount = $dataSourceBackupCount;
  }
  /**
   * @return string
   */
  public function getDataSourceBackupCount()
  {
    return $this->dataSourceBackupCount;
  }
  /**
   * Output only. The GCP resource that the DataSource is associated with.
   *
   * @param DataSourceGcpResourceInfo $dataSourceGcpResourceInfo
   */
  public function setDataSourceGcpResourceInfo(DataSourceGcpResourceInfo $dataSourceGcpResourceInfo)
  {
    $this->dataSourceGcpResourceInfo = $dataSourceGcpResourceInfo;
  }
  /**
   * @return DataSourceGcpResourceInfo
   */
  public function getDataSourceGcpResourceInfo()
  {
    return $this->dataSourceGcpResourceInfo;
  }
  /**
   * Identifier. The resource name of the DataSourceReference. Format: projects/
   * {project}/locations/{location}/dataSourceReferences/{data_source_reference}
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
   * Output only. Total size of the storage used by all backup resources for the
   * referenced datasource.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataSourceReference::class, 'Google_Service_Backupdr_DataSourceReference');
