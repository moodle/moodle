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

namespace Google\Service\Container;

class DatabaseEncryption extends \Google\Collection
{
  /**
   * Should never be set
   */
  public const CURRENT_STATE_CURRENT_STATE_UNSPECIFIED = 'CURRENT_STATE_UNSPECIFIED';
  /**
   * Secrets in etcd are encrypted.
   */
  public const CURRENT_STATE_CURRENT_STATE_ENCRYPTED = 'CURRENT_STATE_ENCRYPTED';
  /**
   * Secrets in etcd are stored in plain text (at etcd level) - this is
   * unrelated to Compute Engine level full disk encryption.
   */
  public const CURRENT_STATE_CURRENT_STATE_DECRYPTED = 'CURRENT_STATE_DECRYPTED';
  /**
   * Encryption (or re-encryption with a different CloudKMS key) of Secrets is
   * in progress.
   */
  public const CURRENT_STATE_CURRENT_STATE_ENCRYPTION_PENDING = 'CURRENT_STATE_ENCRYPTION_PENDING';
  /**
   * Encryption (or re-encryption with a different CloudKMS key) of Secrets in
   * etcd encountered an error.
   */
  public const CURRENT_STATE_CURRENT_STATE_ENCRYPTION_ERROR = 'CURRENT_STATE_ENCRYPTION_ERROR';
  /**
   * De-crypting Secrets to plain text in etcd is in progress.
   */
  public const CURRENT_STATE_CURRENT_STATE_DECRYPTION_PENDING = 'CURRENT_STATE_DECRYPTION_PENDING';
  /**
   * De-crypting Secrets to plain text in etcd encountered an error.
   */
  public const CURRENT_STATE_CURRENT_STATE_DECRYPTION_ERROR = 'CURRENT_STATE_DECRYPTION_ERROR';
  /**
   * Should never be set
   */
  public const STATE_UNKNOWN = 'UNKNOWN';
  /**
   * Secrets in etcd are encrypted.
   */
  public const STATE_ENCRYPTED = 'ENCRYPTED';
  /**
   * Secrets in etcd are stored in plain text (at etcd level) - this is
   * unrelated to Compute Engine level full disk encryption.
   */
  public const STATE_DECRYPTED = 'DECRYPTED';
  protected $collection_key = 'lastOperationErrors';
  /**
   * Output only. The current state of etcd encryption.
   *
   * @var string
   */
  public $currentState;
  /**
   * Output only. Keys in use by the cluster for decrypting existing objects, in
   * addition to the key in `key_name`. Each item is a CloudKMS key resource.
   *
   * @var string[]
   */
  public $decryptionKeys;
  /**
   * Name of CloudKMS key to use for the encryption of secrets in etcd. Ex.
   * projects/my-project/locations/global/keyRings/my-ring/cryptoKeys/my-key
   *
   * @var string
   */
  public $keyName;
  protected $lastOperationErrorsType = OperationError::class;
  protected $lastOperationErrorsDataType = 'array';
  /**
   * The desired state of etcd encryption.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The current state of etcd encryption.
   *
   * Accepted values: CURRENT_STATE_UNSPECIFIED, CURRENT_STATE_ENCRYPTED,
   * CURRENT_STATE_DECRYPTED, CURRENT_STATE_ENCRYPTION_PENDING,
   * CURRENT_STATE_ENCRYPTION_ERROR, CURRENT_STATE_DECRYPTION_PENDING,
   * CURRENT_STATE_DECRYPTION_ERROR
   *
   * @param self::CURRENT_STATE_* $currentState
   */
  public function setCurrentState($currentState)
  {
    $this->currentState = $currentState;
  }
  /**
   * @return self::CURRENT_STATE_*
   */
  public function getCurrentState()
  {
    return $this->currentState;
  }
  /**
   * Output only. Keys in use by the cluster for decrypting existing objects, in
   * addition to the key in `key_name`. Each item is a CloudKMS key resource.
   *
   * @param string[] $decryptionKeys
   */
  public function setDecryptionKeys($decryptionKeys)
  {
    $this->decryptionKeys = $decryptionKeys;
  }
  /**
   * @return string[]
   */
  public function getDecryptionKeys()
  {
    return $this->decryptionKeys;
  }
  /**
   * Name of CloudKMS key to use for the encryption of secrets in etcd. Ex.
   * projects/my-project/locations/global/keyRings/my-ring/cryptoKeys/my-key
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
   * Output only. Records errors seen during DatabaseEncryption update
   * operations.
   *
   * @param OperationError[] $lastOperationErrors
   */
  public function setLastOperationErrors($lastOperationErrors)
  {
    $this->lastOperationErrors = $lastOperationErrors;
  }
  /**
   * @return OperationError[]
   */
  public function getLastOperationErrors()
  {
    return $this->lastOperationErrors;
  }
  /**
   * The desired state of etcd encryption.
   *
   * Accepted values: UNKNOWN, ENCRYPTED, DECRYPTED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatabaseEncryption::class, 'Google_Service_Container_DatabaseEncryption');
