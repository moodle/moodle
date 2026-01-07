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

namespace Google\Service\CloudKMS;

class DecryptResponse extends \Google\Model
{
  /**
   * Not specified.
   */
  public const PROTECTION_LEVEL_PROTECTION_LEVEL_UNSPECIFIED = 'PROTECTION_LEVEL_UNSPECIFIED';
  /**
   * Crypto operations are performed in software.
   */
  public const PROTECTION_LEVEL_SOFTWARE = 'SOFTWARE';
  /**
   * Crypto operations are performed in a Hardware Security Module.
   */
  public const PROTECTION_LEVEL_HSM = 'HSM';
  /**
   * Crypto operations are performed by an external key manager.
   */
  public const PROTECTION_LEVEL_EXTERNAL = 'EXTERNAL';
  /**
   * Crypto operations are performed in an EKM-over-VPC backend.
   */
  public const PROTECTION_LEVEL_EXTERNAL_VPC = 'EXTERNAL_VPC';
  /**
   * Crypto operations are performed in a single-tenant HSM.
   */
  public const PROTECTION_LEVEL_HSM_SINGLE_TENANT = 'HSM_SINGLE_TENANT';
  /**
   * The decrypted data originally supplied in EncryptRequest.plaintext.
   *
   * @var string
   */
  public $plaintext;
  /**
   * Integrity verification field. A CRC32C checksum of the returned
   * DecryptResponse.plaintext. An integrity check of DecryptResponse.plaintext
   * can be performed by computing the CRC32C checksum of
   * DecryptResponse.plaintext and comparing your results to this field. Discard
   * the response in case of non-matching checksum values, and perform a limited
   * number of retries. A persistent mismatch may indicate an issue in your
   * computation of the CRC32C checksum. Note: receiving this response message
   * indicates that KeyManagementService is able to successfully decrypt the
   * ciphertext. Note: This field is defined as int64 for reasons of
   * compatibility across different languages. However, it is a non-negative
   * integer, which will never exceed 2^32-1, and can be safely downconverted to
   * uint32 in languages that support this type.
   *
   * @var string
   */
  public $plaintextCrc32c;
  /**
   * The ProtectionLevel of the CryptoKeyVersion used in decryption.
   *
   * @var string
   */
  public $protectionLevel;
  /**
   * Whether the Decryption was performed using the primary key version.
   *
   * @var bool
   */
  public $usedPrimary;

  /**
   * The decrypted data originally supplied in EncryptRequest.plaintext.
   *
   * @param string $plaintext
   */
  public function setPlaintext($plaintext)
  {
    $this->plaintext = $plaintext;
  }
  /**
   * @return string
   */
  public function getPlaintext()
  {
    return $this->plaintext;
  }
  /**
   * Integrity verification field. A CRC32C checksum of the returned
   * DecryptResponse.plaintext. An integrity check of DecryptResponse.plaintext
   * can be performed by computing the CRC32C checksum of
   * DecryptResponse.plaintext and comparing your results to this field. Discard
   * the response in case of non-matching checksum values, and perform a limited
   * number of retries. A persistent mismatch may indicate an issue in your
   * computation of the CRC32C checksum. Note: receiving this response message
   * indicates that KeyManagementService is able to successfully decrypt the
   * ciphertext. Note: This field is defined as int64 for reasons of
   * compatibility across different languages. However, it is a non-negative
   * integer, which will never exceed 2^32-1, and can be safely downconverted to
   * uint32 in languages that support this type.
   *
   * @param string $plaintextCrc32c
   */
  public function setPlaintextCrc32c($plaintextCrc32c)
  {
    $this->plaintextCrc32c = $plaintextCrc32c;
  }
  /**
   * @return string
   */
  public function getPlaintextCrc32c()
  {
    return $this->plaintextCrc32c;
  }
  /**
   * The ProtectionLevel of the CryptoKeyVersion used in decryption.
   *
   * Accepted values: PROTECTION_LEVEL_UNSPECIFIED, SOFTWARE, HSM, EXTERNAL,
   * EXTERNAL_VPC, HSM_SINGLE_TENANT
   *
   * @param self::PROTECTION_LEVEL_* $protectionLevel
   */
  public function setProtectionLevel($protectionLevel)
  {
    $this->protectionLevel = $protectionLevel;
  }
  /**
   * @return self::PROTECTION_LEVEL_*
   */
  public function getProtectionLevel()
  {
    return $this->protectionLevel;
  }
  /**
   * Whether the Decryption was performed using the primary key version.
   *
   * @param bool $usedPrimary
   */
  public function setUsedPrimary($usedPrimary)
  {
    $this->usedPrimary = $usedPrimary;
  }
  /**
   * @return bool
   */
  public function getUsedPrimary()
  {
    return $this->usedPrimary;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DecryptResponse::class, 'Google_Service_CloudKMS_DecryptResponse');
