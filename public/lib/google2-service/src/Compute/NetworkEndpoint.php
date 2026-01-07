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

namespace Google\Service\Compute;

class NetworkEndpoint extends \Google\Model
{
  /**
   * Optional metadata defined as annotations on the network endpoint.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Represents the port number to which PSC consumer sends packets.
   *
   * Optional. Only valid for network endpoint groups created
   * withGCE_VM_IP_PORTMAP endpoint type.
   *
   * @var int
   */
  public $clientDestinationPort;
  /**
   * Optional fully qualified domain name of network endpoint. This can only be
   * specified when NetworkEndpointGroup.network_endpoint_type
   * isNON_GCP_FQDN_PORT.
   *
   * @var string
   */
  public $fqdn;
  /**
   * The name or a URL of VM instance of this network endpoint. Optional, the
   * field presence depends on the network endpoint type. The field is required
   * for network endpoints of type GCE_VM_IP andGCE_VM_IP_PORT.
   *
   * The instance must be in the same zone of network endpoint group (for zonal
   * NEGs) or in the zone within the region of the NEG (for regional NEGs). If
   * the ipAddress is specified, it must belongs to the VM instance.
   *
   * The name must be 1-63 characters long, and comply withRFC1035 or be a valid
   * URL pointing to an existing instance.
   *
   * @var string
   */
  public $instance;
  /**
   * Optional IPv4 address of network endpoint. The IP address must belong to a
   * VM in Compute Engine (either the primary IP or as part of an aliased IP
   * range). If the IP address is not specified, then the primary IP address for
   * the VM instance in the network that the network endpoint group belongs to
   * will be used.
   *
   * This field is redundant and need not be set for network endpoints of
   * typeGCE_VM_IP. If set, it must be set to the primary internal IP address of
   * the attached VM instance that matches the subnetwork of the NEG. The
   * primary internal IP address from any NIC of a multi-NIC VM instance can be
   * added to a NEG as long as it matches the NEG subnetwork.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * Optional IPv6 address of network endpoint.
   *
   * @var string
   */
  public $ipv6Address;
  /**
   * Optional port number of network endpoint. If not specified, the defaultPort
   * for the network endpoint group will be used.
   *
   * This field can not be set for network endpoints of typeGCE_VM_IP.
   *
   * @var int
   */
  public $port;

  /**
   * Optional metadata defined as annotations on the network endpoint.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Represents the port number to which PSC consumer sends packets.
   *
   * Optional. Only valid for network endpoint groups created
   * withGCE_VM_IP_PORTMAP endpoint type.
   *
   * @param int $clientDestinationPort
   */
  public function setClientDestinationPort($clientDestinationPort)
  {
    $this->clientDestinationPort = $clientDestinationPort;
  }
  /**
   * @return int
   */
  public function getClientDestinationPort()
  {
    return $this->clientDestinationPort;
  }
  /**
   * Optional fully qualified domain name of network endpoint. This can only be
   * specified when NetworkEndpointGroup.network_endpoint_type
   * isNON_GCP_FQDN_PORT.
   *
   * @param string $fqdn
   */
  public function setFqdn($fqdn)
  {
    $this->fqdn = $fqdn;
  }
  /**
   * @return string
   */
  public function getFqdn()
  {
    return $this->fqdn;
  }
  /**
   * The name or a URL of VM instance of this network endpoint. Optional, the
   * field presence depends on the network endpoint type. The field is required
   * for network endpoints of type GCE_VM_IP andGCE_VM_IP_PORT.
   *
   * The instance must be in the same zone of network endpoint group (for zonal
   * NEGs) or in the zone within the region of the NEG (for regional NEGs). If
   * the ipAddress is specified, it must belongs to the VM instance.
   *
   * The name must be 1-63 characters long, and comply withRFC1035 or be a valid
   * URL pointing to an existing instance.
   *
   * @param string $instance
   */
  public function setInstance($instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return string
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * Optional IPv4 address of network endpoint. The IP address must belong to a
   * VM in Compute Engine (either the primary IP or as part of an aliased IP
   * range). If the IP address is not specified, then the primary IP address for
   * the VM instance in the network that the network endpoint group belongs to
   * will be used.
   *
   * This field is redundant and need not be set for network endpoints of
   * typeGCE_VM_IP. If set, it must be set to the primary internal IP address of
   * the attached VM instance that matches the subnetwork of the NEG. The
   * primary internal IP address from any NIC of a multi-NIC VM instance can be
   * added to a NEG as long as it matches the NEG subnetwork.
   *
   * @param string $ipAddress
   */
  public function setIpAddress($ipAddress)
  {
    $this->ipAddress = $ipAddress;
  }
  /**
   * @return string
   */
  public function getIpAddress()
  {
    return $this->ipAddress;
  }
  /**
   * Optional IPv6 address of network endpoint.
   *
   * @param string $ipv6Address
   */
  public function setIpv6Address($ipv6Address)
  {
    $this->ipv6Address = $ipv6Address;
  }
  /**
   * @return string
   */
  public function getIpv6Address()
  {
    return $this->ipv6Address;
  }
  /**
   * Optional port number of network endpoint. If not specified, the defaultPort
   * for the network endpoint group will be used.
   *
   * This field can not be set for network endpoints of typeGCE_VM_IP.
   *
   * @param int $port
   */
  public function setPort($port)
  {
    $this->port = $port;
  }
  /**
   * @return int
   */
  public function getPort()
  {
    return $this->port;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkEndpoint::class, 'Google_Service_Compute_NetworkEndpoint');
