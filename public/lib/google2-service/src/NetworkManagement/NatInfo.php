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

namespace Google\Service\NetworkManagement;

class NatInfo extends \Google\Model
{
  /**
   * Type is unspecified.
   */
  public const CLOUD_NAT_GATEWAY_TYPE_CLOUD_NAT_GATEWAY_TYPE_UNSPECIFIED = 'CLOUD_NAT_GATEWAY_TYPE_UNSPECIFIED';
  /**
   * Public NAT gateway.
   */
  public const CLOUD_NAT_GATEWAY_TYPE_PUBLIC_NAT44 = 'PUBLIC_NAT44';
  /**
   * Public NAT64 gateway.
   */
  public const CLOUD_NAT_GATEWAY_TYPE_PUBLIC_NAT64 = 'PUBLIC_NAT64';
  /**
   * Private NAT gateway for NCC.
   */
  public const CLOUD_NAT_GATEWAY_TYPE_PRIVATE_NAT_NCC = 'PRIVATE_NAT_NCC';
  /**
   * Private NAT gateway for hybrid connectivity.
   */
  public const CLOUD_NAT_GATEWAY_TYPE_PRIVATE_NAT_HYBRID = 'PRIVATE_NAT_HYBRID';
  /**
   * Private NAT64 gateway.
   */
  public const CLOUD_NAT_GATEWAY_TYPE_PRIVATE_NAT64 = 'PRIVATE_NAT64';
  /**
   * Type is unspecified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * From Compute Engine instance's internal address to external address.
   */
  public const TYPE_INTERNAL_TO_EXTERNAL = 'INTERNAL_TO_EXTERNAL';
  /**
   * From Compute Engine instance's external address to internal address.
   */
  public const TYPE_EXTERNAL_TO_INTERNAL = 'EXTERNAL_TO_INTERNAL';
  /**
   * Cloud NAT Gateway.
   */
  public const TYPE_CLOUD_NAT = 'CLOUD_NAT';
  /**
   * Private service connect NAT.
   */
  public const TYPE_PRIVATE_SERVICE_CONNECT = 'PRIVATE_SERVICE_CONNECT';
  /**
   * GKE Pod IP address masquerading.
   */
  public const TYPE_GKE_POD_IP_MASQUERADING = 'GKE_POD_IP_MASQUERADING';
  /**
   * Type of Cloud NAT gateway. Only valid when `type` is CLOUD_NAT.
   *
   * @var string
   */
  public $cloudNatGatewayType;
  /**
   * The name of Cloud NAT Gateway. Only valid when type is CLOUD_NAT.
   *
   * @var string
   */
  public $natGatewayName;
  /**
   * URI of the network where NAT translation takes place.
   *
   * @var string
   */
  public $networkUri;
  /**
   * Destination IP address after NAT translation.
   *
   * @var string
   */
  public $newDestinationIp;
  /**
   * Destination port after NAT translation. Only valid when protocol is TCP or
   * UDP.
   *
   * @var int
   */
  public $newDestinationPort;
  /**
   * Source IP address after NAT translation.
   *
   * @var string
   */
  public $newSourceIp;
  /**
   * Source port after NAT translation. Only valid when protocol is TCP or UDP.
   *
   * @var int
   */
  public $newSourcePort;
  /**
   * Destination IP address before NAT translation.
   *
   * @var string
   */
  public $oldDestinationIp;
  /**
   * Destination port before NAT translation. Only valid when protocol is TCP or
   * UDP.
   *
   * @var int
   */
  public $oldDestinationPort;
  /**
   * Source IP address before NAT translation.
   *
   * @var string
   */
  public $oldSourceIp;
  /**
   * Source port before NAT translation. Only valid when protocol is TCP or UDP.
   *
   * @var int
   */
  public $oldSourcePort;
  /**
   * IP protocol in string format, for example: "TCP", "UDP", "ICMP".
   *
   * @var string
   */
  public $protocol;
  /**
   * Uri of the Cloud Router. Only valid when type is CLOUD_NAT.
   *
   * @var string
   */
  public $routerUri;
  /**
   * Type of NAT.
   *
   * @var string
   */
  public $type;

