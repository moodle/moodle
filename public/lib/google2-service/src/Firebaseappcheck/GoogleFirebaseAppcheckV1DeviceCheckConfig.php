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

namespace Google\Service\Firebaseappcheck;

class GoogleFirebaseAppcheckV1DeviceCheckConfig extends \Google\Model
{
  /**
   * Required. The key identifier of a private key enabled with DeviceCheck,
   * created in your Apple Developer account.
   *
   * @var string
   */
  public $keyId;
  /**
   * Required. The relative resource name of the DeviceCheck configuration
   * object, in the format: ```
   * projects/{project_number}/apps/{app_id}/deviceCheckConfig ```
   *
   * @var string
   */
  public $name;
  /**
   * Required. Input only. The contents of the private key (`.p8`) file
   * associated with the key specified by `key_id`. For security reasons, this
   * field will never be populated in any response.
   *
   * @var string
   */
  public $privateKey;
  /**
   * Output only. Whether the `private_key` field was previously set. Since we
   * will never return the `private_key` field, this field is the only way to
   * find out whether it was previously set.
   *
   * @var bool
   */
  public $privateKeySet;
  /**
   * Specifies the duration for which App Check tokens exchanged from
   * DeviceCheck tokens will be valid. If unset, a default value of 1 hour is
   * assumed. Must be between 30 minutes and 7 days, inclusive.
   *
   * @var string
   */
  public $tokenTtl;

  /**
   * Required. The key identifier of a private key enabled with DeviceCheck,
   * created in your Apple Developer account.
   *
   * @param string $keyId
   */
  public function setKeyId($keyId)
  {
    $this->keyId = $keyId;
  }
  /**
   * @return string
   */
  public function getKeyId()
  {
    return $this->keyId;
  }
  /**
   * Required. The relative resource name of the DeviceCheck configuration
   * object, in the format: ```
   * projects/{project_number}/apps/{app_id}/deviceCheckConfig ```
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
  /**
   * Required. Input only. The contents of the private key (`.p8`) file
   * associated with the key specified by `key_id`. For security reasons, this
   * field will never be populated in any response.
   *
   * @param string $privateKey
   */
  public function setPrivateKey($privateKey)
  {
    $this->privateKey = $privateKey;
  }
  /**
   * @return string
   */
  public function getPrivateKey()
  {
    return $this->privateKey;
  }
  /**
   * Output only. Whether the `private_key` field was previously set. Since we
   * will never return the `private_key` field, this field is the only way to
   * find out whether it was previously set.
   *
   * @param bool $privateKeySet
   */
  public function setPrivateKeySet($privateKeySet)
  {
    $this->privateKeySet = $privateKeySet;
  }
  /**
   * @return bool
   */
  public function getPrivateKeySet()
  {
    return $this->privateKeySet;
  }
  /**
   * Specifies the duration for which App Check tokens exchanged from
   * DeviceCheck tokens will be valid. If unset, a default value of 1 hour is
   * assumed. Must be between 30 minutes and 7 days, inclusive.
   *
   * @param string $tokenTtl
   */
  public function setTokenTtl($tokenTtl)
  {
    $this->tokenTtl = $tokenTtl;
  }
  /**
   * @return string
   */
  public function getTokenTtl()
  {
    return $this->tokenTtl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirebaseAppcheckV1DeviceCheckConfig::class, 'Google_Service_Firebaseappcheck_GoogleFirebaseAppcheckV1DeviceCheckConfig');
