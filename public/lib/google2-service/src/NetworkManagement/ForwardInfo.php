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

class ForwardInfo extends \Google\Model
{
  /**
   * Target not specified.
   */
  public const TARGET_TARGET_UNSPECIFIED = 'TARGET_UNSPECIFIED';
  /**
   * Forwarded to a VPC peering network.
   */
  public const TARGET_PEERING_VPC = 'PEERING_VPC';
  /**
   * Forwarded to a Cloud VPN gateway.
   */
  public const TARGET_VPN_GATEWAY = 'VPN_GATEWAY';
  /**
   * Forwarded to a Cloud Interconnect connection.
   */
  public const TARGET_INTERCONNECT = 'INTERCONNECT';
  /**
   * Forwarded to a Google Kubernetes Engine Container cluster master.
   *
   * @deprecated
   */
  public const TARGET_GKE_MASTER = 'GKE_MASTER';
  /**
   * Forwarded to the next hop of a custom route imported from a peering VPC.
   */
  public const TARGET_IMPORTED_CUSTOM_ROUTE_NEXT_HOP = 'IMPORTED_CUSTOM_ROUTE_NEXT_HOP';
  /**
   * Forwarded to a Cloud SQL instance.
   *
   * @deprecated
   */
  public const TARGET_CLOUD_SQL_INSTANCE = 'CLOUD_SQL_INSTANCE';
  /**
   * Forwarded to a VPC network in another project.
   */
  public const TARGET_ANOTHER_PROJECT = 'ANOTHER_PROJECT';
  /**
   * Forwarded to an NCC Hub.
   */
  public const TARGET_NCC_HUB = 'NCC_HUB';
  /**
   * Forwarded to a router appliance.
   */
  public const TARGET_ROUTER_APPLIANCE = 'ROUTER_APPLIANCE';
  /**
   * Forwarded to a Secure Web Proxy Gateway.
   */
  public const TARGET_SECURE_WEB_PROXY_GATEWAY = 'SECURE_WEB_PROXY_GATEWAY';
  /**
   * IP address of the target (if applicable).
   *
   * @var string
   */
  public $ipAddress;
  /**
   * URI of the resource that the packet is forwarded to.
   *
   * @var string
   */
  public $resourceUri;
  /**
   * Target type where this packet is forwarded to.
   *
   * @var string
   */
  public $target;

  /**
   * IP address of the target (if applicable).
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
   * URI of the resource that the packet is forwarded to.
   *
   * @param string $resourceUri
   */
  public function setResourceUri($resourceUri)
  {
    $this->resourceUri = $resourceUri;
  }
  /**
   * @return string
   */
  public function getResourceUri()
  {
    return $this->resourceUri;
  }
  /**
   * Target type where this packet is forwarded to.
   *
   * Accepted values: TARGET_UNSPECIFIED, PEERING_VPC, VPN_GATEWAY,
   * INTERCONNECT, GKE_MASTER, IMPORTED_CUSTOM_ROUTE_NEXT_HOP,
   * CLOUD_SQL_INSTANCE, ANOTHER_PROJECT, NCC_HUB, ROUTER_APPLIANCE,
   * SECURE_WEB_PROXY_GATEWAY
   *
   * @param self::TARGET_* $target
   */
  public function setTarget($target)
  {
    $this->target = $target;
  }
  /**
   * @return self::TARGET_*
   */
  public function getTarget()
  {
    return $this->target;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ForwardInfo::class, 'Google_Service_NetworkManagement_ForwardInfo');
