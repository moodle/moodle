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

class GoogleCloudKmsV1CryptoKeyVersion extends \Google\Model
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
   * Not specified.
   */
  public const STATE_CRYPTO_KEY_VERSION_STATE_UNSPECIFIED = 'CRYPTO_KEY_VERSION_STATE_UNSPECIFIED';
  /**
   * This version is still being generated. It may not be used, enabled,
   * disabled, or destroyed yet. Cloud KMS will automatically mark this version
   * ENABLED as soon as the version is ready.
   */
  public const STATE_PENDING_GENERATION = 'PENDING_GENERATION';
  /**
   * This version may be used for cryptographic operations.
   */
  public const STATE_ENABLED = 'ENABLED';
  /**
   * This version may not be used, but the key material is still available, and
   * the version can be placed back into the ENABLED state.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * The key material of this version is destroyed and no longer stored. This
   * version may only become ENABLED again if this version is reimport_eligible
   * and the original key material is reimported with a call to
   * KeyManagementService.ImportCryptoKeyVersion.
   */
  public const STATE_DESTROYED = 'DESTROYED';
  /**
   * This version is scheduled for destruction, and will be destroyed soon. Call
   * RestoreCryptoKeyVersion to put it back into the DISABLED state.
   */
  public const STATE_DESTROY_SCHEDULED = 'DESTROY_SCHEDULED';
  /**
   * This version is still being imported. It may not be used, enabled,
   * disabled, or destroyed yet. Cloud KMS will automatically mark this version
   * ENABLED as soon as the version is ready.
   */
  public const STATE_PENDING_IMPORT = 'PENDING_IMPORT';
  /**
   * This version was not imported successfully. It may not be used, enabled,
   * disabled, or destroyed. The submitted key material has been discarded.
   * Additional details can be found in CryptoKeyVersion.import_failure_reason.
   */
  public const STATE_IMPORT_FAILED = 'IMPORT_FAILED';
  /**
   * This version was not generated successfully. It may not be used, enabled,
   * disabled, or destroyed. Additional details can be found in
   * CryptoKeyVersion.generation_failure_reason.
   */
  public const STATE_GENERATION_FAILED = 'GENERATION_FAILED';
  /**
   * This version was destroyed, and it may not be used or enabled again. Cloud
   * KMS is waiting for the corresponding key material residing in an external
   * key manager to be destroyed.
   */
  public const STATE_PENDING_EXTERNAL_DESTRUCTION = 'PENDING_EXTERNAL_DESTRUCTION';
  /**
   * This version was destroyed, and it may not be used or enabled again.
   * However, Cloud KMS could not confirm that the corresponding key material
   * residing in an external key manager was destroyed. Additional details can
   * be found in CryptoKeyVersion.external_destruction_failure_reason.
   */
  public const STATE_EXTERNAL_DESTRUCTION_FAILED = 'EXTERNAL_DESTRUCTION_FAILED';
  /**
   * Output only. The CryptoKeyVersionAlgorithm that this CryptoKeyVersion
   * supports.
   *
   * @var string
   */
  public $algorithm;
  protected $attestationType = GoogleCloudKmsV1KeyOperationAttestation::class;
  protected $attestationDataType = '';
  /**
   * Output only. The time at which this CryptoKeyVersion was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The time this CryptoKeyVersion's key material was destroyed.
   * Only present if state is DESTROYED.
   *
   * @var string
   */
  public $destroyEventTime;
  /**
   * Output only. The time this CryptoKeyVersion's key material is scheduled for
   * destruction. Only present if state is DESTROY_SCHEDULED.
   *
   * @var string
   */
  public $destroyTime;
  /**
   * Output only. The root cause of the most recent external destruction
   * failure. Only present if state is EXTERNAL_DESTRUCTION_FAILED.
   *
   * @var string
   */
  public $externalDestructionFailureReason;
  protected $externalProtectionLevelOptionsType = GoogleCloudKmsV1ExternalProtectionLevelOptions::class;
  protected $externalProtectionLevelOptionsDataType = '';
  /**
   * Output only. The time this CryptoKeyVersion's key material was generated.
   *
   * @var string
   */
  public $generateTime;
  /**
   * Output only. The root cause of the most recent generation failure. Only
   * present if state is GENERATION_FAILED.
   *
   * @var string
   */
  public $generationFailureReason;
  /**
   * Output only. The root cause of the most recent import failure. Only present
   * if state is IMPORT_FAILED.
   *
   * @var string
   */
  public $importFailureReason;
  /**
   * Output only. The name of the ImportJob used in the most recent import of
   * this CryptoKeyVersion. Only present if the underlying key material was
   * imported.
   *
   * @var string
   */
  public $importJob;
  /**
   * Output only. The time at which this CryptoKeyVersion's key material was
   * most recently imported.
   *
   * @var string
   */
  public $importTime;
  /**
   * Output only. The resource name for this CryptoKeyVersion in the format
   * `projects/locations/keyRings/cryptoKeys/cryptoKeyVersions`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The ProtectionLevel describing how crypto operations are
   * performed with this CryptoKeyVersion.
   *
   * @var string
   */
  public $protectionLevel;
  /**
   * Output only. Whether or not this key version is eligible for reimport, by
   * being specified as a target in
   * ImportCryptoKeyVersionRequest.crypto_key_version.
   *
   * @var bool
   */
  public $reimportEligible;
  /**
   * The current state of the CryptoKeyVersion.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The CryptoKeyVersionAlgorithm that this CryptoKeyVersion
   * supports.
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
   * Output only. Statement that was generated and signed by the HSM at key
   * creation time. Use this statement to verify attributes of the key as stored
   * on the HSM, independently of Google. Only provided for key versions with
   * protection_level HSM.
   *
   * @param GoogleCloudKmsV1KeyOperationAttestation $attestation
   */
  public function setAttestation(GoogleCloudKmsV1KeyOperationAttestation $attestation)
  {
    $this->attestation = $attestation;
  }
  /**
   * @return GoogleCloudKmsV1KeyOperationAttestation
   */
  public function getAttestation()
  {
    return $this->attestation;
  }
  /**
   * Output only. The time at which this CryptoKeyVersion was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. The time this CryptoKeyVersion's key material was destroyed.
   * Only present if state is DESTROYED.
   *
   * @param string $destroyEventTime
   */
  public function setDestroyEventTime($destroyEventTime)
  {
    $this->destroyEventTime = $destroyEventTime;
  }
  /**
   * @return string
   */
  public function getDestroyEventTime()
  {
    return $this->destroyEventTime;
  }
  /**
   * Output only. The time this CryptoKeyVersion's key material is scheduled for
   * destruction. Only present if state is DESTROY_SCHEDULED.
   *
   * @param string $destroyTime
   */
  public function setDestroyTime($destroyTime)
  {
    $this->destroyTime = $destroyTime;
  }
  /**
   * @return string
   */
  public function getDestroyTime()
  {
    return $this->destroyTime;
  }
  /**
   * Output only. The root cause of the most recent external destruction
   * failure. Only present if state is EXTERNAL_DESTRUCTION_FAILED.
   *
   * @param string $externalDestructionFailureReason
   */
  public function setExternalDestructionFailureReason($externalDestructionFailureReason)
  {
    $this->externalDestructionFailureReason = $externalDestructionFailureReason;
  }
  /**
   * @return string
   */
  public function getExternalDestructionFailureReason()
  {
    return $this->externalDestructionFailureReason;
  }
  /**
   * ExternalProtectionLevelOptions stores a group of additional fields for
   * configuring a CryptoKeyVersion that are specific to the EXTERNAL protection
   * level and EXTERNAL_VPC protection levels.
   *
   * @param GoogleCloudKmsV1ExternalProtectionLevelOptions $externalProtectionLevelOptions
   */
  public function setExternalProtectionLevelOptions(GoogleCloudKmsV1ExternalProtectionLevelOptions $externalProtectionLevelOptions)
  {
    $this->externalProtectionLevelOptions = $externalProtectionLevelOptions;
  }
  /**
   * @return GoogleCloudKmsV1ExternalProtectionLevelOptions
   */
  public function getExternalProtectionLevelOptions()
  {
    return $this->externalProtectionLevelOptions;
  }
  /**
   * Output only. The time this CryptoKeyVersion's key material was generated.
   *
   * @param string $generateTime
   */
  public function setGenerateTime($generateTime)
  {
    $this->generateTime = $generateTime;
  }
  /**
   * @return string
   */
  public function getGenerateTime()
  {
    return $this->generateTime;
  }
  /**
   * Output only. The root cause of the most recent generation failure. Only
   * present if state is GENERATION_FAILED.
   *
   * @param string $generationFailureReason
   */
  public function setGenerationFailureReason($generationFailureReason)
  {
    $this->generationFailureReason = $generationFailureReason;
  }
  /**
   * @return string
   */
  public function getGenerationFailureReason()
  {
    return $this->generationFailureReason;
  }
  /**
   * Output only. The root cause of the most recent import failure. Only present
   * if state is IMPORT_FAILED.
   *
   * @param string $importFailureReason
   */
  public function setImportFailureReason($importFailureReason)
  {
    $this->importFailureReason = $importFailureReason;
  }
  /**
   * @return string
   */
  public function getImportFailureReason()
  {
    return $this->importFailureReason;
  }
  /**
   * Output only. The name of the ImportJob used in the most recent import of
   * this CryptoKeyVersion. Only present if the underlying key material was
   * imported.
   *
   * @param string $importJob
   */
  public function setImportJob($importJob)
  {
    $this->importJob = $importJob;
  }
  /**
   * @return string
   */
  public function getImportJob()
  {
    return $this->importJob;
  }
  /**
   * Output only. The time at which this CryptoKeyVersion's key material was
   * most recently imported.
   *
   * @param string $importTime
   */
  public function setImportTime($importTime)
  {
    $this->importTime = $importTime;
  }
  /**
   * @return string
   */
  public function getImportTime()
  {
    return $this->importTime;
  }
  /**
   * Output only. The resource name for this CryptoKeyVersion in the format
   * `projects/locations/keyRings/cryptoKeys/cryptoKeyVersions`.
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
   * Output only. The ProtectionLevel describing how crypto operations are
   * performed with this CryptoKeyVersion.
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
   * Output only. Whether or not this key version is eligible for reimport, by
   * being specified as a target in
   * ImportCryptoKeyVersionRequest.crypto_key_version.
   *
   * @param bool $reimportEligible
   */
  public function setReimportEligible($reimportEligible)
  {
    $this->reimportEligible = $reimportEligible;
  }
  /**
   * @return bool
   */
  public function getReimportEligible()
  {
    return $this->reimportEligible;
  }
  /**
   * The current state of the CryptoKeyVersion.
   *
   * Accepted values: CRYPTO_KEY_VERSION_STATE_UNSPECIFIED, PENDING_GENERATION,
   * ENABLED, DISABLED, DESTROYED, DESTROY_SCHEDULED, PENDING_IMPORT,
   * IMPORT_FAILED, GENERATION_FAILED, PENDING_EXTERNAL_DESTRUCTION,
   * EXTERNAL_DESTRUCTION_FAILED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudKmsV1CryptoKeyVersion::class, 'Google_Service_Kmsinventory_GoogleCloudKmsV1CryptoKeyVersion');
