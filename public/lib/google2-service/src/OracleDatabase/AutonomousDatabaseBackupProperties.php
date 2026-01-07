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

class AutonomousDatabaseBackupProperties extends \Google\Model
{
  /**
   * Default unspecified value.
   */
  public const LIFECYCLE_STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Indicates that the resource is in creating state.
   */
  public const LIFECYCLE_STATE_CREATING = 'CREATING';
  /**
   * Indicates that the resource is in active state.
   */
  public const LIFECYCLE_STATE_ACTIVE = 'ACTIVE';
  /**
   * Indicates that the resource is in deleting state.
   */
  public const LIFECYCLE_STATE_DELETING = 'DELETING';
  /**
   * Indicates that the resource is in deleted state.
   */
  public const LIFECYCLE_STATE_DELETED = 'DELETED';
  /**
   * Indicates that the resource is in failed state.
   */
  public const LIFECYCLE_STATE_FAILED = 'FAILED';
  /**
   * Indicates that the resource is in updating state.
   */
  public const LIFECYCLE_STATE_UPDATING = 'UPDATING';
  /**
   * Default unspecified value.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Incremental backups.
   */
  public const TYPE_INCREMENTAL = 'INCREMENTAL';
  /**
   * Full backups.
   */
  public const TYPE_FULL = 'FULL';
  /**
   * Long term backups.
   */
  public const TYPE_LONG_TERM = 'LONG_TERM';
  /**
   * Output only. Timestamp until when the backup will be available.
   *
   * @var string
   */
  public $availableTillTime;
  /**
   * Output only. The OCID of the compartment.
   *
   * @var string
   */
  public $compartmentId;
  /**
   * Output only. The quantity of data in the database, in terabytes.
   *
   * @var float
   */
  public $databaseSizeTb;
  /**
   * Output only. A valid Oracle Database version for Autonomous Database.
   *
   * @var string
   */
  public $dbVersion;
  /**
   * Output only. The date and time the backup completed.
   *
   * @var string
   */
  public $endTime;
  /**
   * Output only. Indicates if the backup is automatic or user initiated.
   *
   * @var bool
   */
  public $isAutomaticBackup;
  /**
   * Output only. Indicates if the backup is long term backup.
   *
   * @var bool
   */
  public $isLongTermBackup;
  /**
   * Output only. Indicates if the backup can be used to restore the Autonomous
   * Database.
   *
   * @var bool
   */
  public $isRestorable;
  /**
   * Optional. The OCID of the key store of Oracle Vault.
   *
   * @var string
   */
  public $keyStoreId;
  /**
   * Optional. The wallet name for Oracle Key Vault.
   *
   * @var string
   */
  public $keyStoreWallet;
  /**
   * Optional. The OCID of the key container that is used as the master
   * encryption key in database transparent data encryption (TDE) operations.
   *
   * @var string
   */
  public $kmsKeyId;
  /**
   * Optional. The OCID of the key container version that is used in database
   * transparent data encryption (TDE) operations KMS Key can have multiple key
   * versions. If none is specified, the current key version (latest) of the Key
   * Id is used for the operation. Autonomous Database Serverless does not use
   * key versions, hence is not applicable for Autonomous Database Serverless
   * instances.
   *
   * @var string
   */
  public $kmsKeyVersionId;
  /**
   * Output only. Additional information about the current lifecycle state.
   *
   * @var string
   */
  public $lifecycleDetails;
  /**
   * Output only. The lifecycle state of the backup.
   *
   * @var string
   */
  public $lifecycleState;
  /**
   * Output only. OCID of the Autonomous Database backup.
   * https://docs.oracle.com/en-
   * us/iaas/Content/General/Concepts/identifiers.htm#Oracle
   *
   * @var string
   */
  public $ocid;
  /**
   * Optional. Retention period in days for the backup.
   *
   * @var int
   */
  public $retentionPeriodDays;
  /**
   * Output only. The backup size in terabytes.
   *
   * @var float
   */
  public $sizeTb;
  /**
   * Output only. The date and time the backup started.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. The type of the backup.
   *
   * @var string
   */
  public $type;
  /**
   * Optional. The OCID of the vault.
   *
   * @var string
   */
  public $vaultId;

