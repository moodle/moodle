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

namespace Google\Service\BackupforGKE;

class VolumeBackup extends \Google\Model
{
  /**
   * Default value, not specified.
   */
  public const FORMAT_VOLUME_BACKUP_FORMAT_UNSPECIFIED = 'VOLUME_BACKUP_FORMAT_UNSPECIFIED';
  /**
   * Compute Engine Persistent Disk snapshot based volume backup.
   */
  public const FORMAT_GCE_PERSISTENT_DISK = 'GCE_PERSISTENT_DISK';
  /**
   * This is an illegal state and should not be encountered.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * A volume for the backup was identified and backup process is about to
   * start.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The volume backup operation has begun and is in the initial "snapshot"
   * phase of the process. Any defined ProtectedApplication "pre" hooks will be
   * executed before entering this state and "post" hooks will be executed upon
   * leaving this state.
   */
  public const STATE_SNAPSHOTTING = 'SNAPSHOTTING';
  /**
   * The snapshot phase of the volume backup operation has completed and the
   * snapshot is now being uploaded to backup storage.
   */
  public const STATE_UPLOADING = 'UPLOADING';
  /**
   * The volume backup operation has completed successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The volume backup operation has failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * This VolumeBackup resource (and its associated artifacts) is in the process
   * of being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The underlying artifacts of a volume backup (eg: persistent disk snapshots)
   * are deleted.
   */
  public const STATE_CLEANED_UP = 'CLEANED_UP';
  /**
   * Output only. The timestamp when the associated underlying volume backup
   * operation completed.
   *
   * @var string
   */
  public $completeTime;
  /**
   * Output only. The timestamp when this VolumeBackup resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The minimum size of the disk to which this VolumeBackup can be
   * restored.
   *
   * @var string
   */
  public $diskSizeBytes;
  /**
   * Output only. `etag` is used for optimistic concurrency control as a way to
   * help prevent simultaneous updates of a volume backup from overwriting each
   * other. It is strongly suggested that systems make use of the `etag` in the
   * read-modify-write cycle to perform volume backup updates in order to avoid
   * race conditions.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. The format used for the volume backup.
   *
   * @var string
   */
  public $format;
  /**
   * Output only. The full name of the VolumeBackup resource. Format:
   * `projects/locations/backupPlans/backups/volumeBackups`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. [Output Only] Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. [Output Only] Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  protected $sourcePvcType = NamespacedName::class;
  protected $sourcePvcDataType = '';
  /**
   * Output only. The current state of this VolumeBackup.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. A human readable message explaining why the VolumeBackup is in
   * its current state. This field is only meant for human consumption and
   * should not be used programmatically as this field is not guaranteed to be
   * consistent.
   *
   * @var string
   */
  public $stateMessage;
  /**
   * Output only. The aggregate size of the underlying artifacts associated with
   * this VolumeBackup in the backup storage. This may change over time when
   * multiple backups of the same volume share the same backup storage location.
   * In particular, this is likely to increase in size when the immediately
   * preceding backup of the same volume is deleted.
   *
   * @var string
   */
  public $storageBytes;
  /**
   * Output only. Server generated global unique identifier of
   * [UUID](https://en.wikipedia.org/wiki/Universally_unique_identifier) format.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The timestamp when this VolumeBackup resource was last
   * updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Output only. A storage system-specific opaque handle to the underlying
   * volume backup.
   *
   * @var string
   */
  public $volumeBackupHandle;

  /**
   * Output only. The timestamp when the associated underlying volume backup
   * operation completed.
   *
   * @param string $completeTime
   */
  public function setCompleteTime($completeTime)
  {
    $this->completeTime = $completeTime;
  }
  /**
   * @return string
   */
  public function getCompleteTime()
  {
    return $this->completeTime;
  }
  /**
   * Output only. The timestamp when this VolumeBackup resource was created.
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
   * Output only. The minimum size of the disk to which this VolumeBackup can be
   * restored.
   *
   * @param string $diskSizeBytes
   */
  public function setDiskSizeBytes($diskSizeBytes)
  {
    $this->diskSizeBytes = $diskSizeBytes;
  }
  /**
   * @return string
   */
  public function getDiskSizeBytes()
  {
    return $this->diskSizeBytes;
  }
  /**
   * Output only. `etag` is used for optimistic concurrency control as a way to
   * help prevent simultaneous updates of a volume backup from overwriting each
   * other. It is strongly suggested that systems make use of the `etag` in the
   * read-modify-write cycle to perform volume backup updates in order to avoid
   * race conditions.
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
   * Output only. The format used for the volume backup.
   *
   * Accepted values: VOLUME_BACKUP_FORMAT_UNSPECIFIED, GCE_PERSISTENT_DISK
   *
   * @param self::FORMAT_* $format
   */
  public function setFormat($format)
  {
    $this->format = $format;
  }
  /**
   * @return self::FORMAT_*
   */
  public function getFormat()
  {
    return $this->format;
  }
  /**
   * Output only. The full name of the VolumeBackup resource. Format:
   * `projects/locations/backupPlans/backups/volumeBackups`.
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
   * Output only. [Output Only] Reserved for future use.
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
   * Output only. [Output Only] Reserved for future use.
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
   * Output only. A reference to the source Kubernetes PVC from which this
   * VolumeBackup was created.
   *
   * @param NamespacedName $sourcePvc
   */
  public function setSourcePvc(NamespacedName $sourcePvc)
  {
    $this->sourcePvc = $sourcePvc;
  }
  /**
   * @return NamespacedName
   */
  public function getSourcePvc()
  {
    return $this->sourcePvc;
  }
  /**
   * Output only. The current state of this VolumeBackup.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, SNAPSHOTTING, UPLOADING,
   * SUCCEEDED, FAILED, DELETING, CLEANED_UP
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
   * Output only. A human readable message explaining why the VolumeBackup is in
   * its current state. This field is only meant for human consumption and
   * should not be used programmatically as this field is not guaranteed to be
   * consistent.
   *
   * @param string $stateMessage
   */
  public function setStateMessage($stateMessage)
  {
    $this->stateMessage = $stateMessage;
  }
  /**
   * @return string
   */
  public function getStateMessage()
  {
    return $this->stateMessage;
  }
  /**
   * Output only. The aggregate size of the underlying artifacts associated with
   * this VolumeBackup in the backup storage. This may change over time when
   * multiple backups of the same volume share the same backup storage location.
   * In particular, this is likely to increase in size when the immediately
   * preceding backup of the same volume is deleted.
   *
   * @param string $storageBytes
   */
  public function setStorageBytes($storageBytes)
  {
    $this->storageBytes = $storageBytes;
  }
  /**
   * @return string
   */
  public function getStorageBytes()
  {
    return $this->storageBytes;
  }
  /**
   * Output only. Server generated global unique identifier of
   * [UUID](https://en.wikipedia.org/wiki/Universally_unique_identifier) format.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. The timestamp when this VolumeBackup resource was last
   * updated.
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
  /**
   * Output only. A storage system-specific opaque handle to the underlying
   * volume backup.
   *
   * @param string $volumeBackupHandle
   */
  public function setVolumeBackupHandle($volumeBackupHandle)
  {
    $this->volumeBackupHandle = $volumeBackupHandle;
  }
  /**
   * @return string
   */
  public function getVolumeBackupHandle()
  {
    return $this->volumeBackupHandle;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VolumeBackup::class, 'Google_Service_BackupforGKE_VolumeBackup');
