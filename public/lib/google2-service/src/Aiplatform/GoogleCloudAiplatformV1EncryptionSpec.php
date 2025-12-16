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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1EncryptionSpec extends \Google\Model
{
  /**
   * Required. The Cloud KMS resource identifier of the customer managed
   * encryption key used to protect a resource. Has the form: `projects/my-
   * project/locations/my-region/keyRings/my-kr/cryptoKeys/my-key`. The key
   * needs to be in the same region as where the compute resource is created.
   *
   * @var string
   */
  public $kmsKeyName;

  /**
   * Required. The Cloud KMS resource identifier of the customer managed
   * encryption key used to protect a resource. Has the form: `projects/my-
   * project/locations/my-region/keyRings/my-kr/cryptoKeys/my-key`. The key
   * needs to be in the same region as where the compute resource is created.
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
class_alias(GoogleCloudAiplatformV1EncryptionSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1EncryptionSpec');
