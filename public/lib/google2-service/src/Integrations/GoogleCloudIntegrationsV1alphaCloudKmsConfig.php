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

namespace Google\Service\Integrations;

class GoogleCloudIntegrationsV1alphaCloudKmsConfig extends \Google\Model
{
  /**
   * Required. A Cloud KMS key is a named object containing one or more key
   * versions, along with metadata for the key. A key exists on exactly one key
   * ring tied to a specific location.
   *
   * @var string
   */
  public $key;
  /**
   * Optional. Each version of a key contains key material used for encryption
   * or signing. A key's version is represented by an integer, starting at 1. To
   * decrypt data or verify a signature, you must use the same key version that
   * was used to encrypt or sign the data.
   *
   * @var string
   */
  public $keyVersion;
  /**
   * Required. Location name of the key ring, e.g. "us-west1".
   *
   * @var string
   */
  public $kmsLocation;
  /**
   * Optional. The gcp project id of the project where the kms key stored. If
   * empty, the kms key is stored at the same project as customer's project and
   * ecrypted with CMEK, otherwise, the kms key is stored in the tenant project
   * and encrypted with GMEK
   *
   * @var string
   */
  public $kmsProjectId;
  /**
   * Required. A key ring organizes keys in a specific Google Cloud location and
   * allows you to manage access control on groups of keys. A key ring's name
   * does not need to be unique across a Google Cloud project, but must be
   * unique within a given location.
   *
   * @var string
   */
  public $kmsRing;

  /**
   * Required. A Cloud KMS key is a named object containing one or more key
   * versions, along with metadata for the key. A key exists on exactly one key
   * ring tied to a specific location.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * Optional. Each version of a key contains key material used for encryption
   * or signing. A key's version is represented by an integer, starting at 1. To
   * decrypt data or verify a signature, you must use the same key version that
   * was used to encrypt or sign the data.
   *
   * @param string $keyVersion
   */
  public function setKeyVersion($keyVersion)
  {
    $this->keyVersion = $keyVersion;
  }
  /**
   * @return string
   */
  public function getKeyVersion()
  {
    return $this->keyVersion;
  }
  /**
   * Required. Location name of the key ring, e.g. "us-west1".
   *
   * @param string $kmsLocation
   */
  public function setKmsLocation($kmsLocation)
  {
    $this->kmsLocation = $kmsLocation;
  }
  /**
   * @return string
   */
  public function getKmsLocation()
  {
    return $this->kmsLocation;
  }
  /**
   * Optional. The gcp project id of the project where the kms key stored. If
   * empty, the kms key is stored at the same project as customer's project and
   * ecrypted with CMEK, otherwise, the kms key is stored in the tenant project
   * and encrypted with GMEK
   *
   * @param string $kmsProjectId
   */
  public function setKmsProjectId($kmsProjectId)
  {
    $this->kmsProjectId = $kmsProjectId;
  }
  /**
   * @return string
   */
  public function getKmsProjectId()
  {
    return $this->kmsProjectId;
  }
  /**
   * Required. A key ring organizes keys in a specific Google Cloud location and
   * allows you to manage access control on groups of keys. A key ring's name
   * does not need to be unique across a Google Cloud project, but must be
   * unique within a given location.
   *
   * @param string $kmsRing
   */
  public function setKmsRing($kmsRing)
  {
    $this->kmsRing = $kmsRing;
  }
  /**
   * @return string
   */
  public function getKmsRing()
  {
    return $this->kmsRing;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaCloudKmsConfig::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaCloudKmsConfig');
