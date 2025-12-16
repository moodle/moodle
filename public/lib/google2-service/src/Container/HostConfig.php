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

namespace Google\Service\Container;

class HostConfig extends \Google\Collection
{
  protected $collection_key = 'header';
  protected $caType = CertificateConfig::class;
  protected $caDataType = 'array';
  /**
   * Capabilities represent the capabilities of the registry host, specifying
   * what operations a host is capable of performing. If not set, containerd
   * enables all capabilities by default.
   *
   * @var string[]
   */
  public $capabilities;
  protected $clientType = CertificateConfigPair::class;
  protected $clientDataType = 'array';
  /**
   * Specifies the maximum duration allowed for a connection attempt to
   * complete. A shorter timeout helps reduce delays when falling back to the
   * original registry if the mirror is unreachable. Maximum allowed value is
   * 180s. If not set, containerd sets default 30s. The value should be a
   * decimal number of seconds with an `s` suffix.
   *
   * @var string
   */
  public $dialTimeout;
  protected $headerType = RegistryHeader::class;
  protected $headerDataType = 'array';
  /**
   * Host configures the registry host/mirror. It supports fully qualified
   * domain names (FQDN) and IP addresses: Specifying port is supported.
   * Wildcards are NOT supported. Examples: - my.customdomain.com -
   * 10.0.1.2:5000
   *
   * @var string
   */
  public $host;
  /**
   * OverridePath is used to indicate the host's API root endpoint is defined in
   * the URL path rather than by the API specification. This may be used with
   * non-compliant OCI registries which are missing the /v2 prefix. If not set,
   * containerd sets default false.
   *
   * @var bool
   */
  public $overridePath;

  /**
   * CA configures the registry host certificate.
   *
   * @param CertificateConfig[] $ca
   */
  public function setCa($ca)
  {
    $this->ca = $ca;
  }
  /**
   * @return CertificateConfig[]
   */
  public function getCa()
  {
    return $this->ca;
  }
  /**
   * Capabilities represent the capabilities of the registry host, specifying
   * what operations a host is capable of performing. If not set, containerd
   * enables all capabilities by default.
   *
   * @param string[] $capabilities
   */
  public function setCapabilities($capabilities)
  {
    $this->capabilities = $capabilities;
  }
  /**
   * @return string[]
   */
  public function getCapabilities()
  {
    return $this->capabilities;
  }
  /**
   * Client configures the registry host client certificate and key.
   *
   * @param CertificateConfigPair[] $client
   */
  public function setClient($client)
  {
    $this->client = $client;
  }
  /**
   * @return CertificateConfigPair[]
   */
  public function getClient()
  {
    return $this->client;
  }
  /**
   * Specifies the maximum duration allowed for a connection attempt to
   * complete. A shorter timeout helps reduce delays when falling back to the
   * original registry if the mirror is unreachable. Maximum allowed value is
   * 180s. If not set, containerd sets default 30s. The value should be a
   * decimal number of seconds with an `s` suffix.
   *
   * @param string $dialTimeout
   */
  public function setDialTimeout($dialTimeout)
  {
    $this->dialTimeout = $dialTimeout;
  }
  /**
   * @return string
   */
  public function getDialTimeout()
  {
    return $this->dialTimeout;
  }
  /**
   * Header configures the registry host headers.
   *
   * @param RegistryHeader[] $header
   */
  public function setHeader($header)
  {
    $this->header = $header;
  }
  /**
   * @return RegistryHeader[]
   */
  public function getHeader()
  {
    return $this->header;
  }
  /**
   * Host configures the registry host/mirror. It supports fully qualified
   * domain names (FQDN) and IP addresses: Specifying port is supported.
   * Wildcards are NOT supported. Examples: - my.customdomain.com -
   * 10.0.1.2:5000
   *
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * OverridePath is used to indicate the host's API root endpoint is defined in
   * the URL path rather than by the API specification. This may be used with
   * non-compliant OCI registries which are missing the /v2 prefix. If not set,
   * containerd sets default false.
   *
   * @param bool $overridePath
   */
  public function setOverridePath($overridePath)
  {
    $this->overridePath = $overridePath;
  }
  /**
   * @return bool
   */
  public function getOverridePath()
  {
    return $this->overridePath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HostConfig::class, 'Google_Service_Container_HostConfig');
