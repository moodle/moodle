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

namespace Google\Service\AndroidManagement;

class BackupServiceToggledEvent extends \Google\Model
{
  /**
   * No value is set
   */
  public const BACKUP_SERVICE_STATE_BACKUP_SERVICE_STATE_UNSPECIFIED = 'BACKUP_SERVICE_STATE_UNSPECIFIED';
  /**
   * Backup service is enabled
   */
  public const BACKUP_SERVICE_STATE_BACKUP_SERVICE_DISABLED = 'BACKUP_SERVICE_DISABLED';
  /**
   * Backup service is disabled
   */
  public const BACKUP_SERVICE_STATE_BACKUP_SERVICE_ENABLED = 'BACKUP_SERVICE_ENABLED';
  /**
   * Package name of the admin app requesting the change.
   *
   * @var string
   */
  public $adminPackageName;
  /**
   * User ID of the admin app from the which the change was requested.
   *
   * @var int
   */
  public $adminUserId;
  /**
   * Whether the backup service is enabled
   *
   * @var string
   */
  public $backupServiceState;

  /**
   * Package name of the admin app requesting the change.
   *
   * @param string $adminPackageName
   */
  public function setAdminPackageName($adminPackageName)
  {
    $this->adminPackageName = $adminPackageName;
  }
  /**
   * @return string
   */
  public function getAdminPackageName()
  {
    return $this->adminPackageName;
  }
  /**
   * User ID of the admin app from the which the change was requested.
   *
   * @param int $adminUserId
   */
  public function setAdminUserId($adminUserId)
  {
    $this->adminUserId = $adminUserId;
  }
  /**
   * @return int
   */
  public function getAdminUserId()
  {
    return $this->adminUserId;
  }
  /**
   * Whether the backup service is enabled
   *
   * Accepted values: BACKUP_SERVICE_STATE_UNSPECIFIED, BACKUP_SERVICE_DISABLED,
   * BACKUP_SERVICE_ENABLED
   *
   * @param self::BACKUP_SERVICE_STATE_* $backupServiceState
   */
  public function setBackupServiceState($backupServiceState)
  {
    $this->backupServiceState = $backupServiceState;
  }
  /**
   * @return self::BACKUP_SERVICE_STATE_*
   */
  public function getBackupServiceState()
  {
    return $this->backupServiceState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupServiceToggledEvent::class, 'Google_Service_AndroidManagement_BackupServiceToggledEvent');
