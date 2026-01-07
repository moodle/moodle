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

namespace Google\Service\Logging;

class CmekSettings extends \Google\Model
{
  /**
   * Optional. The resource name for the configured Cloud KMS key.KMS key name
   * format: "projects/[PROJECT_ID]/locations/[LOCATION]/keyRings/[KEYRING]/cryp
   * toKeys/[KEY]" For example:"projects/my-project/locations/us-
   * central1/keyRings/my-ring/cryptoKeys/my-key"To enable CMEK for the Log
   * Router, set this field to a valid kms_key_name for which the associated
   * service account has the needed cloudkms.cryptoKeyEncrypterDecrypter roles
   * assigned for the key.The Cloud KMS key used by the Log Router can be
   * updated by changing the kms_key_name to a new valid key name or disabled by
   * setting the key name to an empty string. Encryption operations that are in
   * progress will be completed with the key that was in use when they started.
   * Decryption operations will be completed using the key that was used at the
   * time of encryption unless access to that key has been revoked.To disable
   * CMEK for the Log Router, set this field to an empty string.See Enabling
   * CMEK for Log Router (https://cloud.google.com/logging/docs/routing/managed-
   * encryption) for more information.
   *
   * @var string
   */
  public $kmsKeyName;
  /**
   * Output only. The CryptoKeyVersion resource name for the configured Cloud
   * KMS key.KMS key name format: "projects/[PROJECT_ID]/locations/[LOCATION]/ke
   * yRings/[KEYRING]/cryptoKeys/[KEY]/cryptoKeyVersions/[VERSION]" For
   * example:"projects/my-project/locations/us-central1/keyRings/my-
   * ring/cryptoKeys/my-key/cryptoKeyVersions/1"This is a read-only field used
   * to convey the specific configured CryptoKeyVersion of kms_key that has been
   * configured. It will be populated in cases where the CMEK settings are bound
   * to a single key version.If this field is populated, the kms_key is tied to
   * a specific CryptoKeyVersion.
   *
   * @var string
   */
  public $kmsKeyVersionName;
  /**
   * Output only. The resource name of the CMEK settings.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The service account that will be used by the Log Router to
   * access your Cloud KMS key.Before enabling CMEK for Log Router, you must
   * first assign the cloudkms.cryptoKeyEncrypterDecrypter role to the service
   * account that the Log Router will use to access your Cloud KMS key. Use
   * GetCmekSettings to obtain the service account ID.See Enabling CMEK for Log
   * Router (https://cloud.google.com/logging/docs/routing/managed-encryption)
   * for more information.
   *
   * @var string
   */
  public $serviceAccountId;

  /**
   * Optional. The resource name for the configured Cloud KMS key.KMS key name
   * format: "projects/[PROJECT_ID]/locations/[LOCATION]/keyRings/[KEYRING]/cryp
   * toKeys/[KEY]" For example:"projects/my-project/locations/us-
   * central1/keyRings/my-ring/cryptoKeys/my-key"To enable CMEK for the Log
   * Router, set this field to a valid kms_key_name for which the associated
   * service account has the needed cloudkms.cryptoKeyEncrypterDecrypter roles
   * assigned for the key.The Cloud KMS key used by the Log Router can be
   * updated by changing the kms_key_name to a new valid key name or disabled by
   * setting the key name to an empty string. Encryption operations that are in
   * progress will be completed with the key that was in use when they started.
   * Decryption operations will be completed using the key that was used at the
   * time of encryption unless access to that key has been revoked.To disable
   * CMEK for the Log Router, set this field to an empty string.See Enabling
   * CMEK for Log Router (https://cloud.google.com/logging/docs/routing/managed-
   * encryption) for more information.
   *
   * @param string $kmsKeyName
   */
  public function setKmsKeyName($kmsKeyName)
  {
    $this->kmsKeyName = $kmsKeyName;
  }
  /**
   * @return string
   */
  public function getKmsKeyName()
  {
    return $this->kmsKeyName;
  }
  /**
   * Output only. The CryptoKeyVersion resource name for the configured Cloud
   * KMS key.KMS key name format: "projects/[PROJECT_ID]/locations/[LOCATION]/ke
   * yRings/[KEYRING]/cryptoKeys/[KEY]/cryptoKeyVersions/[VERSION]" For
   * example:"projects/my-project/locations/us-central1/keyRings/my-
   * ring/cryptoKeys/my-key/cryptoKeyVersions/1"This is a read-only field used
   * to convey the specific configured CryptoKeyVersion of kms_key that has been
   * configured. It will be populated in cases where the CMEK settings are bound
   * to a single key version.If this field is populated, the kms_key is tied to
   * a specific CryptoKeyVersion.
   *
   * @param string $kmsKeyVersionName
   */
  public function setKmsKeyVersionName($kmsKeyVersionName)
  {
    $this->kmsKeyVersionName = $kmsKeyVersionName;
  }
  /**
   * @return string
   */
  public function getKmsKeyVersionName()
  {
    return $this->kmsKeyVersionName;
  }
  /**
   * Output only. The resource name of the CMEK settings.
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
   * Output only. The service account that will be used by the Log Router to
   * access your Cloud KMS key.Before enabling CMEK for Log Router, you must
   * first assign the cloudkms.cryptoKeyEncrypterDecrypter role to the service
   * account that the Log Router will use to access your Cloud KMS key. Use
   * GetCmekSettings to obtain the service account ID.See Enabling CMEK for Log
   * Router (https://cloud.google.com/logging/docs/routing/managed-encryption)
   * for more information.
   *
   * @param string $serviceAccountId
   */
  public function setServiceAccountId($serviceAccountId)
  {
    $this->serviceAccountId = $serviceAccountId;
  }
  /**
   * @return string
   */
  public function getServiceAccountId()
  {
    return $this->serviceAccountId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CmekSettings::class, 'Google_Service_Logging_CmekSettings');
