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

class Network extends \Google\Collection
{
  public const NETWORK_FIREWALL_POLICY_ENFORCEMENT_ORDER_AFTER_CLASSIC_FIREWALL = 'AFTER_CLASSIC_FIREWALL';
  public const NETWORK_FIREWALL_POLICY_ENFORCEMENT_ORDER_BEFORE_CLASSIC_FIREWALL = 'BEFORE_CLASSIC_FIREWALL';
  protected $collection_key = 'subnetworks';
  protected $internal_gapi_mappings = [
        "iPv4Range" => "IPv4Range",
  ];
  /**
   * Deprecated in favor of subnet mode networks. The range of internal
   * addresses that are legal on this network. This range is aCIDR
   * specification, for example:192.168.0.0/16. Provided by the client when the
   * network is created.
   *
   * @deprecated
   * @var string
   */
  public $iPv4Range;
  /**
   * Must be set to create a VPC network. If not set, a legacy network is
   * created.
   *
   * When set to true, the VPC network is created in auto mode. When set to
   * false, the VPC network is created in custom mode.
   *
   * An auto mode VPC network starts with one subnet per region. Each subnet has
   * a predetermined range as described inAuto mode VPC network IP ranges.
   *
   * For custom mode VPC networks, you can add subnets using the
   * subnetworksinsert method.
   *
   * @var bool
   */
  public $autoCreateSubnetworks;
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * An optional description of this resource. Provide this field when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  /**
   * Enable ULA internal ipv6 on this network. Enabling this feature will assign
   * a /48 from google defined ULA prefix fd20::/20. .
   *
   * @var bool
   */
  public $enableUlaInternalIpv6;
  /**
   * Output only. [Output Only] URL of the firewall policy the network is
   * associated with.
   *
   * @var string
   */
  public $firewallPolicy;
  /**
   * [Output Only] The gateway address for default routing out of the network,
   * selected by Google Cloud.
   *
   * @var string
   */
  public $gatewayIPv4;
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * When enabling ula internal ipv6, caller optionally can specify the /48
   * range they want from the google defined ULA prefix fd20::/20. The input
   * must be a valid /48 ULA IPv6 address and must be within the fd20::/20.
   * Operation will fail if the speficied /48 is already in used by another
   * resource. If the field is not speficied, then a /48 range will be randomly
   * allocated from fd20::/20 and returned via this field. .
   *
   * @var string
   */
  public $internalIpv6Range;
  /**
   * Output only. [Output Only] Type of the resource. Always compute#network for
   * networks.
   *
   * @var string
   */
  public $kind;
  /**
   * Maximum Transmission Unit in bytes. The minimum value for this field is
   * 1300 and the maximum value is 8896. The suggested value is 1500, which is
   * the default MTU used on the Internet, or 8896 if you want to use Jumbo
   * frames. If unspecified, the value defaults to 1460.
   *
   * @var int
   */
  public $mtu;
  /**
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?`. The first character must be a
   * lowercase letter, and all following characters (except for the last
   * character) must be a dash, lowercase letter, or digit. The last character
   * must be a lowercase letter or digit.
   *
   * @var string
   */
  public $name;
  /**
   * The network firewall policy enforcement order. Can be either
   * AFTER_CLASSIC_FIREWALL or BEFORE_CLASSIC_FIREWALL. Defaults to
   * AFTER_CLASSIC_FIREWALL if the field is not specified.
   *
   * @var string
   */
  public $networkFirewallPolicyEnforcementOrder;
  /**
   * A full or partial URL of the network profile to apply to this network. This
   * field can be set only at resource creation time. For example, the following
   * are valid URLs:         - https://www.googleapis.com/compute/{api_version}/
   * projects/{project_id}/global/networkProfiles/{network_profile_name}    -
   * projects/{project_id}/global/networkProfiles/{network_profile_name}
   *
   * @var string
   */
  public $networkProfile;
  protected $paramsType = NetworkParams::class;
  protected $paramsDataType = '';
  protected $peeringsType = NetworkPeering::class;
  protected $peeringsDataType = 'array';
  protected $routingConfigType = NetworkRoutingConfig::class;
  protected $routingConfigDataType = '';
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Output only. [Output Only] Server-defined URL for this resource with the
   * resource id.
   *
   * @var string
   */
  public $selfLinkWithId;
  /**
   * [Output Only] Server-defined fully-qualified URLs for all subnetworks in
   * this VPC network.
   *
   * @var string[]
   */
  public $subnetworks;

