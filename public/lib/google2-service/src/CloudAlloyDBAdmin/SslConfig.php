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

namespace Google\Service\CloudAlloyDBAdmin;

class SslConfig extends \Google\Model
{
  /**
   * Certificate Authority (CA) source not specified. Defaults to
   * CA_SOURCE_MANAGED.
   */
  public const CA_SOURCE_CA_SOURCE_UNSPECIFIED = 'CA_SOURCE_UNSPECIFIED';
  /**
   * Certificate Authority (CA) managed by the AlloyDB Cluster.
   */
  public const CA_SOURCE_CA_SOURCE_MANAGED = 'CA_SOURCE_MANAGED';
  /**
   * SSL mode is not specified. Defaults to ENCRYPTED_ONLY.
   */
  public const SSL_MODE_SSL_MODE_UNSPECIFIED = 'SSL_MODE_UNSPECIFIED';
  /**
   * SSL connections are optional. CA verification not enforced.
   *
   * @deprecated
   */
  public const SSL_MODE_SSL_MODE_ALLOW = 'SSL_MODE_ALLOW';
  /**
   * SSL connections are required. CA verification not enforced. Clients may use
   * locally self-signed certificates (default psql client behavior).
   *
   * @deprecated
   */
  public const SSL_MODE_SSL_MODE_REQUIRE = 'SSL_MODE_REQUIRE';
  /**
   * SSL connections are required. CA verification enforced. Clients must have
   * certificates signed by a Cluster CA, for example, using
   * GenerateClientCertificate.
   *
   * @deprecated
   */
  public const SSL_MODE_SSL_MODE_VERIFY_CA = 'SSL_MODE_VERIFY_CA';
  /**
   * SSL connections are optional. CA verification not enforced.
   */
  public const SSL_MODE_ALLOW_UNENCRYPTED_AND_ENCRYPTED = 'ALLOW_UNENCRYPTED_AND_ENCRYPTED';
  /**
   * SSL connections are required. CA verification not enforced.
   */
  public const SSL_MODE_ENCRYPTED_ONLY = 'ENCRYPTED_ONLY';
  /**
   * Optional. Certificate Authority (CA) source. Only CA_SOURCE_MANAGED is
   * supported currently, and is the default value.
   *
   * @var string
   */
  public $caSource;
  /**
   * Optional. SSL mode. Specifies client-server SSL/TLS connection behavior.
   *
   * @var string
   */
  public $sslMode;

  /**
   * Optional. Certificate Authority (CA) source. Only CA_SOURCE_MANAGED is
   * supported currently, and is the default value.
   *
   * Accepted values: CA_SOURCE_UNSPECIFIED, CA_SOURCE_MANAGED
   *
   * @param self::CA_SOURCE_* $caSource
   */
  public function setCaSource($caSource)
  {
    $this->caSource = $caSource;
  }
  /**
   * @return self::CA_SOURCE_*
   */
  public function getCaSource()
  {
    return $this->caSource;
  }
  /**
   * Optional. SSL mode. Specifies client-server SSL/TLS connection behavior.
   *
   * Accepted values: SSL_MODE_UNSPECIFIED, SSL_MODE_ALLOW, SSL_MODE_REQUIRE,
   * SSL_MODE_VERIFY_CA, ALLOW_UNENCRYPTED_AND_ENCRYPTED, ENCRYPTED_ONLY
   *
   * @param self::SSL_MODE_* $sslMode
   */
  public function setSslMode($sslMode)
  {
    $this->sslMode = $sslMode;
  }
  /**
   * @return self::SSL_MODE_*
   */
  public function getSslMode()
  {
    return $this->sslMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SslConfig::class, 'Google_Service_CloudAlloyDBAdmin_SslConfig');
