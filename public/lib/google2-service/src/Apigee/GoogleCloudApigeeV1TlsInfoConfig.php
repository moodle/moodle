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

class GoogleCloudApigeeV1TlsInfoConfig extends \Google\Collection
{
  protected $collection_key = 'protocols';
  /**
   * List of ciphers that are granted access.
   *
   * @var string[]
   */
  public $ciphers;
  /**
   * Flag that specifies whether client-side authentication is enabled for the
   * target server. Enables two-way TLS.
   *
   * @var bool
   */
  public $clientAuthEnabled;
  protected $commonNameType = GoogleCloudApigeeV1CommonNameConfig::class;
  protected $commonNameDataType = '';
  /**
   * Flag that specifies whether one-way TLS is enabled. Set to `true` to enable
   * one-way TLS.
   *
   * @var bool
   */
  public $enabled;
  /**
   * Flag that enforces TLS settings
   *
   * @var bool
   */
  public $enforce;
  /**
   * Flag that specifies whether to ignore TLS certificate validation errors.
   * Set to `true` to ignore errors.
   *
   * @var bool
   */
  public $ignoreValidationErrors;
  /**
   * Name of the alias used for client-side authentication in the following
   * format: `organizations/{org}/environments/{env}/keystores/{keystore}/aliase
   * s/{alias}`
   *
   * @var string
   */
  public $keyAlias;
  protected $keyAliasReferenceType = GoogleCloudApigeeV1KeyAliasReference::class;
  protected $keyAliasReferenceDataType = '';
  /**
   * List of TLS protocols that are granted access.
   *
   * @var string[]
   */
  public $protocols;
  /**
   * Name of the keystore or keystore reference containing trusted certificates
   * for the server in the following format:
   * `organizations/{org}/environments/{env}/keystores/{keystore}` or
   * `organizations/{org}/environments/{env}/references/{reference}`
   *
   * @var string
   */
  public $trustStore;

  /**
   * List of ciphers that are granted access.
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
   * Flag that specifies whether client-side authentication is enabled for the
   * target server. Enables two-way TLS.
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
   * Common name to validate the target server against.
   *
   * @param GoogleCloudApigeeV1CommonNameConfig $commonName
   */
  public function setCommonName(GoogleCloudApigeeV1CommonNameConfig $commonName)
  {
    $this->commonName = $commonName;
  }
  /**
   * @return GoogleCloudApigeeV1CommonNameConfig
   */
  public function getCommonName()
  {
    return $this->commonName;
  }
  /**
   * Flag that specifies whether one-way TLS is enabled. Set to `true` to enable
   * one-way TLS.
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
   * Flag that enforces TLS settings
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
   * Flag that specifies whether to ignore TLS certificate validation errors.
   * Set to `true` to ignore errors.
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
   * Name of the alias used for client-side authentication in the following
   * format: `organizations/{org}/environments/{env}/keystores/{keystore}/aliase
   * s/{alias}`
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
   * Reference name and alias pair to use for client-side authentication.
   *
   * @param GoogleCloudApigeeV1KeyAliasReference $keyAliasReference
   */
  public function setKeyAliasReference(GoogleCloudApigeeV1KeyAliasReference $keyAliasReference)
  {
    $this->keyAliasReference = $keyAliasReference;
  }
  /**
   * @return GoogleCloudApigeeV1KeyAliasReference
   */
  public function getKeyAliasReference()
  {
    return $this->keyAliasReference;
  }
  /**
   * List of TLS protocols that are granted access.
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
   * Name of the keystore or keystore reference containing trusted certificates
   * for the server in the following format:
   * `organizations/{org}/environments/{env}/keystores/{keystore}` or
   * `organizations/{org}/environments/{env}/references/{reference}`
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
class_alias(GoogleCloudApigeeV1TlsInfoConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1TlsInfoConfig');
