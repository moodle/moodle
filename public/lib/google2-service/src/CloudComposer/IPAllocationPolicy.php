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

class IPAllocationPolicy extends \Google\Model
{
  /**
   * Optional. The IP address range used to allocate IP addresses to pods in the
   * GKE cluster. For Cloud Composer environments in versions
   * composer-1.*.*-airflow-*.*.*, this field is applicable only when
   * `use_ip_aliases` is true. Set to blank to have GKE choose a range with the
   * default size. Set to /netmask (e.g. `/14`) to have GKE choose a range with
   * a specific netmask. Set to a
   * [CIDR](https://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing)
   * notation (e.g. `10.96.0.0/14`) from the RFC-1918 private networks (e.g.
   * `10.0.0.0/8`, `172.16.0.0/12`, `192.168.0.0/16`) to pick a specific range
   * to use.
   *
   * @var string
   */
  public $clusterIpv4CidrBlock;
  /**
   * Optional. The name of the GKE cluster's secondary range used to allocate IP
   * addresses to pods. For Cloud Composer environments in versions
   * composer-1.*.*-airflow-*.*.*, this field is applicable only when
   * `use_ip_aliases` is true.
   *
   * @var string
   */
  public $clusterSecondaryRangeName;
  /**
   * Optional. The IP address range of the services IP addresses in this GKE
   * cluster. For Cloud Composer environments in versions
   * composer-1.*.*-airflow-*.*.*, this field is applicable only when
   * `use_ip_aliases` is true. Set to blank to have GKE choose a range with the
   * default size. Set to /netmask (e.g. `/14`) to have GKE choose a range with
   * a specific netmask. Set to a
   * [CIDR](https://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing)
   * notation (e.g. `10.96.0.0/14`) from the RFC-1918 private networks (e.g.
   * `10.0.0.0/8`, `172.16.0.0/12`, `192.168.0.0/16`) to pick a specific range
   * to use.
   *
   * @var string
   */
  public $servicesIpv4CidrBlock;
  /**
   * Optional. The name of the services' secondary range used to allocate IP
   * addresses to the GKE cluster. For Cloud Composer environments in versions
   * composer-1.*.*-airflow-*.*.*, this field is applicable only when
   * `use_ip_aliases` is true.
   *
   * @var string
   */
  public $servicesSecondaryRangeName;
  /**
   * Optional. Whether or not to enable Alias IPs in the GKE cluster. If `true`,
   * a VPC-native cluster is created. This field is only supported for Cloud
   * Composer environments in versions composer-1.*.*-airflow-*.*.*.
   * Environments in newer versions always use VPC-native GKE clusters.
   *
   * @var bool
   */
  public $useIpAliases;

  /**
   * Optional. The IP address range used to allocate IP addresses to pods in the
   * GKE cluster. For Cloud Composer environments in versions
   * composer-1.*.*-airflow-*.*.*, this field is applicable only when
   * `use_ip_aliases` is true. Set to blank to have GKE choose a range with the
   * default size. Set to /netmask (e.g. `/14`) to have GKE choose a range with
   * a specific netmask. Set to a
   * [CIDR](https://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing)
   * notation (e.g. `10.96.0.0/14`) from the RFC-1918 private networks (e.g.
   * `10.0.0.0/8`, `172.16.0.0/12`, `192.168.0.0/16`) to pick a specific range
   * to use.
   *
   * @param string $clusterIpv4CidrBlock
   */
  public function setClusterIpv4CidrBlock($clusterIpv4CidrBlock)
  {
    $this->clusterIpv4CidrBlock = $clusterIpv4CidrBlock;
  }
  /**
   * @return string
   */
  public function getClusterIpv4CidrBlock()
  {
    return $this->clusterIpv4CidrBlock;
  }
  /**
   * Optional. The name of the GKE cluster's secondary range used to allocate IP
   * addresses to pods. For Cloud Composer environments in versions
   * composer-1.*.*-airflow-*.*.*, this field is applicable only when
   * `use_ip_aliases` is true.
   *
   * @param string $clusterSecondaryRangeName
   */
  public function setClusterSecondaryRangeName($clusterSecondaryRangeName)
  {
    $this->clusterSecondaryRangeName = $clusterSecondaryRangeName;
  }
  /**
   * @return string
   */
  public function getClusterSecondaryRangeName()
  {
    return $this->clusterSecondaryRangeName;
  }
  /**
   * Optional. The IP address range of the services IP addresses in this GKE
   * cluster. For Cloud Composer environments in versions
   * composer-1.*.*-airflow-*.*.*, this field is applicable only when
   * `use_ip_aliases` is true. Set to blank to have GKE choose a range with the
   * default size. Set to /netmask (e.g. `/14`) to have GKE choose a range with
   * a specific netmask. Set to a
   * [CIDR](https://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing)
   * notation (e.g. `10.96.0.0/14`) from the RFC-1918 private networks (e.g.
   * `10.0.0.0/8`, `172.16.0.0/12`, `192.168.0.0/16`) to pick a specific range
   * to use.
   *
   * @param string $servicesIpv4CidrBlock
   */
  public function setServicesIpv4CidrBlock($servicesIpv4CidrBlock)
  {
    $this->servicesIpv4CidrBlock = $servicesIpv4CidrBlock;
  }
  /**
   * @return string
   */
  public function getServicesIpv4CidrBlock()
  {
    return $this->servicesIpv4CidrBlock;
  }
  /**
   * Optional. The name of the services' secondary range used to allocate IP
   * addresses to the GKE cluster. For Cloud Composer environments in versions
   * composer-1.*.*-airflow-*.*.*, this field is applicable only when
   * `use_ip_aliases` is true.
   *
   * @param string $servicesSecondaryRangeName
   */
  public function setServicesSecondaryRangeName($servicesSecondaryRangeName)
  {
    $this->servicesSecondaryRangeName = $servicesSecondaryRangeName;
  }
  /**
   * @return string
   */
  public function getServicesSecondaryRangeName()
  {
    return $this->servicesSecondaryRangeName;
  }
  /**
   * Optional. Whether or not to enable Alias IPs in the GKE cluster. If `true`,
   * a VPC-native cluster is created. This field is only supported for Cloud
   * Composer environments in versions composer-1.*.*-airflow-*.*.*.
   * Environments in newer versions always use VPC-native GKE clusters.
   *
   * @param bool $useIpAliases
   */
  public function setUseIpAliases($useIpAliases)
  {
    $this->useIpAliases = $useIpAliases;
  }
  /**
   * @return bool
   */
  public function getUseIpAliases()
  {
    return $this->useIpAliases;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IPAllocationPolicy::class, 'Google_Service_CloudComposer_IPAllocationPolicy');
