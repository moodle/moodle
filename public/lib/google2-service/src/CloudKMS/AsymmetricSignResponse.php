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

class AsymmetricSignResponse extends \Google\Model
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
   * The resource name of the CryptoKeyVersion used for signing. Check this
   * field to verify that the intended resource was used for signing.
   *
   * @var string
   */
  public $name;
  /**
   * The ProtectionLevel of the CryptoKeyVersion used for signing.
   *
   * @var string
   */
  public $protectionLevel;
  /**
   * The created signature.
   *
   * @var string
   */
  public $signature;
  /**
   * Integrity verification field. A CRC32C checksum of the returned
   * AsymmetricSignResponse.signature. An integrity check of
   * AsymmetricSignResponse.signature can be performed by computing the CRC32C
   * checksum of AsymmetricSignResponse.signature and comparing your results to
   * this field. Discard the response in case of non-matching checksum values,
   * and perform a limited number of retries. A persistent mismatch may indicate
   * an issue in your computation of the CRC32C checksum. Note: This field is
   * defined as int64 for reasons of compatibility across different languages.
   * However, it is a non-negative integer, which will never exceed 2^32-1, and
   * can be safely downconverted to uint32 in languages that support this type.
   *
   * @var string
   */
  public $signatureCrc32c;
  /**
   * Integrity verification field. A flag indicating whether
   * AsymmetricSignRequest.data_crc32c was received by KeyManagementService and
   * used for the integrity verification of the data. A false value of this
   * field indicates either that AsymmetricSignRequest.data_crc32c was left
   * unset or that it was not delivered to KeyManagementService. If you've set
   * AsymmetricSignRequest.data_crc32c but this field is still false, discard
   * the response and perform a limited number of retries.
   *
   * @var bool
   */
  public $verifiedDataCrc32c;
  /**
   * Integrity verification field. A flag indicating whether
   * AsymmetricSignRequest.digest_crc32c was received by KeyManagementService
   * and used for the integrity verification of the digest. A false value of
   * this field indicates either that AsymmetricSignRequest.digest_crc32c was
   * left unset or that it was not delivered to KeyManagementService. If you've
   * set AsymmetricSignRequest.digest_crc32c but this field is still false,
   * discard the response and perform a limited number of retries.
   *
   * @var bool
   */
  public $verifiedDigestCrc32c;

  /**
   * The resource name of the CryptoKeyVersion used for signing. Check this
   * field to verify that the intended resource was used for signing.
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
   * The ProtectionLevel of the CryptoKeyVersion used for signing.
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
   * The created signature.
   *
   * @param string $signature
   */
  public function setSignature($signature)
  {
    $this->signature = $signature;
  }
  /**
   * @return string
   */
  public function getSignature()
  {
    return $this->signature;
  }
  /**
   * Integrity verification field. A CRC32C checksum of the returned
   * AsymmetricSignResponse.signature. An integrity check of
   * AsymmetricSignResponse.signature can be performed by computing the CRC32C
   * checksum of AsymmetricSignResponse.signature and comparing your results to
   * this field. Discard the response in case of non-matching checksum values,
   * and perform a limited number of retries. A persistent mismatch may indicate
   * an issue in your computation of the CRC32C checksum. Note: This field is
   * defined as int64 for reasons of compatibility across different languages.
   * However, it is a non-negative integer, which will never exceed 2^32-1, and
   * can be safely downconverted to uint32 in languages that support this type.
   *
   * @param string $signatureCrc32c
   */
  public function setSignatureCrc32c($signatureCrc32c)
  {
    $this->signatureCrc32c = $signatureCrc32c;
  }
  /**
   * @return string
   */
  public function getSignatureCrc32c()
  {
    return $this->signatureCrc32c;
  }
  /**
   * Integrity verification field. A flag indicating whether
   * AsymmetricSignRequest.data_crc32c was received by KeyManagementService and
   * used for the integrity verification of the data. A false value of this
   * field indicates either that AsymmetricSignRequest.data_crc32c was left
   * unset or that it was not delivered to KeyManagementService. If you've set
   * AsymmetricSignRequest.data_crc32c but this field is still false, discard
   * the response and perform a limited number of retries.
   *
   * @param bool $verifiedDataCrc32c
   */
  public function setVerifiedDataCrc32c($verifiedDataCrc32c)
  {
    $this->verifiedDataCrc32c = $verifiedDataCrc32c;
  }
  /**
   * @return bool
   */
  public function getVerifiedDataCrc32c()
  {
    return $this->verifiedDataCrc32c;
  }
  /**
   * Integrity verification field. A flag indicating whether
   * AsymmetricSignRequest.digest_crc32c was received by KeyManagementService
   * and used for the integrity verification of the digest. A false value of
   * this field indicates either that AsymmetricSignRequest.digest_crc32c was
   * left unset or that it was not delivered to KeyManagementService. If you've
   * set AsymmetricSignRequest.digest_crc32c but this field is still false,
   * discard the response and perform a limited number of retries.
   *
   * @param bool $verifiedDigestCrc32c
   */
  public function setVerifiedDigestCrc32c($verifiedDigestCrc32c)
  {
    $this->verifiedDigestCrc32c = $verifiedDigestCrc32c;
  }
  /**
   * @return bool
   */
  public function getVerifiedDigestCrc32c()
  {
    return $this->verifiedDigestCrc32c;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AsymmetricSignResponse::class, 'Google_Service_CloudKMS_AsymmetricSignResponse');
