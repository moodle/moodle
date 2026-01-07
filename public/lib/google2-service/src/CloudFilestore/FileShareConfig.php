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

namespace Google\Service\CloudFilestore;

class FileShareConfig extends \Google\Collection
{
  protected $collection_key = 'nfsExportOptions';
  /**
   * File share capacity in gigabytes (GB). Filestore defines 1 GB as 1024^3
   * bytes.
   *
   * @var string
   */
  public $capacityGb;
  /**
   * Required. The name of the file share. Must use 1-16 characters for the
   * basic service tier and 1-63 characters for all other service tiers. Must
   * use lowercase letters, numbers, or underscores `[a-z0-9_]`. Must start with
   * a letter. Immutable.
   *
   * @var string
   */
  public $name;
  protected $nfsExportOptionsType = NfsExportOptions::class;
  protected $nfsExportOptionsDataType = 'array';
  /**
   * The resource name of the backup, in the format
   * `projects/{project_number}/locations/{location_id}/backups/{backup_id}`,
   * that this file share has been restored from.
   *
   * @var string
   */
  public $sourceBackup;
  /**
   * The resource name of the BackupDR backup, in the format `projects/{project_
   * id}/locations/{location_id}/backupVaults/{backupvault_id}/dataSources/{data
   * source_id}/backups/{backup_id}`, TODO (b/443690479) - Remove visibility
   * restrictions once the feature is ready
   *
   * @var string
   */
  public $sourceBackupdrBackup;

  /**
   * File share capacity in gigabytes (GB). Filestore defines 1 GB as 1024^3
   * bytes.
   *
   * @param string $capacityGb
   */
  public function setCapacityGb($capacityGb)
  {
    $this->capacityGb = $capacityGb;
  }
  /**
   * @return string
   */
  public function getCapacityGb()
  {
    return $this->capacityGb;
  }
  /**
   * Required. The name of the file share. Must use 1-16 characters for the
   * basic service tier and 1-63 characters for all other service tiers. Must
   * use lowercase letters, numbers, or underscores `[a-z0-9_]`. Must start with
   * a letter. Immutable.
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
   * Nfs Export Options. There is a limit of 10 export options per file share.
   *
   * @param NfsExportOptions[] $nfsExportOptions
   */
  public function setNfsExportOptions($nfsExportOptions)
  {
    $this->nfsExportOptions = $nfsExportOptions;
  }
  /**
   * @return NfsExportOptions[]
   */
  public function getNfsExportOptions()
  {
    return $this->nfsExportOptions;
  }
  /**
   * The resource name of the backup, in the format
   * `projects/{project_number}/locations/{location_id}/backups/{backup_id}`,
   * that this file share has been restored from.
   *
   * @param string $sourceBackup
   */
  public function setSourceBackup($sourceBackup)
  {
    $this->sourceBackup = $sourceBackup;
  }
  /**
   * @return string
   */
  public function getSourceBackup()
  {
    return $this->sourceBackup;
  }
  /**
   * The resource name of the BackupDR backup, in the format `projects/{project_
   * id}/locations/{location_id}/backupVaults/{backupvault_id}/dataSources/{data
   * source_id}/backups/{backup_id}`, TODO (b/443690479) - Remove visibility
   * restrictions once the feature is ready
   *
   * @param string $sourceBackupdrBackup
   */
  public function setSourceBackupdrBackup($sourceBackupdrBackup)
  {
    $this->sourceBackupdrBackup = $sourceBackupdrBackup;
  }
  /**
   * @return string
   */
  public function getSourceBackupdrBackup()
  {
    return $this->sourceBackupdrBackup;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FileShareConfig::class, 'Google_Service_CloudFilestore_FileShareConfig');
