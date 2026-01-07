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

namespace Google\Service\Firestore;

class GoogleFirestoreAdminV1CmekConfig extends \Google\Collection
{
  protected $collection_key = 'activeKeyVersion';
  /**
   * Output only. Currently in-use [KMS key
   * versions](https://cloud.google.com/kms/docs/resource-
   * hierarchy#key_versions). During [key
   * rotation](https://cloud.google.com/kms/docs/key-rotation), there can be
   * multiple in-use key versions. The expected format is `projects/{project_id}
   * /locations/{kms_location}/keyRings/{key_ring}/cryptoKeys/{crypto_key}/crypt
   * oKeyVersions/{key_version}`.
   *
   * @var string[]
   */
  public $activeKeyVersion;
  /**
   * Required. Only keys in the same location as this database are allowed to be
   * used for encryption. For Firestore's nam5 multi-region, this corresponds to
   * Cloud KMS multi-region us. For Firestore's eur3 multi-region, this
   * corresponds to Cloud KMS multi-region europe. See
   * https://cloud.google.com/kms/docs/locations. The expected format is `projec
   * ts/{project_id}/locations/{kms_location}/keyRings/{key_ring}/cryptoKeys/{cr
   * ypto_key}`.
   *
   * @var string
   */
  public $kmsKeyName;

  /**
   * Output only. Currently in-use [KMS key
   * versions](https://cloud.google.com/kms/docs/resource-
   * hierarchy#key_versions). During [key
   * rotation](https://cloud.google.com/kms/docs/key-rotation), there can be
   * multiple in-use key versions. The expected format is `projects/{project_id}
   * /locations/{kms_location}/keyRings/{key_ring}/cryptoKeys/{crypto_key}/crypt
   * oKeyVersions/{key_version}`.
   *
   * @param string[] $activeKeyVersion
   */
  public function setActiveKeyVersion($activeKeyVersion)
  {
    $this->activeKeyVersion = $activeKeyVersion;
  }
  /**
   * @return string[]
   */
  public function getActiveKeyVersion()
  {
    return $this->activeKeyVersion;
  }
  /**
   * Required. Only keys in the same location as this database are allowed to be
   * used for encryption. For Firestore's nam5 multi-region, this corresponds to
   * Cloud KMS multi-region us. For Firestore's eur3 multi-region, this
   * corresponds to Cloud KMS multi-region europe. See
   * https://cloud.google.com/kms/docs/locations. The expected format is `projec
   * ts/{project_id}/locations/{kms_location}/keyRings/{key_ring}/cryptoKeys/{cr
   * ypto_key}`.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirestoreAdminV1CmekConfig::class, 'Google_Service_Firestore_GoogleFirestoreAdminV1CmekConfig');
