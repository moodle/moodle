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

class BackupVault extends \Google\Model
{
  /**
   * BackupVault type not set.
   */
  public const BACKUP_VAULT_TYPE_BACKUP_VAULT_TYPE_UNSPECIFIED = 'BACKUP_VAULT_TYPE_UNSPECIFIED';
  /**
   * BackupVault type is IN_REGION.
   */
  public const BACKUP_VAULT_TYPE_IN_REGION = 'IN_REGION';
  /**
   * BackupVault type is CROSS_REGION.
   */
  public const BACKUP_VAULT_TYPE_CROSS_REGION = 'CROSS_REGION';
  /**
   * Encryption state not set.
   */
  public const ENCRYPTION_STATE_ENCRYPTION_STATE_UNSPECIFIED = 'ENCRYPTION_STATE_UNSPECIFIED';
  /**
   * Encryption state is pending.
   */
  public const ENCRYPTION_STATE_ENCRYPTION_STATE_PENDING = 'ENCRYPTION_STATE_PENDING';
  /**
   * Encryption is complete.
   */
  public const ENCRYPTION_STATE_ENCRYPTION_STATE_COMPLETED = 'ENCRYPTION_STATE_COMPLETED';
  /**
   * Encryption is in progress.
   */
  public const ENCRYPTION_STATE_ENCRYPTION_STATE_IN_PROGRESS = 'ENCRYPTION_STATE_IN_PROGRESS';
  /**
   * Encryption has failed.
   */
  public const ENCRYPTION_STATE_ENCRYPTION_STATE_FAILED = 'ENCRYPTION_STATE_FAILED';
  /**
   * State not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * BackupVault is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * BackupVault is available for use.
   */
  public const STATE_READY = 'READY';
  /**
   * BackupVault is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * BackupVault is not valid and cannot be used.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * BackupVault is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Optional. Region where the backups are stored. Format:
   * `projects/{project_id}/locations/{location}`
   *
   * @var string
   */
  public $backupRegion;
  protected $backupRetentionPolicyType = BackupRetentionPolicy::class;
  protected $backupRetentionPolicyDataType = '';
  /**
   * Optional. Type of backup vault to be created. Default is IN_REGION.
   *
   * @var string
   */
  public $backupVaultType;
  /**
   * Output only. The crypto key version used to encrypt the backup vault.
   * Format: projects/{project}/locations/{location}/keyRings/{key_ring}/cryptoK
   * eys/{crypto_key}/cryptoKeyVersions/{crypto_key_version}
   *
   * @var string
   */
  public $backupsCryptoKeyVersion;
  /**
   * Output only. Create time of the backup vault.
   *
   * @var string
   */
  public $createTime;
  /**
   * Description of the backup vault.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. Name of the Backup vault created in backup region. Format:
   * `projects/{project_id}/locations/{location}/backupVaults/{backup_vault_id}`
   *
   * @var string
   */
  public $destinationBackupVault;
  /**
   * Output only. Field indicating encryption state of CMEK backups.
   *
   * @var string
   */
  public $encryptionState;
  /**
   * Optional. Specifies the KMS config to be used for backup encryption.
   * Format: projects/{project}/locations/{location}/kmsConfigs/{kms_config}
   *
   * @var string
   */
  public $kmsConfig;
  /**
   * Resource labels to represent user provided metadata.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The resource name of the backup vault. Format: `projects/{proje
   * ct_id}/locations/{location}/backupVaults/{backup_vault_id}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Name of the Backup vault created in source region. Format:
   * `projects/{project_id}/locations/{location}/backupVaults/{backup_vault_id}`
   *
   * @var string
   */
  public $sourceBackupVault;
  /**
   * Output only. Region in which the backup vault is created. Format:
   * `projects/{project_id}/locations/{location}`
   *
   * @var string
   */
  public $sourceRegion;
  /**
   * Output only. The backup vault state.
   *
   * @var string
   */
  public $state;

