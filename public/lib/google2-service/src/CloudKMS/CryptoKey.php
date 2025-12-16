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

class CryptoKey extends \Google\Model
{
  /**
   * Not specified.
   */
  public const PURPOSE_CRYPTO_KEY_PURPOSE_UNSPECIFIED = 'CRYPTO_KEY_PURPOSE_UNSPECIFIED';
  /**
   * CryptoKeys with this purpose may be used with Encrypt and Decrypt.
   */
  public const PURPOSE_ENCRYPT_DECRYPT = 'ENCRYPT_DECRYPT';
  /**
   * CryptoKeys with this purpose may be used with AsymmetricSign and
   * GetPublicKey.
   */
  public const PURPOSE_ASYMMETRIC_SIGN = 'ASYMMETRIC_SIGN';
  /**
   * CryptoKeys with this purpose may be used with AsymmetricDecrypt and
   * GetPublicKey.
   */
  public const PURPOSE_ASYMMETRIC_DECRYPT = 'ASYMMETRIC_DECRYPT';
  /**
   * CryptoKeys with this purpose may be used with RawEncrypt and RawDecrypt.
   * This purpose is meant to be used for interoperable symmetric encryption and
   * does not support automatic CryptoKey rotation.
   */
  public const PURPOSE_RAW_ENCRYPT_DECRYPT = 'RAW_ENCRYPT_DECRYPT';
  /**
   * CryptoKeys with this purpose may be used with MacSign.
   */
  public const PURPOSE_MAC = 'MAC';
  /**
   * CryptoKeys with this purpose may be used with GetPublicKey and Decapsulate.
   */
  public const PURPOSE_KEY_ENCAPSULATION = 'KEY_ENCAPSULATION';
  /**
   * Output only. The time at which this CryptoKey was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Immutable. The resource name of the backend environment where the key
   * material for all CryptoKeyVersions associated with this CryptoKey reside
   * and where all related cryptographic operations are performed. Only
   * applicable if CryptoKeyVersions have a ProtectionLevel of EXTERNAL_VPC,
   * with the resource name in the format `projects/locations/ekmConnections`.
   * Only applicable if CryptoKeyVersions have a ProtectionLevel of
   * HSM_SINGLE_TENANT, with the resource name in the format
   * `projects/locations/singleTenantHsmInstances`. Note, this list is non-
   * exhaustive and may apply to additional ProtectionLevels in the future.
   *
   * @var string
   */
  public $cryptoKeyBackend;
  /**
   * Immutable. The period of time that versions of this key spend in the
   * DESTROY_SCHEDULED state before transitioning to DESTROYED. If not specified
   * at creation time, the default duration is 30 days.
   *
   * @var string
   */
  public $destroyScheduledDuration;
  /**
   * Immutable. Whether this key may contain imported versions only.
   *
   * @var bool
   */
  public $importOnly;
  protected $keyAccessJustificationsPolicyType = KeyAccessJustificationsPolicy::class;
  protected $keyAccessJustificationsPolicyDataType = '';
  /**
   * Labels with user-defined metadata. For more information, see [Labeling
   * Keys](https://cloud.google.com/kms/docs/labeling-keys).
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The resource name for this CryptoKey in the format
   * `projects/locations/keyRings/cryptoKeys`.
   *
   * @var string
   */
  public $name;
  /**
   * At next_rotation_time, the Key Management Service will automatically: 1.
   * Create a new version of this CryptoKey. 2. Mark the new version as primary.
   * Key rotations performed manually via CreateCryptoKeyVersion and
   * UpdateCryptoKeyPrimaryVersion do not affect next_rotation_time. Keys with
   * purpose ENCRYPT_DECRYPT support automatic rotation. For other keys, this
   * field must be omitted.
   *
   * @var string
   */
  public $nextRotationTime;
  protected $primaryType = CryptoKeyVersion::class;
  protected $primaryDataType = '';
  /**
   * Immutable. The immutable purpose of this CryptoKey.
   *
   * @var string
   */
  public $purpose;
  /**
   * next_rotation_time will be advanced by this period when the service
   * automatically rotates a key. Must be at least 24 hours and at most 876,000
   * hours. If rotation_period is set, next_rotation_time must also be set. Keys
   * with purpose ENCRYPT_DECRYPT support automatic rotation. For other keys,
   * this field must be omitted.
   *
   * @var string
   */
  public $rotationPeriod;
  protected $versionTemplateType = CryptoKeyVersionTemplate::class;
  protected $versionTemplateDataType = '';

