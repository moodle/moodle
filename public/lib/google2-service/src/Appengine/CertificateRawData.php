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

namespace Google\Service\Appengine;

class CertificateRawData extends \Google\Model
{
  /**
   * Unencrypted PEM encoded RSA private key. This field is set once on
   * certificate creation and then encrypted. The key size must be 2048 bits or
   * fewer. Must include the header and footer. Example: -----BEGIN RSA PRIVATE
   * KEY----- -----END RSA PRIVATE KEY----- @InputOnly
   *
   * @var string
   */
  public $privateKey;
  /**
   * PEM encoded x.509 public key certificate. This field is set once on
   * certificate creation. Must include the header and footer. Example:
   * -----BEGIN CERTIFICATE----- -----END CERTIFICATE-----
   *
   * @var string
   */
  public $publicCertificate;

  /**
   * Unencrypted PEM encoded RSA private key. This field is set once on
   * certificate creation and then encrypted. The key size must be 2048 bits or
   * fewer. Must include the header and footer. Example: -----BEGIN RSA PRIVATE
   * KEY----- -----END RSA PRIVATE KEY----- @InputOnly
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
   * PEM encoded x.509 public key certificate. This field is set once on
   * certificate creation. Must include the header and footer. Example:
   * -----BEGIN CERTIFICATE----- -----END CERTIFICATE-----
   *
   * @param string $publicCertificate
   */
  public function setPublicCertificate($publicCertificate)
  {
    $this->publicCertificate = $publicCertificate;
  }
  /**
   * @return string
   */
  public function getPublicCertificate()
  {
    return $this->publicCertificate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CertificateRawData::class, 'Google_Service_Appengine_CertificateRawData');
