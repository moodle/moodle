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

namespace Google\Service\Compute;

class SslCertificateSelfManagedSslCertificate extends \Google\Model
{
  /**
   * A local certificate file. The certificate must be in PEM format. The
   * certificate chain must be no greater than 5 certs long. The chain must
   * include at least one intermediate cert.
   *
   * @var string
   */
  public $certificate;
  /**
   * A write-only private key in PEM format. Only insert requests will include
   * this field.
   *
   * @var string
   */
  public $privateKey;

  /**
   * A local certificate file. The certificate must be in PEM format. The
   * certificate chain must be no greater than 5 certs long. The chain must
   * include at least one intermediate cert.
   *
   * @param string $certificate
   */
  public function setCertificate($certificate)
  {
    $this->certificate = $certificate;
  }
  /**
   * @return string
   */
  public function getCertificate()
  {
    return $this->certificate;
  }
  /**
   * A write-only private key in PEM format. Only insert requests will include
   * this field.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SslCertificateSelfManagedSslCertificate::class, 'Google_Service_Compute_SslCertificateSelfManagedSslCertificate');
