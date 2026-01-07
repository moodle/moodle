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

class DecapsulateResponse extends \Google\Model
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
   * The resource name of the CryptoKeyVersion used for decapsulation. Check
   * this field to verify that the intended resource was used for decapsulation.
   *
   * @var string
   */
  public $name;
  /**
   * The ProtectionLevel of the CryptoKeyVersion used in decapsulation.
   *
   * @var string
   */
  public $protectionLevel;
  /**
   * The decapsulated shared_secret originally encapsulated with the matching
   * public key.
   *
   * @var string
   */
  public $sharedSecret;
  /**
   * Integrity verification field. A CRC32C checksum of the returned
   * DecapsulateResponse.shared_secret. An integrity check of
   * DecapsulateResponse.shared_secret can be performed by computing the CRC32C
   * checksum of DecapsulateResponse.shared_secret and comparing your results to
   * this field. Discard the response in case of non-matching checksum values,
   * and perform a limited number of retries. A persistent mismatch may indicate
   * an issue in your computation of the CRC32C checksum. Note: receiving this
   * response message indicates that KeyManagementService is able to
   * successfully decrypt the ciphertext. Note: This field is defined as int64
   * for reasons of compatibility across different languages. However, it is a
   * non-negative integer, which will never exceed 2^32-1, and can be safely
   * downconverted to uint32 in languages that support this type.
   *
   * @var string
   */
  public $sharedSecretCrc32c;
  /**
   * Integrity verification field. A flag indicating whether
   * DecapsulateRequest.ciphertext_crc32c was received by KeyManagementService
   * and used for the integrity verification of the ciphertext. A false value of
   * this field indicates either that DecapsulateRequest.ciphertext_crc32c was
   * left unset or that it was not delivered to KeyManagementService. If you've
   * set DecapsulateRequest.ciphertext_crc32c but this field is still false,
   * discard the response and perform a limited number of retries.
   *
   * @var bool
   */
  public $verifiedCiphertextCrc32c;

  /**
   * The resource name of the CryptoKeyVersion used for decapsulation. Check
   * this field to verify that the intended resource was used for decapsulation.
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
   * The ProtectionLevel of the CryptoKeyVersion used in decapsulation.
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
   * The decapsulated shared_secret originally encapsulated with the matching
   * public key.
   *
   * @param string $sharedSecret
   */
  public function setSharedSecret($sharedSecret)
  {
    $this->sharedSecret = $sharedSecret;
  }
  /**
   * @return string
   */
  public function getSharedSecret()
  {
    return $this->sharedSecret;
  }
  /**
   * Integrity verification field. A CRC32C checksum of the returned
   * DecapsulateResponse.shared_secret. An integrity check of
   * DecapsulateResponse.shared_secret can be performed by computing the CRC32C
   * checksum of DecapsulateResponse.shared_secret and comparing your results to
   * this field. Discard the response in case of non-matching checksum values,
   * and perform a limited number of retries. A persistent mismatch may indicate
   * an issue in your computation of the CRC32C checksum. Note: receiving this
   * response message indicates that KeyManagementService is able to
   * successfully decrypt the ciphertext. Note: This field is defined as int64
   * for reasons of compatibility across different languages. However, it is a
   * non-negative integer, which will never exceed 2^32-1, and can be safely
   * downconverted to uint32 in languages that support this type.
   *
   * @param string $sharedSecretCrc32c
   */
  public function setSharedSecretCrc32c($sharedSecretCrc32c)
  {
    $this->sharedSecretCrc32c = $sharedSecretCrc32c;
  }
  /**
   * @return string
   */
  public function getSharedSecretCrc32c()
  {
    return $this->sharedSecretCrc32c;
  }
  /**
   * Integrity verification field. A flag indicating whether
   * DecapsulateRequest.ciphertext_crc32c was received by KeyManagementService
   * and used for the integrity verification of the ciphertext. A false value of
   * this field indicates either that DecapsulateRequest.ciphertext_crc32c was
   * left unset or that it was not delivered to KeyManagementService. If you've
   * set DecapsulateRequest.ciphertext_crc32c but this field is still false,
   * discard the response and perform a limited number of retries.
   *
   * @param bool $verifiedCiphertextCrc32c
   */
  public function setVerifiedCiphertextCrc32c($verifiedCiphertextCrc32c)
  {
    $this->verifiedCiphertextCrc32c = $verifiedCiphertextCrc32c;
  }
  /**
   * @return bool
   */
  public function getVerifiedCiphertextCrc32c()
  {
    return $this->verifiedCiphertextCrc32c;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DecapsulateResponse::class, 'Google_Service_CloudKMS_DecapsulateResponse');
