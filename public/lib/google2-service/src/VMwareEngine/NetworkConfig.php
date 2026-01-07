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

namespace Google\Service\VMwareEngine;

class NetworkConfig extends \Google\Model
{
  /**
   * Output only. DNS Server IP of the Private Cloud. All DNS queries can be
   * forwarded to this address for name resolution of Private Cloud's management
   * entities like vCenter, NSX-T Manager and ESXi hosts.
   *
   * @var string
   */
  public $dnsServerIp;
  /**
   * Required. Management CIDR used by VMware management appliances.
   *
   * @var string
   */
  public $managementCidr;
  /**
   * Output only. The IP address layout version of the management IP address
   * range. Possible versions include: * `managementIpAddressLayoutVersion=1`:
   * Indicates the legacy IP address layout used by some existing private
   * clouds. This is no longer supported for new private clouds as it does not
   * support all features. * `managementIpAddressLayoutVersion=2`: Indicates the
   * latest IP address layout used by all newly created private clouds. This
   * version supports all current features.
   *
   * @var int
   */
  public $managementIpAddressLayoutVersion;
  /**
   * Optional. The relative resource name of the VMware Engine network attached
   * to the private cloud. Specify the name in the following form: `projects/{pr
   * oject}/locations/{location}/vmwareEngineNetworks/{vmware_engine_network_id}
   * ` where `{project}` can either be a project number or a project ID.
   *
   * @var string
   */
  public $vmwareEngineNetwork;
  /**
   * Output only. The canonical name of the VMware Engine network in the form: `
   * projects/{project_number}/locations/{location}/vmwareEngineNetworks/{vmware
   * _engine_network_id}`
   *
   * @var string
   */
  public $vmwareEngineNetworkCanonical;

  /**
   * Output only. DNS Server IP of the Private Cloud. All DNS queries can be
   * forwarded to this address for name resolution of Private Cloud's management
   * entities like vCenter, NSX-T Manager and ESXi hosts.
   *
   * @param string $dnsServerIp
   */
  public function setDnsServerIp($dnsServerIp)
  {
    $this->dnsServerIp = $dnsServerIp;
  }
  /**
   * @return string
   */
  public function getDnsServerIp()
  {
    return $this->dnsServerIp;
  }
  /**
   * Required. Management CIDR used by VMware management appliances.
   *
   * @param string $managementCidr
   */
  public function setManagementCidr($managementCidr)
  {
    $this->managementCidr = $managementCidr;
  }
  /**
   * @return string
   */
  public function getManagementCidr()
  {
    return $this->managementCidr;
  }
  /**
   * Output only. The IP address layout version of the management IP address
   * range. Possible versions include: * `managementIpAddressLayoutVersion=1`:
   * Indicates the legacy IP address layout used by some existing private
   * clouds. This is no longer supported for new private clouds as it does not
   * support all features. * `managementIpAddressLayoutVersion=2`: Indicates the
   * latest IP address layout used by all newly created private clouds. This
   * version supports all current features.
   *
   * @param int $managementIpAddressLayoutVersion
   */
  public function setManagementIpAddressLayoutVersion($managementIpAddressLayoutVersion)
  {
    $this->managementIpAddressLayoutVersion = $managementIpAddressLayoutVersion;
  }
  /**
   * @return int
   */
  public function getManagementIpAddressLayoutVersion()
  {
    return $this->managementIpAddressLayoutVersion;
  }
  /**
   * Optional. The relative resource name of the VMware Engine network attached
   * to the private cloud. Specify the name in the following form: `projects/{pr
   * oject}/locations/{location}/vmwareEngineNetworks/{vmware_engine_network_id}
   * ` where `{project}` can either be a project number or a project ID.
   *
   * @param string $vmwareEngineNetwork
   */
  public function setVmwareEngineNetwork($vmwareEngineNetwork)
  {
    $this->vmwareEngineNetwork = $vmwareEngineNetwork;
  }
  /**
   * @return string
   */
  public function getVmwareEngineNetwork()
  {
    return $this->vmwareEngineNetwork;
  }
  /**
   * Output only. The canonical name of the VMware Engine network in the form: `
   * projects/{project_number}/locations/{location}/vmwareEngineNetworks/{vmware
   * _engine_network_id}`
   *
   * @param string $vmwareEngineNetworkCanonical
   */
  public function setVmwareEngineNetworkCanonical($vmwareEngineNetworkCanonical)
  {
    $this->vmwareEngineNetworkCanonical = $vmwareEngineNetworkCanonical;
  }
  /**
   * @return string
   */
  public function getVmwareEngineNetworkCanonical()
  {
    return $this->vmwareEngineNetworkCanonical;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkConfig::class, 'Google_Service_VMwareEngine_NetworkConfig');
