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

namespace Google\Service\Gmail;

class CseIdentity extends \Google\Model
{
  /**
   * The email address for the sending identity. The email address must be the
   * primary email address of the authenticated user.
   *
   * @var string
   */
  public $emailAddress;
  /**
   * If a key pair is associated, the ID of the key pair, CseKeyPair.
   *
   * @var string
   */
  public $primaryKeyPairId;
  protected $signAndEncryptKeyPairsType = SignAndEncryptKeyPairs::class;
  protected $signAndEncryptKeyPairsDataType = '';

  /**
   * The email address for the sending identity. The email address must be the
   * primary email address of the authenticated user.
   *
   * @param string $emailAddress
   */
  public function setEmailAddress($emailAddress)
  {
    $this->emailAddress = $emailAddress;
  }
  /**
   * @return string
   */
  public function getEmailAddress()
  {
    return $this->emailAddress;
  }
  /**
   * If a key pair is associated, the ID of the key pair, CseKeyPair.
   *
   * @param string $primaryKeyPairId
   */
  public function setPrimaryKeyPairId($primaryKeyPairId)
  {
    $this->primaryKeyPairId = $primaryKeyPairId;
  }
  /**
   * @return string
   */
  public function getPrimaryKeyPairId()
  {
    return $this->primaryKeyPairId;
  }
  /**
   * The configuration of a CSE identity that uses different key pairs for
   * signing and encryption.
   *
   * @param SignAndEncryptKeyPairs $signAndEncryptKeyPairs
   */
  public function setSignAndEncryptKeyPairs(SignAndEncryptKeyPairs $signAndEncryptKeyPairs)
  {
    $this->signAndEncryptKeyPairs = $signAndEncryptKeyPairs;
  }
  /**
   * @return SignAndEncryptKeyPairs
   */
  public function getSignAndEncryptKeyPairs()
  {
    return $this->signAndEncryptKeyPairs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CseIdentity::class, 'Google_Service_Gmail_CseIdentity');
