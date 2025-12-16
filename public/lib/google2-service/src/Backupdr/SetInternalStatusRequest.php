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

class SetInternalStatusRequest extends \Google\Model
{
  /**
   * The possible states of backup configuration. Status not set.
   */
  public const BACKUP_CONFIG_STATE_BACKUP_CONFIG_STATE_UNSPECIFIED = 'BACKUP_CONFIG_STATE_UNSPECIFIED';
  /**
   * The data source is actively protected (i.e. there is a
   * BackupPlanAssociation or Appliance SLA pointing to it)
   */
  public const BACKUP_CONFIG_STATE_ACTIVE = 'ACTIVE';
  /**
   * The data source is no longer protected (but may have backups under it)
   */
  public const BACKUP_CONFIG_STATE_PASSIVE = 'PASSIVE';
  /**
   * Required. Output only. The new BackupConfigState to set for the DataSource.
   *
   * @var string
   */
  public $backupConfigState;
  /**
   * Optional. An optional request ID to identify requests. Specify a unique
   * request ID so that if you must retry your request, the server will know to
   * ignore the request if it has already been completed. The server will
   * guarantee that for at least 60 minutes after the first request. The request
   * ID must be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   *
   * @var string
   */
  public $requestId;
  /**
   * Required. The value required for this method to work. This field must be
   * the 32-byte SHA256 hash of the DataSourceID. The DataSourceID used here is
   * only the final piece of the fully qualified resource path for this
   * DataSource (i.e. the part after '.../dataSources/'). This field exists to
   * make this method difficult to call since it is intended for use only by
   * Backup Appliances.
   *
   * @var string
   */
  public $value;

  /**
   * Required. Output only. The new BackupConfigState to set for the DataSource.
   *
   * Accepted values: BACKUP_CONFIG_STATE_UNSPECIFIED, ACTIVE, PASSIVE
   *
   * @param self::BACKUP_CONFIG_STATE_* $backupConfigState
   */
  public function setBackupConfigState($backupConfigState)
  {
    $this->backupConfigState = $backupConfigState;
  }
  /**
   * @return self::BACKUP_CONFIG_STATE_*
   */
  public function getBackupConfigState()
  {
    return $this->backupConfigState;
  }
  /**
   * Optional. An optional request ID to identify requests. Specify a unique
   * request ID so that if you must retry your request, the server will know to
   * ignore the request if it has already been completed. The server will
   * guarantee that for at least 60 minutes after the first request. The request
   * ID must be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
  /**
   * Required. The value required for this method to work. This field must be
   * the 32-byte SHA256 hash of the DataSourceID. The DataSourceID used here is
   * only the final piece of the fully qualified resource path for this
   * DataSource (i.e. the part after '.../dataSources/'). This field exists to
   * make this method difficult to call since it is intended for use only by
   * Backup Appliances.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SetInternalStatusRequest::class, 'Google_Service_Backupdr_SetInternalStatusRequest');
