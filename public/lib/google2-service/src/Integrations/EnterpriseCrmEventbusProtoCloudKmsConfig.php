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

class EnterpriseCrmEventbusProtoCloudKmsConfig extends \Google\Model
{
  /**
   * Optional. The id of GCP project where the KMS key is stored. If not
   * provided, assume the key is stored in the same GCP project defined in
   * Client (tag 14).
   *
   * @var string
   */
  public $gcpProjectId;
  /**
   * A Cloud KMS key is a named object containing one or more key versions,
   * along with metadata for the key. A key exists on exactly one key ring tied
   * to a specific location.
   *
   * @var string
   */
  public $keyName;
  /**
   * A key ring organizes keys in a specific Google Cloud location and allows
   * you to manage access control on groups of keys. A key ring's name does not
   * need to be unique across a Google Cloud project, but must be unique within
   * a given location.
   *
   * @var string
   */
  public $keyRingName;
  /**
   * Optional. Each version of a key contains key material used for encryption
   * or signing. A key's version is represented by an integer, starting at 1. To
   * decrypt data or verify a signature, you must use the same key version that
   * was used to encrypt or sign the data.
   *
   * @var string
   */
  public $keyVersionName;
  /**
   * Location name of the key ring, e.g. "us-west1".
   *
   * @var string
   */
  public $locationName;
  /**
   * Optional. The service account used for authentication of this KMS key. If
   * this is not provided, the service account in Client.clientSource will be
   * used.
   *
   * @var string
   */
  public $serviceAccount;

  /**
   * Optional. The id of GCP project where the KMS key is stored. If not
   * provided, assume the key is stored in the same GCP project defined in
   * Client (tag 14).
   *
   * @param string $gcpProjectId
   */
  public function setGcpProjectId($gcpProjectId)
  {
    $this->gcpProjectId = $gcpProjectId;
  }
  /**
   * @return string
   */
  public function getGcpProjectId()
  {
    return $this->gcpProjectId;
  }
  /**
   * A Cloud KMS key is a named object containing one or more key versions,
   * along with metadata for the key. A key exists on exactly one key ring tied
   * to a specific location.
   *
   * @param string $keyName
   */
  public function setKeyName($keyName)
  {
    $this->keyName = $keyName;
  }
  /**
   * @return string
   */
  public function getKeyName()
  {
    return $this->keyName;
  }
  /**
   * A key ring organizes keys in a specific Google Cloud location and allows
   * you to manage access control on groups of keys. A key ring's name does not
   * need to be unique across a Google Cloud project, but must be unique within
   * a given location.
   *
   * @param string $keyRingName
   */
  public function setKeyRingName($keyRingName)
  {
    $this->keyRingName = $keyRingName;
  }
  /**
   * @return string
   */
  public function getKeyRingName()
  {
    return $this->keyRingName;
  }
  /**
   * Optional. Each version of a key contains key material used for encryption
   * or signing. A key's version is represented by an integer, starting at 1. To
   * decrypt data or verify a signature, you must use the same key version that
   * was used to encrypt or sign the data.
   *
   * @param string $keyVersionName
   */
  public function setKeyVersionName($keyVersionName)
  {
    $this->keyVersionName = $keyVersionName;
  }
  /**
   * @return string
   */
  public function getKeyVersionName()
  {
    return $this->keyVersionName;
  }
  /**
   * Location name of the key ring, e.g. "us-west1".
   *
   * @param string $locationName
   */
  public function setLocationName($locationName)
  {
    $this->locationName = $locationName;
  }
  /**
   * @return string
   */
  public function getLocationName()
  {
    return $this->locationName;
  }
  /**
   * Optional. The service account used for authentication of this KMS key. If
   * this is not provided, the service account in Client.clientSource will be
   * used.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoCloudKmsConfig::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoCloudKmsConfig');
