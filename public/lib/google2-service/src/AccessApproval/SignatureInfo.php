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

namespace Google\Service\AccessApproval;

class SignatureInfo extends \Google\Model
{
  /**
   * Not specified.
   */
  public const GOOGLE_KEY_ALGORITHM_CRYPTO_KEY_VERSION_ALGORITHM_UNSPECIFIED = 'CRYPTO_KEY_VERSION_ALGORITHM_UNSPECIFIED';
  /**
   * Creates symmetric encryption keys.
   */
  public const GOOGLE_KEY_ALGORITHM_GOOGLE_SYMMETRIC_ENCRYPTION = 'GOOGLE_SYMMETRIC_ENCRYPTION';
  /**
   * AES-GCM (Galois Counter Mode) using 128-bit keys.
   */
  public const GOOGLE_KEY_ALGORITHM_AES_128_GCM = 'AES_128_GCM';
  /**
   * AES-GCM (Galois Counter Mode) using 256-bit keys.
   */
  public const GOOGLE_KEY_ALGORITHM_AES_256_GCM = 'AES_256_GCM';
  /**
   * AES-CBC (Cipher Block Chaining Mode) using 128-bit keys.
   */
  public const GOOGLE_KEY_ALGORITHM_AES_128_CBC = 'AES_128_CBC';
  /**
   * AES-CBC (Cipher Block Chaining Mode) using 256-bit keys.
   */
  public const GOOGLE_KEY_ALGORITHM_AES_256_CBC = 'AES_256_CBC';
  /**
   * AES-CTR (Counter Mode) using 128-bit keys.
   */
  public const GOOGLE_KEY_ALGORITHM_AES_128_CTR = 'AES_128_CTR';
  /**
   * AES-CTR (Counter Mode) using 256-bit keys.
   */
  public const GOOGLE_KEY_ALGORITHM_AES_256_CTR = 'AES_256_CTR';
  /**
   * RSASSA-PSS 2048 bit key with a SHA256 digest.
   */
  public const GOOGLE_KEY_ALGORITHM_RSA_SIGN_PSS_2048_SHA256 = 'RSA_SIGN_PSS_2048_SHA256';
  /**
   * RSASSA-PSS 3072 bit key with a SHA256 digest.
   */
  public const GOOGLE_KEY_ALGORITHM_RSA_SIGN_PSS_3072_SHA256 = 'RSA_SIGN_PSS_3072_SHA256';
  /**
   * RSASSA-PSS 4096 bit key with a SHA256 digest.
   */
  public const GOOGLE_KEY_ALGORITHM_RSA_SIGN_PSS_4096_SHA256 = 'RSA_SIGN_PSS_4096_SHA256';
  /**
   * RSASSA-PSS 4096 bit key with a SHA512 digest.
   */
  public const GOOGLE_KEY_ALGORITHM_RSA_SIGN_PSS_4096_SHA512 = 'RSA_SIGN_PSS_4096_SHA512';
  /**
   * RSASSA-PKCS1-v1_5 with a 2048 bit key and a SHA256 digest.
   */
  public const GOOGLE_KEY_ALGORITHM_RSA_SIGN_PKCS1_2048_SHA256 = 'RSA_SIGN_PKCS1_2048_SHA256';
  /**
   * RSASSA-PKCS1-v1_5 with a 3072 bit key and a SHA256 digest.
   */
  public const GOOGLE_KEY_ALGORITHM_RSA_SIGN_PKCS1_3072_SHA256 = 'RSA_SIGN_PKCS1_3072_SHA256';
  /**
   * RSASSA-PKCS1-v1_5 with a 4096 bit key and a SHA256 digest.
   */
  public const GOOGLE_KEY_ALGORITHM_RSA_SIGN_PKCS1_4096_SHA256 = 'RSA_SIGN_PKCS1_4096_SHA256';
  /**
   * RSASSA-PKCS1-v1_5 with a 4096 bit key and a SHA512 digest.
   */
  public const GOOGLE_KEY_ALGORITHM_RSA_SIGN_PKCS1_4096_SHA512 = 'RSA_SIGN_PKCS1_4096_SHA512';
  /**
   * RSASSA-PKCS1-v1_5 signing without encoding, with a 2048 bit key.
   */
  public const GOOGLE_KEY_ALGORITHM_RSA_SIGN_RAW_PKCS1_2048 = 'RSA_SIGN_RAW_PKCS1_2048';
  /**
   * RSASSA-PKCS1-v1_5 signing without encoding, with a 3072 bit key.
   */
  public const GOOGLE_KEY_ALGORITHM_RSA_SIGN_RAW_PKCS1_3072 = 'RSA_SIGN_RAW_PKCS1_3072';
  /**
   * RSASSA-PKCS1-v1_5 signing without encoding, with a 4096 bit key.
   */
  public const GOOGLE_KEY_ALGORITHM_RSA_SIGN_RAW_PKCS1_4096 = 'RSA_SIGN_RAW_PKCS1_4096';
  /**
   * RSAES-OAEP 2048 bit key with a SHA256 digest.
   */
  public const GOOGLE_KEY_ALGORITHM_RSA_DECRYPT_OAEP_2048_SHA256 = 'RSA_DECRYPT_OAEP_2048_SHA256';
  /**
   * RSAES-OAEP 3072 bit key with a SHA256 digest.
   */
  public const GOOGLE_KEY_ALGORITHM_RSA_DECRYPT_OAEP_3072_SHA256 = 'RSA_DECRYPT_OAEP_3072_SHA256';
  /**
   * RSAES-OAEP 4096 bit key with a SHA256 digest.
   */
  public const GOOGLE_KEY_ALGORITHM_RSA_DECRYPT_OAEP_4096_SHA256 = 'RSA_DECRYPT_OAEP_4096_SHA256';
  /**
   * RSAES-OAEP 4096 bit key with a SHA512 digest.
   */
  public const GOOGLE_KEY_ALGORITHM_RSA_DECRYPT_OAEP_4096_SHA512 = 'RSA_DECRYPT_OAEP_4096_SHA512';
  /**
   * RSAES-OAEP 2048 bit key with a SHA1 digest.
   */
  public const GOOGLE_KEY_ALGORITHM_RSA_DECRYPT_OAEP_2048_SHA1 = 'RSA_DECRYPT_OAEP_2048_SHA1';
  /**
   * RSAES-OAEP 3072 bit key with a SHA1 digest.
   */
  public const GOOGLE_KEY_ALGORITHM_RSA_DECRYPT_OAEP_3072_SHA1 = 'RSA_DECRYPT_OAEP_3072_SHA1';
  /**
   * RSAES-OAEP 4096 bit key with a SHA1 digest.
   */
  public const GOOGLE_KEY_ALGORITHM_RSA_DECRYPT_OAEP_4096_SHA1 = 'RSA_DECRYPT_OAEP_4096_SHA1';
  /**
   * ECDSA on the NIST P-256 curve with a SHA256 digest. Other hash functions
   * can also be used: https://cloud.google.com/kms/docs/create-validate-
   * signatures#ecdsa_support_for_other_hash_algorithms
   */
  public const GOOGLE_KEY_ALGORITHM_EC_SIGN_P256_SHA256 = 'EC_SIGN_P256_SHA256';
  /**
   * ECDSA on the NIST P-384 curve with a SHA384 digest. Other hash functions
   * can also be used: https://cloud.google.com/kms/docs/create-validate-
   * signatures#ecdsa_support_for_other_hash_algorithms
   */
  public const GOOGLE_KEY_ALGORITHM_EC_SIGN_P384_SHA384 = 'EC_SIGN_P384_SHA384';
  /**
   * ECDSA on the non-NIST secp256k1 curve. This curve is only supported for HSM
   * protection level. Other hash functions can also be used:
   * https://cloud.google.com/kms/docs/create-validate-
   * signatures#ecdsa_support_for_other_hash_algorithms
   */
  public const GOOGLE_KEY_ALGORITHM_EC_SIGN_SECP256K1_SHA256 = 'EC_SIGN_SECP256K1_SHA256';
  /**
   * EdDSA on the Curve25519 in pure mode (taking data as input).
   */
  public const GOOGLE_KEY_ALGORITHM_EC_SIGN_ED25519 = 'EC_SIGN_ED25519';
  /**
   * HMAC-SHA256 signing with a 256 bit key.
   */
  public const GOOGLE_KEY_ALGORITHM_HMAC_SHA256 = 'HMAC_SHA256';
  /**
   * HMAC-SHA1 signing with a 160 bit key.
   */
  public const GOOGLE_KEY_ALGORITHM_HMAC_SHA1 = 'HMAC_SHA1';
  /**
   * HMAC-SHA384 signing with a 384 bit key.
   */
  public const GOOGLE_KEY_ALGORITHM_HMAC_SHA384 = 'HMAC_SHA384';
  /**
   * HMAC-SHA512 signing with a 512 bit key.
   */
  public const GOOGLE_KEY_ALGORITHM_HMAC_SHA512 = 'HMAC_SHA512';
  /**
   * HMAC-SHA224 signing with a 224 bit key.
   */
  public const GOOGLE_KEY_ALGORITHM_HMAC_SHA224 = 'HMAC_SHA224';
  /**
   * Algorithm representing symmetric encryption by an external key manager.
   */
  public const GOOGLE_KEY_ALGORITHM_EXTERNAL_SYMMETRIC_ENCRYPTION = 'EXTERNAL_SYMMETRIC_ENCRYPTION';
  /**
   * ML-KEM-768 (FIPS 203)
   */
  public const GOOGLE_KEY_ALGORITHM_ML_KEM_768 = 'ML_KEM_768';
  /**
   * ML-KEM-1024 (FIPS 203)
   */
  public const GOOGLE_KEY_ALGORITHM_ML_KEM_1024 = 'ML_KEM_1024';
  /**
   * X-Wing hybrid KEM combining ML-KEM-768 with X25519 following
   * datatracker.ietf.org/doc/draft-connolly-cfrg-xwing-kem/.
   */
  public const GOOGLE_KEY_ALGORITHM_KEM_XWING = 'KEM_XWING';
  /**
   * The post-quantum Module-Lattice-Based Digital Signature Algorithm, at
   * security level 1. Randomized version.
   */
  public const GOOGLE_KEY_ALGORITHM_PQ_SIGN_ML_DSA_44 = 'PQ_SIGN_ML_DSA_44';
  /**
   * The post-quantum Module-Lattice-Based Digital Signature Algorithm, at
   * security level 3. Randomized version.
   */
  public const GOOGLE_KEY_ALGORITHM_PQ_SIGN_ML_DSA_65 = 'PQ_SIGN_ML_DSA_65';
  /**
   * The post-quantum Module-Lattice-Based Digital Signature Algorithm, at
   * security level 5. Randomized version.
   */
  public const GOOGLE_KEY_ALGORITHM_PQ_SIGN_ML_DSA_87 = 'PQ_SIGN_ML_DSA_87';
  /**
   * The post-quantum stateless hash-based digital signature algorithm, at
   * security level 1. Randomized version.
   */
  public const GOOGLE_KEY_ALGORITHM_PQ_SIGN_SLH_DSA_SHA2_128S = 'PQ_SIGN_SLH_DSA_SHA2_128S';
  /**
   * The post-quantum stateless hash-based digital signature algorithm, at
   * security level 1. Randomized pre-hash version supporting SHA256 digests.
   */
  public const GOOGLE_KEY_ALGORITHM_PQ_SIGN_HASH_SLH_DSA_SHA2_128S_SHA256 = 'PQ_SIGN_HASH_SLH_DSA_SHA2_128S_SHA256';
  /**
   * The post-quantum Module-Lattice-Based Digital Signature Algorithm, at
   * security level 1. Randomized version supporting externally-computed message
   * representatives.
   */
  public const GOOGLE_KEY_ALGORITHM_PQ_SIGN_ML_DSA_44_EXTERNAL_MU = 'PQ_SIGN_ML_DSA_44_EXTERNAL_MU';
  /**
   * The post-quantum Module-Lattice-Based Digital Signature Algorithm, at
   * security level 3. Randomized version supporting externally-computed message
   * representatives.
   */
  public const GOOGLE_KEY_ALGORITHM_PQ_SIGN_ML_DSA_65_EXTERNAL_MU = 'PQ_SIGN_ML_DSA_65_EXTERNAL_MU';
  /**
   * The post-quantum Module-Lattice-Based Digital Signature Algorithm, at
   * security level 5. Randomized version supporting externally-computed message
   * representatives.
   */
  public const GOOGLE_KEY_ALGORITHM_PQ_SIGN_ML_DSA_87_EXTERNAL_MU = 'PQ_SIGN_ML_DSA_87_EXTERNAL_MU';
  /**
   * The resource name of the customer CryptoKeyVersion used for signing.
   *
   * @var string
   */
  public $customerKmsKeyVersion;
  /**
   * The hashing algorithm used for signature verification. It will only be
   * present in the case of Google managed keys.
   *
   * @var string
   */
  public $googleKeyAlgorithm;
  /**
   * The public key for the Google default signing, encoded in PEM format. The
   * signature was created using a private key which may be verified using this
   * public key.
   *
   * @var string
   */
  public $googlePublicKeyPem;
  /**
   * The ApprovalRequest that is serialized without the SignatureInfo message
   * field. This data is used with the hashing algorithm to generate the digital
   * signature, and it can be used for signature verification.
   *
   * @var string
   */
  public $serializedApprovalRequest;
  /**
   * The digital signature.
   *
   * @var string
   */
  public $signature;

