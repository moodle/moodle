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

class ServerAndClientVerification extends \Google\Model
{
  /**
   * Required. Input only. PEM-encoded server root CA certificate.
   *
   * @var string
   */
  public $caCertificate;
  /**
   * Required. Input only. PEM-encoded certificate used by the source database
   * to authenticate the client identity (i.e., the Datastream's identity). This
   * certificate is signed by either a root certificate trusted by the server or
   * one or more intermediate certificates (which is stored with the leaf
   * certificate) to link the this certificate to the trusted root certificate.
   *
   * @var string
   */
  public $clientCertificate;
  /**
   * Optional. Input only. PEM-encoded private key associated with the client
   * certificate. This value will be used during the SSL/TLS handshake, allowing
   * the PostgreSQL server to authenticate the client's identity, i.e. identity
   * of the Datastream.
   *
   * @var string
   */
  public $clientKey;
  /**
   * Optional. The hostname mentioned in the Subject or SAN extension of the
   * server certificate. If this field is not provided, the hostname in the
   * server certificate is not validated.
   *
   * @var string
   */
  public $serverCertificateHostname;

  /**
   * Required. Input only. PEM-encoded server root CA certificate.
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
   * Required. Input only. PEM-encoded certificate used by the source database
   * to authenticate the client identity (i.e., the Datastream's identity). This
   * certificate is signed by either a root certificate trusted by the server or
   * one or more intermediate certificates (which is stored with the leaf
   * certificate) to link the this certificate to the trusted root certificate.
   *
   * @param string $clientCertificate
   */
  public function setClientCertificate($clientCertificate)
  {
    $this->clientCertificate = $clientCertificate;
  }
  /**
   * @return string
   */
  public function getClientCertificate()
  {
    return $this->clientCertificate;
  }
  /**
   * Optional. Input only. PEM-encoded private key associated with the client
   * certificate. This value will be used during the SSL/TLS handshake, allowing
   * the PostgreSQL server to authenticate the client's identity, i.e. identity
   * of the Datastream.
   *
   * @param string $clientKey
   */
  public function setClientKey($clientKey)
  {
    $this->clientKey = $clientKey;
  }
  /**
   * @return string
   */
  public function getClientKey()
  {
    return $this->clientKey;
  }
  /**
   * Optional. The hostname mentioned in the Subject or SAN extension of the
   * server certificate. If this field is not provided, the hostname in the
   * server certificate is not validated.
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
class_alias(ServerAndClientVerification::class, 'Google_Service_Datastream_ServerAndClientVerification');