  /**
   * Output only. The time at which this CryptoKey was created.
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
   * material for all CryptoKeyVersions associated with this CryptoKey reside
   * and where all related cryptographic operations are performed. Only
   * applicable if CryptoKeyVersions have a ProtectionLevel of EXTERNAL_VPC,
   * with the resource name in the format `projects/locations/ekmConnections`.
   * Only applicable if CryptoKeyVersions have a ProtectionLevel of
   * HSM_SINGLE_TENANT, with the resource name in the format
   * `projects/locations/singleTenantHsmInstances`. Note, this list is non-
   * exhaustive and may apply to additional ProtectionLevels in the future.
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
   * Immutable. The period of time that versions of this key spend in the
   * DESTROY_SCHEDULED state before transitioning to DESTROYED. If not specified
   * at creation time, the default duration is 30 days.
   *
   * @param string $destroyScheduledDuration
   */
  public function setDestroyScheduledDuration($destroyScheduledDuration)
  {
    $this->destroyScheduledDuration = $destroyScheduledDuration;
  }
  /**
   * @return string
   */
  public function getDestroyScheduledDuration()
  {
    return $this->destroyScheduledDuration;
  }
  /**
   * Immutable. Whether this key may contain imported versions only.
   *
   * @param bool $importOnly
   */
  public function setImportOnly($importOnly)
  {
    $this->importOnly = $importOnly;
  }
  /**
   * @return bool
   */
  public function getImportOnly()
  {
    return $this->importOnly;
  }
  /**
   * Optional. The policy used for Key Access Justifications Policy Enforcement.
   * If this field is present and this key is enrolled in Key Access
   * Justifications Policy Enforcement, the policy will be evaluated in encrypt,
   * decrypt, and sign operations, and the operation will fail if rejected by
   * the policy. The policy is defined by specifying zero or more allowed
   * justification codes. https://cloud.google.com/assured-workloads/key-access-
   * justifications/docs/justification-codes By default, this field is absent,
   * and all justification codes are allowed.
   *
   * @param KeyAccessJustificationsPolicy $keyAccessJustificationsPolicy
   */
  public function setKeyAccessJustificationsPolicy(KeyAccessJustificationsPolicy $keyAccessJustificationsPolicy)
  {
    $this->keyAccessJustificationsPolicy = $keyAccessJustificationsPolicy;
  }
  /**
   * @return KeyAccessJustificationsPolicy
   */
  public function getKeyAccessJustificationsPolicy()
  {
    return $this->keyAccessJustificationsPolicy;
  }
  /**
   * Labels with user-defined metadata. For more information, see [Labeling
   * Keys](https://cloud.google.com/kms/docs/labeling-keys).
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. The resource name for this CryptoKey in the format
   * `projects/locations/keyRings/cryptoKeys`.
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
   * At next_rotation_time, the Key Management Service will automatically: 1.
   * Create a new version of this CryptoKey. 2. Mark the new version as primary.
   * Key rotations performed manually via CreateCryptoKeyVersion and
   * UpdateCryptoKeyPrimaryVersion do not affect next_rotation_time. Keys with
   * purpose ENCRYPT_DECRYPT support automatic rotation. For other keys, this
   * field must be omitted.
   *
   * @param string $nextRotationTime
   */
  public function setNextRotationTime($nextRotationTime)
  {
    $this->nextRotationTime = $nextRotationTime;
  }
  /**
   * @return string
   */
  public function getNextRotationTime()
  {
    return $this->nextRotationTime;
  }
  /**
   * Output only. A copy of the "primary" CryptoKeyVersion that will be used by
   * Encrypt when this CryptoKey is given in EncryptRequest.name. The
   * CryptoKey's primary version can be updated via
   * UpdateCryptoKeyPrimaryVersion. Keys with purpose ENCRYPT_DECRYPT may have a
   * primary. For other keys, this field will be omitted.
   *
   * @param CryptoKeyVersion $primary
   */
  public function setPrimary(CryptoKeyVersion $primary)
  {
    $this->primary = $primary;
  }
  /**
   * @return CryptoKeyVersion
   */
  public function getPrimary()
  {
    return $this->primary;
  }
  /**
   * Immutable. The immutable purpose of this CryptoKey.
   *
   * Accepted values: CRYPTO_KEY_PURPOSE_UNSPECIFIED, ENCRYPT_DECRYPT,
   * ASYMMETRIC_SIGN, ASYMMETRIC_DECRYPT, RAW_ENCRYPT_DECRYPT, MAC,
   * KEY_ENCAPSULATION
   *
   * @param self::PURPOSE_* $purpose
   */
  public function setPurpose($purpose)
  {
    $this->purpose = $purpose;
  }
  /**
   * @return self::PURPOSE_*
   */
  public function getPurpose()
  {
    return $this->purpose;
  }
  /**
   * next_rotation_time will be advanced by this period when the service
   * automatically rotates a key. Must be at least 24 hours and at most 876,000
   * hours. If rotation_period is set, next_rotation_time must also be set. Keys
   * with purpose ENCRYPT_DECRYPT support automatic rotation. For other keys,
   * this field must be omitted.
   *
   * @param string $rotationPeriod
   */
  public function setRotationPeriod($rotationPeriod)
  {
    $this->rotationPeriod = $rotationPeriod;
  }
  /**
   * @return string
   */
  public function getRotationPeriod()
  {
    return $this->rotationPeriod;
  }
  /**
   * A template describing settings for new CryptoKeyVersion instances. The
   * properties of new CryptoKeyVersion instances created by either
   * CreateCryptoKeyVersion or auto-rotation are controlled by this template.
   *
   * @param CryptoKeyVersionTemplate $versionTemplate
   */
  public function setVersionTemplate(CryptoKeyVersionTemplate $versionTemplate)
  {
    $this->versionTemplate = $versionTemplate;
  }
  /**
   * @return CryptoKeyVersionTemplate
   */
  public function getVersionTemplate()
  {
    return $this->versionTemplate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CryptoKey::class, 'Google_Service_CloudKMS_CryptoKey');
