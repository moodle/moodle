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

class TargetHttpsProxy extends \Google\Collection
{
  /**
   * The load balancer will not attempt to negotiate QUIC with clients.
   */
  public const QUIC_OVERRIDE_DISABLE = 'DISABLE';
  /**
   * The load balancer will attempt to negotiate QUIC with clients.
   */
  public const QUIC_OVERRIDE_ENABLE = 'ENABLE';
  /**
   * No overrides to the default QUIC policy. This option is implicit if no QUIC
   * override has been specified in the request.
   */
  public const QUIC_OVERRIDE_NONE = 'NONE';
  /**
   * TLS 1.3 Early Data is not advertised, and any (invalid) attempts to send
   * Early Data will be rejected by closing the connection.
   */
  public const TLS_EARLY_DATA_DISABLED = 'DISABLED';
  /**
   * This enables TLS 1.3 0-RTT, and only allows Early Data to be included on
   * requests with safe HTTP methods (GET, HEAD, OPTIONS, TRACE). This mode does
   * not enforce any other limitations for requests with Early Data. The
   * application owner should validate that Early Data is acceptable for a given
   * request path.
   */
  public const TLS_EARLY_DATA_PERMISSIVE = 'PERMISSIVE';
  /**
   * This enables TLS 1.3 0-RTT, and only allows Early Data to be included on
   * requests with safe HTTP methods (GET, HEAD, OPTIONS, TRACE) without query
   * parameters. Requests that send Early Data with non-idempotent HTTP methods
   * or with query parameters will be rejected with a HTTP 425.
   */
  public const TLS_EARLY_DATA_STRICT = 'STRICT';
  /**
   * This enables TLS 1.3 Early Data for requests with any HTTP method including
   * non-idempotent methods list POST. This mode does not enforce any other
   * limitations. This may be valuable for gRPC use cases. However, we do not
   * recommend this method unless you have evaluated your security stance and
   * mitigated the risk of replay attacks using other mechanisms.
   */
  public const TLS_EARLY_DATA_UNRESTRICTED = 'UNRESTRICTED';
  protected $collection_key = 'sslCertificates';
  /**
   * Optional. A URL referring to a networksecurity.AuthorizationPolicy resource
   * that describes how the proxy should authorize inbound traffic. If left
   * blank, access will not be restricted by an authorization policy.
   *
   *  Refer to the AuthorizationPolicy resource for additional details.
   *
   *  authorizationPolicy only applies to a globalTargetHttpsProxy attached
   * toglobalForwardingRules with theloadBalancingScheme set to
   * INTERNAL_SELF_MANAGED.
   *
   *  Note: This field currently has no impact.
   *
   * @var string
   */
  public $authorizationPolicy;
  /**
   * URL of a certificate map that identifies a certificate map associated with
   * the given target proxy. This field can only be set for Global external
   * Application Load Balancer or Classic Application Load Balancer. For other
   * products use Certificate Manager Certificates instead.
   *
   * If set, sslCertificates will be ignored.
   *
   *  Accepted format is//certificatemanager.googleapis.com/projects/{project}/l
   * ocations/{location}/certificateMaps/{resourceName}.
   *
   * @var string
   */
  public $certificateMap;
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
   * inserting a TargetHttpsProxy. An up-to-date fingerprint must be provided in
   * order to patch the TargetHttpsProxy; otherwise, the request will fail with
   * error 412 conditionNotMet. To see the latest fingerprint, make a get()
   * request to retrieve the TargetHttpsProxy.
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
   * Output only. [Output Only] Type of resource. Alwayscompute#targetHttpsProxy
   * for target HTTPS proxies.
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
   * Specifies the QUIC override policy for this TargetHttpsProxy resource. This
   * setting determines whether the load balancer attempts to negotiate QUIC
   * with clients. You can specify NONE, ENABLE, orDISABLE.        - When quic-
   * override is set to NONE,    Google manages whether QUIC is used.    - When
   * quic-override is set to ENABLE, the    load balancer uses QUIC when
   * possible.    - When quic-override is set to DISABLE, the    load balancer
   * doesn't use QUIC.    - If the quic-override flag is not specified,NONE is
   * implied.
   *
   * @var string
   */
  public $quicOverride;
  /**
   * Output only. [Output Only] URL of the region where the regional
   * TargetHttpsProxy resides. This field is not applicable to global
   * TargetHttpsProxies.
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
   * Optional. A URL referring to a networksecurity.ServerTlsPolicy resource
   * that describes how the proxy should authenticate inbound traffic.
   *
   *  serverTlsPolicy only applies to a globalTargetHttpsProxy attached
   * toglobalForwardingRules with theloadBalancingScheme set to
   * INTERNAL_SELF_MANAGED or EXTERNAL orEXTERNAL_MANAGED or INTERNAL_MANAGED.
   * It also applies to a regional TargetHttpsProxy attached to regional
   * forwardingRules with theloadBalancingScheme set to EXTERNAL_MANAGED
   * orINTERNAL_MANAGED. For details whichServerTlsPolicy resources are accepted
   * withINTERNAL_SELF_MANAGED and which with EXTERNAL,INTERNAL_MANAGED,
   * EXTERNAL_MANAGEDloadBalancingScheme consult ServerTlsPolicy documentation.
   *
   *   If left blank, communications are not encrypted.
   *
   * @var string
   */
  public $serverTlsPolicy;
  /**
   * URLs to SslCertificate resources that are used to authenticate connections
   * between users and the load balancer. At least one SSL certificate must be
   * specified. SslCertificates do not apply when the load balancing scheme is
   * set to INTERNAL_SELF_MANAGED.
   *
   * The URLs should refer to a SSL Certificate resource or Certificate Manager
   * Certificate resource. Mixing Classic Certificates and Certificate Manager
   * Certificates is not allowed. Certificate Manager Certificates must include
   * the certificatemanager API namespace. Using Certificate Manager
   * Certificates in this field is not supported by Global external Application
   * Load Balancer or Classic Application Load Balancer, use certificate_map
   * instead.
   *
   * Currently, you may specify up to 15 Classic SSL Certificates or up to 100
   * Certificate Manager Certificates.
   *
   * Certificate Manager Certificates accepted formats are:        - //certifica
   * temanager.googleapis.com/projects/{project}/locations/{location}/certificat
   * es/{resourceName}.    - https://certificatemanager.googleapis.com/v1alpha1/
   * projects/{project}/locations/{location}/certificates/{resourceName}.
   *
   * @var string[]
   */
  public $sslCertificates;
  /**
   * URL of SslPolicy resource that will be associated with the TargetHttpsProxy
   * resource. If not set, the TargetHttpsProxy resource has no SSL policy
   * configured.
   *
   * @var string
   */
  public $sslPolicy;
  /**
   * Specifies whether TLS 1.3 0-RTT Data ("Early Data") should be accepted for
   * this service. Early Data allows a TLS resumption handshake to include the
   * initial application payload (a HTTP request) alongside the handshake,
   * reducing the effective round trips to "zero". This applies to TLS 1.3
   * connections over TCP (HTTP/2) as well as over UDP (QUIC/h3).
   *
   * This can improve application performance, especially on networks where
   * interruptions may be common, such as on mobile.
   *
   * Requests with Early Data will have the "Early-Data" HTTP header set on the
   * request, with a value of "1", to allow the backend to determine whether
   * Early Data was included.
   *
   * Note: TLS Early Data may allow requests to be replayed, as the data is sent
   * to the backend before the handshake has fully completed. Applications that
   * allow idempotent HTTP methods to make non-idempotent changes, such as a GET
   * request updating a database, should not accept Early Data on those
   * requests, and reject requests with the "Early-Data: 1" HTTP header by
   * returning a HTTP 425 (Too Early) status code, in order to remain RFC
   * compliant.
   *
   * The default value is DISABLED.
   *
   * @var string
   */
  public $tlsEarlyData;
  /**
   * A fully-qualified or valid partial URL to the UrlMap resource that defines
   * the mapping from URL to the BackendService. For example, the following are
   * all valid URLs for specifying a URL map:        -
   * https://www.googleapis.compute/v1/projects/project/global/urlMaps/url-map
   * - projects/project/global/urlMaps/url-map     - global/urlMaps/url-map
   *
   * @var string
   */
  public $urlMap;