  /**
   * Output only. Timestamp until when the backup will be available.
   *
   * @param string $availableTillTime
   */
  public function setAvailableTillTime($availableTillTime)
  {
    $this->availableTillTime = $availableTillTime;
  }
  /**
   * @return string
   */
  public function getAvailableTillTime()
  {
    return $this->availableTillTime;
  }
  /**
   * Output only. The OCID of the compartment.
   *
   * @param string $compartmentId
   */
  public function setCompartmentId($compartmentId)
  {
    $this->compartmentId = $compartmentId;
  }
  /**
   * @return string
   */
  public function getCompartmentId()
  {
    return $this->compartmentId;
  }
  /**
   * Output only. The quantity of data in the database, in terabytes.
   *
   * @param float $databaseSizeTb
   */
  public function setDatabaseSizeTb($databaseSizeTb)
  {
    $this->databaseSizeTb = $databaseSizeTb;
  }
  /**
   * @return float
   */
  public function getDatabaseSizeTb()
  {
    return $this->databaseSizeTb;
  }
  /**
   * Output only. A valid Oracle Database version for Autonomous Database.
   *
   * @param string $dbVersion
   */
  public function setDbVersion($dbVersion)
  {
    $this->dbVersion = $dbVersion;
  }
  /**
   * @return string
   */
  public function getDbVersion()
  {
    return $this->dbVersion;
  }
  /**
   * Output only. The date and time the backup completed.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Output only. Indicates if the backup is automatic or user initiated.
   *
   * @param bool $isAutomaticBackup
   */
  public function setIsAutomaticBackup($isAutomaticBackup)
  {
    $this->isAutomaticBackup = $isAutomaticBackup;
  }
  /**
   * @return bool
   */
  public function getIsAutomaticBackup()
  {
    return $this->isAutomaticBackup;
  }
  /**
   * Output only. Indicates if the backup is long term backup.
   *
   * @param bool $isLongTermBackup
   */
  public function setIsLongTermBackup($isLongTermBackup)
  {
    $this->isLongTermBackup = $isLongTermBackup;
  }
  /**
   * @return bool
   */
  public function getIsLongTermBackup()
  {
    return $this->isLongTermBackup;
  }
  /**
   * Output only. Indicates if the backup can be used to restore the Autonomous
   * Database.
   *
   * @param bool $isRestorable
   */
  public function setIsRestorable($isRestorable)
  {
    $this->isRestorable = $isRestorable;
  }
  /**
   * @return bool
   */
  public function getIsRestorable()
  {
    return $this->isRestorable;
  }
  /**
   * Optional. The OCID of the key store of Oracle Vault.
   *
   * @param string $keyStoreId
   */
  public function setKeyStoreId($keyStoreId)
  {
    $this->keyStoreId = $keyStoreId;
  }
  /**
   * @return string
   */
  public function getKeyStoreId()
  {
    return $this->keyStoreId;
  }
  /**
   * Optional. The wallet name for Oracle Key Vault.
   *
   * @param string $keyStoreWallet
   */
  public function setKeyStoreWallet($keyStoreWallet)
  {
    $this->keyStoreWallet = $keyStoreWallet;
  }
  /**
   * @return string
   */
  public function getKeyStoreWallet()
  {
    return $this->keyStoreWallet;
  }
  /**
   * Optional. The OCID of the key container that is used as the master
   * encryption key in database transparent data encryption (TDE) operations.
   *
   * @param string $kmsKeyId
   */
  public function setKmsKeyId($kmsKeyId)
  {
    $this->kmsKeyId = $kmsKeyId;
  }
  /**
   * @return string
   */
  public function getKmsKeyId()
  {
    return $this->kmsKeyId;
  }
  /**
   * Optional. The OCID of the key container version that is used in database
   * transparent data encryption (TDE) operations KMS Key can have multiple key
   * versions. If none is specified, the current key version (latest) of the Key
   * Id is used for the operation. Autonomous Database Serverless does not use
   * key versions, hence is not applicable for Autonomous Database Serverless
   * instances.
   *
   * @param string $kmsKeyVersionId
   */
  public function setKmsKeyVersionId($kmsKeyVersionId)
  {
    $this->kmsKeyVersionId = $kmsKeyVersionId;
  }
  /**
   * @return string
   */
  public function getKmsKeyVersionId()
  {
    return $this->kmsKeyVersionId;
  }
  /**
   * Output only. Additional information about the current lifecycle state.
   *
   * @param string $lifecycleDetails
   */
  public function setLifecycleDetails($lifecycleDetails)
  {
    $this->lifecycleDetails = $lifecycleDetails;
  }
  /**
   * @return string
   */
  public function getLifecycleDetails()
  {
    return $this->lifecycleDetails;
  }
  /**
   * Output only. The lifecycle state of the backup.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING, DELETED,
   * FAILED, UPDATING
   *
   * @param self::LIFECYCLE_STATE_* $lifecycleState
   */
  public function setLifecycleState($lifecycleState)
  {
    $this->lifecycleState = $lifecycleState;
  }
  /**
   * @return self::LIFECYCLE_STATE_*
   */
  public function getLifecycleState()
  {
    return $this->lifecycleState;
  }
  /**
   * Output only. OCID of the Autonomous Database backup.
   * https://docs.oracle.com/en-
   * us/iaas/Content/General/Concepts/identifiers.htm#Oracle
   *
   * @param string $ocid
   */
  public function setOcid($ocid)
  {
    $this->ocid = $ocid;
  }
  /**
   * @return string
   */
  public function getOcid()
  {
    return $this->ocid;
  }
  /**
   * Optional. Retention period in days for the backup.
   *
   * @param int $retentionPeriodDays
   */
  public function setRetentionPeriodDays($retentionPeriodDays)
  {
    $this->retentionPeriodDays = $retentionPeriodDays;
  }
  /**
   * @return int
   */
  public function getRetentionPeriodDays()
  {
    return $this->retentionPeriodDays;
  }
  /**
   * Output only. The backup size in terabytes.
   *
   * @param float $sizeTb
   */
  public function setSizeTb($sizeTb)
  {
    $this->sizeTb = $sizeTb;
  }
  /**
   * @return float
   */
  public function getSizeTb()
  {
    return $this->sizeTb;
  }
  /**
   * Output only. The date and time the backup started.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Output only. The type of the backup.
   *
   * Accepted values: TYPE_UNSPECIFIED, INCREMENTAL, FULL, LONG_TERM
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
  /**
   * Optional. The OCID of the vault.
   *
   * @param string $vaultId
   */
  public function setVaultId($vaultId)
  {
    $this->vaultId = $vaultId;
  }
  /**
   * @return string
   */
  public function getVaultId()
  {
    return $this->vaultId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutonomousDatabaseBackupProperties::class, 'Google_Service_OracleDatabase_AutonomousDatabaseBackupProperties');
