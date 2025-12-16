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

class VolumeRestore extends \Google\Model
{
  /**
   * This is an illegal state and should not be encountered.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * A volume for the restore was identified and restore process is about to
   * start.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The volume is currently being restored.
   */
  public const STATE_RESTORING = 'RESTORING';
  /**
   * The volume has been successfully restored.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The volume restoration process failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * This VolumeRestore resource is in the process of being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Default
   */
  public const VOLUME_TYPE_VOLUME_TYPE_UNSPECIFIED = 'VOLUME_TYPE_UNSPECIFIED';
  /**
   * Compute Engine Persistent Disk volume
   */
  public const VOLUME_TYPE_GCE_PERSISTENT_DISK = 'GCE_PERSISTENT_DISK';
  /**
   * Output only. The timestamp when the associated underlying volume
   * restoration completed.
   *
   * @var string
   */
  public $completeTime;
  /**
   * Output only. The timestamp when this VolumeRestore resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. `etag` is used for optimistic concurrency control as a way to
   * help prevent simultaneous updates of a volume restore from overwriting each
   * other. It is strongly suggested that systems make use of the `etag` in the
   * read-modify-write cycle to perform volume restore updates in order to avoid
   * race conditions.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. Full name of the VolumeRestore resource. Format:
   * `projects/locations/restorePlans/restores/volumeRestores`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The current state of this VolumeRestore.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. A human readable message explaining why the VolumeRestore is
   * in its current state.
   *
   * @var string
   */
  public $stateMessage;
  protected $targetPvcType = NamespacedName::class;
  protected $targetPvcDataType = '';
  /**
   * Output only. Server generated global unique identifier of
   * [UUID](https://en.wikipedia.org/wiki/Universally_unique_identifier) format.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The timestamp when this VolumeRestore resource was last
   * updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Output only. The full name of the VolumeBackup from which the volume will
   * be restored. Format:
   * `projects/locations/backupPlans/backups/volumeBackups`.
   *
   * @var string
   */
  public $volumeBackup;
  /**
   * Output only. A storage system-specific opaque handler to the underlying
   * volume created for the target PVC from the volume backup.
   *
   * @var string
   */
  public $volumeHandle;
  /**
   * Output only. The type of volume provisioned
   *
   * @var string
   */
  public $volumeType;

  /**
   * Output only. The timestamp when the associated underlying volume
   * restoration completed.
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
   * Output only. The timestamp when this VolumeRestore resource was created.
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
   * Output only. `etag` is used for optimistic concurrency control as a way to
   * help prevent simultaneous updates of a volume restore from overwriting each
   * other. It is strongly suggested that systems make use of the `etag` in the
   * read-modify-write cycle to perform volume restore updates in order to avoid
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
   * Output only. Full name of the VolumeRestore resource. Format:
   * `projects/locations/restorePlans/restores/volumeRestores`
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
   * Output only. The current state of this VolumeRestore.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, RESTORING, SUCCEEDED, FAILED,
   * DELETING
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
   * Output only. A human readable message explaining why the VolumeRestore is
   * in its current state.
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
   * Output only. The reference to the target Kubernetes PVC to be restored.
   *
   * @param NamespacedName $targetPvc
   */
  public function setTargetPvc(NamespacedName $targetPvc)
  {
    $this->targetPvc = $targetPvc;
  }
  /**
   * @return NamespacedName
   */
  public function getTargetPvc()
  {
    return $this->targetPvc;
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
   * Output only. The timestamp when this VolumeRestore resource was last
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
   * Output only. The full name of the VolumeBackup from which the volume will
   * be restored. Format:
   * `projects/locations/backupPlans/backups/volumeBackups`.
   *
   * @param string $volumeBackup
   */
  public function setVolumeBackup($volumeBackup)
  {
    $this->volumeBackup = $volumeBackup;
  }
  /**
   * @return string
   */
  public function getVolumeBackup()
  {
    return $this->volumeBackup;
  }
  /**
   * Output only. A storage system-specific opaque handler to the underlying
   * volume created for the target PVC from the volume backup.
   *
   * @param string $volumeHandle
   */
  public function setVolumeHandle($volumeHandle)
  {
    $this->volumeHandle = $volumeHandle;
  }
  /**
   * @return string
   */
  public function getVolumeHandle()
  {
    return $this->volumeHandle;
  }
  /**
   * Output only. The type of volume provisioned
   *
   * Accepted values: VOLUME_TYPE_UNSPECIFIED, GCE_PERSISTENT_DISK
   *
   * @param self::VOLUME_TYPE_* $volumeType
   */
  public function setVolumeType($volumeType)
  {
    $this->volumeType = $volumeType;
  }
  /**
   * @return self::VOLUME_TYPE_*
   */
  public function getVolumeType()
  {
    return $this->volumeType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VolumeRestore::class, 'Google_Service_BackupforGKE_VolumeRestore');
