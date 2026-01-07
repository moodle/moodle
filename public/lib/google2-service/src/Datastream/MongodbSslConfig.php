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

class MongodbSslConfig extends \Google\Model
{
  /**
   * Optional. Input only. PEM-encoded certificate of the CA that signed the
   * source database server's certificate.
   *
   * @var string
   */
  public $caCertificate;
  /**
   * Output only. Indicates whether the ca_certificate field is set.
   *
   * @var bool
   */
  public $caCertificateSet;
  /**
   * Optional. Input only. PEM-encoded certificate that will be used by the
   * replica to authenticate against the source database server. If this field
   * is used then the 'client_key' and the 'ca_certificate' fields are
   * mandatory.
   *
   * @var string
   */
  public $clientCertificate;
  /**
   * Output only. Indicates whether the client_certificate field is set.
   *
   * @var bool
   */
  public $clientCertificateSet;
  /**
   * Optional. Input only. PEM-encoded private key associated with the Client
   * Certificate. If this field is used then the 'client_certificate' and the
   * 'ca_certificate' fields are mandatory.
   *
   * @var string
   */
  public $clientKey;
  /**
   * Output only. Indicates whether the client_key field is set.
   *
   * @var bool
   */
  public $clientKeySet;
  /**
   * Optional. Input only. A reference to a Secret Manager resource name storing
   * the PEM-encoded private key associated with the Client Certificate. If this
   * field is used then the 'client_certificate' and the 'ca_certificate' fields
   * are mandatory. Mutually exclusive with the `client_key` field.
   *
   * @var string
   */
  public $secretManagerStoredClientKey;

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
   * Output only. Indicates whether the ca_certificate field is set.
   *
   * @param bool $caCertificateSet
   */
  public function setCaCertificateSet($caCertificateSet)
  {
    $this->caCertificateSet = $caCertificateSet;
  }
  /**
   * @return bool
   */
  public function getCaCertificateSet()
  {
    return $this->caCertificateSet;
  }
  /**
   * Optional. Input only. PEM-encoded certificate that will be used by the
   * replica to authenticate against the source database server. If this field
   * is used then the 'client_key' and the 'ca_certificate' fields are
   * mandatory.
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
   * Output only. Indicates whether the client_certificate field is set.
   *
   * @param bool $clientCertificateSet
   */
  public function setClientCertificateSet($clientCertificateSet)
  {
    $this->clientCertificateSet = $clientCertificateSet;
  }
  /**
   * @return bool
   */
  public function getClientCertificateSet()
  {
    return $this->clientCertificateSet;
  }
  /**
   * Optional. Input only. PEM-encoded private key associated with the Client
   * Certificate. If this field is used then the 'client_certificate' and the
   * 'ca_certificate' fields are mandatory.
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
   * Output only. Indicates whether the client_key field is set.
   *
   * @param bool $clientKeySet
   */
  public function setClientKeySet($clientKeySet)
  {
    $this->clientKeySet = $clientKeySet;
  }
  /**
   * @return bool
   */
  public function getClientKeySet()
  {
    return $this->clientKeySet;
  }
  /**
   * Optional. Input only. A reference to a Secret Manager resource name storing
   * the PEM-encoded private key associated with the Client Certificate. If this
   * field is used then the 'client_certificate' and the 'ca_certificate' fields
   * are mandatory. Mutually exclusive with the `client_key` field.
   *
   * @param string $secretManagerStoredClientKey
   */
  public function setSecretManagerStoredClientKey($secretManagerStoredClientKey)
  {
    $this->secretManagerStoredClientKey = $secretManagerStoredClientKey;
  }
  /**
   * @return string
   */
  public function getSecretManagerStoredClientKey()
  {
    return $this->secretManagerStoredClientKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MongodbSslConfig::class, 'Google_Service_Datastream_MongodbSslConfig');
