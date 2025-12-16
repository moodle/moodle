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

namespace Google\Service\BigtableAdmin;

class CopyBackupRequest extends \Google\Model
{
  /**
   * Required. The id of the new backup. The `backup_id` along with `parent` are
   * combined as {parent}/backups/{backup_id} to create the full backup name, of
   * the form: `projects/{project}/instances/{instance}/clusters/{cluster}/backu
   * ps/{backup_id}`. This string must be between 1 and 50 characters in length
   * and match the regex _a-zA-Z0-9*.
   *
   * @var string
   */
  public $backupId;
  /**
   * Required. Required. The expiration time of the copied backup with
   * microsecond granularity that must be at least 6 hours and at most 30 days
   * from the time the request is received. Once the `expire_time` has passed,
   * Cloud Bigtable will delete the backup and free the resources used by the
   * backup.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Required. The source backup to be copied from. The source backup needs to
   * be in READY state for it to be copied. Copying a copied backup is not
   * allowed. Once CopyBackup is in progress, the source backup cannot be
   * deleted or cleaned up on expiration until CopyBackup is finished. Values
   * are of the form: `projects//instances//clusters//backups/`.
   *
   * @var string
   */
  public $sourceBackup;

  /**
   * Required. The id of the new backup. The `backup_id` along with `parent` are
   * combined as {parent}/backups/{backup_id} to create the full backup name, of
   * the form: `projects/{project}/instances/{instance}/clusters/{cluster}/backu
   * ps/{backup_id}`. This string must be between 1 and 50 characters in length
   * and match the regex _a-zA-Z0-9*.
   *
   * @param string $backupId
   */
  public function setBackupId($backupId)
  {
    $this->backupId = $backupId;
  }
  /**
   * @return string
   */
  public function getBackupId()
  {
    return $this->backupId;
  }
  /**
   * Required. Required. The expiration time of the copied backup with
   * microsecond granularity that must be at least 6 hours and at most 30 days
   * from the time the request is received. Once the `expire_time` has passed,
   * Cloud Bigtable will delete the backup and free the resources used by the
   * backup.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Required. The source backup to be copied from. The source backup needs to
   * be in READY state for it to be copied. Copying a copied backup is not
   * allowed. Once CopyBackup is in progress, the source backup cannot be
   * deleted or cleaned up on expiration until CopyBackup is finished. Values
   * are of the form: `projects//instances//clusters//backups/`.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CopyBackupRequest::class, 'Google_Service_BigtableAdmin_CopyBackupRequest');
