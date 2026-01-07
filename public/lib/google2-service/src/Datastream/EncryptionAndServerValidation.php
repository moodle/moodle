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

namespace Google\Service\Datastream;

class EncryptionAndServerValidation extends \Google\Model
{
  /**
   * Optional. Input only. PEM-encoded certificate of the CA that signed the
   * source database server's certificate.
   *
   * @var string
   */
  public $caCertificate;
  /**
   * Optional. The hostname mentioned in the Subject or SAN extension of the
   * server certificate. This field is used for bypassing the hostname
   * validation while verifying server certificate. This is required for
   * scenarios where the host name that datastream connects to is different from
   * the certificate's subject. This specifically happens for private
   * connectivity. It could also happen when the customer provides a public IP
   * in connection profile but the same is not present in the server
   * certificate.
   *
   * @var string
   */
  public $serverCertificateHostname;

  /**
   * Optional. Input only. PEM-encoded certificate of the CA that signed the
   * source database server's certificate.
   *
   * @param string $caCertificate
   */
  public function setCaCertificate($caCertificate)
  {
    $this->caCertificate = $caCertificate;
  }
  /**
   * @return string
   */
  public function getCaCertificate()
  {
    return $this->caCertificate;
  }
  /**
   * Optional. The hostname mentioned in the Subject or SAN extension of the
   * server certificate. This field is used for bypassing the hostname
   * validation while verifying server certificate. This is required for
   * scenarios where the host name that datastream connects to is different from
   * the certificate's subject. This specifically happens for private
   * connectivity. It could also happen when the customer provides a public IP
   * in connection profile but the same is not present in the server
   * certificate.
   *
   * @param string $serverCertificateHostname
   */
  public function setServerCertificateHostname($serverCertificateHostname)
  {
    $this->serverCertificateHostname = $serverCertificateHostname;
  }
  /**
   * @return string
   */
  public function getServerCertificateHostname()
  {
    return $this->serverCertificateHostname;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EncryptionAndServerValidation::class, 'Google_Service_Datastream_EncryptionAndServerValidation');
