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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1EncryptionSpec extends \Google\Model
{
  /**
   * Required. The name of customer-managed encryption key that is used to
   * secure a resource and its sub-resources. If empty, the resource is secured
   * by our default encryption key. Only the key in the same location as this
   * resource is allowed to be used for encryption. Format: `projects/{project}/
   * locations/{location}/keyRings/{keyRing}/cryptoKeys/{key}`
   *
   * @var string
   */
  public $kmsKey;
  /**
   * Immutable. The resource name of the encryption key specification resource.
   * Format: projects/{project}/locations/{location}/encryptionSpec
   *
   * @var string
   */
  public $name;

  /**
   * Required. The name of customer-managed encryption key that is used to
   * secure a resource and its sub-resources. If empty, the resource is secured
   * by our default encryption key. Only the key in the same location as this
   * resource is allowed to be used for encryption. Format: `projects/{project}/
   * locations/{location}/keyRings/{keyRing}/cryptoKeys/{key}`
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
   * Immutable. The resource name of the encryption key specification resource.
   * Format: projects/{project}/locations/{location}/encryptionSpec
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1EncryptionSpec::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1EncryptionSpec');
