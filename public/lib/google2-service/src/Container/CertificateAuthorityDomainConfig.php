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

class CertificateAuthorityDomainConfig extends \Google\Collection
{
  protected $collection_key = 'fqdns';
  /**
   * List of fully qualified domain names (FQDN). Specifying port is supported.
   * Wildcards are NOT supported. Examples: - my.customdomain.com -
   * 10.0.1.2:5000
   *
   * @var string[]
   */
  public $fqdns;
  protected $gcpSecretManagerCertificateConfigType = GCPSecretManagerCertificateConfig::class;
  protected $gcpSecretManagerCertificateConfigDataType = '';

  /**
   * List of fully qualified domain names (FQDN). Specifying port is supported.
   * Wildcards are NOT supported. Examples: - my.customdomain.com -
   * 10.0.1.2:5000
   *
   * @param string[] $fqdns
   */
  public function setFqdns($fqdns)
  {
    $this->fqdns = $fqdns;
  }
  /**
   * @return string[]
   */
  public function getFqdns()
  {
    return $this->fqdns;
  }
  /**
   * Google Secret Manager (GCP) certificate configuration.
   *
   * @param GCPSecretManagerCertificateConfig $gcpSecretManagerCertificateConfig
   */
  public function setGcpSecretManagerCertificateConfig(GCPSecretManagerCertificateConfig $gcpSecretManagerCertificateConfig)
  {
    $this->gcpSecretManagerCertificateConfig = $gcpSecretManagerCertificateConfig;
  }
  /**
   * @return GCPSecretManagerCertificateConfig
   */
  public function getGcpSecretManagerCertificateConfig()
  {
    return $this->gcpSecretManagerCertificateConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CertificateAuthorityDomainConfig::class, 'Google_Service_Container_CertificateAuthorityDomainConfig');
