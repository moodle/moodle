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

namespace Google\Service\CloudAlloyDBAdmin;

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
  protected $collection_key = 'kmsKeyVersions';
  /**
   * Output only. Type of encryption.
   *
   * @var string
   */
  public $encryptionType;
  /**
   * Output only. Cloud KMS key versions that are being used to protect the
   * database or the backup.
   *
   * @var string[]
   */
  public $kmsKeyVersions;

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
   * Output only. Cloud KMS key versions that are being used to protect the
   * database or the backup.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EncryptionInfo::class, 'Google_Service_CloudAlloyDBAdmin_EncryptionInfo');