  /**
   * Deprecated in favor of subnet mode networks. The range of internal
   * addresses that are legal on this network. This range is aCIDR
   * specification, for example:192.168.0.0/16. Provided by the client when the
   * network is created.
   *
   * @deprecated
   * @param string $iPv4Range
   */
  public function setIPv4Range($iPv4Range)
  {
    $this->iPv4Range = $iPv4Range;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getIPv4Range()
  {
    return $this->iPv4Range;
  }
  /**
   * Must be set to create a VPC network. If not set, a legacy network is
   * created.
   *
   * When set to true, the VPC network is created in auto mode. When set to
   * false, the VPC network is created in custom mode.
   *
   * An auto mode VPC network starts with one subnet per region. Each subnet has
   * a predetermined range as described inAuto mode VPC network IP ranges.
   *
   * For custom mode VPC networks, you can add subnets using the
   * subnetworksinsert method.
   *
   * @param bool $autoCreateSubnetworks
   */
  public function setAutoCreateSubnetworks($autoCreateSubnetworks)
  {
    $this->autoCreateSubnetworks = $autoCreateSubnetworks;
  }
  /**
   * @return bool
   */
  public function getAutoCreateSubnetworks()
  {
    return $this->autoCreateSubnetworks;
  }
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @param string $creationTimestamp
   */
  public function setCreationTimestamp($creationTimestamp)
  {
    $this->creationTimestamp = $creationTimestamp;
  }
  /**
   * @return string
   */
  public function getCreationTimestamp()
  {
    return $this->creationTimestamp;
  }
  /**
   * An optional description of this resource. Provide this field when you
   * create the resource.
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
   * Enable ULA internal ipv6 on this network. Enabling this feature will assign
   * a /48 from google defined ULA prefix fd20::/20. .
   *
   * @param bool $enableUlaInternalIpv6
   */
  public function setEnableUlaInternalIpv6($enableUlaInternalIpv6)
  {
    $this->enableUlaInternalIpv6 = $enableUlaInternalIpv6;
  }
  /**
   * @return bool
   */
  public function getEnableUlaInternalIpv6()
  {
    return $this->enableUlaInternalIpv6;
  }
  /**
   * Output only. [Output Only] URL of the firewall policy the network is
   * associated with.
   *
   * @param string $firewallPolicy
   */
  public function setFirewallPolicy($firewallPolicy)
  {
    $this->firewallPolicy = $firewallPolicy;
  }
  /**
   * @return string
   */
  public function getFirewallPolicy()
  {
    return $this->firewallPolicy;
  }
  /**
   * [Output Only] The gateway address for default routing out of the network,
   * selected by Google Cloud.
   *
   * @param string $gatewayIPv4
   */
  public function setGatewayIPv4($gatewayIPv4)
  {
    $this->gatewayIPv4 = $gatewayIPv4;
  }
  /**
   * @return string
   */
  public function getGatewayIPv4()
  {
    return $this->gatewayIPv4;
  }
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * When enabling ula internal ipv6, caller optionally can specify the /48
   * range they want from the google defined ULA prefix fd20::/20. The input
   * must be a valid /48 ULA IPv6 address and must be within the fd20::/20.
   * Operation will fail if the speficied /48 is already in used by another
   * resource. If the field is not speficied, then a /48 range will be randomly
   * allocated from fd20::/20 and returned via this field. .
   *
   * @param string $internalIpv6Range
   */
  public function setInternalIpv6Range($internalIpv6Range)
  {
    $this->internalIpv6Range = $internalIpv6Range;
  }
  /**
   * @return string
   */
  public function getInternalIpv6Range()
  {
    return $this->internalIpv6Range;
  }
  /**
   * Output only. [Output Only] Type of the resource. Always compute#network for
   * networks.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Maximum Transmission Unit in bytes. The minimum value for this field is
   * 1300 and the maximum value is 8896. The suggested value is 1500, which is
   * the default MTU used on the Internet, or 8896 if you want to use Jumbo
   * frames. If unspecified, the value defaults to 1460.
   *
   * @param int $mtu
   */
  public function setMtu($mtu)
  {
    $this->mtu = $mtu;
  }
  /**
   * @return int
   */
  public function getMtu()
  {
    return $this->mtu;
  }
  /**
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?`. The first character must be a
   * lowercase letter, and all following characters (except for the last
   * character) must be a dash, lowercase letter, or digit. The last character
   * must be a lowercase letter or digit.
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
   * The network firewall policy enforcement order. Can be either
   * AFTER_CLASSIC_FIREWALL or BEFORE_CLASSIC_FIREWALL. Defaults to
   * AFTER_CLASSIC_FIREWALL if the field is not specified.
   *
   * Accepted values: AFTER_CLASSIC_FIREWALL, BEFORE_CLASSIC_FIREWALL
   *
   * @param self::NETWORK_FIREWALL_POLICY_ENFORCEMENT_ORDER_* $networkFirewallPolicyEnforcementOrder
   */
  public function setNetworkFirewallPolicyEnforcementOrder($networkFirewallPolicyEnforcementOrder)
  {
    $this->networkFirewallPolicyEnforcementOrder = $networkFirewallPolicyEnforcementOrder;
  }
  /**
   * @return self::NETWORK_FIREWALL_POLICY_ENFORCEMENT_ORDER_*
   */
  public function getNetworkFirewallPolicyEnforcementOrder()
  {
    return $this->networkFirewallPolicyEnforcementOrder;
  }
  /**
   * A full or partial URL of the network profile to apply to this network. This
   * field can be set only at resource creation time. For example, the following
   * are valid URLs:         - https://www.googleapis.com/compute/{api_version}/
   * projects/{project_id}/global/networkProfiles/{network_profile_name}    -
   * projects/{project_id}/global/networkProfiles/{network_profile_name}
   *
   * @param string $networkProfile
   */
  public function setNetworkProfile($networkProfile)
  {
    $this->networkProfile = $networkProfile;
  }
  /**
   * @return string
   */
  public function getNetworkProfile()
  {
    return $this->networkProfile;
  }
  /**
   * Input only. [Input Only] Additional params passed with the request, but not
   * persisted as part of resource payload.
   *
   * @param NetworkParams $params
   */
  public function setParams(NetworkParams $params)
  {
    $this->params = $params;
  }
  /**
   * @return NetworkParams
   */
  public function getParams()
  {
    return $this->params;
  }
  /**
   * Output only. [Output Only] A list of network peerings for the resource.
   *
   * @param NetworkPeering[] $peerings
   */
  public function setPeerings($peerings)
  {
    $this->peerings = $peerings;
  }
  /**
   * @return NetworkPeering[]
   */
  public function getPeerings()
  {
    return $this->peerings;
  }
  /**
   * The network-level routing configuration for this network.  Used by Cloud
   * Router to determine what type of network-wide routing behavior to enforce.
   *
   * @param NetworkRoutingConfig $routingConfig
   */
  public function setRoutingConfig(NetworkRoutingConfig $routingConfig)
  {
    $this->routingConfig = $routingConfig;
  }
  /**
   * @return NetworkRoutingConfig
   */
  public function getRoutingConfig()
  {
    return $this->routingConfig;
  }
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Output only. [Output Only] Server-defined URL for this resource with the
   * resource id.
   *
   * @param string $selfLinkWithId
   */
  public function setSelfLinkWithId($selfLinkWithId)
  {
    $this->selfLinkWithId = $selfLinkWithId;
  }
  /**
   * @return string
   */
  public function getSelfLinkWithId()
  {
    return $this->selfLinkWithId;
  }
  /**
   * [Output Only] Server-defined fully-qualified URLs for all subnetworks in
   * this VPC network.
   *
   * @param string[] $subnetworks
   */
  public function setSubnetworks($subnetworks)
  {
    $this->subnetworks = $subnetworks;
  }
  /**
   * @return string[]
   */
  public function getSubnetworks()
  {
    return $this->subnetworks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Network::class, 'Google_Service_Compute_Network');
