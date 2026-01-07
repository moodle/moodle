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

class SignAndEncryptKeyPairs extends \Google\Model
{
  /**
   * The ID of the CseKeyPair that encrypts signed outgoing mail.
   *
   * @var string
   */
  public $encryptionKeyPairId;
  /**
   * The ID of the CseKeyPair that signs outgoing mail.
   *
   * @var string
   */
  public $signingKeyPairId;

  /**
   * The ID of the CseKeyPair that encrypts signed outgoing mail.
   *
   * @param string $encryptionKeyPairId
   */
  public function setEncryptionKeyPairId($encryptionKeyPairId)
  {
    $this->encryptionKeyPairId = $encryptionKeyPairId;
  }
  /**
   * @return string
   */
  public function getEncryptionKeyPairId()
  {
    return $this->encryptionKeyPairId;
  }
  /**
   * The ID of the CseKeyPair that signs outgoing mail.
   *
   * @param string $signingKeyPairId
   */
  public function setSigningKeyPairId($signingKeyPairId)
  {
    $this->signingKeyPairId = $signingKeyPairId;
  }
  /**
   * @return string
   */
  public function getSigningKeyPairId()
  {
    return $this->signingKeyPairId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SignAndEncryptKeyPairs::class, 'Google_Service_Gmail_SignAndEncryptKeyPairs');
