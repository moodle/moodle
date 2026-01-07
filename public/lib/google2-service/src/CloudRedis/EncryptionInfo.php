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

namespace Google\Service\CloudRedis;

class EncryptionInfo extends \Google\Collection
{
  /**
   * Encryption type not specified. Defaults to GOOGLE_DEFAULT_ENCRYPTION.
   */
  public const ENCRYPTION_TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * The data is encrypted at rest with a key that is fully managed by Google.
   * No key version will be populated. This is the default state.
   */
  public const ENCRYPTION_TYPE_GOOGLE_DEFAULT_ENCRYPTION = 'GOOGLE_DEFAULT_ENCRYPTION';
  /**
   * The data is encrypted at rest with a key that is managed by the customer.
   * KMS key versions will be populated.
   */
  public const ENCRYPTION_TYPE_CUSTOMER_MANAGED_ENCRYPTION = 'CUSTOMER_MANAGED_ENCRYPTION';
  /**
   * The default value. This value is unused.
   */
  public const KMS_KEY_PRIMARY_STATE_KMS_KEY_STATE_UNSPECIFIED = 'KMS_KEY_STATE_UNSPECIFIED';
  /**
   * The KMS key is enabled and correctly configured.
   */
  public const KMS_KEY_PRIMARY_STATE_ENABLED = 'ENABLED';
  /**
   * Permission denied on the KMS key.
   */
  public const KMS_KEY_PRIMARY_STATE_PERMISSION_DENIED = 'PERMISSION_DENIED';
  /**
   * The KMS key is disabled.
   */
  public const KMS_KEY_PRIMARY_STATE_DISABLED = 'DISABLED';
  /**
   * The KMS key is destroyed.
   */
  public const KMS_KEY_PRIMARY_STATE_DESTROYED = 'DESTROYED';
  /**
   * The KMS key is scheduled to be destroyed.
   */
  public const KMS_KEY_PRIMARY_STATE_DESTROY_SCHEDULED = 'DESTROY_SCHEDULED';
  /**
   * The EKM key is unreachable.
   */
  public const KMS_KEY_PRIMARY_STATE_EKM_KEY_UNREACHABLE_DETECTED = 'EKM_KEY_UNREACHABLE_DETECTED';
  /**
   * Billing is disabled for the project.
   */
  public const KMS_KEY_PRIMARY_STATE_BILLING_DISABLED = 'BILLING_DISABLED';
  /**
   * All other unknown failures.
   */
  public const KMS_KEY_PRIMARY_STATE_UNKNOWN_FAILURE = 'UNKNOWN_FAILURE';
  protected $collection_key = 'kmsKeyVersions';
  /**
   * Output only. Type of encryption.
   *
   * @var string
   */
  public $encryptionType;
  /**
   * Output only. The state of the primary version of the KMS key perceived by
   * the system. This field is not populated in backups.
   *
   * @var string
   */
  public $kmsKeyPrimaryState;
  /**
   * Output only. KMS key versions that are being used to protect the data at-
   * rest.
   *
   * @var string[]
   */
  public $kmsKeyVersions;
  /**
   * Output only. The most recent time when the encryption info was updated.
   *
   * @var string
   */
  public $lastUpdateTime;

  /**
   * Output only. Type of encryption.
   *
   * Accepted values: TYPE_UNSPECIFIED, GOOGLE_DEFAULT_ENCRYPTION,
   * CUSTOMER_MANAGED_ENCRYPTION
   *
   * @param self::ENCRYPTION_TYPE_* $encryptionType
   */
  public function setEncryptionType($encryptionType)
  {
    $this->encryptionType = $encryptionType;
  }
  /**
   * @return self::ENCRYPTION_TYPE_*
   */
  public function getEncryptionType()
  {
    return $this->encryptionType;
  }
  /**
   * Output only. The state of the primary version of the KMS key perceived by
   * the system. This field is not populated in backups.
   *
   * Accepted values: KMS_KEY_STATE_UNSPECIFIED, ENABLED, PERMISSION_DENIED,
   * DISABLED, DESTROYED, DESTROY_SCHEDULED, EKM_KEY_UNREACHABLE_DETECTED,
   * BILLING_DISABLED, UNKNOWN_FAILURE
   *
   * @param self::KMS_KEY_PRIMARY_STATE_* $kmsKeyPrimaryState
   */
  public function setKmsKeyPrimaryState($kmsKeyPrimaryState)
  {
    $this->kmsKeyPrimaryState = $kmsKeyPrimaryState;
  }
  /**
   * @return self::KMS_KEY_PRIMARY_STATE_*
   */
  public function getKmsKeyPrimaryState()
  {
    return $this->kmsKeyPrimaryState;
  }
  /**
   * Output only. KMS key versions that are being used to protect the data at-
   * rest.
   *
   * @param string[] $kmsKeyVersions
   */
  public function setKmsKeyVersions($kmsKeyVersions)
  {
    $this->kmsKeyVersions = $kmsKeyVersions;
  }
  /**
   * @return string[]
   */
  public function getKmsKeyVersions()
  {
    return $this->kmsKeyVersions;
  }
  /**
   * Output only. The most recent time when the encryption info was updated.
   *
   * @param string $lastUpdateTime
   */
  public function setLastUpdateTime($lastUpdateTime)
  {
    $this->lastUpdateTime = $lastUpdateTime;
  }
  /**
   * @return string
   */
  public function getLastUpdateTime()
  {
    return $this->lastUpdateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EncryptionInfo::class, 'Google_Service_CloudRedis_EncryptionInfo');
