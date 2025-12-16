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

namespace Google\Service\Kmsinventory;

class GoogleCloudKmsV1CryptoKeyVersionTemplate extends \Google\Model
{
  /**
   * Not specified.
   */
  public const ALGORITHM_CRYPTO_KEY_VERSION_ALGORITHM_UNSPECIFIED = 'CRYPTO_KEY_VERSION_ALGORITHM_UNSPECIFIED';
  /**
   * Creates symmetric encryption keys.
   */
  public const ALGORITHM_GOOGLE_SYMMETRIC_ENCRYPTION = 'GOOGLE_SYMMETRIC_ENCRYPTION';
  /**
   * AES-GCM (Galois Counter Mode) using 128-bit keys.
   */
  public const ALGORITHM_AES_128_GCM = 'AES_128_GCM';
  /**
   * AES-GCM (Galois Counter Mode) using 256-bit keys.
   */
  public const ALGORITHM_AES_256_GCM = 'AES_256_GCM';
  /**
   * AES-CBC (Cipher Block Chaining Mode) using 128-bit keys.
   */
  public const ALGORITHM_AES_128_CBC = 'AES_128_CBC';
  /**
   * AES-CBC (Cipher Block Chaining Mode) using 256-bit keys.
   */
  public const ALGORITHM_AES_256_CBC = 'AES_256_CBC';
  /**
   * AES-CTR (Counter Mode) using 128-bit keys.
   */
  public const ALGORITHM_AES_128_CTR = 'AES_128_CTR';
  /**
   * AES-CTR (Counter Mode) using 256-bit keys.
   */
  public const ALGORITHM_AES_256_CTR = 'AES_256_CTR';
  /**
   * RSASSA-PSS 2048 bit key with a SHA256 digest.
   */
  public const ALGORITHM_RSA_SIGN_PSS_2048_SHA256 = 'RSA_SIGN_PSS_2048_SHA256';
  /**
   * RSASSA-PSS 3072 bit key with a SHA256 digest.
   */
  public const ALGORITHM_RSA_SIGN_PSS_3072_SHA256 = 'RSA_SIGN_PSS_3072_SHA256';
  /**
   * RSASSA-PSS 4096 bit key with a SHA256 digest.
   */
  public const ALGORITHM_RSA_SIGN_PSS_4096_SHA256 = 'RSA_SIGN_PSS_4096_SHA256';
  /**
   * RSASSA-PSS 4096 bit key with a SHA512 digest.
   */
  public const ALGORITHM_RSA_SIGN_PSS_4096_SHA512 = 'RSA_SIGN_PSS_4096_SHA512';
  /**
   * RSASSA-PKCS1-v1_5 with a 2048 bit key and a SHA256 digest.
   */
  public const ALGORITHM_RSA_SIGN_PKCS1_2048_SHA256 = 'RSA_SIGN_PKCS1_2048_SHA256';
  /**
   * RSASSA-PKCS1-v1_5 with a 3072 bit key and a SHA256 digest.
   */
  public const ALGORITHM_RSA_SIGN_PKCS1_3072_SHA256 = 'RSA_SIGN_PKCS1_3072_SHA256';
  /**
   * RSASSA-PKCS1-v1_5 with a 4096 bit key and a SHA256 digest.
   */
  public const ALGORITHM_RSA_SIGN_PKCS1_4096_SHA256 = 'RSA_SIGN_PKCS1_4096_SHA256';
  /**
   * RSASSA-PKCS1-v1_5 with a 4096 bit key and a SHA512 digest.
   */
  public const ALGORITHM_RSA_SIGN_PKCS1_4096_SHA512 = 'RSA_SIGN_PKCS1_4096_SHA512';
  /**
   * RSASSA-PKCS1-v1_5 signing without encoding, with a 2048 bit key.
   */
  public const ALGORITHM_RSA_SIGN_RAW_PKCS1_2048 = 'RSA_SIGN_RAW_PKCS1_2048';
  /**
   * RSASSA-PKCS1-v1_5 signing without encoding, with a 3072 bit key.
   */
  public const ALGORITHM_RSA_SIGN_RAW_PKCS1_3072 = 'RSA_SIGN_RAW_PKCS1_3072';
  /**
   * RSASSA-PKCS1-v1_5 signing without encoding, with a 4096 bit key.
   */
  public const ALGORITHM_RSA_SIGN_RAW_PKCS1_4096 = 'RSA_SIGN_RAW_PKCS1_4096';
  /**
   * RSAES-OAEP 2048 bit key with a SHA256 digest.
   */
  public const ALGORITHM_RSA_DECRYPT_OAEP_2048_SHA256 = 'RSA_DECRYPT_OAEP_2048_SHA256';
  /**
   * RSAES-OAEP 3072 bit key with a SHA256 digest.
   */
  public const ALGORITHM_RSA_DECRYPT_OAEP_3072_SHA256 = 'RSA_DECRYPT_OAEP_3072_SHA256';
  /**
   * RSAES-OAEP 4096 bit key with a SHA256 digest.
   */
  public const ALGORITHM_RSA_DECRYPT_OAEP_4096_SHA256 = 'RSA_DECRYPT_OAEP_4096_SHA256';
  /**
   * RSAES-OAEP 4096 bit key with a SHA512 digest.
   */
  public const ALGORITHM_RSA_DECRYPT_OAEP_4096_SHA512 = 'RSA_DECRYPT_OAEP_4096_SHA512';
  /**
   * RSAES-OAEP 2048 bit key with a SHA1 digest.
   */
  public const ALGORITHM_RSA_DECRYPT_OAEP_2048_SHA1 = 'RSA_DECRYPT_OAEP_2048_SHA1';
  /**
   * RSAES-OAEP 3072 bit key with a SHA1 digest.
   */
  public const ALGORITHM_RSA_DECRYPT_OAEP_3072_SHA1 = 'RSA_DECRYPT_OAEP_3072_SHA1';
  /**
   * RSAES-OAEP 4096 bit key with a SHA1 digest.
   */
  public const ALGORITHM_RSA_DECRYPT_OAEP_4096_SHA1 = 'RSA_DECRYPT_OAEP_4096_SHA1';
  /**
   * ECDSA on the NIST P-256 curve with a SHA256 digest. Other hash functions
   * can also be used: https://cloud.google.com/kms/docs/create-validate-
   * signatures#ecdsa_support_for_other_hash_algorithms
   */
  public const ALGORITHM_EC_SIGN_P256_SHA256 = 'EC_SIGN_P256_SHA256';
  /**
   * ECDSA on the NIST P-384 curve with a SHA384 digest. Other hash functions
   * can also be used: https://cloud.google.com/kms/docs/create-validate-
   * signatures#ecdsa_support_for_other_hash_algorithms
   */
  public const ALGORITHM_EC_SIGN_P384_SHA384 = 'EC_SIGN_P384_SHA384';
  /**
   * ECDSA on the non-NIST secp256k1 curve. This curve is only supported for HSM
   * protection level. Other hash functions can also be used:
   * https://cloud.google.com/kms/docs/create-validate-
   * signatures#ecdsa_support_for_other_hash_algorithms
   */
  public const ALGORITHM_EC_SIGN_SECP256K1_SHA256 = 'EC_SIGN_SECP256K1_SHA256';
  /**
   * EdDSA on the Curve25519 in pure mode (taking data as input).
   */
  public const ALGORITHM_EC_SIGN_ED25519 = 'EC_SIGN_ED25519';
  /**
   * HMAC-SHA256 signing with a 256 bit key.
   */
  public const ALGORITHM_HMAC_SHA256 = 'HMAC_SHA256';
  /**
   * HMAC-SHA1 signing with a 160 bit key.
   */
  public const ALGORITHM_HMAC_SHA1 = 'HMAC_SHA1';
  /**
   * HMAC-SHA384 signing with a 384 bit key.
   */
  public const ALGORITHM_HMAC_SHA384 = 'HMAC_SHA384';
  /**
   * HMAC-SHA512 signing with a 512 bit key.
   */
  public const ALGORITHM_HMAC_SHA512 = 'HMAC_SHA512';
  /**
   * HMAC-SHA224 signing with a 224 bit key.
   */
  public const ALGORITHM_HMAC_SHA224 = 'HMAC_SHA224';
  /**
   * Algorithm representing symmetric encryption by an external key manager.
   */
  public const ALGORITHM_EXTERNAL_SYMMETRIC_ENCRYPTION = 'EXTERNAL_SYMMETRIC_ENCRYPTION';
  /**
   * ML-KEM-768 (FIPS 203)
   */
  public const ALGORITHM_ML_KEM_768 = 'ML_KEM_768';
  /**
   * ML-KEM-1024 (FIPS 203)
   */
  public const ALGORITHM_ML_KEM_1024 = 'ML_KEM_1024';
  /**
   * X-Wing hybrid KEM combining ML-KEM-768 with X25519 following
   * datatracker.ietf.org/doc/draft-connolly-cfrg-xwing-kem/.
   */
  public const ALGORITHM_KEM_XWING = 'KEM_XWING';
  /**
   * The post-quantum Module-Lattice-Based Digital Signature Algorithm, at
   * security level 1. Randomized version.
   */
  public const ALGORITHM_PQ_SIGN_ML_DSA_44 = 'PQ_SIGN_ML_DSA_44';
  /**
   * The post-quantum Module-Lattice-Based Digital Signature Algorithm, at
   * security level 3. Randomized version.
   */
  public const ALGORITHM_PQ_SIGN_ML_DSA_65 = 'PQ_SIGN_ML_DSA_65';
  /**
   * The post-quantum Module-Lattice-Based Digital Signature Algorithm, at
   * security level 5. Randomized version.
   */
  public const ALGORITHM_PQ_SIGN_ML_DSA_87 = 'PQ_SIGN_ML_DSA_87';
  /**
   * The post-quantum stateless hash-based digital signature algorithm, at
   * security level 1. Randomized version.
   */
  public const ALGORITHM_PQ_SIGN_SLH_DSA_SHA2_128S = 'PQ_SIGN_SLH_DSA_SHA2_128S';
  /**
   * The post-quantum stateless hash-based digital signature algorithm, at
   * security level 1. Randomized pre-hash version supporting SHA256 digests.
   */
  public const ALGORITHM_PQ_SIGN_HASH_SLH_DSA_SHA2_128S_SHA256 = 'PQ_SIGN_HASH_SLH_DSA_SHA2_128S_SHA256';
  /**
   * The post-quantum Module-Lattice-Based Digital Signature Algorithm, at
   * security level 1. Randomized version supporting externally-computed message
   * representatives.
   */
  public const ALGORITHM_PQ_SIGN_ML_DSA_44_EXTERNAL_MU = 'PQ_SIGN_ML_DSA_44_EXTERNAL_MU';
  /**
   * The post-quantum Module-Lattice-Based Digital Signature Algorithm, at
   * security level 3. Randomized version supporting externally-computed message
   * representatives.
   */
  public const ALGORITHM_PQ_SIGN_ML_DSA_65_EXTERNAL_MU = 'PQ_SIGN_ML_DSA_65_EXTERNAL_MU';
  /**
   * The post-quantum Module-Lattice-Based Digital Signature Algorithm, at
   * security level 5. Randomized version supporting externally-computed message
   * representatives.
   */
  public const ALGORITHM_PQ_SIGN_ML_DSA_87_EXTERNAL_MU = 'PQ_SIGN_ML_DSA_87_EXTERNAL_MU';
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
   * Required. Algorithm to use when creating a CryptoKeyVersion based on this
   * template. For backwards compatibility, GOOGLE_SYMMETRIC_ENCRYPTION is
   * implied if both this field is omitted and CryptoKey.purpose is
   * ENCRYPT_DECRYPT.
   *
   * @var string
   */
  public $algorithm;
  /**
   * ProtectionLevel to use when creating a CryptoKeyVersion based on this
   * template. Immutable. Defaults to SOFTWARE.
   *
   * @var string
   */
  public $protectionLevel;

