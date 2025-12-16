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

class BackupVault extends \Google\Model
{
  /**
   * Access restriction not set. If user does not provide any value or pass this
   * value, it will be changed to WITHIN_ORGANIZATION.
   */
  public const ACCESS_RESTRICTION_ACCESS_RESTRICTION_UNSPECIFIED = 'ACCESS_RESTRICTION_UNSPECIFIED';
  /**
   * Access to or from resources outside your current project will be denied.
   */
  public const ACCESS_RESTRICTION_WITHIN_PROJECT = 'WITHIN_PROJECT';
  /**
   * Access to or from resources outside your current organization will be
   * denied.
   */
  public const ACCESS_RESTRICTION_WITHIN_ORGANIZATION = 'WITHIN_ORGANIZATION';
  /**
   * No access restriction.
   */
  public const ACCESS_RESTRICTION_UNRESTRICTED = 'UNRESTRICTED';
  /**
   * Access to or from resources outside your current organization will be
   * denied except for backup appliance.
   */
  public const ACCESS_RESTRICTION_WITHIN_ORG_BUT_UNRESTRICTED_FOR_BA = 'WITHIN_ORG_BUT_UNRESTRICTED_FOR_BA';
  /**
   * Inheritance behavior not set. This will default to
   * `INHERIT_VAULT_RETENTION`.
   */
  public const BACKUP_RETENTION_INHERITANCE_BACKUP_RETENTION_INHERITANCE_UNSPECIFIED = 'BACKUP_RETENTION_INHERITANCE_UNSPECIFIED';
  /**
   * The enforced retention end time of a backup will be inherited from the
   * backup vault's `backup_minimum_enforced_retention_duration` field. This is
   * the default behavior.
   */
  public const BACKUP_RETENTION_INHERITANCE_INHERIT_VAULT_RETENTION = 'INHERIT_VAULT_RETENTION';
  /**
   * The enforced retention end time of a backup will always match the expire
   * time of the backup. If this is set, the backup's enforced retention end
   * time will be set to match the expire time during creation of the backup.
   * When updating, the ERET and expire time must be updated together and have
   * the same value. Invalid update requests will be rejected by the server.
   */
  public const BACKUP_RETENTION_INHERITANCE_MATCH_BACKUP_EXPIRE_TIME = 'MATCH_BACKUP_EXPIRE_TIME';
  /**
   * State not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The backup vault is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The backup vault has been created and is fully usable.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The backup vault is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The backup vault is experiencing an issue and might be unusable.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * The backup vault is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Optional. Note: This field is added for future use case and will not be
   * supported in the current release. Access restriction for the backup vault.
   * Default value is WITHIN_ORGANIZATION if not provided during creation.
   *
   * @var string
   */
  public $accessRestriction;
  /**
   * Optional. User annotations. See https://google.aip.dev/128#annotations
   * Stores small amounts of arbitrary data.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Output only. The number of backups in this backup vault.
   *
   * @var string
   */
  public $backupCount;
  /**
   * Required. The default and minimum enforced retention for each backup within
   * the backup vault. The enforced retention for each backup can be extended.
   * Note: Longer minimum enforced retention period impacts potential storage
   * costs post introductory trial. We recommend starting with a short duration
   * of 3 days or less.
   *
   * @var string
   */
  public $backupMinimumEnforcedRetentionDuration;
  /**
   * Optional. Setting for how a backup's enforced retention end time is
   * inherited.
   *
   * @var string
   */
  public $backupRetentionInheritance;
  /**
   * Output only. The time when the instance was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Set to true when there are no backups nested under this
   * resource.
   *
   * @var bool
   */
  public $deletable;
  /**
   * Optional. The description of the BackupVault instance (2048 characters or
   * less).
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Time after which the BackupVault resource is locked.
   *
   * @var string
   */
  public $effectiveTime;
  protected $encryptionConfigType = EncryptionConfig::class;
  protected $encryptionConfigDataType = '';
  /**
   * Optional. Server specified ETag for the backup vault resource to prevent
   * simultaneous updates from overwiting each other.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. Resource labels to represent user provided metadata. No labels
   * currently defined:
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Identifier. Name of the backup vault to create. It must have
   * the format`"projects/{project}/locations/{location}/backupVaults/{backupvau
   * lt}"`. `{backupvault}` cannot be changed after creation. It must be between
   * 3-63 characters long and must be unique within the project and location.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Service account used by the BackupVault Service for this
   * BackupVault. The user should grant this account permissions in their
   * workload project to enable the service to run backups and restores there.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Output only. The BackupVault resource instance state.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Total size of the storage used by all backup resources.
   *
   * @var string
   */
  public $totalStoredBytes;
  /**
   * Output only. Immutable after resource creation until resource deletion.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time when the instance was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Note: This field is added for future use case and will not be
   * supported in the current release. Access restriction for the backup vault.
   * Default value is WITHIN_ORGANIZATION if not provided during creation.
   *
   * Accepted values: ACCESS_RESTRICTION_UNSPECIFIED, WITHIN_PROJECT,
   * WITHIN_ORGANIZATION, UNRESTRICTED, WITHIN_ORG_BUT_UNRESTRICTED_FOR_BA
   *
   * @param self::ACCESS_RESTRICTION_* $accessRestriction
   */
  public function setAccessRestriction($accessRestriction)
  {
    $this->accessRestriction = $accessRestriction;
  }
  /**
   * @return self::ACCESS_RESTRICTION_*
   */
  public function getAccessRestriction()
  {
    return $this->accessRestriction;
  }
  /**
   * Optional. User annotations. See https://google.aip.dev/128#annotations
   * Stores small amounts of arbitrary data.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Output only. The number of backups in this backup vault.
   *
   * @param string $backupCount
   */
  public function setBackupCount($backupCount)
  {
    $this->backupCount = $backupCount;
  }
  /**
   * @return string
   */
  public function getBackupCount()
  {
    return $this->backupCount;
  }
  /**
   * Required. The default and minimum enforced retention for each backup within
   * the backup vault. The enforced retention for each backup can be extended.
   * Note: Longer minimum enforced retention period impacts potential storage
   * costs post introductory trial. We recommend starting with a short duration
   * of 3 days or less.
   *
   * @param string $backupMinimumEnforcedRetentionDuration
   */
  public function setBackupMinimumEnforcedRetentionDuration($backupMinimumEnforcedRetentionDuration)
  {
    $this->backupMinimumEnforcedRetentionDuration = $backupMinimumEnforcedRetentionDuration;
  }
  /**
   * @return string
   */
  public function getBackupMinimumEnforcedRetentionDuration()
  {
    return $this->backupMinimumEnforcedRetentionDuration;
  }
  /**
   * Optional. Setting for how a backup's enforced retention end time is
   * inherited.
   *
   * Accepted values: BACKUP_RETENTION_INHERITANCE_UNSPECIFIED,
   * INHERIT_VAULT_RETENTION, MATCH_BACKUP_EXPIRE_TIME
   *
   * @param self::BACKUP_RETENTION_INHERITANCE_* $backupRetentionInheritance
   */
  public function setBackupRetentionInheritance($backupRetentionInheritance)
  {
    $this->backupRetentionInheritance = $backupRetentionInheritance;
  }
  /**
   * @return self::BACKUP_RETENTION_INHERITANCE_*
   */
  public function getBackupRetentionInheritance()
  {
    return $this->backupRetentionInheritance;
  }
  /**
   * Output only. The time when the instance was created.
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
   * Output only. Set to true when there are no backups nested under this
   * resource.
   *
   * @param bool $deletable
   */
  public function setDeletable($deletable)
  {
    $this->deletable = $deletable;
  }
  /**
   * @return bool
   */
  public function getDeletable()
  {
    return $this->deletable;
  }
  /**
   * Optional. The description of the BackupVault instance (2048 characters or
   * less).
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
   * Optional. Time after which the BackupVault resource is locked.
   *
   * @param string $effectiveTime
   */
  public function setEffectiveTime($effectiveTime)
  {
    $this->effectiveTime = $effectiveTime;
  }
  /**
   * @return string
   */
  public function getEffectiveTime()
  {
    return $this->effectiveTime;
  }
  /**
   * Optional. The encryption config of the backup vault.
   *
   * @param EncryptionConfig $encryptionConfig
   */
  public function setEncryptionConfig(EncryptionConfig $encryptionConfig)
  {
    $this->encryptionConfig = $encryptionConfig;
  }
  /**
   * @return EncryptionConfig
   */
  public function getEncryptionConfig()
  {
    return $this->encryptionConfig;
  }
  /**
   * Optional. Server specified ETag for the backup vault resource to prevent
   * simultaneous updates from overwiting each other.
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
   * Optional. Resource labels to represent user provided metadata. No labels
   * currently defined:
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
   * Output only. Identifier. Name of the backup vault to create. It must have
   * the format`"projects/{project}/locations/{location}/backupVaults/{backupvau
   * lt}"`. `{backupvault}` cannot be changed after creation. It must be between
   * 3-63 characters long and must be unique within the project and location.
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
   * Output only. Service account used by the BackupVault Service for this
   * BackupVault. The user should grant this account permissions in their
   * workload project to enable the service to run backups and restores there.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Output only. The BackupVault resource instance state.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING, ERROR,
   * UPDATING
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
   * Output only. Total size of the storage used by all backup resources.
   *
   * @param string $totalStoredBytes
   */
  public function setTotalStoredBytes($totalStoredBytes)
  {
    $this->totalStoredBytes = $totalStoredBytes;
  }
  /**
   * @return string
   */
  public function getTotalStoredBytes()
  {
    return $this->totalStoredBytes;
  }
  /**
   * Output only. Immutable after resource creation until resource deletion.
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
   * Output only. The time when the instance was updated.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupVault::class, 'Google_Service_Backupdr_BackupVault');