  /**
   * Type of Cloud NAT gateway. Only valid when `type` is CLOUD_NAT.
   *
   * Accepted values: CLOUD_NAT_GATEWAY_TYPE_UNSPECIFIED, PUBLIC_NAT44,
   * PUBLIC_NAT64, PRIVATE_NAT_NCC, PRIVATE_NAT_HYBRID, PRIVATE_NAT64
   *
   * @param self::CLOUD_NAT_GATEWAY_TYPE_* $cloudNatGatewayType
   */
  public function setCloudNatGatewayType($cloudNatGatewayType)
  {
    $this->cloudNatGatewayType = $cloudNatGatewayType;
  }
  /**
   * @return self::CLOUD_NAT_GATEWAY_TYPE_*
   */
  public function getCloudNatGatewayType()
  {
    return $this->cloudNatGatewayType;
  }
  /**
   * The name of Cloud NAT Gateway. Only valid when type is CLOUD_NAT.
   *
   * @param string $natGatewayName
   */
  public function setNatGatewayName($natGatewayName)
  {
    $this->natGatewayName = $natGatewayName;
  }
  /**
   * @return string
   */
  public function getNatGatewayName()
  {
    return $this->natGatewayName;
  }
  /**
   * URI of the network where NAT translation takes place.
   *
   * @param string $networkUri
   */
  public function setNetworkUri($networkUri)
  {
    $this->networkUri = $networkUri;
  }
  /**
   * @return string
   */
  public function getNetworkUri()
  {
    return $this->networkUri;
  }
  /**
   * Destination IP address after NAT translation.
   *
   * @param string $newDestinationIp
   */
  public function setNewDestinationIp($newDestinationIp)
  {
    $this->newDestinationIp = $newDestinationIp;
  }
  /**
   * @return string
   */
  public function getNewDestinationIp()
  {
    return $this->newDestinationIp;
  }
  /**
   * Destination port after NAT translation. Only valid when protocol is TCP or
   * UDP.
   *
   * @param int $newDestinationPort
   */
  public function setNewDestinationPort($newDestinationPort)
  {
    $this->newDestinationPort = $newDestinationPort;
  }
  /**
   * @return int
   */
  public function getNewDestinationPort()
  {
    return $this->newDestinationPort;
  }
  /**
   * Source IP address after NAT translation.
   *
   * @param string $newSourceIp
   */
  public function setNewSourceIp($newSourceIp)
  {
    $this->newSourceIp = $newSourceIp;
  }
  /**
   * @return string
   */
  public function getNewSourceIp()
  {
    return $this->newSourceIp;
  }
  /**
   * Source port after NAT translation. Only valid when protocol is TCP or UDP.
   *
   * @param int $newSourcePort
   */
  public function setNewSourcePort($newSourcePort)
  {
    $this->newSourcePort = $newSourcePort;
  }
  /**
   * @return int
   */
  public function getNewSourcePort()
  {
    return $this->newSourcePort;
  }
  /**
   * Destination IP address before NAT translation.
   *
   * @param string $oldDestinationIp
   */
  public function setOldDestinationIp($oldDestinationIp)
  {
    $this->oldDestinationIp = $oldDestinationIp;
  }
  /**
   * @return string
   */
  public function getOldDestinationIp()
  {
    return $this->oldDestinationIp;
  }
  /**
   * Destination port before NAT translation. Only valid when protocol is TCP or
   * UDP.
   *
   * @param int $oldDestinationPort
   */
  public function setOldDestinationPort($oldDestinationPort)
  {
    $this->oldDestinationPort = $oldDestinationPort;
  }
  /**
   * @return int
   */
  public function getOldDestinationPort()
  {
    return $this->oldDestinationPort;
  }
  /**
   * Source IP address before NAT translation.
   *
   * @param string $oldSourceIp
   */
  public function setOldSourceIp($oldSourceIp)
  {
    $this->oldSourceIp = $oldSourceIp;
  }
  /**
   * @return string
   */
  public function getOldSourceIp()
  {
    return $this->oldSourceIp;
  }
  /**
   * Source port before NAT translation. Only valid when protocol is TCP or UDP.
   *
   * @param int $oldSourcePort
   */
  public function setOldSourcePort($oldSourcePort)
  {
    $this->oldSourcePort = $oldSourcePort;
  }
  /**
   * @return int
   */
  public function getOldSourcePort()
  {
    return $this->oldSourcePort;
  }
  /**
   * IP protocol in string format, for example: "TCP", "UDP", "ICMP".
   *
   * @param string $protocol
   */
  public function setProtocol($protocol)
  {
    $this->protocol = $protocol;
  }
  /**
   * @return string
   */
  public function getProtocol()
  {
    return $this->protocol;
  }
  /**
   * Uri of the Cloud Router. Only valid when type is CLOUD_NAT.
   *
   * @param string $routerUri
   */
  public function setRouterUri($routerUri)
  {
    $this->routerUri = $routerUri;
  }
  /**
   * @return string
   */
  public function getRouterUri()
  {
    return $this->routerUri;
  }
  /**
   * Type of NAT.
   *
   * Accepted values: TYPE_UNSPECIFIED, INTERNAL_TO_EXTERNAL,
   * EXTERNAL_TO_INTERNAL, CLOUD_NAT, PRIVATE_SERVICE_CONNECT,
   * GKE_POD_IP_MASQUERADING
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NatInfo::class, 'Google_Service_NetworkManagement_NatInfo');