  /**
   * Optional. A URL referring to a networksecurity.AuthorizationPolicy resource
   * that describes how the proxy should authorize inbound traffic. If left
   * blank, access will not be restricted by an authorization policy.
   *
   *  Refer to the AuthorizationPolicy resource for additional details.
   *
   *  authorizationPolicy only applies to a globalTargetHttpsProxy attached
   * toglobalForwardingRules with theloadBalancingScheme set to
   * INTERNAL_SELF_MANAGED.
   *
   *  Note: This field currently has no impact.
   *
   * @param string $authorizationPolicy
   */
  public function setAuthorizationPolicy($authorizationPolicy)
  {
    $this->authorizationPolicy = $authorizationPolicy;
  }
  /**
   * @return string
   */
  public function getAuthorizationPolicy()
  {
    return $this->authorizationPolicy;
  }
  /**
   * URL of a certificate map that identifies a certificate map associated with
   * the given target proxy. This field can only be set for Global external
   * Application Load Balancer or Classic Application Load Balancer. For other
   * products use Certificate Manager Certificates instead.
   *
   * If set, sslCertificates will be ignored.
   *
   *  Accepted format is//certificatemanager.googleapis.com/projects/{project}/l
   * ocations/{location}/certificateMaps/{resourceName}.
   *
   * @param string $certificateMap
   */
  public function setCertificateMap($certificateMap)
  {
    $this->certificateMap = $certificateMap;
  }
  /**
   * @return string
   */
  public function getCertificateMap()
  {
    return $this->certificateMap;
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
   * inserting a TargetHttpsProxy. An up-to-date fingerprint must be provided in
   * order to patch the TargetHttpsProxy; otherwise, the request will fail with
   * error 412 conditionNotMet. To see the latest fingerprint, make a get()
   * request to retrieve the TargetHttpsProxy.
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
   * Output only. [Output Only] Type of resource. Alwayscompute#targetHttpsProxy
   * for target HTTPS proxies.
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
   * Specifies the QUIC override policy for this TargetHttpsProxy resource. This
   * setting determines whether the load balancer attempts to negotiate QUIC
   * with clients. You can specify NONE, ENABLE, orDISABLE.        - When quic-
   * override is set to NONE,    Google manages whether QUIC is used.    - When
   * quic-override is set to ENABLE, the    load balancer uses QUIC when
   * possible.    - When quic-override is set to DISABLE, the    load balancer
   * doesn't use QUIC.    - If the quic-override flag is not specified,NONE is
   * implied.
   *
   * Accepted values: DISABLE, ENABLE, NONE
   *
   * @param self::QUIC_OVERRIDE_* $quicOverride
   */
  public function setQuicOverride($quicOverride)
  {
    $this->quicOverride = $quicOverride;
  }
  /**
   * @return self::QUIC_OVERRIDE_*
   */
  public function getQuicOverride()
  {
    return $this->quicOverride;
  }
  /**
   * Output only. [Output Only] URL of the region where the regional
   * TargetHttpsProxy resides. This field is not applicable to global
   * TargetHttpsProxies.
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
   * Optional. A URL referring to a networksecurity.ServerTlsPolicy resource
   * that describes how the proxy should authenticate inbound traffic.
   *
   *  serverTlsPolicy only applies to a globalTargetHttpsProxy attached
   * toglobalForwardingRules with theloadBalancingScheme set to
   * INTERNAL_SELF_MANAGED or EXTERNAL orEXTERNAL_MANAGED or INTERNAL_MANAGED.
   * It also applies to a regional TargetHttpsProxy attached to regional
   * forwardingRules with theloadBalancingScheme set to EXTERNAL_MANAGED
   * orINTERNAL_MANAGED. For details whichServerTlsPolicy resources are accepted
   * withINTERNAL_SELF_MANAGED and which with EXTERNAL,INTERNAL_MANAGED,
   * EXTERNAL_MANAGEDloadBalancingScheme consult ServerTlsPolicy documentation.
   *
   *   If left blank, communications are not encrypted.
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
   * URLs to SslCertificate resources that are used to authenticate connections
   * between users and the load balancer. At least one SSL certificate must be
   * specified. SslCertificates do not apply when the load balancing scheme is
   * set to INTERNAL_SELF_MANAGED.
   *
   * The URLs should refer to a SSL Certificate resource or Certificate Manager
   * Certificate resource. Mixing Classic Certificates and Certificate Manager
   * Certificates is not allowed. Certificate Manager Certificates must include
   * the certificatemanager API namespace. Using Certificate Manager
   * Certificates in this field is not supported by Global external Application
   * Load Balancer or Classic Application Load Balancer, use certificate_map
   * instead.
   *
   * Currently, you may specify up to 15 Classic SSL Certificates or up to 100
   * Certificate Manager Certificates.
   *
   * Certificate Manager Certificates accepted formats are:        - //certifica
   * temanager.googleapis.com/projects/{project}/locations/{location}/certificat
   * es/{resourceName}.    - https://certificatemanager.googleapis.com/v1alpha1/
   * projects/{project}/locations/{location}/certificates/{resourceName}.
   *
   * @param string[] $sslCertificates
   */
  public function setSslCertificates($sslCertificates)
  {
    $this->sslCertificates = $sslCertificates;
  }
  /**
   * @return string[]
   */
  public function getSslCertificates()
  {
    return $this->sslCertificates;
  }
  /**
   * URL of SslPolicy resource that will be associated with the TargetHttpsProxy
   * resource. If not set, the TargetHttpsProxy resource has no SSL policy
   * configured.
   *
   * @param string $sslPolicy
   */
  public function setSslPolicy($sslPolicy)
  {
    $this->sslPolicy = $sslPolicy;
  }
  /**
   * @return string
   */
  public function getSslPolicy()
  {
    return $this->sslPolicy;
  }
  /**
   * Specifies whether TLS 1.3 0-RTT Data ("Early Data") should be accepted for
   * this service. Early Data allows a TLS resumption handshake to include the
   * initial application payload (a HTTP request) alongside the handshake,
   * reducing the effective round trips to "zero". This applies to TLS 1.3
   * connections over TCP (HTTP/2) as well as over UDP (QUIC/h3).
   *
   * This can improve application performance, especially on networks where
   * interruptions may be common, such as on mobile.
   *
   * Requests with Early Data will have the "Early-Data" HTTP header set on the
   * request, with a value of "1", to allow the backend to determine whether
   * Early Data was included.
   *
   * Note: TLS Early Data may allow requests to be replayed, as the data is sent
   * to the backend before the handshake has fully completed. Applications that
   * allow idempotent HTTP methods to make non-idempotent changes, such as a GET
   * request updating a database, should not accept Early Data on those
   * requests, and reject requests with the "Early-Data: 1" HTTP header by
   * returning a HTTP 425 (Too Early) status code, in order to remain RFC
   * compliant.
   *
   * The default value is DISABLED.
   *
   * Accepted values: DISABLED, PERMISSIVE, STRICT, UNRESTRICTED
   *
   * @param self::TLS_EARLY_DATA_* $tlsEarlyData
   */
  public function setTlsEarlyData($tlsEarlyData)
  {
    $this->tlsEarlyData = $tlsEarlyData;
  }
  /**
   * @return self::TLS_EARLY_DATA_*
   */
  public function getTlsEarlyData()
  {
    return $this->tlsEarlyData;
  }
  /**
   * A fully-qualified or valid partial URL to the UrlMap resource that defines
   * the mapping from URL to the BackendService. For example, the following are
   * all valid URLs for specifying a URL map:        -
   * https://www.googleapis.compute/v1/projects/project/global/urlMaps/url-map
   * - projects/project/global/urlMaps/url-map     - global/urlMaps/url-map
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
class_alias(TargetHttpsProxy::class, 'Google_Service_Compute_TargetHttpsProxy');
