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

class GoogleFirestoreAdminV1CustomerManagedEncryptionOptions extends \Google\Model
{
  /**
   * Required. Only keys in the same location as the database are allowed to be
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
   * Required. Only keys in the same location as the database are allowed to be
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
class_alias(GoogleFirestoreAdminV1CustomerManagedEncryptionOptions::class, 'Google_Service_Firestore_GoogleFirestoreAdminV1CustomerManagedEncryptionOptions');
