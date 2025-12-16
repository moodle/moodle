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

namespace Google\Service\Compute;

class CustomerEncryptionKey extends \Google\Model
{
  /**
   * The name of the encryption key that is stored in Google Cloud KMS. For
   * example:
   *
   * "kmsKeyName": "projects/kms_project_id/locations/region/keyRings/
   * key_region/cryptoKeys/key
   *
   * The fully-qualifed key name may be returned for resource GET requests. For
   * example:
   *
   * "kmsKeyName": "projects/kms_project_id/locations/region/keyRings/
   * key_region/cryptoKeys/key /cryptoKeyVersions/1
   *
   * @var string
   */
  public $kmsKeyName;
  /**
   * The service account being used for the encryption request for the given KMS
   * key. If absent, the Compute Engine default service account is used. For
   * example:
   *
   * "kmsKeyServiceAccount": "name@project_id.iam.gserviceaccount.com/
   *
   * @var string
   */
  public $kmsKeyServiceAccount;
  /**
   * Specifies a 256-bit customer-supplied encryption key, encoded in RFC 4648
   * base64 to either encrypt or decrypt this resource. You can provide either
   * the rawKey or thersaEncryptedKey. For example:
   *
   * "rawKey": "SGVsbG8gZnJvbSBHb29nbGUgQ2xvdWQgUGxhdGZvcm0="
   *
   * @var string
   */
  public $rawKey;
  /**
   * Specifies an RFC 4648 base64 encoded, RSA-wrapped 2048-bit customer-
   * supplied encryption key to either encrypt or decrypt this resource. You can
   * provide either the rawKey or thersaEncryptedKey. For example:
   *
   * "rsaEncryptedKey":
   * "ieCx/NcW06PcT7Ep1X6LUTc/hLvUDYyzSZPPVCVPTVEohpeHASqC8uw5TzyO9U+Fka9JFH
   * z0mBibXUInrC/jEk014kCK/NPjYgEMOyssZ4ZINPKxlUh2zn1bV+MCaTICrdmuSBTWlUUiFoD
   * D6PYznLwh8ZNdaheCeZ8ewEXgFQ8V+sDroLaN3Xs3MDTXQEMMoNUXMCZEIpg9Vtp9x2oe=="
   *
   * The key must meet the following requirements before you can provide it to
   * Compute Engine:         1. The key is wrapped using a RSA public key
   * certificate provided by     Google.     2. After being wrapped, the key
   * must be encoded in RFC 4648 base64     encoding.
   *
   * Gets the RSA public key certificate provided by Google at:
   *
   * https://cloud-certs.storage.googleapis.com/google-cloud-csek-ingress.pem
   *
   * @var string
   */
  public $rsaEncryptedKey;
  /**
   * [Output only] TheRFC 4648 base64 encoded SHA-256 hash of the customer-
   * supplied encryption key that protects this resource.
   *
   * @var string
   */
  public $sha256;

  /**
   * The name of the encryption key that is stored in Google Cloud KMS. For
   * example:
   *
   * "kmsKeyName": "projects/kms_project_id/locations/region/keyRings/
   * key_region/cryptoKeys/key
   *
   * The fully-qualifed key name may be returned for resource GET requests. For
   * example:
   *
   * "kmsKeyName": "projects/kms_project_id/locations/region/keyRings/
   * key_region/cryptoKeys/key /cryptoKeyVersions/1
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
   * The service account being used for the encryption request for the given KMS
   * key. If absent, the Compute Engine default service account is used. For
   * example:
   *
   * "kmsKeyServiceAccount": "name@project_id.iam.gserviceaccount.com/
   *
   * @param string $kmsKeyServiceAccount
   */
  public function setKmsKeyServiceAccount($kmsKeyServiceAccount)
  {
    $this->kmsKeyServiceAccount = $kmsKeyServiceAccount;
  }
  /**
   * @return string
   */
  public function getKmsKeyServiceAccount()
  {
    return $this->kmsKeyServiceAccount;
  }
  /**
   * Specifies a 256-bit customer-supplied encryption key, encoded in RFC 4648
   * base64 to either encrypt or decrypt this resource. You can provide either
   * the rawKey or thersaEncryptedKey. For example:
   *
   * "rawKey": "SGVsbG8gZnJvbSBHb29nbGUgQ2xvdWQgUGxhdGZvcm0="
   *
   * @param string $rawKey
   */
  public function setRawKey($rawKey)
  {
    $this->rawKey = $rawKey;
  }
  /**
   * @return string
   */
  public function getRawKey()
  {
    return $this->rawKey;
  }
  /**
   * Specifies an RFC 4648 base64 encoded, RSA-wrapped 2048-bit customer-
   * supplied encryption key to either encrypt or decrypt this resource. You can
   * provide either the rawKey or thersaEncryptedKey. For example:
   *
   * "rsaEncryptedKey":
   * "ieCx/NcW06PcT7Ep1X6LUTc/hLvUDYyzSZPPVCVPTVEohpeHASqC8uw5TzyO9U+Fka9JFH
   * z0mBibXUInrC/jEk014kCK/NPjYgEMOyssZ4ZINPKxlUh2zn1bV+MCaTICrdmuSBTWlUUiFoD
   * D6PYznLwh8ZNdaheCeZ8ewEXgFQ8V+sDroLaN3Xs3MDTXQEMMoNUXMCZEIpg9Vtp9x2oe=="
   *
   * The key must meet the following requirements before you can provide it to
   * Compute Engine:         1. The key is wrapped using a RSA public key
   * certificate provided by     Google.     2. After being wrapped, the key
   * must be encoded in RFC 4648 base64     encoding.
   *
   * Gets the RSA public key certificate provided by Google at:
   *
   * https://cloud-certs.storage.googleapis.com/google-cloud-csek-ingress.pem
   *
   * @param string $rsaEncryptedKey
   */
  public function setRsaEncryptedKey($rsaEncryptedKey)
  {
    $this->rsaEncryptedKey = $rsaEncryptedKey;
  }
  /**
   * @return string
   */
  public function getRsaEncryptedKey()
  {
    return $this->rsaEncryptedKey;
  }
  /**
   * [Output only] TheRFC 4648 base64 encoded SHA-256 hash of the customer-
   * supplied encryption key that protects this resource.
   *
   * @param string $sha256
   */
  public function setSha256($sha256)
  {
    $this->sha256 = $sha256;
  }
  /**
   * @return string
   */
  public function getSha256()
  {
    return $this->sha256;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomerEncryptionKey::class, 'Google_Service_Compute_CustomerEncryptionKey');
