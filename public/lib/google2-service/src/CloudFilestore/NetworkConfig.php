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

namespace Google\Service\CloudFilestore;

class NetworkConfig extends \Google\Collection
{
  /**
   * Not set.
   */
  public const CONNECT_MODE_CONNECT_MODE_UNSPECIFIED = 'CONNECT_MODE_UNSPECIFIED';
  /**
   * Connect via direct peering to the Filestore service.
   */
  public const CONNECT_MODE_DIRECT_PEERING = 'DIRECT_PEERING';
  /**
   * Connect to your Filestore instance using Private Service Access. Private
   * services access provides an IP address range for multiple Google Cloud
   * services, including Filestore.
   */
  public const CONNECT_MODE_PRIVATE_SERVICE_ACCESS = 'PRIVATE_SERVICE_ACCESS';
  /**
   * Connect to your Filestore instance using Private Service Connect. A
   * connection policy must exist in the region for the VPC network and the
   * google-cloud-filestore service class.
   */
  public const CONNECT_MODE_PRIVATE_SERVICE_CONNECT = 'PRIVATE_SERVICE_CONNECT';
  protected $collection_key = 'modes';
  /**
   * The network connect mode of the Filestore instance. If not provided, the
   * connect mode defaults to DIRECT_PEERING.
   *
   * @var string
   */
  public $connectMode;
  /**
   * Output only. IPv4 addresses in the format
   * `{octet1}.{octet2}.{octet3}.{octet4}` or IPv6 addresses in the format
   * `{block1}:{block2}:{block3}:{block4}:{block5}:{block6}:{block7}:{block8}`.
   *
   * @var string[]
   */
  public $ipAddresses;
  /**
   * Internet protocol versions for which the instance has IP addresses
   * assigned. For this version, only MODE_IPV4 is supported.
   *
   * @var string[]
   */
  public $modes;
  /**
   * The name of the Google Compute Engine [VPC
   * network](https://cloud.google.com/vpc/docs/vpc) to which the instance is
   * connected.
   *
   * @var string
   */
  public $network;
  protected $pscConfigType = PscConfig::class;
  protected $pscConfigDataType = '';
  /**
   * Optional, reserved_ip_range can have one of the following two types of
   * values. * CIDR range value when using DIRECT_PEERING connect mode. *
   * [Allocated IP address range](https://cloud.google.com/compute/docs/ip-
   * addresses/reserve-static-internal-ip-address) when using
   * PRIVATE_SERVICE_ACCESS connect mode. When the name of an allocated IP
   * address range is specified, it must be one of the ranges associated with
   * the private service access connection. When specified as a direct CIDR
   * value, it must be a /29 CIDR block for Basic tier, a /24 CIDR block for
   * High Scale tier, or a /26 CIDR block for Enterprise tier in one of the
   * [internal IP address ranges](https://www.arin.net/reference/research/statis
   * tics/address_filters/) that identifies the range of IP addresses reserved
   * for this instance. For example, 10.0.0.0/29, 192.168.0.0/24 or
   * 192.168.0.0/26, respectively. The range you specify can't overlap with
   * either existing subnets or assigned IP address ranges for other Filestore
   * instances in the selected VPC network.
   *
   * @var string
   */
  public $reservedIpRange;

  /**
   * The network connect mode of the Filestore instance. If not provided, the
   * connect mode defaults to DIRECT_PEERING.
   *
   * Accepted values: CONNECT_MODE_UNSPECIFIED, DIRECT_PEERING,
   * PRIVATE_SERVICE_ACCESS, PRIVATE_SERVICE_CONNECT
   *
   * @param self::CONNECT_MODE_* $connectMode
   */
  public function setConnectMode($connectMode)
  {
    $this->connectMode = $connectMode;
  }
  /**
   * @return self::CONNECT_MODE_*
   */
  public function getConnectMode()
  {
    return $this->connectMode;
  }
  /**
   * Output only. IPv4 addresses in the format
   * `{octet1}.{octet2}.{octet3}.{octet4}` or IPv6 addresses in the format
   * `{block1}:{block2}:{block3}:{block4}:{block5}:{block6}:{block7}:{block8}`.
   *
   * @param string[] $ipAddresses
   */
  public function setIpAddresses($ipAddresses)
  {
    $this->ipAddresses = $ipAddresses;
  }
  /**
   * @return string[]
   */
  public function getIpAddresses()
  {
    return $this->ipAddresses;
  }
  /**
   * Internet protocol versions for which the instance has IP addresses
   * assigned. For this version, only MODE_IPV4 is supported.
   *
   * @param string[] $modes
   */
  public function setModes($modes)
  {
    $this->modes = $modes;
  }
  /**
   * @return string[]
   */
  public function getModes()
  {
    return $this->modes;
  }
  /**
   * The name of the Google Compute Engine [VPC
   * network](https://cloud.google.com/vpc/docs/vpc) to which the instance is
   * connected.
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Optional. Private Service Connect configuration. Should only be set when
   * connect_mode is PRIVATE_SERVICE_CONNECT.
   *
   * @param PscConfig $pscConfig
   */
  public function setPscConfig(PscConfig $pscConfig)
  {
    $this->pscConfig = $pscConfig;
  }
  /**
   * @return PscConfig
   */
  public function getPscConfig()
  {
    return $this->pscConfig;
  }
  /**
   * Optional, reserved_ip_range can have one of the following two types of
   * values. * CIDR range value when using DIRECT_PEERING connect mode. *
   * [Allocated IP address range](https://cloud.google.com/compute/docs/ip-
   * addresses/reserve-static-internal-ip-address) when using
   * PRIVATE_SERVICE_ACCESS connect mode. When the name of an allocated IP
   * address range is specified, it must be one of the ranges associated with
   * the private service access connection. When specified as a direct CIDR
   * value, it must be a /29 CIDR block for Basic tier, a /24 CIDR block for
   * High Scale tier, or a /26 CIDR block for Enterprise tier in one of the
   * [internal IP address ranges](https://www.arin.net/reference/research/statis
   * tics/address_filters/) that identifies the range of IP addresses reserved
   * for this instance. For example, 10.0.0.0/29, 192.168.0.0/24 or
   * 192.168.0.0/26, respectively. The range you specify can't overlap with
   * either existing subnets or assigned IP address ranges for other Filestore
   * instances in the selected VPC network.
   *
   * @param string $reservedIpRange
   */
  public function setReservedIpRange($reservedIpRange)
  {
    $this->reservedIpRange = $reservedIpRange;
  }
  /**
   * @return string
   */
  public function getReservedIpRange()
  {
    return $this->reservedIpRange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkConfig::class, 'Google_Service_CloudFilestore_NetworkConfig');
