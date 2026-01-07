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

namespace Google\Service\CertificateManager;

class Certificate extends \Google\Collection
{
  /**
   * Use the DEFAULT scope if you plan to use the certificate with global
   * external Application Load Balancer, global external proxy Network Load
   * Balancer, or any of the regional Google Cloud services.
   */
  public const SCOPE_DEFAULT = 'DEFAULT';
  /**
   * Use the EDGE_CACHE scope if you plan to use the certificate with Media CDN.
   * The certificates are served from Edge Points of Presence. See
   * https://cloud.google.com/vpc/docs/edge-locations.
   */
  public const SCOPE_EDGE_CACHE = 'EDGE_CACHE';
  /**
   * Use the ALL_REGIONS scope if you plan to use the certificate with cross-
   * region internal Application Load Balancer. The certificates are served from
   * all Google Cloud regions. See
   * https://cloud.google.com/compute/docs/regions-zones.
   */
  public const SCOPE_ALL_REGIONS = 'ALL_REGIONS';
  /**
   * Associated with certificates used as client certificates in Backend mTLS.
   */
  public const SCOPE_CLIENT_AUTH = 'CLIENT_AUTH';
  protected $collection_key = 'usedBy';
  /**
   * Output only. The creation timestamp of a Certificate.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. One or more paragraphs of text description of a certificate.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. The expiry timestamp of a Certificate.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Optional. Set of labels associated with a Certificate.
   *
   * @var string[]
   */
  public $labels;
  protected $managedType = ManagedCertificate::class;
  protected $managedDataType = '';
  protected $managedIdentityType = ManagedIdentityCertificate::class;
  protected $managedIdentityDataType = '';
  /**
   * Identifier. A user-defined name of the certificate. Certificate names must
   * be unique globally and match pattern `projects/locations/certificates`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The PEM-encoded certificate chain.
   *
   * @var string
   */
  public $pemCertificate;
  /**
   * Output only. The list of Subject Alternative Names of dnsName type defined
   * in the certificate (see RFC 5280 4.2.1.6). Managed certificates that
   * haven't been provisioned yet have this field populated with a value of the
   * managed.domains field.
   *
   * @var string[]
   */
  public $sanDnsnames;
  /**
   * Optional. Immutable. The scope of the certificate.
   *
   * @var string
   */
  public $scope;
  protected $selfManagedType = SelfManagedCertificate::class;
  protected $selfManagedDataType = '';
  /**
   * Output only. The last update timestamp of a Certificate.
   *
   * @var string
   */
  public $updateTime;
  protected $usedByType = UsedBy::class;
  protected $usedByDataType = 'array';

  /**
   * Output only. The creation timestamp of a Certificate.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. One or more paragraphs of text description of a certificate.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. The expiry timestamp of a Certificate.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Optional. Set of labels associated with a Certificate.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * If set, contains configuration and state of a managed certificate.
   *
   * @param ManagedCertificate $managed
   */
  public function setManaged(ManagedCertificate $managed)
  {
    $this->managed = $managed;
  }
  /**
   * @return ManagedCertificate
   */
  public function getManaged()
  {
    return $this->managed;
  }
  /**
   * If set, contains configuration and state of a managed identity certificate.
   *
   * @param ManagedIdentityCertificate $managedIdentity
   */
  public function setManagedIdentity(ManagedIdentityCertificate $managedIdentity)
  {
    $this->managedIdentity = $managedIdentity;
  }
  /**
   * @return ManagedIdentityCertificate
   */
  public function getManagedIdentity()
  {
    return $this->managedIdentity;
  }
  /**
   * Identifier. A user-defined name of the certificate. Certificate names must
   * be unique globally and match pattern `projects/locations/certificates`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The PEM-encoded certificate chain.
   *
   * @param string $pemCertificate
   */
  public function setPemCertificate($pemCertificate)
  {
    $this->pemCertificate = $pemCertificate;
  }
  /**
   * @return string
   */
  public function getPemCertificate()
  {
    return $this->pemCertificate;
  }
  /**
   * Output only. The list of Subject Alternative Names of dnsName type defined
   * in the certificate (see RFC 5280 4.2.1.6). Managed certificates that
   * haven't been provisioned yet have this field populated with a value of the
   * managed.domains field.
   *
   * @param string[] $sanDnsnames
   */
  public function setSanDnsnames($sanDnsnames)
  {
    $this->sanDnsnames = $sanDnsnames;
  }
  /**
   * @return string[]
   */
  public function getSanDnsnames()
  {
    return $this->sanDnsnames;
  }
  /**
   * Optional. Immutable. The scope of the certificate.
   *
   * Accepted values: DEFAULT, EDGE_CACHE, ALL_REGIONS, CLIENT_AUTH
   *
   * @param self::SCOPE_* $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return self::SCOPE_*
   */
  public function getScope()
  {
    return $this->scope;
  }
  /**
   * If set, defines data of a self-managed certificate.
   *
   * @param SelfManagedCertificate $selfManaged
   */
  public function setSelfManaged(SelfManagedCertificate $selfManaged)
  {
    $this->selfManaged = $selfManaged;
  }
  /**
   * @return SelfManagedCertificate
   */
  public function getSelfManaged()
  {
    return $this->selfManaged;
  }
  /**
   * Output only. The last update timestamp of a Certificate.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Output only. The list of resources that use this Certificate.
   *
   * @param UsedBy[] $usedBy
   */
  public function setUsedBy($usedBy)
  {
    $this->usedBy = $usedBy;
  }
  /**
   * @return UsedBy[]
   */
  public function getUsedBy()
  {
    return $this->usedBy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Certificate::class, 'Google_Service_CertificateManager_Certificate');
