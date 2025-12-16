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

class EncryptResponse extends \Google\Model
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
   * The encrypted data.
   *
   * @var string
   */
  public $ciphertext;
  /**
   * Integrity verification field. A CRC32C checksum of the returned
   * EncryptResponse.ciphertext. An integrity check of
   * EncryptResponse.ciphertext can be performed by computing the CRC32C
   * checksum of EncryptResponse.ciphertext and comparing your results to this
   * field. Discard the response in case of non-matching checksum values, and
   * perform a limited number of retries. A persistent mismatch may indicate an
   * issue in your computation of the CRC32C checksum. Note: This field is
   * defined as int64 for reasons of compatibility across different languages.
   * However, it is a non-negative integer, which will never exceed 2^32-1, and
   * can be safely downconverted to uint32 in languages that support this type.
   *
   * @var string
   */
  public $ciphertextCrc32c;
  /**
   * The resource name of the CryptoKeyVersion used in encryption. Check this
   * field to verify that the intended resource was used for encryption.
   *
   * @var string
   */
  public $name;
  /**
   * The ProtectionLevel of the CryptoKeyVersion used in encryption.
   *
   * @var string
   */
  public $protectionLevel;
  /**
   * Integrity verification field. A flag indicating whether
   * EncryptRequest.additional_authenticated_data_crc32c was received by
   * KeyManagementService and used for the integrity verification of the AAD. A
   * false value of this field indicates either that
   * EncryptRequest.additional_authenticated_data_crc32c was left unset or that
   * it was not delivered to KeyManagementService. If you've set
   * EncryptRequest.additional_authenticated_data_crc32c but this field is still
   * false, discard the response and perform a limited number of retries.
   *
   * @var bool
   */
  public $verifiedAdditionalAuthenticatedDataCrc32c;
  /**
   * Integrity verification field. A flag indicating whether
   * EncryptRequest.plaintext_crc32c was received by KeyManagementService and
   * used for the integrity verification of the plaintext. A false value of this
   * field indicates either that EncryptRequest.plaintext_crc32c was left unset
   * or that it was not delivered to KeyManagementService. If you've set
   * EncryptRequest.plaintext_crc32c but this field is still false, discard the
   * response and perform a limited number of retries.
   *
   * @var bool
   */
  public $verifiedPlaintextCrc32c;

  /**
   * The encrypted data.
   *
   * @param string $ciphertext
   */
  public function setCiphertext($ciphertext)
  {
    $this->ciphertext = $ciphertext;
  }
  /**
   * @return string
   */
  public function getCiphertext()
  {
    return $this->ciphertext;
  }
  /**
   * Integrity verification field. A CRC32C checksum of the returned
   * EncryptResponse.ciphertext. An integrity check of
   * EncryptResponse.ciphertext can be performed by computing the CRC32C
   * checksum of EncryptResponse.ciphertext and comparing your results to this
   * field. Discard the response in case of non-matching checksum values, and
   * perform a limited number of retries. A persistent mismatch may indicate an
   * issue in your computation of the CRC32C checksum. Note: This field is
   * defined as int64 for reasons of compatibility across different languages.
   * However, it is a non-negative integer, which will never exceed 2^32-1, and
   * can be safely downconverted to uint32 in languages that support this type.
   *
   * @param string $ciphertextCrc32c
   */
  public function setCiphertextCrc32c($ciphertextCrc32c)
  {
    $this->ciphertextCrc32c = $ciphertextCrc32c;
  }
  /**
   * @return string
   */
  public function getCiphertextCrc32c()
  {
    return $this->ciphertextCrc32c;
  }
  /**
   * The resource name of the CryptoKeyVersion used in encryption. Check this
   * field to verify that the intended resource was used for encryption.
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
   * The ProtectionLevel of the CryptoKeyVersion used in encryption.
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
   * Integrity verification field. A flag indicating whether
   * EncryptRequest.additional_authenticated_data_crc32c was received by
   * KeyManagementService and used for the integrity verification of the AAD. A
   * false value of this field indicates either that
   * EncryptRequest.additional_authenticated_data_crc32c was left unset or that
   * it was not delivered to KeyManagementService. If you've set
   * EncryptRequest.additional_authenticated_data_crc32c but this field is still
   * false, discard the response and perform a limited number of retries.
   *
   * @param bool $verifiedAdditionalAuthenticatedDataCrc32c
   */
  public function setVerifiedAdditionalAuthenticatedDataCrc32c($verifiedAdditionalAuthenticatedDataCrc32c)
  {
    $this->verifiedAdditionalAuthenticatedDataCrc32c = $verifiedAdditionalAuthenticatedDataCrc32c;
  }
  /**
   * @return bool
   */
  public function getVerifiedAdditionalAuthenticatedDataCrc32c()
  {
    return $this->verifiedAdditionalAuthenticatedDataCrc32c;
  }
  /**
   * Integrity verification field. A flag indicating whether
   * EncryptRequest.plaintext_crc32c was received by KeyManagementService and
   * used for the integrity verification of the plaintext. A false value of this
   * field indicates either that EncryptRequest.plaintext_crc32c was left unset
   * or that it was not delivered to KeyManagementService. If you've set
   * EncryptRequest.plaintext_crc32c but this field is still false, discard the
   * response and perform a limited number of retries.
   *
   * @param bool $verifiedPlaintextCrc32c
   */
  public function setVerifiedPlaintextCrc32c($verifiedPlaintextCrc32c)
  {
    $this->verifiedPlaintextCrc32c = $verifiedPlaintextCrc32c;
  }
  /**
   * @return bool
   */
  public function getVerifiedPlaintextCrc32c()
  {
    return $this->verifiedPlaintextCrc32c;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EncryptResponse::class, 'Google_Service_CloudKMS_EncryptResponse');
