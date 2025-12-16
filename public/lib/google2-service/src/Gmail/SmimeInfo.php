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

class SmimeInfo extends \Google\Model
{
  /**
   * Encrypted key password, when key is encrypted.
   *
   * @var string
   */
  public $encryptedKeyPassword;
  /**
   * When the certificate expires (in milliseconds since epoch).
   *
   * @var string
   */
  public $expiration;
  /**
   * The immutable ID for the SmimeInfo.
   *
   * @var string
   */
  public $id;
  /**
   * Whether this SmimeInfo is the default one for this user's send-as address.
   *
   * @var bool
   */
  public $isDefault;
  /**
   * The S/MIME certificate issuer's common name.
   *
   * @var string
   */
  public $issuerCn;
  /**
   * PEM formatted X509 concatenated certificate string (standard base64
   * encoding). Format used for returning key, which includes public key as well
   * as certificate chain (not private key).
   *
   * @var string
   */
  public $pem;
  /**
   * PKCS#12 format containing a single private/public key pair and certificate
   * chain. This format is only accepted from client for creating a new
   * SmimeInfo and is never returned, because the private key is not intended to
   * be exported. PKCS#12 may be encrypted, in which case encryptedKeyPassword
   * should be set appropriately.
   *
   * @var string
   */
  public $pkcs12;

  /**
   * Encrypted key password, when key is encrypted.
   *
   * @param string $encryptedKeyPassword
   */
  public function setEncryptedKeyPassword($encryptedKeyPassword)
  {
    $this->encryptedKeyPassword = $encryptedKeyPassword;
  }
  /**
   * @return string
   */
  public function getEncryptedKeyPassword()
  {
    return $this->encryptedKeyPassword;
  }
  /**
   * When the certificate expires (in milliseconds since epoch).
   *
   * @param string $expiration
   */
  public function setExpiration($expiration)
  {
    $this->expiration = $expiration;
  }
  /**
   * @return string
   */
  public function getExpiration()
  {
    return $this->expiration;
  }
  /**
   * The immutable ID for the SmimeInfo.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Whether this SmimeInfo is the default one for this user's send-as address.
   *
   * @param bool $isDefault
   */
  public function setIsDefault($isDefault)
  {
    $this->isDefault = $isDefault;
  }
  /**
   * @return bool
   */
  public function getIsDefault()
  {
    return $this->isDefault;
  }
  /**
   * The S/MIME certificate issuer's common name.
   *
   * @param string $issuerCn
   */
  public function setIssuerCn($issuerCn)
  {
    $this->issuerCn = $issuerCn;
  }
  /**
   * @return string
   */
  public function getIssuerCn()
  {
    return $this->issuerCn;
  }
  /**
   * PEM formatted X509 concatenated certificate string (standard base64
   * encoding). Format used for returning key, which includes public key as well
   * as certificate chain (not private key).
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
   * PKCS#12 format containing a single private/public key pair and certificate
   * chain. This format is only accepted from client for creating a new
   * SmimeInfo and is never returned, because the private key is not intended to
   * be exported. PKCS#12 may be encrypted, in which case encryptedKeyPassword
   * should be set appropriately.
   *
   * @param string $pkcs12
   */
  public function setPkcs12($pkcs12)
  {
    $this->pkcs12 = $pkcs12;
  }
  /**
   * @return string
   */
  public function getPkcs12()
  {
    return $this->pkcs12;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SmimeInfo::class, 'Google_Service_Gmail_SmimeInfo');
