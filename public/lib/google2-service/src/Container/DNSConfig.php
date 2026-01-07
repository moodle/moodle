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

class DNSConfig extends \Google\Model
{
  /**
   * Default value
   */
  public const CLUSTER_DNS_PROVIDER_UNSPECIFIED = 'PROVIDER_UNSPECIFIED';
  /**
   * Use GKE default DNS provider(kube-dns) for DNS resolution.
   */
  public const CLUSTER_DNS_PLATFORM_DEFAULT = 'PLATFORM_DEFAULT';
  /**
   * Use CloudDNS for DNS resolution.
   */
  public const CLUSTER_DNS_CLOUD_DNS = 'CLOUD_DNS';
  /**
   * Use KubeDNS for DNS resolution.
   */
  public const CLUSTER_DNS_KUBE_DNS = 'KUBE_DNS';
  /**
   * Default value, will be inferred as cluster scope.
   */
  public const CLUSTER_DNS_SCOPE_DNS_SCOPE_UNSPECIFIED = 'DNS_SCOPE_UNSPECIFIED';
  /**
   * DNS records are accessible from within the cluster.
   */
  public const CLUSTER_DNS_SCOPE_CLUSTER_SCOPE = 'CLUSTER_SCOPE';
  /**
   * DNS records are accessible from within the VPC.
   */
  public const CLUSTER_DNS_SCOPE_VPC_SCOPE = 'VPC_SCOPE';
  /**
   * Optional. The domain used in Additive VPC scope.
   *
   * @var string
   */
  public $additiveVpcScopeDnsDomain;
  /**
   * cluster_dns indicates which in-cluster DNS provider should be used.
   *
   * @var string
   */
  public $clusterDns;
  /**
   * cluster_dns_domain is the suffix used for all cluster service records.
   *
   * @var string
   */
  public $clusterDnsDomain;
  /**
   * cluster_dns_scope indicates the scope of access to cluster DNS records.
   *
   * @var string
   */
  public $clusterDnsScope;

  /**
   * Optional. The domain used in Additive VPC scope.
   *
   * @param string $additiveVpcScopeDnsDomain
   */
  public function setAdditiveVpcScopeDnsDomain($additiveVpcScopeDnsDomain)
  {
    $this->additiveVpcScopeDnsDomain = $additiveVpcScopeDnsDomain;
  }
  /**
   * @return string
   */
  public function getAdditiveVpcScopeDnsDomain()
  {
    return $this->additiveVpcScopeDnsDomain;
  }
  /**
   * cluster_dns indicates which in-cluster DNS provider should be used.
   *
   * Accepted values: PROVIDER_UNSPECIFIED, PLATFORM_DEFAULT, CLOUD_DNS,
   * KUBE_DNS
   *
   * @param self::CLUSTER_DNS_* $clusterDns
   */
  public function setClusterDns($clusterDns)
  {
    $this->clusterDns = $clusterDns;
  }
  /**
   * @return self::CLUSTER_DNS_*
   */
  public function getClusterDns()
  {
    return $this->clusterDns;
  }
  /**
   * cluster_dns_domain is the suffix used for all cluster service records.
   *
   * @param string $clusterDnsDomain
   */
  public function setClusterDnsDomain($clusterDnsDomain)
  {
    $this->clusterDnsDomain = $clusterDnsDomain;
  }
  /**
   * @return string
   */
  public function getClusterDnsDomain()
  {
    return $this->clusterDnsDomain;
  }
  /**
   * cluster_dns_scope indicates the scope of access to cluster DNS records.
   *
   * Accepted values: DNS_SCOPE_UNSPECIFIED, CLUSTER_SCOPE, VPC_SCOPE
   *
   * @param self::CLUSTER_DNS_SCOPE_* $clusterDnsScope
   */
  public function setClusterDnsScope($clusterDnsScope)
  {
    $this->clusterDnsScope = $clusterDnsScope;
  }
  /**
   * @return self::CLUSTER_DNS_SCOPE_*
   */
  public function getClusterDnsScope()
  {
    return $this->clusterDnsScope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DNSConfig::class, 'Google_Service_Container_DNSConfig');
