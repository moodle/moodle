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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementVersionsV1AttestationCredential extends \Google\Model
{
  /**
   * Represents an unspecified public key trust level.
   */
  public const KEY_TRUST_LEVEL_KEY_TRUST_LEVEL_UNSPECIFIED = 'KEY_TRUST_LEVEL_UNSPECIFIED';
  /**
   * Represents a HW key.
   */
  public const KEY_TRUST_LEVEL_CHROME_BROWSER_HW_KEY = 'CHROME_BROWSER_HW_KEY';
  /**
   * Represents an OS key.
   */
  public const KEY_TRUST_LEVEL_CHROME_BROWSER_OS_KEY = 'CHROME_BROWSER_OS_KEY';
  /**
   * Represents an unspecified public key type.
   */
  public const KEY_TYPE_KEY_TYPE_UNSPECIFIED = 'KEY_TYPE_UNSPECIFIED';
  /**
   * Represents a RSA key.
   */
  public const KEY_TYPE_RSA_KEY = 'RSA_KEY';
  /**
   * Represents an EC key.
   */
  public const KEY_TYPE_EC_KEY = 'EC_KEY';
  /**
   * Output only. Latest rotation timestamp of the public key rotation.
   *
   * @var string
   */
  public $keyRotationTime;
  /**
   * Output only. Trust level of the public key.
   *
   * @var string
   */
  public $keyTrustLevel;
  /**
   * Output only. Type of the public key.
   *
   * @var string
   */
  public $keyType;
  /**
   * Output only. Value of the public key.
   *
   * @var string
   */
  public $publicKey;

  /**
   * Output only. Latest rotation timestamp of the public key rotation.
   *
   * @param string $keyRotationTime
   */
  public function setKeyRotationTime($keyRotationTime)
  {
    $this->keyRotationTime = $keyRotationTime;
  }
  /**
   * @return string
   */
  public function getKeyRotationTime()
  {
    return $this->keyRotationTime;
  }
  /**
   * Output only. Trust level of the public key.
   *
   * Accepted values: KEY_TRUST_LEVEL_UNSPECIFIED, CHROME_BROWSER_HW_KEY,
   * CHROME_BROWSER_OS_KEY
   *
   * @param self::KEY_TRUST_LEVEL_* $keyTrustLevel
   */
  public function setKeyTrustLevel($keyTrustLevel)
  {
    $this->keyTrustLevel = $keyTrustLevel;
  }
  /**
   * @return self::KEY_TRUST_LEVEL_*
   */
  public function getKeyTrustLevel()
  {
    return $this->keyTrustLevel;
  }
  /**
   * Output only. Type of the public key.
   *
   * Accepted values: KEY_TYPE_UNSPECIFIED, RSA_KEY, EC_KEY
   *
   * @param self::KEY_TYPE_* $keyType
   */
  public function setKeyType($keyType)
  {
    $this->keyType = $keyType;
  }
  /**
   * @return self::KEY_TYPE_*
   */
  public function getKeyType()
  {
    return $this->keyType;
  }
  /**
   * Output only. Value of the public key.
   *
   * @param string $publicKey
   */
  public function setPublicKey($publicKey)
  {
    $this->publicKey = $publicKey;
  }
  /**
   * @return string
   */
  public function getPublicKey()
  {
    return $this->publicKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementVersionsV1AttestationCredential::class, 'Google_Service_ChromeManagement_GoogleChromeManagementVersionsV1AttestationCredential');
