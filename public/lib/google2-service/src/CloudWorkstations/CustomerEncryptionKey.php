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

namespace Google\Service\CloudWorkstations;

class CustomerEncryptionKey extends \Google\Model
{
  /**
   * Immutable. The name of the Google Cloud KMS encryption key. For example, `"
   * projects/PROJECT_ID/locations/REGION/keyRings/KEY_RING/cryptoKeys/KEY_NAME"
   * `. The key must be in the same region as the workstation configuration.
   *
   * @var string
   */
  public $kmsKey;
  /**
   * Immutable. The service account to use with the specified KMS key. We
   * recommend that you use a separate service account and follow KMS best
   * practices. For more information, see [Separation of
   * duties](https://cloud.google.com/kms/docs/separation-of-duties) and `gcloud
   * kms keys add-iam-policy-binding`
   * [`--member`](https://cloud.google.com/sdk/gcloud/reference/kms/keys/add-
   * iam-policy-binding#--member).
   *
   * @var string
   */
  public $kmsKeyServiceAccount;

  /**
   * Immutable. The name of the Google Cloud KMS encryption key. For example, `"
   * projects/PROJECT_ID/locations/REGION/keyRings/KEY_RING/cryptoKeys/KEY_NAME"
   * `. The key must be in the same region as the workstation configuration.
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
  /**
   * Immutable. The service account to use with the specified KMS key. We
   * recommend that you use a separate service account and follow KMS best
   * practices. For more information, see [Separation of
   * duties](https://cloud.google.com/kms/docs/separation-of-duties) and `gcloud
   * kms keys add-iam-policy-binding`
   * [`--member`](https://cloud.google.com/sdk/gcloud/reference/kms/keys/add-
   * iam-policy-binding#--member).
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomerEncryptionKey::class, 'Google_Service_CloudWorkstations_CustomerEncryptionKey');
