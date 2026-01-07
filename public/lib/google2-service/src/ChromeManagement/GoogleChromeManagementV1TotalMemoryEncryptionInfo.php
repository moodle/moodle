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

class GoogleChromeManagementV1TotalMemoryEncryptionInfo extends \Google\Model
{
  /**
   * Memory encryption algorithm is not set.
   */
  public const ENCRYPTION_ALGORITHM_MEMORY_ENCRYPTION_ALGORITHM_UNSPECIFIED = 'MEMORY_ENCRYPTION_ALGORITHM_UNSPECIFIED';
  /**
   * The memory encryption algorithm being used is unknown.
   */
  public const ENCRYPTION_ALGORITHM_MEMORY_ENCRYPTION_ALGORITHM_UNKNOWN = 'MEMORY_ENCRYPTION_ALGORITHM_UNKNOWN';
  /**
   * The memory encryption algorithm is using the AES_XTS encryption algorithm
   * with a 128 bit block cypher.
   */
  public const ENCRYPTION_ALGORITHM_MEMORY_ENCRYPTION_ALGORITHM_AES_XTS_128 = 'MEMORY_ENCRYPTION_ALGORITHM_AES_XTS_128';
  /**
   * The memory encryption algorithm is using the AES_XTS encryption algorithm
   * with a 256 bit block cypher.
   */
  public const ENCRYPTION_ALGORITHM_MEMORY_ENCRYPTION_ALGORITHM_AES_XTS_256 = 'MEMORY_ENCRYPTION_ALGORITHM_AES_XTS_256';
  /**
   * Memory encryption state is not set.
   */
  public const ENCRYPTION_STATE_MEMORY_ENCRYPTION_STATE_UNSPECIFIED = 'MEMORY_ENCRYPTION_STATE_UNSPECIFIED';
  /**
   * The memory encryption state is unknown.
   */
  public const ENCRYPTION_STATE_MEMORY_ENCRYPTION_STATE_UNKNOWN = 'MEMORY_ENCRYPTION_STATE_UNKNOWN';
  /**
   * Memory encrpytion on the device is disabled.
   */
  public const ENCRYPTION_STATE_MEMORY_ENCRYPTION_STATE_DISABLED = 'MEMORY_ENCRYPTION_STATE_DISABLED';
  /**
   * Memory encryption on the device uses total memory encryption.
   */
  public const ENCRYPTION_STATE_MEMORY_ENCRYPTION_STATE_TME = 'MEMORY_ENCRYPTION_STATE_TME';
  /**
   * Memory encryption on the device uses multi-key total memory encryption.
   */
  public const ENCRYPTION_STATE_MEMORY_ENCRYPTION_STATE_MKTME = 'MEMORY_ENCRYPTION_STATE_MKTME';
  /**
   * Memory encryption algorithm.
   *
   * @var string
   */
  public $encryptionAlgorithm;
  /**
   * The state of memory encryption on the device.
   *
   * @var string
   */
  public $encryptionState;
  /**
   * The length of the encryption keys.
   *
   * @var string
   */
  public $keyLength;
  /**
   * The maximum number of keys that can be used for encryption.
   *
   * @var string
   */
  public $maxKeys;

  /**
   * Memory encryption algorithm.
   *
   * Accepted values: MEMORY_ENCRYPTION_ALGORITHM_UNSPECIFIED,
   * MEMORY_ENCRYPTION_ALGORITHM_UNKNOWN,
   * MEMORY_ENCRYPTION_ALGORITHM_AES_XTS_128,
   * MEMORY_ENCRYPTION_ALGORITHM_AES_XTS_256
   *
   * @param self::ENCRYPTION_ALGORITHM_* $encryptionAlgorithm
   */
  public function setEncryptionAlgorithm($encryptionAlgorithm)
  {
    $this->encryptionAlgorithm = $encryptionAlgorithm;
  }
  /**
   * @return self::ENCRYPTION_ALGORITHM_*
   */
  public function getEncryptionAlgorithm()
  {
    return $this->encryptionAlgorithm;
  }
  /**
   * The state of memory encryption on the device.
   *
   * Accepted values: MEMORY_ENCRYPTION_STATE_UNSPECIFIED,
   * MEMORY_ENCRYPTION_STATE_UNKNOWN, MEMORY_ENCRYPTION_STATE_DISABLED,
   * MEMORY_ENCRYPTION_STATE_TME, MEMORY_ENCRYPTION_STATE_MKTME
   *
   * @param self::ENCRYPTION_STATE_* $encryptionState
   */
  public function setEncryptionState($encryptionState)
  {
    $this->encryptionState = $encryptionState;
  }
  /**
   * @return self::ENCRYPTION_STATE_*
   */
  public function getEncryptionState()
  {
    return $this->encryptionState;
  }
  /**
   * The length of the encryption keys.
   *
   * @param string $keyLength
   */
  public function setKeyLength($keyLength)
  {
    $this->keyLength = $keyLength;
  }
  /**
   * @return string
   */
  public function getKeyLength()
  {
    return $this->keyLength;
  }
  /**
   * The maximum number of keys that can be used for encryption.
   *
   * @param string $maxKeys
   */
  public function setMaxKeys($maxKeys)
  {
    $this->maxKeys = $maxKeys;
  }
  /**
   * @return string
   */
  public function getMaxKeys()
  {
    return $this->maxKeys;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1TotalMemoryEncryptionInfo::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1TotalMemoryEncryptionInfo');
