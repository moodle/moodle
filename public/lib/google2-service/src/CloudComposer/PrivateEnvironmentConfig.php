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

namespace Google\Service\CloudComposer;

class PrivateEnvironmentConfig extends \Google\Model
{
  /**
   * Optional. When specified, the environment will use Private Service Connect
   * instead of VPC peerings to connect to Cloud SQL in the Tenant Project, and
   * the PSC endpoint in the Customer Project will use an IP address from this
   * subnetwork.
   *
   * @var string
   */
  public $cloudComposerConnectionSubnetwork;
  /**
   * Optional. The CIDR block from which IP range for Cloud Composer Network in
   * tenant project will be reserved. Needs to be disjoint from
   * private_cluster_config.master_ipv4_cidr_block and
   * cloud_sql_ipv4_cidr_block. This field is supported for Cloud Composer
   * environments in versions composer-2.*.*-airflow-*.*.* and newer.
   *
   * @var string
   */
  public $cloudComposerNetworkIpv4CidrBlock;
  /**
   * Output only. The IP range reserved for the tenant project's Cloud Composer
   * network. This field is supported for Cloud Composer environments in
   * versions composer-2.*.*-airflow-*.*.* and newer.
   *
   * @var string
   */
  public $cloudComposerNetworkIpv4ReservedRange;
  /**
   * Optional. The CIDR block from which IP range in tenant project will be
   * reserved for Cloud SQL. Needs to be disjoint from
   * `web_server_ipv4_cidr_block`.
   *
   * @var string
   */
  public $cloudSqlIpv4CidrBlock;
  /**
   * Optional. If `true`, builds performed during operations that install Python
   * packages have only private connectivity to Google services (including
   * Artifact Registry) and VPC network (if either `NodeConfig.network` and
   * `NodeConfig.subnetwork` fields or `NodeConfig.composer_network_attachment`
   * field are specified). If `false`, the builds also have access to the
   * internet. This field is supported for Cloud Composer environments in
   * versions composer-3-airflow-*.*.*-build.* and newer.
   *
   * @var bool
   */
  public $enablePrivateBuildsOnly;
  /**
   * Optional. If `true`, a Private IP Cloud Composer environment is created. If
   * this field is set to true, `IPAllocationPolicy.use_ip_aliases` must be set
   * to true for Cloud Composer environments in versions
   * composer-1.*.*-airflow-*.*.*.
   *
   * @var bool
   */
  public $enablePrivateEnvironment;
  /**
   * Optional. When enabled, IPs from public (non-RFC1918) ranges can be used
   * for `IPAllocationPolicy.cluster_ipv4_cidr_block` and
   * `IPAllocationPolicy.service_ipv4_cidr_block`.
   *
   * @var bool
   */
  public $enablePrivatelyUsedPublicIps;
  protected $networkingConfigType = NetworkingConfig::class;
  protected $networkingConfigDataType = '';
  protected $privateClusterConfigType = PrivateClusterConfig::class;
  protected $privateClusterConfigDataType = '';
  /**
   * Optional. The CIDR block from which IP range for web server will be
   * reserved. Needs to be disjoint from
   * `private_cluster_config.master_ipv4_cidr_block` and
   * `cloud_sql_ipv4_cidr_block`. This field is supported for Cloud Composer
   * environments in versions composer-1.*.*-airflow-*.*.*.
   *
   * @var string
   */
  public $webServerIpv4CidrBlock;
  /**
   * Output only. The IP range reserved for the tenant project's App Engine VMs.
   * This field is supported for Cloud Composer environments in versions
   * composer-1.*.*-airflow-*.*.*.
   *
   * @var string
   */
  public $webServerIpv4ReservedRange;

