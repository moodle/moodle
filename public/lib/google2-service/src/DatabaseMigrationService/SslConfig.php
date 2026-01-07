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

namespace Google\Service\DatabaseMigrationService;

class SslConfig extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const TYPE_SSL_TYPE_UNSPECIFIED = 'SSL_TYPE_UNSPECIFIED';
  /**
   * Only 'ca_certificate' specified.
   */
  public const TYPE_SERVER_ONLY = 'SERVER_ONLY';
  /**
   * Both server ('ca_certificate'), and client ('client_key',
   * 'client_certificate') specified.
   */
  public const TYPE_SERVER_CLIENT = 'SERVER_CLIENT';
  /**
   * Mandates SSL encryption for all connections. This doesn’t require
   * certificate verification.
   */
  public const TYPE_REQUIRED = 'REQUIRED';
  /**
   * Connection is not encrypted.
   */
  public const TYPE_NONE = 'NONE';
  /**
   * Required. Input only. The x509 PEM-encoded certificate of the CA that
   * signed the source database server's certificate. The replica will use this
   * certificate to verify it's connecting to the right host.
   *
   * @var string
   */
  public $caCertificate;
  /**
   * Input only. The x509 PEM-encoded certificate that will be used by the
   * replica to authenticate against the source database server.If this field is
   * used then the 'client_key' field is mandatory.
   *
   * @var string
   */
  public $clientCertificate;
  /**
   * Input only. The unencrypted PKCS#1 or PKCS#8 PEM-encoded private key
   * associated with the Client Certificate. If this field is used then the
   * 'client_certificate' field is mandatory.
   *
   * @var string
   */
  public $clientKey;
  /**
   * Optional. SSL flags used for establishing SSL connection to the source
   * database. Only source specific flags are supported. An object containing a
   * list of "key": "value" pairs. Example: { "server_certificate_hostname":
   * "server.com"}.
   *
   * @var string[]
   */
  public $sslFlags;
  /**
   * Optional. The ssl config type according to 'client_key',
   * 'client_certificate' and 'ca_certificate'.
   *
   * @var string
   */
  public $type;

  /**
   * Required. Input only. The x509 PEM-encoded certificate of the CA that
   * signed the source database server's certificate. The replica will use this
   * certificate to verify it's connecting to the right host.
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
   * Input only. The x509 PEM-encoded certificate that will be used by the
   * replica to authenticate against the source database server.If this field is
   * used then the 'client_key' field is mandatory.
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
   * Input only. The unencrypted PKCS#1 or PKCS#8 PEM-encoded private key
   * associated with the Client Certificate. If this field is used then the
   * 'client_certificate' field is mandatory.
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
   * Optional. SSL flags used for establishing SSL connection to the source
   * database. Only source specific flags are supported. An object containing a
   * list of "key": "value" pairs. Example: { "server_certificate_hostname":
   * "server.com"}.
   *
   * @param string[] $sslFlags
   */
  public function setSslFlags($sslFlags)
  {
    $this->sslFlags = $sslFlags;
  }
  /**
   * @return string[]
   */
  public function getSslFlags()
  {
    return $this->sslFlags;
  }
  /**
   * Optional. The ssl config type according to 'client_key',
   * 'client_certificate' and 'ca_certificate'.
   *
   * Accepted values: SSL_TYPE_UNSPECIFIED, SERVER_ONLY, SERVER_CLIENT,
   * REQUIRED, NONE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SslConfig::class, 'Google_Service_DatabaseMigrationService_SslConfig');