  /**
   * Optional. Region where the backups are stored. Format:
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
   * Optional. Backup retention policy defining the retenton of backups.
   *
   * @param BackupRetentionPolicy $backupRetentionPolicy
   */
  public function setBackupRetentionPolicy(BackupRetentionPolicy $backupRetentionPolicy)
  {
    $this->backupRetentionPolicy = $backupRetentionPolicy;
  }
  /**
   * @return BackupRetentionPolicy
   */
  public function getBackupRetentionPolicy()
  {
    return $this->backupRetentionPolicy;
  }
  /**
   * Optional. Type of backup vault to be created. Default is IN_REGION.
   *
   * Accepted values: BACKUP_VAULT_TYPE_UNSPECIFIED, IN_REGION, CROSS_REGION
   *
   * @param self::BACKUP_VAULT_TYPE_* $backupVaultType
   */
  public function setBackupVaultType($backupVaultType)
  {
    $this->backupVaultType = $backupVaultType;
  }
  /**
   * @return self::BACKUP_VAULT_TYPE_*
   */
  public function getBackupVaultType()
  {
    return $this->backupVaultType;
  }
  /**
   * Output only. The crypto key version used to encrypt the backup vault.
   * Format: projects/{project}/locations/{location}/keyRings/{key_ring}/cryptoK
   * eys/{crypto_key}/cryptoKeyVersions/{crypto_key_version}
   *
   * @param string $backupsCryptoKeyVersion
   */
  public function setBackupsCryptoKeyVersion($backupsCryptoKeyVersion)
  {
    $this->backupsCryptoKeyVersion = $backupsCryptoKeyVersion;
  }
  /**
   * @return string
   */
  public function getBackupsCryptoKeyVersion()
  {
    return $this->backupsCryptoKeyVersion;
  }
  /**
   * Output only. Create time of the backup vault.
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
   * Description of the backup vault.
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
   * Output only. Name of the Backup vault created in backup region. Format:
   * `projects/{project_id}/locations/{location}/backupVaults/{backup_vault_id}`
   *
   * @param string $destinationBackupVault
   */
  public function setDestinationBackupVault($destinationBackupVault)
  {
    $this->destinationBackupVault = $destinationBackupVault;
  }
  /**
   * @return string
   */
  public function getDestinationBackupVault()
  {
    return $this->destinationBackupVault;
  }
  /**
   * Output only. Field indicating encryption state of CMEK backups.
   *
   * Accepted values: ENCRYPTION_STATE_UNSPECIFIED, ENCRYPTION_STATE_PENDING,
   * ENCRYPTION_STATE_COMPLETED, ENCRYPTION_STATE_IN_PROGRESS,
   * ENCRYPTION_STATE_FAILED
   *
   * @param self::ENCRYPTION_STATE_* $encryptionState
   */
  public function setEncryptionState($encryptionState)
  {
    $this->encryptionState = $encryptionState;
  }
  /**
   * @return self::ENCRYPTION_STATE_*
   */
  public function getEncryptionState()
  {
    return $this->encryptionState;
  }
  /**
   * Optional. Specifies the KMS config to be used for backup encryption.
   * Format: projects/{project}/locations/{location}/kmsConfigs/{kms_config}
   *
   * @param string $kmsConfig
   */
  public function setKmsConfig($kmsConfig)
  {
    $this->kmsConfig = $kmsConfig;
  }
  /**
   * @return string
   */
  public function getKmsConfig()
  {
    return $this->kmsConfig;
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
   * Identifier. The resource name of the backup vault. Format: `projects/{proje
   * ct_id}/locations/{location}/backupVaults/{backup_vault_id}`.
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
   * Output only. Name of the Backup vault created in source region. Format:
   * `projects/{project_id}/locations/{location}/backupVaults/{backup_vault_id}`
   *
   * @param string $sourceBackupVault
   */
  public function setSourceBackupVault($sourceBackupVault)
  {
    $this->sourceBackupVault = $sourceBackupVault;
  }
  /**
   * @return string
   */
  public function getSourceBackupVault()
  {
    return $this->sourceBackupVault;
  }
  /**
   * Output only. Region in which the backup vault is created. Format:
   * `projects/{project_id}/locations/{location}`
   *
   * @param string $sourceRegion
   */
  public function setSourceRegion($sourceRegion)
  {
    $this->sourceRegion = $sourceRegion;
  }
  /**
   * @return string
   */
  public function getSourceRegion()
  {
    return $this->sourceRegion;
  }
  /**
   * Output only. The backup vault state.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, READY, DELETING, ERROR,
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupVault::class, 'Google_Service_NetAppFiles_BackupVault');
