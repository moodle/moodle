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

namespace Google\Service\SQLAdmin;

class InstancesRestoreBackupRequest extends \Google\Collection
{
  protected $collection_key = 'restoreInstanceClearOverridesFieldNames';
  /**
   * The name of the backup that's used to restore a Cloud SQL instance: Format:
   * projects/{project-id}/backups/{backup-uid}. Only one of
   * restore_backup_context, backup, backupdr_backup can be passed to the input.
   *
   * @var string
   */
  public $backup;
  /**
   * The name of the backup that's used to restore a Cloud SQL instance: Format:
   * "projects/{project-id}/locations/{location}/backupVaults/{backupvault}/data
   * Sources/{datasource}/backups/{backup-uid}". Only one of
   * restore_backup_context, backup, backupdr_backup can be passed to the input.
   *
   * @var string
   */
  public $backupdrBackup;
  protected $restoreBackupContextType = RestoreBackupContext::class;
  protected $restoreBackupContextDataType = '';
  /**
   * Optional. This field has the same purpose as restore_instance_settings,
   * changes any instance settings stored in the backup you are restoring from.
   * With the difference that these fields are cleared in the settings.
   *
   * @var string[]
   */
  public $restoreInstanceClearOverridesFieldNames;
  protected $restoreInstanceSettingsType = DatabaseInstance::class;
  protected $restoreInstanceSettingsDataType = '';

  /**
   * The name of the backup that's used to restore a Cloud SQL instance: Format:
   * projects/{project-id}/backups/{backup-uid}. Only one of
   * restore_backup_context, backup, backupdr_backup can be passed to the input.
   *
   * @param string $backup
   */
  public function setBackup($backup)
  {
    $this->backup = $backup;
  }
  /**
   * @return string
   */
  public function getBackup()
  {
    return $this->backup;
  }
  /**
   * The name of the backup that's used to restore a Cloud SQL instance: Format:
   * "projects/{project-id}/locations/{location}/backupVaults/{backupvault}/data
   * Sources/{datasource}/backups/{backup-uid}". Only one of
   * restore_backup_context, backup, backupdr_backup can be passed to the input.
   *
   * @param string $backupdrBackup
   */
  public function setBackupdrBackup($backupdrBackup)
  {
    $this->backupdrBackup = $backupdrBackup;
  }
  /**
   * @return string
   */
  public function getBackupdrBackup()
  {
    return $this->backupdrBackup;
  }
  /**
   * Parameters required to perform the restore backup operation.
   *
   * @param RestoreBackupContext $restoreBackupContext
   */
  public function setRestoreBackupContext(RestoreBackupContext $restoreBackupContext)
  {
    $this->restoreBackupContext = $restoreBackupContext;
  }
  /**
   * @return RestoreBackupContext
   */
  public function getRestoreBackupContext()
  {
    return $this->restoreBackupContext;
  }
  /**
   * Optional. This field has the same purpose as restore_instance_settings,
   * changes any instance settings stored in the backup you are restoring from.
   * With the difference that these fields are cleared in the settings.
   *
   * @param string[] $restoreInstanceClearOverridesFieldNames
   */
  public function setRestoreInstanceClearOverridesFieldNames($restoreInstanceClearOverridesFieldNames)
  {
    $this->restoreInstanceClearOverridesFieldNames = $restoreInstanceClearOverridesFieldNames;
  }
  /**
   * @return string[]
   */
  public function getRestoreInstanceClearOverridesFieldNames()
  {
    return $this->restoreInstanceClearOverridesFieldNames;
  }
  /**
   * Optional. By using this parameter, Cloud SQL overrides any instance
   * settings stored in the backup you are restoring from. You can't change the
   * instance's major database version and you can only increase the disk size.
   * You can use this field to restore new instances only. This field is not
   * applicable for restore to existing instances.
   *
   * @param DatabaseInstance $restoreInstanceSettings
   */
  public function setRestoreInstanceSettings(DatabaseInstance $restoreInstanceSettings)
  {
    $this->restoreInstanceSettings = $restoreInstanceSettings;
  }
  /**
   * @return DatabaseInstance
   */
  public function getRestoreInstanceSettings()
  {
    return $this->restoreInstanceSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstancesRestoreBackupRequest::class, 'Google_Service_SQLAdmin_InstancesRestoreBackupRequest');
