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

namespace Google\Service\Integrations;

class GoogleCloudConnectorsV1SslConfig extends \Google\Collection
{
  /**
   * Cert type unspecified.
   */
  public const CLIENT_CERT_TYPE_CERT_TYPE_UNSPECIFIED = 'CERT_TYPE_UNSPECIFIED';
  /**
   * Privacy Enhanced Mail (PEM) Type
   */
  public const CLIENT_CERT_TYPE_PEM = 'PEM';
  /**
   * Cert type unspecified.
   */
  public const SERVER_CERT_TYPE_CERT_TYPE_UNSPECIFIED = 'CERT_TYPE_UNSPECIFIED';
  /**
   * Privacy Enhanced Mail (PEM) Type
   */
  public const SERVER_CERT_TYPE_PEM = 'PEM';
  /**
   * Public Trust Model. Takes the Default Java trust store.
   */
  public const TRUST_MODEL_PUBLIC = 'PUBLIC';
  /**
   * Private Trust Model. Takes custom/private trust store.
   */
  public const TRUST_MODEL_PRIVATE = 'PRIVATE';
  /**
   * Insecure Trust Model. Accept all certificates.
   */
  public const TRUST_MODEL_INSECURE = 'INSECURE';
  /**
   * No SSL configuration required.
   */
  public const TYPE_SSL_TYPE_UNSPECIFIED = 'SSL_TYPE_UNSPECIFIED';
  /**
   * TLS Handshake
   */
  public const TYPE_TLS = 'TLS';
  /**
   * mutual TLS (MTLS) Handshake
   */
  public const TYPE_MTLS = 'MTLS';
  protected $collection_key = 'additionalVariables';
  protected $additionalVariablesType = GoogleCloudConnectorsV1ConfigVariable::class;
  protected $additionalVariablesDataType = 'array';
  /**
   * Optional. Type of Client Cert (PEM/JKS/.. etc.)
   *
   * @var string
   */
  public $clientCertType;
  protected $clientCertificateType = GoogleCloudConnectorsV1Secret::class;
  protected $clientCertificateDataType = '';
  protected $clientPrivateKeyType = GoogleCloudConnectorsV1Secret::class;
  protected $clientPrivateKeyDataType = '';
  protected $clientPrivateKeyPassType = GoogleCloudConnectorsV1Secret::class;
  protected $clientPrivateKeyPassDataType = '';
  protected $privateServerCertificateType = GoogleCloudConnectorsV1Secret::class;
  protected $privateServerCertificateDataType = '';
  /**
   * Optional. Type of Server Cert (PEM/JKS/.. etc.)
   *
   * @var string
   */
  public $serverCertType;
  /**
   * Optional. Trust Model of the SSL connection
   *
   * @var string
   */
  public $trustModel;
  /**
   * Optional. Controls the ssl type for the given connector version.
   *
   * @var string
   */
  public $type;
  /**
   * Optional. Bool for enabling SSL
   *
   * @var bool
   */
  public $useSsl;

  /**
   * Optional. Additional SSL related field values
   *
   * @param GoogleCloudConnectorsV1ConfigVariable[] $additionalVariables
   */
  public function setAdditionalVariables($additionalVariables)
  {
    $this->additionalVariables = $additionalVariables;
  }
  /**
   * @return GoogleCloudConnectorsV1ConfigVariable[]
   */
  public function getAdditionalVariables()
  {
    return $this->additionalVariables;
  }
  /**
   * Optional. Type of Client Cert (PEM/JKS/.. etc.)
   *
   * Accepted values: CERT_TYPE_UNSPECIFIED, PEM
   *
   * @param self::CLIENT_CERT_TYPE_* $clientCertType
   */
  public function setClientCertType($clientCertType)
  {
    $this->clientCertType = $clientCertType;
  }
  /**
   * @return self::CLIENT_CERT_TYPE_*
   */
  public function getClientCertType()
  {
    return $this->clientCertType;
  }
  /**
   * Optional. Client Certificate
   *
   * @param GoogleCloudConnectorsV1Secret $clientCertificate
   */
  public function setClientCertificate(GoogleCloudConnectorsV1Secret $clientCertificate)
  {
    $this->clientCertificate = $clientCertificate;
  }
  /**
   * @return GoogleCloudConnectorsV1Secret
   */
  public function getClientCertificate()
  {
    return $this->clientCertificate;
  }
  /**
   * Optional. Client Private Key
   *
   * @param GoogleCloudConnectorsV1Secret $clientPrivateKey
   */
  public function setClientPrivateKey(GoogleCloudConnectorsV1Secret $clientPrivateKey)
  {
    $this->clientPrivateKey = $clientPrivateKey;
  }
  /**
   * @return GoogleCloudConnectorsV1Secret
   */
  public function getClientPrivateKey()
  {
    return $this->clientPrivateKey;
  }
  /**
   * Optional. Secret containing the passphrase protecting the Client Private
   * Key
   *
   * @param GoogleCloudConnectorsV1Secret $clientPrivateKeyPass
   */
  public function setClientPrivateKeyPass(GoogleCloudConnectorsV1Secret $clientPrivateKeyPass)
  {
    $this->clientPrivateKeyPass = $clientPrivateKeyPass;
  }
  /**
   * @return GoogleCloudConnectorsV1Secret
   */
  public function getClientPrivateKeyPass()
  {
    return $this->clientPrivateKeyPass;
  }
  /**
   * Optional. Private Server Certificate. Needs to be specified if trust model
   * is `PRIVATE`.
   *
   * @param GoogleCloudConnectorsV1Secret $privateServerCertificate
   */
  public function setPrivateServerCertificate(GoogleCloudConnectorsV1Secret $privateServerCertificate)
  {
    $this->privateServerCertificate = $privateServerCertificate;
  }
  /**
   * @return GoogleCloudConnectorsV1Secret
   */
  public function getPrivateServerCertificate()
  {
    return $this->privateServerCertificate;
  }
  /**
   * Optional. Type of Server Cert (PEM/JKS/.. etc.)
   *
   * Accepted values: CERT_TYPE_UNSPECIFIED, PEM
   *
   * @param self::SERVER_CERT_TYPE_* $serverCertType
   */
  public function setServerCertType($serverCertType)
  {
    $this->serverCertType = $serverCertType;
  }
  /**
   * @return self::SERVER_CERT_TYPE_*
   */
  public function getServerCertType()
  {
    return $this->serverCertType;
  }
  /**
   * Optional. Trust Model of the SSL connection
   *
   * Accepted values: PUBLIC, PRIVATE, INSECURE
   *
   * @param self::TRUST_MODEL_* $trustModel
   */
  public function setTrustModel($trustModel)
  {
    $this->trustModel = $trustModel;
  }
  /**
   * @return self::TRUST_MODEL_*
   */
  public function getTrustModel()
  {
    return $this->trustModel;
  }
  /**
   * Optional. Controls the ssl type for the given connector version.
   *
   * Accepted values: SSL_TYPE_UNSPECIFIED, TLS, MTLS
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
  /**
   * Optional. Bool for enabling SSL
   *
   * @param bool $useSsl
   */
  public function setUseSsl($useSsl)
  {
    $this->useSsl = $useSsl;
  }
  /**
   * @return bool
   */
  public function getUseSsl()
  {
    return $this->useSsl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudConnectorsV1SslConfig::class, 'Google_Service_Integrations_GoogleCloudConnectorsV1SslConfig');