  /**
   * The resource name of the customer CryptoKeyVersion used for signing.
   *
   * @param string $customerKmsKeyVersion
   */
  public function setCustomerKmsKeyVersion($customerKmsKeyVersion)
  {
    $this->customerKmsKeyVersion = $customerKmsKeyVersion;
  }
  /**
   * @return string
   */
  public function getCustomerKmsKeyVersion()
  {
    return $this->customerKmsKeyVersion;
  }
  /**
   * The hashing algorithm used for signature verification. It will only be
   * present in the case of Google managed keys.
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
   * @param self::GOOGLE_KEY_ALGORITHM_* $googleKeyAlgorithm
   */
  public function setGoogleKeyAlgorithm($googleKeyAlgorithm)
  {
    $this->googleKeyAlgorithm = $googleKeyAlgorithm;
  }
  /**
   * @return self::GOOGLE_KEY_ALGORITHM_*
   */
  public function getGoogleKeyAlgorithm()
  {
    return $this->googleKeyAlgorithm;
  }
  /**
   * The public key for the Google default signing, encoded in PEM format. The
   * signature was created using a private key which may be verified using this
   * public key.
   *
   * @param string $googlePublicKeyPem
   */
  public function setGooglePublicKeyPem($googlePublicKeyPem)
  {
    $this->googlePublicKeyPem = $googlePublicKeyPem;
  }
  /**
   * @return string
   */
  public function getGooglePublicKeyPem()
  {
    return $this->googlePublicKeyPem;
  }
  /**
   * The ApprovalRequest that is serialized without the SignatureInfo message
   * field. This data is used with the hashing algorithm to generate the digital
   * signature, and it can be used for signature verification.
   *
   * @param string $serializedApprovalRequest
   */
  public function setSerializedApprovalRequest($serializedApprovalRequest)
  {
    $this->serializedApprovalRequest = $serializedApprovalRequest;
  }
  /**
   * @return string
   */
  public function getSerializedApprovalRequest()
  {
    return $this->serializedApprovalRequest;
  }
  /**
   * The digital signature.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SignatureInfo::class, 'Google_Service_AccessApproval_SignatureInfo');
