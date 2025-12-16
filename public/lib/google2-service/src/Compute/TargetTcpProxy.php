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

class TargetTcpProxy extends \Google\Model
{
  public const PROXY_HEADER_NONE = 'NONE';
  public const PROXY_HEADER_PROXY_V1 = 'PROXY_V1';
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  /**
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#targetTcpProxy for target TCP proxies.
   *
   * @var string
   */
  public $kind;
  /**
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
   *
   * @var string
   */
  public $name;
  /**
   * This field only applies when the forwarding rule that references this
   * target proxy has a loadBalancingScheme set toINTERNAL_SELF_MANAGED.
   *
   * When this field is set to true, Envoy proxies set up inbound traffic
   * interception and bind to the IP address and port specified in the
   * forwarding rule. This is generally useful when using Traffic Director to
   * configure Envoy as a gateway or middle proxy (in other words, not a sidecar
   * proxy). The Envoy proxy listens for inbound requests and handles requests
   * when it receives them.
   *
   * The default is false.
   *
   * @var bool
   */
  public $proxyBind;
  /**
   * Specifies the type of proxy header to append before sending data to the
   * backend, either NONE or PROXY_V1. The default is NONE.
   *
   * @var string
   */
  public $proxyHeader;
  /**
   * Output only. [Output Only] URL of the region where the regional TCP proxy
   * resides. This field is not applicable to global TCP proxy.
   *
   * @var string
   */
  public $region;
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * URL to the BackendService resource.
   *
   * @var string
   */
  public $service;

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
   * An optional description of this resource. Provide this property when you
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
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
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
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#targetTcpProxy for target TCP proxies.
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
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
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
   * This field only applies when the forwarding rule that references this
   * target proxy has a loadBalancingScheme set toINTERNAL_SELF_MANAGED.
   *
   * When this field is set to true, Envoy proxies set up inbound traffic
   * interception and bind to the IP address and port specified in the
   * forwarding rule. This is generally useful when using Traffic Director to
   * configure Envoy as a gateway or middle proxy (in other words, not a sidecar
   * proxy). The Envoy proxy listens for inbound requests and handles requests
   * when it receives them.
   *
   * The default is false.
   *
   * @param bool $proxyBind
   */
  public function setProxyBind($proxyBind)
  {
    $this->proxyBind = $proxyBind;
  }
  /**
   * @return bool
   */
  public function getProxyBind()
  {
    return $this->proxyBind;
  }
  /**
   * Specifies the type of proxy header to append before sending data to the
   * backend, either NONE or PROXY_V1. The default is NONE.
   *
   * Accepted values: NONE, PROXY_V1
   *
   * @param self::PROXY_HEADER_* $proxyHeader
   */
  public function setProxyHeader($proxyHeader)
  {
    $this->proxyHeader = $proxyHeader;
  }
  /**
   * @return self::PROXY_HEADER_*
   */
  public function getProxyHeader()
  {
    return $this->proxyHeader;
  }
  /**
   * Output only. [Output Only] URL of the region where the regional TCP proxy
   * resides. This field is not applicable to global TCP proxy.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
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
   * URL to the BackendService resource.
   *
   * @param string $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return string
   */
  public function getService()
  {
    return $this->service;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TargetTcpProxy::class, 'Google_Service_Compute_TargetTcpProxy');