  /**
   * Optional. When specified, the environment will use Private Service Connect
   * instead of VPC peerings to connect to Cloud SQL in the Tenant Project, and
   * the PSC endpoint in the Customer Project will use an IP address from this
   * subnetwork.
   *
   * @param string $cloudComposerConnectionSubnetwork
   */
  public function setCloudComposerConnectionSubnetwork($cloudComposerConnectionSubnetwork)
  {
    $this->cloudComposerConnectionSubnetwork = $cloudComposerConnectionSubnetwork;
  }
  /**
   * @return string
   */
  public function getCloudComposerConnectionSubnetwork()
  {
    return $this->cloudComposerConnectionSubnetwork;
  }
  /**
   * Optional. The CIDR block from which IP range for Cloud Composer Network in
   * tenant project will be reserved. Needs to be disjoint from
   * private_cluster_config.master_ipv4_cidr_block and
   * cloud_sql_ipv4_cidr_block. This field is supported for Cloud Composer
   * environments in versions composer-2.*.*-airflow-*.*.* and newer.
   *
   * @param string $cloudComposerNetworkIpv4CidrBlock
   */
  public function setCloudComposerNetworkIpv4CidrBlock($cloudComposerNetworkIpv4CidrBlock)
  {
    $this->cloudComposerNetworkIpv4CidrBlock = $cloudComposerNetworkIpv4CidrBlock;
  }
  /**
   * @return string
   */
  public function getCloudComposerNetworkIpv4CidrBlock()
  {
    return $this->cloudComposerNetworkIpv4CidrBlock;
  }
  /**
   * Output only. The IP range reserved for the tenant project's Cloud Composer
   * network. This field is supported for Cloud Composer environments in
   * versions composer-2.*.*-airflow-*.*.* and newer.
   *
   * @param string $cloudComposerNetworkIpv4ReservedRange
   */
  public function setCloudComposerNetworkIpv4ReservedRange($cloudComposerNetworkIpv4ReservedRange)
  {
    $this->cloudComposerNetworkIpv4ReservedRange = $cloudComposerNetworkIpv4ReservedRange;
  }
  /**
   * @return string
   */
  public function getCloudComposerNetworkIpv4ReservedRange()
  {
    return $this->cloudComposerNetworkIpv4ReservedRange;
  }
  /**
   * Optional. The CIDR block from which IP range in tenant project will be
   * reserved for Cloud SQL. Needs to be disjoint from
   * `web_server_ipv4_cidr_block`.
   *
   * @param string $cloudSqlIpv4CidrBlock
   */
  public function setCloudSqlIpv4CidrBlock($cloudSqlIpv4CidrBlock)
  {
    $this->cloudSqlIpv4CidrBlock = $cloudSqlIpv4CidrBlock;
  }
  /**
   * @return string
   */
  public function getCloudSqlIpv4CidrBlock()
  {
    return $this->cloudSqlIpv4CidrBlock;
  }
  /**
   * Optional. If `true`, builds performed during operations that install Python
   * packages have only private connectivity to Google services (including
   * Artifact Registry) and VPC network (if either `NodeConfig.network` and
   * `NodeConfig.subnetwork` fields or `NodeConfig.composer_network_attachment`
   * field are specified). If `false`, the builds also have access to the
   * internet. This field is supported for Cloud Composer environments in
   * versions composer-3-airflow-*.*.*-build.* and newer.
   *
   * @param bool $enablePrivateBuildsOnly
   */
  public function setEnablePrivateBuildsOnly($enablePrivateBuildsOnly)
  {
    $this->enablePrivateBuildsOnly = $enablePrivateBuildsOnly;
  }
  /**
   * @return bool
   */
  public function getEnablePrivateBuildsOnly()
  {
    return $this->enablePrivateBuildsOnly;
  }
  /**
   * Optional. If `true`, a Private IP Cloud Composer environment is created. If
   * this field is set to true, `IPAllocationPolicy.use_ip_aliases` must be set
   * to true for Cloud Composer environments in versions
   * composer-1.*.*-airflow-*.*.*.
   *
   * @param bool $enablePrivateEnvironment
   */
  public function setEnablePrivateEnvironment($enablePrivateEnvironment)
  {
    $this->enablePrivateEnvironment = $enablePrivateEnvironment;
  }
  /**
   * @return bool
   */
  public function getEnablePrivateEnvironment()
  {
    return $this->enablePrivateEnvironment;
  }
  /**
   * Optional. When enabled, IPs from public (non-RFC1918) ranges can be used
   * for `IPAllocationPolicy.cluster_ipv4_cidr_block` and
   * `IPAllocationPolicy.service_ipv4_cidr_block`.
   *
   * @param bool $enablePrivatelyUsedPublicIps
   */
  public function setEnablePrivatelyUsedPublicIps($enablePrivatelyUsedPublicIps)
  {
    $this->enablePrivatelyUsedPublicIps = $enablePrivatelyUsedPublicIps;
  }
  /**
   * @return bool
   */
  public function getEnablePrivatelyUsedPublicIps()
  {
    return $this->enablePrivatelyUsedPublicIps;
  }
  /**
   * Optional. Configuration for the network connections configuration in the
   * environment.
   *
   * @param NetworkingConfig $networkingConfig
   */
  public function setNetworkingConfig(NetworkingConfig $networkingConfig)
  {
    $this->networkingConfig = $networkingConfig;
  }
  /**
   * @return NetworkingConfig
   */
  public function getNetworkingConfig()
  {
    return $this->networkingConfig;
  }
  /**
   * Optional. Configuration for the private GKE cluster for a Private IP Cloud
   * Composer environment.
   *
   * @param PrivateClusterConfig $privateClusterConfig
   */
  public function setPrivateClusterConfig(PrivateClusterConfig $privateClusterConfig)
  {
    $this->privateClusterConfig = $privateClusterConfig;
  }
  /**
   * @return PrivateClusterConfig
   */
  public function getPrivateClusterConfig()
  {
    return $this->privateClusterConfig;
  }
  /**
   * Optional. The CIDR block from which IP range for web server will be
   * reserved. Needs to be disjoint from
   * `private_cluster_config.master_ipv4_cidr_block` and
   * `cloud_sql_ipv4_cidr_block`. This field is supported for Cloud Composer
   * environments in versions composer-1.*.*-airflow-*.*.*.
   *
   * @param string $webServerIpv4CidrBlock
   */
  public function setWebServerIpv4CidrBlock($webServerIpv4CidrBlock)
  {
    $this->webServerIpv4CidrBlock = $webServerIpv4CidrBlock;
  }
  /**
   * @return string
   */
  public function getWebServerIpv4CidrBlock()
  {
    return $this->webServerIpv4CidrBlock;
  }
  /**
   * Output only. The IP range reserved for the tenant project's App Engine VMs.
   * This field is supported for Cloud Composer environments in versions
   * composer-1.*.*-airflow-*.*.*.
   *
   * @param string $webServerIpv4ReservedRange
   */
  public function setWebServerIpv4ReservedRange($webServerIpv4ReservedRange)
  {
    $this->webServerIpv4ReservedRange = $webServerIpv4ReservedRange;
  }
  /**
   * @return string
   */
  public function getWebServerIpv4ReservedRange()
  {
    return $this->webServerIpv4ReservedRange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PrivateEnvironmentConfig::class, 'Google_Service_CloudComposer_PrivateEnvironmentConfig');
