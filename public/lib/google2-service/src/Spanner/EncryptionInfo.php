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

namespace Google\Service\Spanner;

class EncryptionInfo extends \Google\Model
{
  /**
   * Encryption type was not specified, though data at rest remains encrypted.
   */
  public const ENCRYPTION_TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * The data is encrypted at rest with a key that is fully managed by Google.
   * No key version or status will be populated. This is the default state.
   */
  public const ENCRYPTION_TYPE_GOOGLE_DEFAULT_ENCRYPTION = 'GOOGLE_DEFAULT_ENCRYPTION';
  /**
   * The data is encrypted at rest with a key that is managed by the customer.
   * The active version of the key. `kms_key_version` will be populated, and
   * `encryption_status` may be populated.
   */
  public const ENCRYPTION_TYPE_CUSTOMER_MANAGED_ENCRYPTION = 'CUSTOMER_MANAGED_ENCRYPTION';
  protected $encryptionStatusType = Status::class;
  protected $encryptionStatusDataType = '';
  /**
   * Output only. The type of encryption.
   *
   * @var string
   */
  public $encryptionType;
  /**
   * Output only. A Cloud KMS key version that is being used to protect the
   * database or backup.
   *
   * @var string
   */
  public $kmsKeyVersion;

  /**
   * Output only. If present, the status of a recent encrypt/decrypt call on
   * underlying data for this database or backup. Regardless of status, data is
   * always encrypted at rest.
   *
   * @param Status $encryptionStatus
   */
  public function setEncryptionStatus(Status $encryptionStatus)
  {
    $this->encryptionStatus = $encryptionStatus;
  }
  /**
   * @return Status
   */
  public function getEncryptionStatus()
  {
    return $this->encryptionStatus;
  }
  /**
   * Output only. The type of encryption.
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
   * Output only. A Cloud KMS key version that is being used to protect the
   * database or backup.
   *
   * @param string $kmsKeyVersion
   */
  public function setKmsKeyVersion($kmsKeyVersion)
  {
    $this->kmsKeyVersion = $kmsKeyVersion;
  }
  /**
   * @return string
   */
  public function getKmsKeyVersion()
  {
    return $this->kmsKeyVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EncryptionInfo::class, 'Google_Service_Spanner_EncryptionInfo');
