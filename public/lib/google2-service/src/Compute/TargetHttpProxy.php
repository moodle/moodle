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

class TargetHttpProxy extends \Google\Model
{
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
   * Fingerprint of this resource. A hash of the contents stored in this object.
   * This field is used in optimistic locking. This field will be ignored when
   * inserting a TargetHttpProxy. An up-to-date fingerprint must be provided in
   * order to patch/update the TargetHttpProxy; otherwise, the request will fail
   * with error 412 conditionNotMet. To see the latest fingerprint, make a get()
   * request to retrieve the TargetHttpProxy.
   *
   * @var string
   */
  public $fingerprint;
  /**
   * Specifies how long to keep a connection open, after completing a response,
   * while there is no matching traffic (in seconds). If an HTTP keep-alive is
   * not specified, a default value (610 seconds) will be used.
   *
   * For global external Application Load Balancers, the minimum allowed value
   * is 5 seconds and the maximum allowed value is 1200 seconds.
   *
   * For classic Application Load Balancers, this option is not supported.
   *
   * @var int
   */
  public $httpKeepAliveTimeoutSec;
  /**
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] Type of resource. Always compute#targetHttpProxy
   * for target HTTP proxies.
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
   * Output only. [Output Only] URL of the region where the regional Target HTTP
   * Proxy resides. This field is not applicable to global Target HTTP Proxies.
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
   * URL to the UrlMap resource that defines the mapping from URL to the
   * BackendService.
   *
   * @var string
   */
  public $urlMap;

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
   * Fingerprint of this resource. A hash of the contents stored in this object.
   * This field is used in optimistic locking. This field will be ignored when
   * inserting a TargetHttpProxy. An up-to-date fingerprint must be provided in
   * order to patch/update the TargetHttpProxy; otherwise, the request will fail
   * with error 412 conditionNotMet. To see the latest fingerprint, make a get()
   * request to retrieve the TargetHttpProxy.
   *
   * @param string $fingerprint
   */
  public function setFingerprint($fingerprint)
  {
    $this->fingerprint = $fingerprint;
  }
  /**
   * @return string
   */
  public function getFingerprint()
  {
    return $this->fingerprint;
  }
  /**
   * Specifies how long to keep a connection open, after completing a response,
   * while there is no matching traffic (in seconds). If an HTTP keep-alive is
   * not specified, a default value (610 seconds) will be used.
   *
   * For global external Application Load Balancers, the minimum allowed value
   * is 5 seconds and the maximum allowed value is 1200 seconds.
   *
   * For classic Application Load Balancers, this option is not supported.
   *
   * @param int $httpKeepAliveTimeoutSec
   */
  public function setHttpKeepAliveTimeoutSec($httpKeepAliveTimeoutSec)
  {
    $this->httpKeepAliveTimeoutSec = $httpKeepAliveTimeoutSec;
  }
  /**
   * @return int
   */
  public function getHttpKeepAliveTimeoutSec()
  {
    return $this->httpKeepAliveTimeoutSec;
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
   * Output only. [Output Only] Type of resource. Always compute#targetHttpProxy
   * for target HTTP proxies.
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
   * Output only. [Output Only] URL of the region where the regional Target HTTP
   * Proxy resides. This field is not applicable to global Target HTTP Proxies.
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
   * URL to the UrlMap resource that defines the mapping from URL to the
   * BackendService.
   *
   * @param string $urlMap
   */
  public function setUrlMap($urlMap)
  {
    $this->urlMap = $urlMap;
  }
  /**
   * @return string
   */
  public function getUrlMap()
  {
    return $this->urlMap;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TargetHttpProxy::class, 'Google_Service_Compute_TargetHttpProxy');
