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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1TlsInfo extends \Google\Collection
{
  protected $collection_key = 'protocols';
  /**
   * The SSL/TLS cipher suites to be used. For programmable proxies, it must be
   * one of the cipher suite names listed in: http://docs.oracle.com/javase/8/do
   * cs/technotes/guides/security/StandardNames.html#ciphersuites. For
   * configurable proxies, it must follow the configuration specified in:
   * https://commondatastorage.googleapis.com/chromium-boringssl-
   * docs/ssl.h.html#Cipher-suite-configuration. This setting has no effect for
   * configurable proxies when negotiating TLS 1.3.
   *
   * @var string[]
   */
  public $ciphers;
  /**
   * Optional. Enables two-way TLS.
   *
   * @var bool
   */
  public $clientAuthEnabled;
  protected $commonNameType = GoogleCloudApigeeV1TlsInfoCommonName::class;
  protected $commonNameDataType = '';
  /**
   * Required. Enables TLS. If false, neither one-way nor two-way TLS will be
   * enabled.
   *
   * @var bool
   */
  public $enabled;
  /**
   * TLS is strictly enforced.
   *
   * @var bool
   */
  public $enforce;
  /**
   * If true, Edge ignores TLS certificate errors. Valid when configuring TLS
   * for target servers and target endpoints, and when configuring virtual hosts
   * that use 2-way TLS. When used with a target endpoint/target server, if the
   * backend system uses SNI and returns a cert with a subject Distinguished
   * Name (DN) that does not match the hostname, there is no way to ignore the
   * error and the connection fails.
   *
   * @var bool
   */
  public $ignoreValidationErrors;
  /**
   * Required if `client_auth_enabled` is true. The resource ID for the alias
   * containing the private key and cert.
   *
   * @var string
   */
  public $keyAlias;
  /**
   * Required if `client_auth_enabled` is true. The resource ID of the keystore.
   *
   * @var string
   */
  public $keyStore;
  /**
   * The TLS versioins to be used.
   *
   * @var string[]
   */
  public $protocols;
  /**
   * The resource ID of the truststore.
   *
   * @var string
   */
  public $trustStore;

  /**
   * The SSL/TLS cipher suites to be used. For programmable proxies, it must be
   * one of the cipher suite names listed in: http://docs.oracle.com/javase/8/do
   * cs/technotes/guides/security/StandardNames.html#ciphersuites. For
   * configurable proxies, it must follow the configuration specified in:
   * https://commondatastorage.googleapis.com/chromium-boringssl-
   * docs/ssl.h.html#Cipher-suite-configuration. This setting has no effect for
   * configurable proxies when negotiating TLS 1.3.
   *
   * @param string[] $ciphers
   */
  public function setCiphers($ciphers)
  {
    $this->ciphers = $ciphers;
  }
  /**
   * @return string[]
   */
  public function getCiphers()
  {
    return $this->ciphers;
  }
  /**
   * Optional. Enables two-way TLS.
   *
   * @param bool $clientAuthEnabled
   */
  public function setClientAuthEnabled($clientAuthEnabled)
  {
    $this->clientAuthEnabled = $clientAuthEnabled;
  }
  /**
   * @return bool
   */
  public function getClientAuthEnabled()
  {
    return $this->clientAuthEnabled;
  }
  /**
   * The TLS Common Name of the certificate.
   *
   * @param GoogleCloudApigeeV1TlsInfoCommonName $commonName
   */
  public function setCommonName(GoogleCloudApigeeV1TlsInfoCommonName $commonName)
  {
    $this->commonName = $commonName;
  }
  /**
   * @return GoogleCloudApigeeV1TlsInfoCommonName
   */
  public function getCommonName()
  {
    return $this->commonName;
  }
  /**
   * Required. Enables TLS. If false, neither one-way nor two-way TLS will be
   * enabled.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * TLS is strictly enforced.
   *
   * @param bool $enforce
   */
  public function setEnforce($enforce)
  {
    $this->enforce = $enforce;
  }
  /**
   * @return bool
   */
  public function getEnforce()
  {
    return $this->enforce;
  }
  /**
   * If true, Edge ignores TLS certificate errors. Valid when configuring TLS
   * for target servers and target endpoints, and when configuring virtual hosts
   * that use 2-way TLS. When used with a target endpoint/target server, if the
   * backend system uses SNI and returns a cert with a subject Distinguished
   * Name (DN) that does not match the hostname, there is no way to ignore the
   * error and the connection fails.
   *
   * @param bool $ignoreValidationErrors
   */
  public function setIgnoreValidationErrors($ignoreValidationErrors)
  {
    $this->ignoreValidationErrors = $ignoreValidationErrors;
  }
  /**
   * @return bool
   */
  public function getIgnoreValidationErrors()
  {
    return $this->ignoreValidationErrors;
  }
  /**
   * Required if `client_auth_enabled` is true. The resource ID for the alias
   * containing the private key and cert.
   *
   * @param string $keyAlias
   */
  public function setKeyAlias($keyAlias)
  {
    $this->keyAlias = $keyAlias;
  }
  /**
   * @return string
   */
  public function getKeyAlias()
  {
    return $this->keyAlias;
  }
  /**
   * Required if `client_auth_enabled` is true. The resource ID of the keystore.
   *
   * @param string $keyStore
   */
  public function setKeyStore($keyStore)
  {
    $this->keyStore = $keyStore;
  }
  /**
   * @return string
   */
  public function getKeyStore()
  {
    return $this->keyStore;
  }
  /**
   * The TLS versioins to be used.
   *
   * @param string[] $protocols
   */
  public function setProtocols($protocols)
  {
    $this->protocols = $protocols;
  }
  /**
   * @return string[]
   */
  public function getProtocols()
  {
    return $this->protocols;
  }
  /**
   * The resource ID of the truststore.
   *
   * @param string $trustStore
   */
  public function setTrustStore($trustStore)
  {
    $this->trustStore = $trustStore;
  }
  /**
   * @return string
   */
  public function getTrustStore()
  {
    return $this->trustStore;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1TlsInfo::class, 'Google_Service_Apigee_GoogleCloudApigeeV1TlsInfo');
