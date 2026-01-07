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

class CustomerEncryptionKey extends \Google\Model
{
  /**
   * Optional. The name of the encryption key that is stored in Google Cloud
   * KMS.
   *
   * @var string
   */
  public $kmsKeyName;
  /**
   * Optional. The service account being used for the encryption request for the
   * given KMS key. If absent, the Compute Engine default service account is
   * used.
   *
   * @var string
   */
  public $kmsKeyServiceAccount;
  /**
   * Optional. Specifies a 256-bit customer-supplied encryption key.
   *
   * @var string
   */
  public $rawKey;
  /**
   * Optional. RSA-wrapped 2048-bit customer-supplied encryption key to either
   * encrypt or decrypt this resource.
   *
   * @var string
   */
  public $rsaEncryptedKey;

  /**
   * Optional. The name of the encryption key that is stored in Google Cloud
   * KMS.
   *
   * @param string $kmsKeyName
   */
  public function setKmsKeyName($kmsKeyName)
  {
    $this->kmsKeyName = $kmsKeyName;
  }
  /**
   * @return string
   */
  public function getKmsKeyName()
  {
    return $this->kmsKeyName;
  }
  /**
   * Optional. The service account being used for the encryption request for the
   * given KMS key. If absent, the Compute Engine default service account is
   * used.
   *
   * @param string $kmsKeyServiceAccount
   */
  public function setKmsKeyServiceAccount($kmsKeyServiceAccount)
  {
    $this->kmsKeyServiceAccount = $kmsKeyServiceAccount;
  }
  /**
   * @return string
   */
  public function getKmsKeyServiceAccount()
  {
    return $this->kmsKeyServiceAccount;
  }
  /**
   * Optional. Specifies a 256-bit customer-supplied encryption key.
   *
   * @param string $rawKey
   */
  public function setRawKey($rawKey)
  {
    $this->rawKey = $rawKey;
  }
  /**
   * @return string
   */
  public function getRawKey()
  {
    return $this->rawKey;
  }
  /**
   * Optional. RSA-wrapped 2048-bit customer-supplied encryption key to either
   * encrypt or decrypt this resource.
   *
   * @param string $rsaEncryptedKey
   */
  public function setRsaEncryptedKey($rsaEncryptedKey)
  {
    $this->rsaEncryptedKey = $rsaEncryptedKey;
  }
  /**
   * @return string
   */
  public function getRsaEncryptedKey()
  {
    return $this->rsaEncryptedKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomerEncryptionKey::class, 'Google_Service_Backupdr_CustomerEncryptionKey');
