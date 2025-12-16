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

class GoogleCloudConnectorsV1EncryptionKey extends \Google\Model
{
  /**
   * Value type is not specified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Google Managed.
   */
  public const TYPE_GOOGLE_MANAGED = 'GOOGLE_MANAGED';
  /**
   * Customer Managed.
   */
  public const TYPE_CUSTOMER_MANAGED = 'CUSTOMER_MANAGED';
  /**
   * Optional. The [KMS key name] with which the content of the Operation is
   * encrypted. The expected format: `projects/locations/keyRings/cryptoKeys`.
   * Will be empty string if google managed.
   *
   * @var string
   */
  public $kmsKeyName;
  /**
   * Type.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. The [KMS key name] with which the content of the Operation is
   * encrypted. The expected format: `projects/locations/keyRings/cryptoKeys`.
   * Will be empty string if google managed.
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
   * Type.
   *
   * Accepted values: TYPE_UNSPECIFIED, GOOGLE_MANAGED, CUSTOMER_MANAGED
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudConnectorsV1EncryptionKey::class, 'Google_Service_Integrations_GoogleCloudConnectorsV1EncryptionKey');
