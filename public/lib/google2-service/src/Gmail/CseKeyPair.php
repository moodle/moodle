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

class CseKeyPair extends \Google\Collection
{
  /**
   * The current state of the key pair is not set. The key pair is neither
   * turned on nor turned off.
   */
  public const ENABLEMENT_STATE_stateUnspecified = 'stateUnspecified';
  /**
   * The key pair is turned on. For any email messages that this key pair
   * encrypts, Gmail decrypts the messages and signs any outgoing mail with the
   * private key. To turn on a key pair, use the EnableCseKeyPair method.
   */
  public const ENABLEMENT_STATE_enabled = 'enabled';
  /**
   * The key pair is turned off. Authenticated users cannot decrypt email
   * messages nor sign outgoing messages. If a key pair is turned off for more
   * than 30 days, you can permanently delete it. To turn off a key pair, use
   * the DisableCseKeyPair method.
   */
  public const ENABLEMENT_STATE_disabled = 'disabled';
  protected $collection_key = 'subjectEmailAddresses';
  /**
   * Output only. If a key pair is set to `DISABLED`, the time that the key
   * pair's state changed from `ENABLED` to `DISABLED`. This field is present
   * only when the key pair is in state `DISABLED`.
   *
   * @var string
   */
  public $disableTime;
  /**
   * Output only. The current state of the key pair.
   *
   * @var string
   */
  public $enablementState;
  /**
   * Output only. The immutable ID for the client-side encryption S/MIME key
   * pair.
   *
   * @var string
   */
  public $keyPairId;
  /**
   * Output only. The public key and its certificate chain, in
   * [PEM](https://en.wikipedia.org/wiki/Privacy-Enhanced_Mail) format.
   *
   * @var string
   */
  public $pem;
  /**
   * Input only. The public key and its certificate chain. The chain must be in
   * [PKCS#7](https://en.wikipedia.org/wiki/PKCS_7) format and use PEM encoding
   * and ASCII armor.
   *
   * @var string
   */
  public $pkcs7;
  protected $privateKeyMetadataType = CsePrivateKeyMetadata::class;
  protected $privateKeyMetadataDataType = 'array';
  /**
   * Output only. The email address identities that are specified on the leaf
   * certificate.
   *
   * @var string[]
   */
  public $subjectEmailAddresses;

  /**
   * Output only. If a key pair is set to `DISABLED`, the time that the key
   * pair's state changed from `ENABLED` to `DISABLED`. This field is present
   * only when the key pair is in state `DISABLED`.
   *
   * @param string $disableTime
   */
  public function setDisableTime($disableTime)
  {
    $this->disableTime = $disableTime;
  }
  /**
   * @return string
   */
  public function getDisableTime()
  {
    return $this->disableTime;
  }
  /**
   * Output only. The current state of the key pair.
   *
   * Accepted values: stateUnspecified, enabled, disabled
   *
   * @param self::ENABLEMENT_STATE_* $enablementState
   */
  public function setEnablementState($enablementState)
  {
    $this->enablementState = $enablementState;
  }
  /**
   * @return self::ENABLEMENT_STATE_*
   */
  public function getEnablementState()
  {
    return $this->enablementState;
  }
  /**
   * Output only. The immutable ID for the client-side encryption S/MIME key
   * pair.
   *
   * @param string $keyPairId
   */
  public function setKeyPairId($keyPairId)
  {
    $this->keyPairId = $keyPairId;
  }
  /**
   * @return string
   */
  public function getKeyPairId()
  {
    return $this->keyPairId;
  }
  /**
   * Output only. The public key and its certificate chain, in
   * [PEM](https://en.wikipedia.org/wiki/Privacy-Enhanced_Mail) format.
   *
   * @param string $pem
   */
  public function setPem($pem)
  {
    $this->pem = $pem;
  }
  /**
   * @return string
   */
  public function getPem()
  {
    return $this->pem;
  }
  /**
   * Input only. The public key and its certificate chain. The chain must be in
   * [PKCS#7](https://en.wikipedia.org/wiki/PKCS_7) format and use PEM encoding
   * and ASCII armor.
   *
   * @param string $pkcs7
   */
  public function setPkcs7($pkcs7)
  {
    $this->pkcs7 = $pkcs7;
  }
  /**
   * @return string
   */
  public function getPkcs7()
  {
    return $this->pkcs7;
  }
  /**
   * Metadata for instances of this key pair's private key.
   *
   * @param CsePrivateKeyMetadata[] $privateKeyMetadata
   */
  public function setPrivateKeyMetadata($privateKeyMetadata)
  {
    $this->privateKeyMetadata = $privateKeyMetadata;
  }
  /**
   * @return CsePrivateKeyMetadata[]
   */
  public function getPrivateKeyMetadata()
  {
    return $this->privateKeyMetadata;
  }
  /**
   * Output only. The email address identities that are specified on the leaf
   * certificate.
   *
   * @param string[] $subjectEmailAddresses
   */
  public function setSubjectEmailAddresses($subjectEmailAddresses)
  {
    $this->subjectEmailAddresses = $subjectEmailAddresses;
  }
  /**
   * @return string[]
   */
  public function getSubjectEmailAddresses()
  {
    return $this->subjectEmailAddresses;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CseKeyPair::class, 'Google_Service_Gmail_CseKeyPair');
