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

namespace Google\Service\NetAppFiles;

class Backup extends \Google\Model
{
  /**
   * Unspecified backup type.
   */
  public const BACKUP_TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Manual backup type.
   */
  public const BACKUP_TYPE_MANUAL = 'MANUAL';
  /**
   * Scheduled backup type.
   */
  public const BACKUP_TYPE_SCHEDULED = 'SCHEDULED';
  /**
   * State not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Backup is being created. While in this state, the snapshot for the backup
   * point-in-time may not have been created yet, and so the point-in-time may
   * not have been fixed.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Backup is being uploaded. While in this state, none of the writes to the
   * volume will be included in the backup.
   */
  public const STATE_UPLOADING = 'UPLOADING';
  /**
   * Backup is available for use.
   */
  public const STATE_READY = 'READY';
  /**
   * Backup is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Backup is not valid and cannot be used for creating new volumes or
   * restoring existing volumes.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * Backup is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Output only. Region in which backup is stored. Format:
   * `projects/{project_id}/locations/{location}`
   *
   * @var string
   */
  public $backupRegion;
  /**
   * Output only. Type of backup, manually created or created by a backup
   * policy.
   *
   * @var string
   */
  public $backupType;
  /**
   * Output only. Total size of all backups in a chain in bytes = baseline
   * backup size + sum(incremental backup size)
   *
   * @var string
   */
  public $chainStorageBytes;
  /**
   * Output only. The time when the backup was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * A description of the backup with 2048 characters or less. Requests with
   * longer descriptions will be rejected.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. The time until which the backup is not deletable.
   *
   * @var string
   */
  public $enforcedRetentionEndTime;
  /**
   * Resource labels to represent user provided metadata.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The resource name of the backup. Format: `projects/{project_id}
   * /locations/{location}/backupVaults/{backup_vault_id}/backups/{backup_id}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Reserved for future use
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * If specified, backup will be created from the given snapshot. If not
   * specified, there will be a new snapshot taken to initiate the backup
   * creation. Format: `projects/{project_id}/locations/{location}/volumes/{volu
   * me_id}/snapshots/{snapshot_id}`
   *
   * @var string
   */
  public $sourceSnapshot;
  /**
   * Volume full name of this backup belongs to. Format:
   * `projects/{projects_id}/locations/{location}/volumes/{volume_id}`
   *
   * @var string
   */
  public $sourceVolume;
  /**
   * Output only. The backup state.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Region of the volume from which the backup was created.
   * Format: `projects/{project_id}/locations/{location}`
   *
   * @var string
   */
  public $volumeRegion;
  /**
   * Output only. Size of the file system when the backup was created. When
   * creating a new volume from the backup, the volume capacity will have to be
   * at least as big.
   *
   * @var string
   */
  public $volumeUsageBytes;

  /**
   * Output only. Region in which backup is stored. Format:
   * `projects/{project_id}/locations/{location}`
   *
   * @param string $backupRegion
   */
  public function setBackupRegion($backupRegion)
  {
    $this->backupRegion = $backupRegion;
  }
  /**
   * @return string
   */
  public function getBackupRegion()
  {
    return $this->backupRegion;
  }
  /**
   * Output only. Type of backup, manually created or created by a backup
   * policy.
   *
   * Accepted values: TYPE_UNSPECIFIED, MANUAL, SCHEDULED
   *
   * @param self::BACKUP_TYPE_* $backupType
   */
  public function setBackupType($backupType)
  {
    $this->backupType = $backupType;
  }
  /**
   * @return self::BACKUP_TYPE_*
   */
  public function getBackupType()
  {
    return $this->backupType;
  }
  /**
   * Output only. Total size of all backups in a chain in bytes = baseline
   * backup size + sum(incremental backup size)
   *
   * @param string $chainStorageBytes
   */
  public function setChainStorageBytes($chainStorageBytes)
  {
    $this->chainStorageBytes = $chainStorageBytes;
  }
  /**
   * @return string
   */
  public function getChainStorageBytes()
  {
    return $this->chainStorageBytes;
  }
  /**
   * Output only. The time when the backup was created.
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
   * A description of the backup with 2048 characters or less. Requests with
   * longer descriptions will be rejected.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. The time until which the backup is not deletable.
   *
   * @param string $enforcedRetentionEndTime
   */
  public function setEnforcedRetentionEndTime($enforcedRetentionEndTime)
  {
    $this->enforcedRetentionEndTime = $enforcedRetentionEndTime;
  }
  /**
   * @return string
   */
  public function getEnforcedRetentionEndTime()
  {
    return $this->enforcedRetentionEndTime;
  }
  /**
   * Resource labels to represent user provided metadata.
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
   * Identifier. The resource name of the backup. Format: `projects/{project_id}
   * /locations/{location}/backupVaults/{backup_vault_id}/backups/{backup_id}`.
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
   * Output only. Reserved for future use
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. Reserved for future use
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * If specified, backup will be created from the given snapshot. If not
   * specified, there will be a new snapshot taken to initiate the backup
   * creation. Format: `projects/{project_id}/locations/{location}/volumes/{volu
   * me_id}/snapshots/{snapshot_id}`
   *
   * @param string $sourceSnapshot
   */
  public function setSourceSnapshot($sourceSnapshot)
  {
    $this->sourceSnapshot = $sourceSnapshot;
  }
  /**
   * @return string
   */
  public function getSourceSnapshot()
  {
    return $this->sourceSnapshot;
  }
  /**
   * Volume full name of this backup belongs to. Format:
   * `projects/{projects_id}/locations/{location}/volumes/{volume_id}`
   *
   * @param string $sourceVolume
   */
  public function setSourceVolume($sourceVolume)
  {
    $this->sourceVolume = $sourceVolume;
  }
  /**
   * @return string
   */
  public function getSourceVolume()
  {
    return $this->sourceVolume;
  }
  /**
   * Output only. The backup state.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, UPLOADING, READY, DELETING,
   * ERROR, UPDATING
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
   * Output only. Region of the volume from which the backup was created.
   * Format: `projects/{project_id}/locations/{location}`
   *
   * @param string $volumeRegion
   */
  public function setVolumeRegion($volumeRegion)
  {
    $this->volumeRegion = $volumeRegion;
  }
  /**
   * @return string
   */
  public function getVolumeRegion()
  {
    return $this->volumeRegion;
  }
  /**
   * Output only. Size of the file system when the backup was created. When
   * creating a new volume from the backup, the volume capacity will have to be
   * at least as big.
   *
   * @param string $volumeUsageBytes
   */
  public function setVolumeUsageBytes($volumeUsageBytes)
  {
    $this->volumeUsageBytes = $volumeUsageBytes;
  }
  /**
   * @return string
   */
  public function getVolumeUsageBytes()
  {
    return $this->volumeUsageBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Backup::class, 'Google_Service_NetAppFiles_Backup');
