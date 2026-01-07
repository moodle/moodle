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

namespace Google\Service\StorageBatchOperations;

class RewriteObject extends \Google\Model
{
  /**
   * Required. Resource name of the Cloud KMS key that will be used to encrypt
   * the object. The Cloud KMS key must be located in same location as the
   * object. Refer to https://cloud.google.com/storage/docs/encryption/using-
   * customer-managed-keys#add-object-key for additional documentation. Format:
   * projects/{project}/locations/{location}/keyRings/{keyring}/cryptoKeys/{key}
   * For example: "projects/123456/locations/us-central1/keyRings/my-
   * keyring/cryptoKeys/my-key". The object will be rewritten and set with the
   * specified KMS key.
   *
   * @var string
   */
  public $kmsKey;

  /**
   * Required. Resource name of the Cloud KMS key that will be used to encrypt
   * the object. The Cloud KMS key must be located in same location as the
   * object. Refer to https://cloud.google.com/storage/docs/encryption/using-
   * customer-managed-keys#add-object-key for additional documentation. Format:
   * projects/{project}/locations/{location}/keyRings/{keyring}/cryptoKeys/{key}
   * For example: "projects/123456/locations/us-central1/keyRings/my-
   * keyring/cryptoKeys/my-key". The object will be rewritten and set with the
   * specified KMS key.
   *
   * @param string $kmsKey
   */
  public function setKmsKey($kmsKey)
  {
    $this->kmsKey = $kmsKey;
  }
  /**
   * @return string
   */
  public function getKmsKey()
  {
    return $this->kmsKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RewriteObject::class, 'Google_Service_StorageBatchOperations_RewriteObject');
