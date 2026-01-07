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

namespace Google\Service\CloudFunctions;

class GenerateUploadUrlRequest extends \Google\Model
{
  /**
   * Unspecified
   */
  public const ENVIRONMENT_ENVIRONMENT_UNSPECIFIED = 'ENVIRONMENT_UNSPECIFIED';
  /**
   * Gen 1
   */
  public const ENVIRONMENT_GEN_1 = 'GEN_1';
  /**
   * Gen 2
   */
  public const ENVIRONMENT_GEN_2 = 'GEN_2';
  /**
   * The function environment the generated upload url will be used for. The
   * upload url for 2nd Gen functions can also be used for 1st gen functions,
   * but not vice versa. If not specified, 2nd generation-style upload URLs are
   * generated.
   *
   * @var string
   */
  public $environment;
  /**
   * Resource name of a KMS crypto key (managed by the user) used to
   * encrypt/decrypt function source code objects in intermediate Cloud Storage
   * buckets. When you generate an upload url and upload your source code, it
   * gets copied to an intermediate Cloud Storage bucket. The source code is
   * then copied to a versioned directory in the sources bucket in the consumer
   * project during the function deployment. It must match the pattern `projects
   * /{project}/locations/{location}/keyRings/{key_ring}/cryptoKeys/{crypto_key}
   * `. The Google Cloud Functions service account
   * (service-{project_number}@gcf-admin-robot.iam.gserviceaccount.com) must be
   * granted the role 'Cloud KMS CryptoKey Encrypter/Decrypter
   * (roles/cloudkms.cryptoKeyEncrypterDecrypter)' on the
   * Key/KeyRing/Project/Organization (least access preferred).
   *
   * @var string
   */
  public $kmsKeyName;

  /**
   * The function environment the generated upload url will be used for. The
   * upload url for 2nd Gen functions can also be used for 1st gen functions,
   * but not vice versa. If not specified, 2nd generation-style upload URLs are
   * generated.
   *
   * Accepted values: ENVIRONMENT_UNSPECIFIED, GEN_1, GEN_2
   *
   * @param self::ENVIRONMENT_* $environment
   */
  public function setEnvironment($environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return self::ENVIRONMENT_*
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * Resource name of a KMS crypto key (managed by the user) used to
   * encrypt/decrypt function source code objects in intermediate Cloud Storage
   * buckets. When you generate an upload url and upload your source code, it
   * gets copied to an intermediate Cloud Storage bucket. The source code is
   * then copied to a versioned directory in the sources bucket in the consumer
   * project during the function deployment. It must match the pattern `projects
   * /{project}/locations/{location}/keyRings/{key_ring}/cryptoKeys/{crypto_key}
   * `. The Google Cloud Functions service account
   * (service-{project_number}@gcf-admin-robot.iam.gserviceaccount.com) must be
   * granted the role 'Cloud KMS CryptoKey Encrypter/Decrypter
   * (roles/cloudkms.cryptoKeyEncrypterDecrypter)' on the
   * Key/KeyRing/Project/Organization (least access preferred).
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
class_alias(GenerateUploadUrlRequest::class, 'Google_Service_CloudFunctions_GenerateUploadUrlRequest');
