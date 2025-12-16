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

namespace Google\Service\CertificateAuthorityService;

class KeyVersionSpec extends \Google\Model
{
  /**
   * Not specified.
   */
  public const ALGORITHM_SIGN_HASH_ALGORITHM_UNSPECIFIED = 'SIGN_HASH_ALGORITHM_UNSPECIFIED';
  /**
   * maps to CryptoKeyVersionAlgorithm.RSA_SIGN_PSS_2048_SHA256
   */
  public const ALGORITHM_RSA_PSS_2048_SHA256 = 'RSA_PSS_2048_SHA256';
  /**
   * maps to CryptoKeyVersionAlgorithm. RSA_SIGN_PSS_3072_SHA256
   */
  public const ALGORITHM_RSA_PSS_3072_SHA256 = 'RSA_PSS_3072_SHA256';
  /**
   * maps to CryptoKeyVersionAlgorithm.RSA_SIGN_PSS_4096_SHA256
   */
  public const ALGORITHM_RSA_PSS_4096_SHA256 = 'RSA_PSS_4096_SHA256';
  /**
   * maps to CryptoKeyVersionAlgorithm.RSA_SIGN_PKCS1_2048_SHA256
   */
  public const ALGORITHM_RSA_PKCS1_2048_SHA256 = 'RSA_PKCS1_2048_SHA256';
  /**
   * maps to CryptoKeyVersionAlgorithm.RSA_SIGN_PKCS1_3072_SHA256
   */
  public const ALGORITHM_RSA_PKCS1_3072_SHA256 = 'RSA_PKCS1_3072_SHA256';
  /**
   * maps to CryptoKeyVersionAlgorithm.RSA_SIGN_PKCS1_4096_SHA256
   */
  public const ALGORITHM_RSA_PKCS1_4096_SHA256 = 'RSA_PKCS1_4096_SHA256';
  /**
   * maps to CryptoKeyVersionAlgorithm.EC_SIGN_P256_SHA256
   */
  public const ALGORITHM_EC_P256_SHA256 = 'EC_P256_SHA256';
  /**
   * maps to CryptoKeyVersionAlgorithm.EC_SIGN_P384_SHA384
   */
  public const ALGORITHM_EC_P384_SHA384 = 'EC_P384_SHA384';
  /**
   * The algorithm to use for creating a managed Cloud KMS key for a for a
   * simplified experience. All managed keys will be have their ProtectionLevel
   * as `HSM`.
   *
   * @var string
   */
  public $algorithm;
  /**
   * The resource name for an existing Cloud KMS CryptoKeyVersion in the format
   * `projects/locations/keyRings/cryptoKeys/cryptoKeyVersions`. This option
   * enables full flexibility in the key's capabilities and properties.
   *
   * @var string
   */
  public $cloudKmsKeyVersion;

  /**
   * The algorithm to use for creating a managed Cloud KMS key for a for a
   * simplified experience. All managed keys will be have their ProtectionLevel
   * as `HSM`.
   *
   * Accepted values: SIGN_HASH_ALGORITHM_UNSPECIFIED, RSA_PSS_2048_SHA256,
   * RSA_PSS_3072_SHA256, RSA_PSS_4096_SHA256, RSA_PKCS1_2048_SHA256,
   * RSA_PKCS1_3072_SHA256, RSA_PKCS1_4096_SHA256, EC_P256_SHA256,
   * EC_P384_SHA384
   *
   * @param self::ALGORITHM_* $algorithm
   */
  public function setAlgorithm($algorithm)
  {
    $this->algorithm = $algorithm;
  }
  /**
   * @return self::ALGORITHM_*
   */
  public function getAlgorithm()
  {
    return $this->algorithm;
  }
  /**
   * The resource name for an existing Cloud KMS CryptoKeyVersion in the format
   * `projects/locations/keyRings/cryptoKeys/cryptoKeyVersions`. This option
   * enables full flexibility in the key's capabilities and properties.
   *
   * @param string $cloudKmsKeyVersion
   */
  public function setCloudKmsKeyVersion($cloudKmsKeyVersion)
  {
    $this->cloudKmsKeyVersion = $cloudKmsKeyVersion;
  }
  /**
   * @return string
   */
  public function getCloudKmsKeyVersion()
  {
    return $this->cloudKmsKeyVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(KeyVersionSpec::class, 'Google_Service_CertificateAuthorityService_KeyVersionSpec');
