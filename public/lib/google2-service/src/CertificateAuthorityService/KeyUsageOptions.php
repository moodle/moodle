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

namespace Google\Service\CertificateAuthorityService;

class KeyUsageOptions extends \Google\Model
{
  /**
   * The key may be used to sign certificates.
   *
   * @var bool
   */
  public $certSign;
  /**
   * The key may be used for cryptographic commitments. Note that this may also
   * be referred to as "non-repudiation".
   *
   * @var bool
   */
  public $contentCommitment;
  /**
   * The key may be used sign certificate revocation lists.
   *
   * @var bool
   */
  public $crlSign;
  /**
   * The key may be used to encipher data.
   *
   * @var bool
   */
  public $dataEncipherment;
  /**
   * The key may be used to decipher only.
   *
   * @var bool
   */
  public $decipherOnly;
  /**
   * The key may be used for digital signatures.
   *
   * @var bool
   */
  public $digitalSignature;
  /**
   * The key may be used to encipher only.
   *
   * @var bool
   */
  public $encipherOnly;
  /**
   * The key may be used in a key agreement protocol.
   *
   * @var bool
   */
  public $keyAgreement;
  /**
   * The key may be used to encipher other keys.
   *
   * @var bool
   */
  public $keyEncipherment;

  /**
   * The key may be used to sign certificates.
   *
   * @param bool $certSign
   */
  public function setCertSign($certSign)
  {
    $this->certSign = $certSign;
  }
  /**
   * @return bool
   */
  public function getCertSign()
  {
    return $this->certSign;
  }
  /**
   * The key may be used for cryptographic commitments. Note that this may also
   * be referred to as "non-repudiation".
   *
   * @param bool $contentCommitment
   */
  public function setContentCommitment($contentCommitment)
  {
    $this->contentCommitment = $contentCommitment;
  }
  /**
   * @return bool
   */
  public function getContentCommitment()
  {
    return $this->contentCommitment;
  }
  /**
   * The key may be used sign certificate revocation lists.
   *
   * @param bool $crlSign
   */
  public function setCrlSign($crlSign)
  {
    $this->crlSign = $crlSign;
  }
  /**
   * @return bool
   */
  public function getCrlSign()
  {
    return $this->crlSign;
  }
  /**
   * The key may be used to encipher data.
   *
   * @param bool $dataEncipherment
   */
  public function setDataEncipherment($dataEncipherment)
  {
    $this->dataEncipherment = $dataEncipherment;
  }
  /**
   * @return bool
   */
  public function getDataEncipherment()
  {
    return $this->dataEncipherment;
  }
  /**
   * The key may be used to decipher only.
   *
   * @param bool $decipherOnly
   */
  public function setDecipherOnly($decipherOnly)
  {
    $this->decipherOnly = $decipherOnly;
  }
  /**
   * @return bool
   */
  public function getDecipherOnly()
  {
    return $this->decipherOnly;
  }
  /**
   * The key may be used for digital signatures.
   *
   * @param bool $digitalSignature
   */
  public function setDigitalSignature($digitalSignature)
  {
    $this->digitalSignature = $digitalSignature;
  }
  /**
   * @return bool
   */
  public function getDigitalSignature()
  {
    return $this->digitalSignature;
  }
  /**
   * The key may be used to encipher only.
   *
   * @param bool $encipherOnly
   */
  public function setEncipherOnly($encipherOnly)
  {
    $this->encipherOnly = $encipherOnly;
  }
  /**
   * @return bool
   */
  public function getEncipherOnly()
  {
    return $this->encipherOnly;
  }
  /**
   * The key may be used in a key agreement protocol.
   *
   * @param bool $keyAgreement
   */
  public function setKeyAgreement($keyAgreement)
  {
    $this->keyAgreement = $keyAgreement;
  }
  /**
   * @return bool
   */
  public function getKeyAgreement()
  {
    return $this->keyAgreement;
  }
  /**
   * The key may be used to encipher other keys.
   *
   * @param bool $keyEncipherment
   */
  public function setKeyEncipherment($keyEncipherment)
  {
    $this->keyEncipherment = $keyEncipherment;
  }
  /**
   * @return bool
   */
  public function getKeyEncipherment()
  {
    return $this->keyEncipherment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(KeyUsageOptions::class, 'Google_Service_CertificateAuthorityService_KeyUsageOptions');
