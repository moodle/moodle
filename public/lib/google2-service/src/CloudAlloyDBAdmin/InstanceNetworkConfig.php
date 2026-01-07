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

namespace Google\Service\CloudAlloyDBAdmin;

class InstanceNetworkConfig extends \Google\Collection
{
  protected $collection_key = 'authorizedExternalNetworks';
  /**
   * Optional. Name of the allocated IP range for the private IP AlloyDB
   * instance, for example: "google-managed-services-default". If set, the
   * instance IPs will be created from this allocated range and will override
   * the IP range used by the parent cluster. The range name must comply with
   * [RFC 1035](http://datatracker.ietf.org/doc/html/rfc1035). Specifically, the
   * name must be 1-63 characters long and match the regular expression
   * [a-z]([-a-z0-9]*[a-z0-9])?.
   *
   * @var string
   */
  public $allocatedIpRangeOverride;
  protected $authorizedExternalNetworksType = AuthorizedNetwork::class;
  protected $authorizedExternalNetworksDataType = 'array';
  /**
   * Optional. Enabling an outbound public IP address to support a database
   * server sending requests out into the internet.
   *
   * @var bool
   */
  public $enableOutboundPublicIp;
  /**
   * Optional. Enabling public ip for the instance.
   *
   * @var bool
   */
  public $enablePublicIp;
  /**
   * Output only. The resource link for the VPC network in which instance
   * resources are created and from which they are accessible via Private IP.
   * This will be the same value as the parent cluster's network. It is
   * specified in the form: //
   * `projects/{project_number}/global/networks/{network_id}`.
   *
   * @var string
   */
  public $network;

  /**
   * Optional. Name of the allocated IP range for the private IP AlloyDB
   * instance, for example: "google-managed-services-default". If set, the
   * instance IPs will be created from this allocated range and will override
   * the IP range used by the parent cluster. The range name must comply with
   * [RFC 1035](http://datatracker.ietf.org/doc/html/rfc1035). Specifically, the
   * name must be 1-63 characters long and match the regular expression
   * [a-z]([-a-z0-9]*[a-z0-9])?.
   *
   * @param string $allocatedIpRangeOverride
   */
  public function setAllocatedIpRangeOverride($allocatedIpRangeOverride)
  {
    $this->allocatedIpRangeOverride = $allocatedIpRangeOverride;
  }
  /**
   * @return string
   */
  public function getAllocatedIpRangeOverride()
  {
    return $this->allocatedIpRangeOverride;
  }
  /**
   * Optional. A list of external network authorized to access this instance.
   *
   * @param AuthorizedNetwork[] $authorizedExternalNetworks
   */
  public function setAuthorizedExternalNetworks($authorizedExternalNetworks)
  {
    $this->authorizedExternalNetworks = $authorizedExternalNetworks;
  }
  /**
   * @return AuthorizedNetwork[]
   */
  public function getAuthorizedExternalNetworks()
  {
    return $this->authorizedExternalNetworks;
  }
  /**
   * Optional. Enabling an outbound public IP address to support a database
   * server sending requests out into the internet.
   *
   * @param bool $enableOutboundPublicIp
   */
  public function setEnableOutboundPublicIp($enableOutboundPublicIp)
  {
    $this->enableOutboundPublicIp = $enableOutboundPublicIp;
  }
  /**
   * @return bool
   */
  public function getEnableOutboundPublicIp()
  {
    return $this->enableOutboundPublicIp;
  }
  /**
   * Optional. Enabling public ip for the instance.
   *
   * @param bool $enablePublicIp
   */
  public function setEnablePublicIp($enablePublicIp)
  {
    $this->enablePublicIp = $enablePublicIp;
  }
  /**
   * @return bool
   */
  public function getEnablePublicIp()
  {
    return $this->enablePublicIp;
  }
  /**
   * Output only. The resource link for the VPC network in which instance
   * resources are created and from which they are accessible via Private IP.
   * This will be the same value as the parent cluster's network. It is
   * specified in the form: //
   * `projects/{project_number}/global/networks/{network_id}`.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceNetworkConfig::class, 'Google_Service_CloudAlloyDBAdmin_InstanceNetworkConfig');
