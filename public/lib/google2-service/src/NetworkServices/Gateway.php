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

namespace Google\Service\NetworkServices;

class Gateway extends \Google\Collection
{
  /**
   * Defaults to NONE.
   */
  public const ENVOY_HEADERS_ENVOY_HEADERS_UNSPECIFIED = 'ENVOY_HEADERS_UNSPECIFIED';
  /**
   * Suppress envoy debug headers.
   */
  public const ENVOY_HEADERS_NONE = 'NONE';
  /**
   * Envoy will insert default internal debug headers into upstream requests:
   * x-envoy-attempt-count, x-envoy-is-timeout-retry, x-envoy-expected-rq-
   * timeout-ms, x-envoy-original-path, x-envoy-upstream-stream-duration-ms
   */
  public const ENVOY_HEADERS_DEBUG_HEADERS = 'DEBUG_HEADERS';
  /**
   * The type when IP version is not specified. Defaults to IPV4.
   */
  public const IP_VERSION_IP_VERSION_UNSPECIFIED = 'IP_VERSION_UNSPECIFIED';
  /**
   * The type for IP version 4.
   */
  public const IP_VERSION_IPV4 = 'IPV4';
  /**
   * The type for IP version 6.
   */
  public const IP_VERSION_IPV6 = 'IPV6';
  /**
   * The routing mode is explicit; clients are configured to send traffic
   * through the gateway. This is the default routing mode.
   */
  public const ROUTING_MODE_EXPLICIT_ROUTING_MODE = 'EXPLICIT_ROUTING_MODE';
  /**
   * The routing mode is next-hop. Clients are unaware of the gateway, and a
   * route (advanced route or other route type) can be configured to direct
   * traffic from client to gateway. The gateway then acts as a next-hop to the
   * destination.
   */
  public const ROUTING_MODE_NEXT_HOP_ROUTING_MODE = 'NEXT_HOP_ROUTING_MODE';
  /**
   * The type of the customer managed gateway is unspecified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * The type of the customer managed gateway is TrafficDirector Open Mesh.
   */
  public const TYPE_OPEN_MESH = 'OPEN_MESH';
  /**
   * The type of the customer managed gateway is SecureWebGateway (SWG).
   */
  public const TYPE_SECURE_WEB_GATEWAY = 'SECURE_WEB_GATEWAY';
  protected $collection_key = 'ports';
  /**
   * Optional. Zero or one IPv4 or IPv6 address on which the Gateway will
   * receive the traffic. When no address is provided, an IP from the subnetwork
   * is allocated This field only applies to gateways of type
   * 'SECURE_WEB_GATEWAY'. Gateways of type 'OPEN_MESH' listen on 0.0.0.0 for
   * IPv4 and :: for IPv6.
   *
   * @var string[]
   */
  public $addresses;
  /**
   * Optional. A fully-qualified Certificates URL reference. The proxy presents
   * a Certificate (selected based on SNI) when establishing a TLS connection.
   * This feature only applies to gateways of type 'SECURE_WEB_GATEWAY'.
   *
   * @var string[]
   */
  public $certificateUrls;
  /**
   * Output only. The timestamp when the resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. A free-text description of the resource. Max length 1024
   * characters.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Determines if envoy will insert internal debug headers into
   * upstream requests. Other Envoy headers may still be injected. By default,
   * envoy will not insert any debug headers.
   *
   * @var string
   */
  public $envoyHeaders;
  /**
   * Optional. A fully-qualified GatewaySecurityPolicy URL reference. Defines
   * how a server should apply security policy to inbound (VM to Proxy)
   * initiated connections. For example:
   * `projects/locations/gatewaySecurityPolicies/swg-policy`. This policy is
   * specific to gateways of type 'SECURE_WEB_GATEWAY'.
   *
   * @var string
   */
  public $gatewaySecurityPolicy;
  /**
   * Optional. The IP Version that will be used by this gateway. Valid options
   * are IPV4 or IPV6. Default is IPV4.
   *
   * @var string
   */
  public $ipVersion;
  /**
   * Optional. Set of label tags associated with the Gateway resource.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. Name of the Gateway resource. It matches pattern
   * `projects/locations/gateways/`.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The relative resource name identifying the VPC network that is
   * using this configuration. For example:
   * `projects/global/networks/network-1`. Currently, this field is specific to
   * gateways of type 'SECURE_WEB_GATEWAY'.
   *
   * @var string
   */
  public $network;
  /**
   * Required. One or more port numbers (1-65535), on which the Gateway will
   * receive traffic. The proxy binds to the specified ports. Gateways of type
   * 'SECURE_WEB_GATEWAY' are limited to 5 ports. Gateways of type 'OPEN_MESH'
   * listen on 0.0.0.0 for IPv4 and :: for IPv6 and support multiple ports.
   *
   * @var int[]
   */
  public $ports;
  /**
   * Optional. The routing mode of the Gateway. This field is configurable only
   * for gateways of type SECURE_WEB_GATEWAY. This field is required for
   * gateways of type SECURE_WEB_GATEWAY.
   *
   * @var string
   */
  public $routingMode;
  /**
   * Optional. Scope determines how configuration across multiple Gateway
   * instances are merged. The configuration for multiple Gateway instances with
   * the same scope will be merged as presented as a single configuration to the
   * proxy/load balancer. Max length 64 characters. Scope should start with a
   * letter and can only have letters, numbers, hyphens.
   *
   * @var string
   */
  public $scope;
  /**
   * Output only. Server-defined URL of this resource
   *
   * @var string
   */
  public $selfLink;
  /**
   * Optional. A fully-qualified ServerTLSPolicy URL reference. Specifies how
   * TLS traffic is terminated. If empty, TLS termination is disabled.
   *
   * @var string
   */
  public $serverTlsPolicy;
  /**
   * Optional. The relative resource name identifying the subnetwork in which
   * this SWG is allocated. For example: `projects/regions/us-
   * central1/subnetworks/network-1` Currently, this field is specific to
   * gateways of type 'SECURE_WEB_GATEWAY".
   *
   * @var string
   */
  public $subnetwork;
  /**
   * Immutable. The type of the customer managed gateway. This field is
   * required. If unspecified, an error is returned.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. The timestamp when the resource was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Zero or one IPv4 or IPv6 address on which the Gateway will
   * receive the traffic. When no address is provided, an IP from the subnetwork
   * is allocated This field only applies to gateways of type
   * 'SECURE_WEB_GATEWAY'. Gateways of type 'OPEN_MESH' listen on 0.0.0.0 for
   * IPv4 and :: for IPv6.
   *
   * @param string[] $addresses
   */
  public function setAddresses($addresses)
  {
    $this->addresses = $addresses;
  }
  /**
   * @return string[]
   */
  public function getAddresses()
  {
    return $this->addresses;
  }
  /**
   * Optional. A fully-qualified Certificates URL reference. The proxy presents
   * a Certificate (selected based on SNI) when establishing a TLS connection.
   * This feature only applies to gateways of type 'SECURE_WEB_GATEWAY'.
   *
   * @param string[] $certificateUrls
   */
  public function setCertificateUrls($certificateUrls)
  {
    $this->certificateUrls = $certificateUrls;
  }
  /**
   * @return string[]
   */
  public function getCertificateUrls()
  {
    return $this->certificateUrls;
  }
  /**
   * Output only. The timestamp when the resource was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. A free-text description of the resource. Max length 1024
   * characters.
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
   * Optional. Determines if envoy will insert internal debug headers into
   * upstream requests. Other Envoy headers may still be injected. By default,
   * envoy will not insert any debug headers.
   *
   * Accepted values: ENVOY_HEADERS_UNSPECIFIED, NONE, DEBUG_HEADERS
   *
   * @param self::ENVOY_HEADERS_* $envoyHeaders
   */
  public function setEnvoyHeaders($envoyHeaders)
  {
    $this->envoyHeaders = $envoyHeaders;
  }
  /**
   * @return self::ENVOY_HEADERS_*
   */
  public function getEnvoyHeaders()
  {
    return $this->envoyHeaders;
  }
  /**
   * Optional. A fully-qualified GatewaySecurityPolicy URL reference. Defines
   * how a server should apply security policy to inbound (VM to Proxy)
   * initiated connections. For example:
   * `projects/locations/gatewaySecurityPolicies/swg-policy`. This policy is
   * specific to gateways of type 'SECURE_WEB_GATEWAY'.
   *
   * @param string $gatewaySecurityPolicy
   */
  public function setGatewaySecurityPolicy($gatewaySecurityPolicy)
  {
    $this->gatewaySecurityPolicy = $gatewaySecurityPolicy;
  }
  /**
   * @return string
   */
  public function getGatewaySecurityPolicy()
  {
    return $this->gatewaySecurityPolicy;
  }
  /**
   * Optional. The IP Version that will be used by this gateway. Valid options
   * are IPV4 or IPV6. Default is IPV4.
   *
   * Accepted values: IP_VERSION_UNSPECIFIED, IPV4, IPV6
   *
   * @param self::IP_VERSION_* $ipVersion
   */
  public function setIpVersion($ipVersion)
  {
    $this->ipVersion = $ipVersion;
  }
  /**
   * @return self::IP_VERSION_*
   */
  public function getIpVersion()
  {
    return $this->ipVersion;
  }
  /**
   * Optional. Set of label tags associated with the Gateway resource.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Identifier. Name of the Gateway resource. It matches pattern
   * `projects/locations/gateways/`.
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
   * Optional. The relative resource name identifying the VPC network that is
   * using this configuration. For example:
   * `projects/global/networks/network-1`. Currently, this field is specific to
   * gateways of type 'SECURE_WEB_GATEWAY'.
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
   * Required. One or more port numbers (1-65535), on which the Gateway will
   * receive traffic. The proxy binds to the specified ports. Gateways of type
   * 'SECURE_WEB_GATEWAY' are limited to 5 ports. Gateways of type 'OPEN_MESH'
   * listen on 0.0.0.0 for IPv4 and :: for IPv6 and support multiple ports.
   *
   * @param int[] $ports
   */
  public function setPorts($ports)
  {
    $this->ports = $ports;
  }
  /**
   * @return int[]
   */
  public function getPorts()
  {
    return $this->ports;
  }
  /**
   * Optional. The routing mode of the Gateway. This field is configurable only
   * for gateways of type SECURE_WEB_GATEWAY. This field is required for
   * gateways of type SECURE_WEB_GATEWAY.
   *
   * Accepted values: EXPLICIT_ROUTING_MODE, NEXT_HOP_ROUTING_MODE
   *
   * @param self::ROUTING_MODE_* $routingMode
   */
  public function setRoutingMode($routingMode)
  {
    $this->routingMode = $routingMode;
  }
  /**
   * @return self::ROUTING_MODE_*
   */
  public function getRoutingMode()
  {
    return $this->routingMode;
  }
  /**
   * Optional. Scope determines how configuration across multiple Gateway
   * instances are merged. The configuration for multiple Gateway instances with
   * the same scope will be merged as presented as a single configuration to the
   * proxy/load balancer. Max length 64 characters. Scope should start with a
   * letter and can only have letters, numbers, hyphens.
   *
   * @param string $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return string
   */
  public function getScope()
  {
    return $this->scope;
  }
  /**
   * Output only. Server-defined URL of this resource
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
   * Optional. A fully-qualified ServerTLSPolicy URL reference. Specifies how
   * TLS traffic is terminated. If empty, TLS termination is disabled.
   *
   * @param string $serverTlsPolicy
   */
  public function setServerTlsPolicy($serverTlsPolicy)
  {
    $this->serverTlsPolicy = $serverTlsPolicy;
  }
  /**
   * @return string
   */
  public function getServerTlsPolicy()
  {
    return $this->serverTlsPolicy;
  }
  /**
   * Optional. The relative resource name identifying the subnetwork in which
   * this SWG is allocated. For example: `projects/regions/us-
   * central1/subnetworks/network-1` Currently, this field is specific to
   * gateways of type 'SECURE_WEB_GATEWAY".
   *
   * @param string $subnetwork
   */
  public function setSubnetwork($subnetwork)
  {
    $this->subnetwork = $subnetwork;
  }
  /**
   * @return string
   */
  public function getSubnetwork()
  {
    return $this->subnetwork;
  }
  /**
   * Immutable. The type of the customer managed gateway. This field is
   * required. If unspecified, an error is returned.
   *
   * Accepted values: TYPE_UNSPECIFIED, OPEN_MESH, SECURE_WEB_GATEWAY
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
  /**
   * Output only. The timestamp when the resource was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Gateway::class, 'Google_Service_NetworkServices_Gateway');
