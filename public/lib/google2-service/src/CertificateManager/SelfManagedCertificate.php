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

namespace Google\Service\CertificateManager;

class SelfManagedCertificate extends \Google\Model
{
  /**
   * Optional. Input only. The PEM-encoded certificate chain. Leaf certificate
   * comes first, followed by intermediate ones if any.
   *
   * @var string
   */
  public $pemCertificate;
  /**
   * Optional. Input only. The PEM-encoded private key of the leaf certificate.
   *
   * @var string
   */
  public $pemPrivateKey;

  /**
   * Optional. Input only. The PEM-encoded certificate chain. Leaf certificate
   * comes first, followed by intermediate ones if any.
   *
   * @param string $pemCertificate
   */
  public function setPemCertificate($pemCertificate)
  {
    $this->pemCertificate = $pemCertificate;
  }
  /**
   * @return string
   */
  public function getPemCertificate()
  {
    return $this->pemCertificate;
  }
  /**
   * Optional. Input only. The PEM-encoded private key of the leaf certificate.
   *
   * @param string $pemPrivateKey
   */
  public function setPemPrivateKey($pemPrivateKey)
  {
    $this->pemPrivateKey = $pemPrivateKey;
  }
  /**
   * @return string
   */
  public function getPemPrivateKey()
  {
    return $this->pemPrivateKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SelfManagedCertificate::class, 'Google_Service_CertificateManager_SelfManagedCertificate');
