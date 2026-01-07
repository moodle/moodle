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

class EncryptionInfo extends \Google\Model
{
  /**
   * Encryption type was not specified, though data at rest remains encrypted.
   */
  public const ENCRYPTION_TYPE_ENCRYPTION_TYPE_UNSPECIFIED = 'ENCRYPTION_TYPE_UNSPECIFIED';
  /**
   * The data backing this resource is encrypted at rest with a key that is
   * fully managed by Google. No key version or status will be populated. This
   * is the default state.
   */
  public const ENCRYPTION_TYPE_GOOGLE_DEFAULT_ENCRYPTION = 'GOOGLE_DEFAULT_ENCRYPTION';
  /**
   * The data backing this resource is encrypted at rest with a key that is
   * managed by the customer. The in-use version of the key and its status are
   * populated for CMEK-protected tables. CMEK-protected backups are pinned to
   * the key version that was in use at the time the backup was taken. This key
   * version is populated but its status is not tracked and is reported as
   * `UNKNOWN`.
   */
  public const ENCRYPTION_TYPE_CUSTOMER_MANAGED_ENCRYPTION = 'CUSTOMER_MANAGED_ENCRYPTION';
  protected $encryptionStatusType = Status::class;
  protected $encryptionStatusDataType = '';
  /**
   * Output only. The type of encryption used to protect this resource.
   *
   * @var string
   */
  public $encryptionType;
  /**
   * Output only. The version of the Cloud KMS key specified in the parent
   * cluster that is in use for the data underlying this table.
   *
   * @var string
   */
  public $kmsKeyVersion;

  /**
   * Output only. The status of encrypt/decrypt calls on underlying data for
   * this resource. Regardless of status, the existing data is always encrypted
   * at rest.
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
   * Output only. The type of encryption used to protect this resource.
   *
   * Accepted values: ENCRYPTION_TYPE_UNSPECIFIED, GOOGLE_DEFAULT_ENCRYPTION,
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
   * Output only. The version of the Cloud KMS key specified in the parent
   * cluster that is in use for the data underlying this table.
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
class_alias(EncryptionInfo::class, 'Google_Service_BigtableAdmin_EncryptionInfo');