  /**
   * Required. Algorithm to use when creating a CryptoKeyVersion based on this
   * template. For backwards compatibility, GOOGLE_SYMMETRIC_ENCRYPTION is
   * implied if both this field is omitted and CryptoKey.purpose is
   * ENCRYPT_DECRYPT.
   *
   * Accepted values: CRYPTO_KEY_VERSION_ALGORITHM_UNSPECIFIED,
   * GOOGLE_SYMMETRIC_ENCRYPTION, AES_128_GCM, AES_256_GCM, AES_128_CBC,
   * AES_256_CBC, AES_128_CTR, AES_256_CTR, RSA_SIGN_PSS_2048_SHA256,
   * RSA_SIGN_PSS_3072_SHA256, RSA_SIGN_PSS_4096_SHA256,
   * RSA_SIGN_PSS_4096_SHA512, RSA_SIGN_PKCS1_2048_SHA256,
   * RSA_SIGN_PKCS1_3072_SHA256, RSA_SIGN_PKCS1_4096_SHA256,
   * RSA_SIGN_PKCS1_4096_SHA512, RSA_SIGN_RAW_PKCS1_2048,
   * RSA_SIGN_RAW_PKCS1_3072, RSA_SIGN_RAW_PKCS1_4096,
   * RSA_DECRYPT_OAEP_2048_SHA256, RSA_DECRYPT_OAEP_3072_SHA256,
   * RSA_DECRYPT_OAEP_4096_SHA256, RSA_DECRYPT_OAEP_4096_SHA512,
   * RSA_DECRYPT_OAEP_2048_SHA1, RSA_DECRYPT_OAEP_3072_SHA1,
   * RSA_DECRYPT_OAEP_4096_SHA1, EC_SIGN_P256_SHA256, EC_SIGN_P384_SHA384,
   * EC_SIGN_SECP256K1_SHA256, EC_SIGN_ED25519, HMAC_SHA256, HMAC_SHA1,
   * HMAC_SHA384, HMAC_SHA512, HMAC_SHA224, EXTERNAL_SYMMETRIC_ENCRYPTION,
   * ML_KEM_768, ML_KEM_1024, KEM_XWING, PQ_SIGN_ML_DSA_44, PQ_SIGN_ML_DSA_65,
   * PQ_SIGN_ML_DSA_87, PQ_SIGN_SLH_DSA_SHA2_128S,
   * PQ_SIGN_HASH_SLH_DSA_SHA2_128S_SHA256, PQ_SIGN_ML_DSA_44_EXTERNAL_MU,
   * PQ_SIGN_ML_DSA_65_EXTERNAL_MU, PQ_SIGN_ML_DSA_87_EXTERNAL_MU
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
   * ProtectionLevel to use when creating a CryptoKeyVersion based on this
   * template. Immutable. Defaults to SOFTWARE.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudKmsV1CryptoKeyVersionTemplate::class, 'Google_Service_Kmsinventory_GoogleCloudKmsV1CryptoKeyVersionTemplate');
