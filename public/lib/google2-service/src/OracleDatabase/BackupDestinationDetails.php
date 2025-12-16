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

namespace Google\Service\OracleDatabase;

class BackupDestinationDetails extends \Google\Model
{
  /**
   * Default unspecified value.
   */
  public const TYPE_BACKUP_DESTINATION_TYPE_UNSPECIFIED = 'BACKUP_DESTINATION_TYPE_UNSPECIFIED';
  /**
   * Backup destination type is NFS.
   */
  public const TYPE_NFS = 'NFS';
  /**
   * Backup destination type is Recovery Appliance.
   */
  public const TYPE_RECOVERY_APPLIANCE = 'RECOVERY_APPLIANCE';
  /**
   * Backup destination type is Object Store.
   */
  public const TYPE_OBJECT_STORE = 'OBJECT_STORE';
  /**
   * Backup destination type is Local.
   */
  public const TYPE_LOCAL = 'LOCAL';
  /**
   * Backup destination type is DBRS.
   */
  public const TYPE_DBRS = 'DBRS';
  /**
   * Optional. The type of the database backup destination.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. The type of the database backup destination.
   *
   * Accepted values: BACKUP_DESTINATION_TYPE_UNSPECIFIED, NFS,
   * RECOVERY_APPLIANCE, OBJECT_STORE, LOCAL, DBRS
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupDestinationDetails::class, 'Google_Service_OracleDatabase_BackupDestinationDetails');
