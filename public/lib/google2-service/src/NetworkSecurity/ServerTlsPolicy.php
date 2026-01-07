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

namespace Google\Service\NetworkSecurity;

class ServerTlsPolicy extends \Google\Model
{
  /**
   * This field applies only for Traffic Director policies. It is must be set to
   * false for Application Load Balancer policies. Determines if server allows
   * plaintext connections. If set to true, server allows plain text
   * connections. By default, it is set to false. This setting is not exclusive
   * of other encryption modes. For example, if `allow_open` and `mtls_policy`
   * are set, server allows both plain text and mTLS connections. See
   * documentation of other encryption modes to confirm compatibility. Consider
   * using it if you wish to upgrade in place your deployment to TLS while
   * having mixed TLS and non-TLS traffic reaching port :80.
   *
   * @var bool
   */
  public $allowOpen;
  /**
   * Output only. The timestamp when the resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Free-text description of the resource.
   *
   * @var string
   */
  public $description;
  /**
   * Set of label tags associated with the resource.
   *
   * @var string[]
   */
  public $labels;
  protected $mtlsPolicyType = MTLSPolicy::class;
  protected $mtlsPolicyDataType = '';
  /**
   * Required. Name of the ServerTlsPolicy resource. It matches the pattern
   * `projects/locations/{location}/serverTlsPolicies/{server_tls_policy}`
   *
   * @var string
   */
  public $name;
  protected $serverCertificateType = GoogleCloudNetworksecurityV1CertificateProvider::class;
  protected $serverCertificateDataType = '';
  /**
   * Output only. The timestamp when the resource was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * This field applies only for Traffic Director policies. It is must be set to
   * false for Application Load Balancer policies. Determines if server allows
   * plaintext connections. If set to true, server allows plain text
   * connections. By default, it is set to false. This setting is not exclusive
   * of other encryption modes. For example, if `allow_open` and `mtls_policy`
   * are set, server allows both plain text and mTLS connections. See
   * documentation of other encryption modes to confirm compatibility. Consider
   * using it if you wish to upgrade in place your deployment to TLS while
   * having mixed TLS and non-TLS traffic reaching port :80.
   *
   * @param bool $allowOpen
   */
  public function setAllowOpen($allowOpen)
  {
    $this->allowOpen = $allowOpen;
  }
  /**
   * @return bool
   */
  public function getAllowOpen()
  {
    return $this->allowOpen;
  }
  /**
   * Output only. The timestamp when the resource was created.
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
   * Free-text description of the resource.
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
   * Set of label tags associated with the resource.
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
   * This field is required if the policy is used with Application Load
   * Balancers. This field can be empty for Traffic Director. Defines a
   * mechanism to provision peer validation certificates for peer to peer
   * authentication (Mutual TLS - mTLS). If not specified, client certificate
   * will not be requested. The connection is treated as TLS and not mTLS. If
   * `allow_open` and `mtls_policy` are set, server allows both plain text and
   * mTLS connections.
   *
   * @param MTLSPolicy $mtlsPolicy
   */
  public function setMtlsPolicy(MTLSPolicy $mtlsPolicy)
  {
    $this->mtlsPolicy = $mtlsPolicy;
  }
  /**
   * @return MTLSPolicy
   */
  public function getMtlsPolicy()
  {
    return $this->mtlsPolicy;
  }
  /**
   * Required. Name of the ServerTlsPolicy resource. It matches the pattern
   * `projects/locations/{location}/serverTlsPolicies/{server_tls_policy}`
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
   * Optional if policy is to be used with Traffic Director. For Application
   * Load Balancers must be empty. Defines a mechanism to provision server
   * identity (public and private keys). Cannot be combined with `allow_open` as
   * a permissive mode that allows both plain text and TLS is not supported.
   *
   * @param GoogleCloudNetworksecurityV1CertificateProvider $serverCertificate
   */
  public function setServerCertificate(GoogleCloudNetworksecurityV1CertificateProvider $serverCertificate)
  {
    $this->serverCertificate = $serverCertificate;
  }
  /**
   * @return GoogleCloudNetworksecurityV1CertificateProvider
   */
  public function getServerCertificate()
  {
    return $this->serverCertificate;
  }
  /**
   * Output only. The timestamp when the resource was updated.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServerTlsPolicy::class, 'Google_Service_NetworkSecurity_ServerTlsPolicy');
