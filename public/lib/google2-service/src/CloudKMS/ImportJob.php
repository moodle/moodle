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

class ImportJob extends \Google\Model
{
  /**
   * Not specified.
   */
  public const IMPORT_METHOD_IMPORT_METHOD_UNSPECIFIED = 'IMPORT_METHOD_UNSPECIFIED';
  /**
   * This ImportMethod represents the CKM_RSA_AES_KEY_WRAP key wrapping scheme
   * defined in the PKCS #11 standard. In summary, this involves wrapping the
   * raw key with an ephemeral AES key, and wrapping the ephemeral AES key with
   * a 3072 bit RSA key. For more details, see [RSA AES key wrap
   * mechanism](http://docs.oasis-open.org/pkcs11/pkcs11-
   * curr/v2.40/cos01/pkcs11-curr-v2.40-cos01.html#_Toc408226908).
   */
  public const IMPORT_METHOD_RSA_OAEP_3072_SHA1_AES_256 = 'RSA_OAEP_3072_SHA1_AES_256';
  /**
   * This ImportMethod represents the CKM_RSA_AES_KEY_WRAP key wrapping scheme
   * defined in the PKCS #11 standard. In summary, this involves wrapping the
   * raw key with an ephemeral AES key, and wrapping the ephemeral AES key with
   * a 4096 bit RSA key. For more details, see [RSA AES key wrap
   * mechanism](http://docs.oasis-open.org/pkcs11/pkcs11-
   * curr/v2.40/cos01/pkcs11-curr-v2.40-cos01.html#_Toc408226908).
   */
  public const IMPORT_METHOD_RSA_OAEP_4096_SHA1_AES_256 = 'RSA_OAEP_4096_SHA1_AES_256';
  /**
   * This ImportMethod represents the CKM_RSA_AES_KEY_WRAP key wrapping scheme
   * defined in the PKCS #11 standard. In summary, this involves wrapping the
   * raw key with an ephemeral AES key, and wrapping the ephemeral AES key with
   * a 3072 bit RSA key. For more details, see [RSA AES key wrap
   * mechanism](http://docs.oasis-open.org/pkcs11/pkcs11-
   * curr/v2.40/cos01/pkcs11-curr-v2.40-cos01.html#_Toc408226908).
   */
  public const IMPORT_METHOD_RSA_OAEP_3072_SHA256_AES_256 = 'RSA_OAEP_3072_SHA256_AES_256';
  /**
   * This ImportMethod represents the CKM_RSA_AES_KEY_WRAP key wrapping scheme
   * defined in the PKCS #11 standard. In summary, this involves wrapping the
   * raw key with an ephemeral AES key, and wrapping the ephemeral AES key with
   * a 4096 bit RSA key. For more details, see [RSA AES key wrap
   * mechanism](http://docs.oasis-open.org/pkcs11/pkcs11-
   * curr/v2.40/cos01/pkcs11-curr-v2.40-cos01.html#_Toc408226908).
   */
  public const IMPORT_METHOD_RSA_OAEP_4096_SHA256_AES_256 = 'RSA_OAEP_4096_SHA256_AES_256';
  /**
   * This ImportMethod represents RSAES-OAEP with a 3072 bit RSA key. The key
   * material to be imported is wrapped directly with the RSA key. Due to
   * technical limitations of RSA wrapping, this method cannot be used to wrap
   * RSA keys for import.
   */
  public const IMPORT_METHOD_RSA_OAEP_3072_SHA256 = 'RSA_OAEP_3072_SHA256';
  /**
   * This ImportMethod represents RSAES-OAEP with a 4096 bit RSA key. The key
   * material to be imported is wrapped directly with the RSA key. Due to
   * technical limitations of RSA wrapping, this method cannot be used to wrap
   * RSA keys for import.
   */
  public const IMPORT_METHOD_RSA_OAEP_4096_SHA256 = 'RSA_OAEP_4096_SHA256';
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
  public const STATE_IMPORT_JOB_STATE_UNSPECIFIED = 'IMPORT_JOB_STATE_UNSPECIFIED';
  /**
   * The wrapping key for this job is still being generated. It may not be used.
   * Cloud KMS will automatically mark this job as ACTIVE as soon as the
   * wrapping key is generated.
   */
  public const STATE_PENDING_GENERATION = 'PENDING_GENERATION';
  /**
   * This job may be used in CreateCryptoKey and CreateCryptoKeyVersion
   * requests.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * This job can no longer be used and may not leave this state once entered.
   */
  public const STATE_EXPIRED = 'EXPIRED';
  protected $attestationType = KeyOperationAttestation::class;
  protected $attestationDataType = '';
  /**
   * Output only. The time at which this ImportJob was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Immutable. The resource name of the backend environment where the key
   * material for the wrapping key resides and where all related cryptographic
   * operations are performed. Currently, this field is only populated for keys
   * stored in HSM_SINGLE_TENANT. Note, this list is non-exhaustive and may
   * apply to additional ProtectionLevels in the future.
   *
   * @var string
   */
  public $cryptoKeyBackend;
  /**
   * Output only. The time this ImportJob expired. Only present if state is
   * EXPIRED.
   *
   * @var string
   */
  public $expireEventTime;
  /**
   * Output only. The time at which this ImportJob is scheduled for expiration
   * and can no longer be used to import key material.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Output only. The time this ImportJob's key material was generated.
   *
   * @var string
   */
  public $generateTime;
  /**
   * Required. Immutable. The wrapping method to be used for incoming key
   * material.
   *
   * @var string
   */
  public $importMethod;
  /**
   * Output only. The resource name for this ImportJob in the format
   * `projects/locations/keyRings/importJobs`.
   *
   * @var string
   */
  public $name;
  /**
   * Required. Immutable. The protection level of the ImportJob. This must match
   * the protection_level of the version_template on the CryptoKey you attempt
   * to import into.
   *
   * @var string
   */
  public $protectionLevel;
  protected $publicKeyType = WrappingPublicKey::class;
  protected $publicKeyDataType = '';
  /**
   * Output only. The current state of the ImportJob, indicating if it can be
   * used.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. Statement that was generated and signed by the key creator
   * (for example, an HSM) at key creation time. Use this statement to verify
   * attributes of the key as stored on the HSM, independently of Google. Only
   * present if the chosen ImportMethod is one with a protection level of HSM.
   *
   * @param KeyOperationAttestation $attestation
   */
  public function setAttestation(KeyOperationAttestation $attestation)
  {
    $this->attestation = $attestation;
  }
  /**
   * @return KeyOperationAttestation
   */
  public function getAttestation()
  {
    return $this->attestation;
  }
  /**
   * Output only. The time at which this ImportJob was created.
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
   * Immutable. The resource name of the backend environment where the key
   * material for the wrapping key resides and where all related cryptographic
   * operations are performed. Currently, this field is only populated for keys
   * stored in HSM_SINGLE_TENANT. Note, this list is non-exhaustive and may
   * apply to additional ProtectionLevels in the future.
   *
   * @param string $cryptoKeyBackend
   */
  public function setCryptoKeyBackend($cryptoKeyBackend)
  {
    $this->cryptoKeyBackend = $cryptoKeyBackend;
  }
  /**
   * @return string
   */
  public function getCryptoKeyBackend()
  {
    return $this->cryptoKeyBackend;
  }
  /**
   * Output only. The time this ImportJob expired. Only present if state is
   * EXPIRED.
   *
   * @param string $expireEventTime
   */
  public function setExpireEventTime($expireEventTime)
  {
    $this->expireEventTime = $expireEventTime;
  }
  /**
   * @return string
   */
  public function getExpireEventTime()
  {
    return $this->expireEventTime;
  }
  /**
   * Output only. The time at which this ImportJob is scheduled for expiration
   * and can no longer be used to import key material.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Output only. The time this ImportJob's key material was generated.
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
   * Required. Immutable. The wrapping method to be used for incoming key
   * material.
   *
   * Accepted values: IMPORT_METHOD_UNSPECIFIED, RSA_OAEP_3072_SHA1_AES_256,
   * RSA_OAEP_4096_SHA1_AES_256, RSA_OAEP_3072_SHA256_AES_256,
   * RSA_OAEP_4096_SHA256_AES_256, RSA_OAEP_3072_SHA256, RSA_OAEP_4096_SHA256
   *
   * @param self::IMPORT_METHOD_* $importMethod
   */
  public function setImportMethod($importMethod)
  {
    $this->importMethod = $importMethod;
  }
  /**
   * @return self::IMPORT_METHOD_*
   */
  public function getImportMethod()
  {
    return $this->importMethod;
  }
  /**
   * Output only. The resource name for this ImportJob in the format
   * `projects/locations/keyRings/importJobs`.
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
   * Required. Immutable. The protection level of the ImportJob. This must match
   * the protection_level of the version_template on the CryptoKey you attempt
   * to import into.
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
   * Output only. The public key with which to wrap key material prior to
   * import. Only returned if state is ACTIVE.
   *
   * @param WrappingPublicKey $publicKey
   */
  public function setPublicKey(WrappingPublicKey $publicKey)
  {
    $this->publicKey = $publicKey;
  }
  /**
   * @return WrappingPublicKey
   */
  public function getPublicKey()
  {
    return $this->publicKey;
  }
  /**
   * Output only. The current state of the ImportJob, indicating if it can be
   * used.
   *
   * Accepted values: IMPORT_JOB_STATE_UNSPECIFIED, PENDING_GENERATION, ACTIVE,
   * EXPIRED
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
class_alias(ImportJob::class, 'Google_Service_CloudKMS_ImportJob');
