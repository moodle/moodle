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

class BackupApplianceLockInfo extends \Google\Model
{
  /**
   * Required. The ID of the backup/recovery appliance that created this lock.
   *
   * @var string
   */
  public $backupApplianceId;
  /**
   * Required. The name of the backup/recovery appliance that created this lock.
   *
   * @var string
   */
  public $backupApplianceName;
  /**
   * The image name that depends on this Backup.
   *
   * @var string
   */
  public $backupImage;
  /**
   * The job name on the backup/recovery appliance that created this lock.
   *
   * @var string
   */
  public $jobName;
  /**
   * Required. The reason for the lock: e.g. MOUNT/RESTORE/BACKUP/etc. The value
   * of this string is only meaningful to the client and it is not interpreted
   * by the BackupVault service.
   *
   * @var string
   */
  public $lockReason;
  /**
   * The SLA on the backup/recovery appliance that owns the lock.
   *
   * @var string
   */
  public $slaId;

  /**
   * Required. The ID of the backup/recovery appliance that created this lock.
   *
   * @param string $backupApplianceId
   */
  public function setBackupApplianceId($backupApplianceId)
  {
    $this->backupApplianceId = $backupApplianceId;
  }
  /**
   * @return string
   */
  public function getBackupApplianceId()
  {
    return $this->backupApplianceId;
  }
  /**
   * Required. The name of the backup/recovery appliance that created this lock.
   *
   * @param string $backupApplianceName
   */
  public function setBackupApplianceName($backupApplianceName)
  {
    $this->backupApplianceName = $backupApplianceName;
  }
  /**
   * @return string
   */
  public function getBackupApplianceName()
  {
    return $this->backupApplianceName;
  }
  /**
   * The image name that depends on this Backup.
   *
   * @param string $backupImage
   */
  public function setBackupImage($backupImage)
  {
    $this->backupImage = $backupImage;
  }
  /**
   * @return string
   */
  public function getBackupImage()
  {
    return $this->backupImage;
  }
  /**
   * The job name on the backup/recovery appliance that created this lock.
   *
   * @param string $jobName
   */
  public function setJobName($jobName)
  {
    $this->jobName = $jobName;
  }
  /**
   * @return string
   */
  public function getJobName()
  {
    return $this->jobName;
  }
  /**
   * Required. The reason for the lock: e.g. MOUNT/RESTORE/BACKUP/etc. The value
   * of this string is only meaningful to the client and it is not interpreted
   * by the BackupVault service.
   *
   * @param string $lockReason
   */
  public function setLockReason($lockReason)
  {
    $this->lockReason = $lockReason;
  }
  /**
   * @return string
   */
  public function getLockReason()
  {
    return $this->lockReason;
  }
  /**
   * The SLA on the backup/recovery appliance that owns the lock.
   *
   * @param string $slaId
   */
  public function setSlaId($slaId)
  {
    $this->slaId = $slaId;
  }
  /**
   * @return string
   */
  public function getSlaId()
  {
    return $this->slaId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupApplianceLockInfo::class, 'Google_Service_Backupdr_BackupApplianceLockInfo');
