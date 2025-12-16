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

class BackendService extends \Google\Collection
{
  /**
   * Automatically uses the best compression based on the Accept-Encoding header
   * sent by the client.
   */
  public const COMPRESSION_MODE_AUTOMATIC = 'AUTOMATIC';
  /**
   * Disables compression. Existing compressed responses cached by Cloud CDN
   * will not be served to clients.
   */
  public const COMPRESSION_MODE_DISABLED = 'DISABLED';
  public const EXTERNAL_MANAGED_MIGRATION_STATE_PREPARE = 'PREPARE';
  public const EXTERNAL_MANAGED_MIGRATION_STATE_TEST_ALL_TRAFFIC = 'TEST_ALL_TRAFFIC';
  public const EXTERNAL_MANAGED_MIGRATION_STATE_TEST_BY_PERCENTAGE = 'TEST_BY_PERCENTAGE';
  /**
   * Only send IPv4 traffic to the backends of the Backend Service (Instance
   * Group, Managed Instance Group, Network Endpoint Group) regardless of
   * traffic from the client to the proxy. Only IPv4 health-checks are used to
   * check the health of the backends. This is the default setting.
   */
  public const IP_ADDRESS_SELECTION_POLICY_IPV4_ONLY = 'IPV4_ONLY';
  /**
   * Only send IPv6 traffic to the backends of the Backend Service (Instance
   * Group, Managed Instance Group, Network Endpoint Group) regardless of
   * traffic from the client to the proxy. Only IPv6 health-checks are used to
   * check the health of the backends.
   */
  public const IP_ADDRESS_SELECTION_POLICY_IPV6_ONLY = 'IPV6_ONLY';
  /**
   * Unspecified IP address selection policy.
   */
  public const IP_ADDRESS_SELECTION_POLICY_IP_ADDRESS_SELECTION_POLICY_UNSPECIFIED = 'IP_ADDRESS_SELECTION_POLICY_UNSPECIFIED';
  /**
   * Prioritize the connection to the endpoints IPv6 address over its IPv4
   * address (provided there is a healthy IPv6 address).
   */
  public const IP_ADDRESS_SELECTION_POLICY_PREFER_IPV6 = 'PREFER_IPV6';
  /**
   * Signifies that this will be used for classic Application Load Balancers,
   * global external proxy Network Load Balancers, or external passthrough
   * Network Load Balancers.
   */
  public const LOAD_BALANCING_SCHEME_EXTERNAL = 'EXTERNAL';
  /**
   * Signifies that this will be used for global external Application Load
   * Balancers, regional external Application Load Balancers, or regional
   * external proxy Network Load Balancers.
   */
  public const LOAD_BALANCING_SCHEME_EXTERNAL_MANAGED = 'EXTERNAL_MANAGED';
  /**
   * Signifies that this will be used for internal passthrough Network Load
   * Balancers.
   */
  public const LOAD_BALANCING_SCHEME_INTERNAL = 'INTERNAL';
  /**
   * Signifies that this will be used for internal Application Load Balancers.
   */
  public const LOAD_BALANCING_SCHEME_INTERNAL_MANAGED = 'INTERNAL_MANAGED';
  /**
   * Signifies that this will be used by Traffic Director.
   */
  public const LOAD_BALANCING_SCHEME_INTERNAL_SELF_MANAGED = 'INTERNAL_SELF_MANAGED';
  public const LOAD_BALANCING_SCHEME_INVALID_LOAD_BALANCING_SCHEME = 'INVALID_LOAD_BALANCING_SCHEME';
  public const LOCALITY_LB_POLICY_INVALID_LB_POLICY = 'INVALID_LB_POLICY';
  /**
   * An O(1) algorithm which selects two random healthy hosts and picks the host
   * which has fewer active requests.
   */
  public const LOCALITY_LB_POLICY_LEAST_REQUEST = 'LEAST_REQUEST';
  /**
   * This algorithm implements consistent hashing to backends. Maglev can be
   * used as a drop in replacement for the ring hash load balancer. Maglev is
   * not as stable as ring hash but has faster table lookup build times and host
   * selection times. For more information about Maglev, seeMaglev: A Fast and
   * Reliable Software Network Load Balancer.
   */
  public const LOCALITY_LB_POLICY_MAGLEV = 'MAGLEV';
  /**
   * Backend host is selected based on the client connection metadata, i.e.,
   * connections are opened to the same address as the destination address of
   * the incoming connection before the connection was redirected to the load
   * balancer.
   */
  public const LOCALITY_LB_POLICY_ORIGINAL_DESTINATION = 'ORIGINAL_DESTINATION';
  /**
   * The load balancer selects a random healthy host.
   */
  public const LOCALITY_LB_POLICY_RANDOM = 'RANDOM';
  /**
   * The ring/modulo hash load balancer implements consistent hashing to
   * backends. The algorithm has the property that the addition/removal of a
   * host from a set of N hosts only affects 1/N of the requests.
   */
  public const LOCALITY_LB_POLICY_RING_HASH = 'RING_HASH';
  /**
   * This is a simple policy in which each healthy backend is selected in round
   * robin order. This is the default.
   */
  public const LOCALITY_LB_POLICY_ROUND_ROBIN = 'ROUND_ROBIN';
  /**
   * Per-instance weighted Load Balancing via health check reported weights. In
   * internal passthrough network load balancing, it is weighted rendezvous
   * hashing. This option is only supported in internal passthrough network load
   * balancing.
   */
  public const LOCALITY_LB_POLICY_WEIGHTED_GCP_RENDEZVOUS = 'WEIGHTED_GCP_RENDEZVOUS';
  /**
   * Per-instance weighted Load Balancing via health check reported weights. If
   * set, the Backend Service must configure a non legacy HTTP-based Health
   * Check, and health check replies are expected to contain non-standard HTTP
   * response header field X-Load-Balancing-Endpoint-Weight to specify the per-
   * instance weights. If set, Load Balancing is weighted based on the per-
   * instance weights reported in the last processed health check replies, as
   * long as every instance either reported a valid weight or had
   * UNAVAILABLE_WEIGHT. Otherwise, Load Balancing remains equal-weight. This
   * option is only supported in Network Load Balancing.
   */
  public const LOCALITY_LB_POLICY_WEIGHTED_MAGLEV = 'WEIGHTED_MAGLEV';
  /**
   * Per-endpoint weighted round-robin Load Balancing using weights computed
   * from Backend reported Custom Metrics. If set, the Backend Service responses
   * are expected to contain non-standard HTTP response header field Endpoint-
   * Load-Metrics. The reported metrics to use for computing the weights are
   * specified via the customMetrics fields.
   */
  public const LOCALITY_LB_POLICY_WEIGHTED_ROUND_ROBIN = 'WEIGHTED_ROUND_ROBIN';
  /**
   * gRPC (available for Traffic Director).
   */
  public const PROTOCOL_GRPC = 'GRPC';
  /**
   * HTTP2 over cleartext
   */
  public const PROTOCOL_H2C = 'H2C';
  public const PROTOCOL_HTTP = 'HTTP';
  /**
   * HTTP/2 with SSL.
   */
  public const PROTOCOL_HTTP2 = 'HTTP2';
  public const PROTOCOL_HTTPS = 'HTTPS';
  /**
   * TCP proxying with SSL.
   */
  public const PROTOCOL_SSL = 'SSL';
  /**
   * TCP proxying or TCP pass-through.
   */
  public const PROTOCOL_TCP = 'TCP';
  /**
   * UDP.
   */
  public const PROTOCOL_UDP = 'UDP';
  /**
   * If a Backend Service has UNSPECIFIED as its protocol, it can be used with
   * any L3/L4 Forwarding Rules.
   */
  public const PROTOCOL_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * 2-tuple hash on packet's source and destination IP addresses. Connections
   * from the same source IP address to the same destination IP address will be
   * served by the same backend VM while that VM remains healthy.
   */
  public const SESSION_AFFINITY_CLIENT_IP = 'CLIENT_IP';
  /**
   * 1-tuple hash only on packet's source IP address. Connections from the same
   * source IP address will be served by the same backend VM while that VM
   * remains healthy. This option can only be used for Internal TCP/UDP Load
   * Balancing.
   */
  public const SESSION_AFFINITY_CLIENT_IP_NO_DESTINATION = 'CLIENT_IP_NO_DESTINATION';
  /**
   * 5-tuple hash on packet's source and destination IP addresses, IP protocol,
   * and source and destination ports. Connections for the same IP protocol from
   * the same source IP address and port to the same destination IP address and
   * port will be served by the same backend VM while that VM remains healthy.
   * This option cannot be used for HTTP(S) load balancing.
   */
  public const SESSION_AFFINITY_CLIENT_IP_PORT_PROTO = 'CLIENT_IP_PORT_PROTO';
  /**
   * 3-tuple hash on packet's source and destination IP addresses, and IP
   * protocol. Connections for the same IP protocol from the same source IP
   * address to the same destination IP address will be served by the same
   * backend VM while that VM remains healthy. This option cannot be used for
   * HTTP(S) load balancing.
   */
  public const SESSION_AFFINITY_CLIENT_IP_PROTO = 'CLIENT_IP_PROTO';
  /**
   * Hash based on a cookie generated by the L7 loadbalancer. Only valid for
   * HTTP(S) load balancing.
   */
  public const SESSION_AFFINITY_GENERATED_COOKIE = 'GENERATED_COOKIE';
  /**
   * The hash is based on a user specified header field.
   */
  public const SESSION_AFFINITY_HEADER_FIELD = 'HEADER_FIELD';
  /**
   * The hash is based on a user provided cookie.
   */
  public const SESSION_AFFINITY_HTTP_COOKIE = 'HTTP_COOKIE';
  /**
   * No session affinity. Connections from the same client IP may go to any
   * instance in the pool.
   */
  public const SESSION_AFFINITY_NONE = 'NONE';
  /**
   * Strong cookie-based affinity. Connections bearing the same cookie will be
   * served by the same backend VM while that VM remains healthy, as long as the
   * cookie has not expired.
   */
  public const SESSION_AFFINITY_STRONG_COOKIE_AFFINITY = 'STRONG_COOKIE_AFFINITY';
  protected $collection_key = 'usedBy';
  /**
   * Lifetime of cookies in seconds. This setting is applicable to Application
   * Load Balancers and Traffic Director and requires GENERATED_COOKIE or
   * HTTP_COOKIE session affinity.
   *
   * If set to 0, the cookie is non-persistent and lasts only until the end of
   * the browser session (or equivalent). The maximum allowed value is two weeks
   * (1,209,600).
   *
   * Not supported when the backend service is referenced by a URL map that is
   * bound to target gRPC proxy that has validateForProxyless field set to true.
   *
   * @var int
   */
  public $affinityCookieTtlSec;
  protected $backendsType = Backend::class;
  protected $backendsDataType = 'array';
  protected $cdnPolicyType = BackendServiceCdnPolicy::class;
  protected $cdnPolicyDataType = '';
  protected $circuitBreakersType = CircuitBreakers::class;
  protected $circuitBreakersDataType = '';
  /**
   * Compress text responses using Brotli or gzip compression, based on the
   * client's Accept-Encoding header.
   *
   * @var string
   */
  public $compressionMode;
  protected $connectionDrainingType = ConnectionDraining::class;
  protected $connectionDrainingDataType = '';
  protected $connectionTrackingPolicyType = BackendServiceConnectionTrackingPolicy::class;
  protected $connectionTrackingPolicyDataType = '';
  protected $consistentHashType = ConsistentHashLoadBalancerSettings::class;
  protected $consistentHashDataType = '';
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  protected $customMetricsType = BackendServiceCustomMetric::class;
  protected $customMetricsDataType = 'array';
  /**
   * Headers that the load balancer adds to proxied requests. See [Creating
   * custom headers](https://cloud.google.com/load-balancing/docs/custom-
   * headers).
   *
   * @var string[]
   */
  public $customRequestHeaders;
  /**
   * Headers that the load balancer adds to proxied responses. See [Creating
   * custom headers](https://cloud.google.com/load-balancing/docs/custom-
   * headers).
   *
   * @var string[]
   */
  public $customResponseHeaders;
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  /**
   * [Output Only] The resource URL for the edge security policy associated with
   * this backend service.
   *
   * @var string
   */
  public $edgeSecurityPolicy;
  /**
   * If true, enables Cloud CDN for the backend service of a global external
   * Application Load Balancer.
   *
   * @var bool
   */
  public $enableCDN;
  /**
   * Specifies the canary migration state. Possible values are PREPARE,
   * TEST_BY_PERCENTAGE, and TEST_ALL_TRAFFIC.
   *
   * To begin the migration from EXTERNAL to EXTERNAL_MANAGED, the state must be
   * changed to PREPARE. The state must be changed to TEST_ALL_TRAFFIC before
   * the loadBalancingScheme can be changed to EXTERNAL_MANAGED. Optionally, the
   * TEST_BY_PERCENTAGE state can be used to migrate traffic by percentage using
   * externalManagedMigrationTestingPercentage.
   *
   * Rolling back a migration requires the states to be set in reverse order. So
   * changing the scheme from EXTERNAL_MANAGED to EXTERNAL requires the state to
   * be set to TEST_ALL_TRAFFIC at the same time. Optionally, the
   * TEST_BY_PERCENTAGE state can be used to migrate some traffic back to
   * EXTERNAL or PREPARE can be used to migrate all traffic back to EXTERNAL.
   *
   * @var string
   */
  public $externalManagedMigrationState;
  /**
   * Determines the fraction of requests that should be processed by the Global
   * external Application Load Balancer.
   *
   * The value of this field must be in the range [0, 100].
   *
   * Session affinity options will slightly affect this routing behavior, for
   * more details, see:Session Affinity.
   *
   * This value can only be set if the loadBalancingScheme in the BackendService
   * is set to EXTERNAL (when using the classic Application Load Balancer) and
   * the migration state is TEST_BY_PERCENTAGE.
   *
   * @var float
   */
  public $externalManagedMigrationTestingPercentage;
  protected $failoverPolicyType = BackendServiceFailoverPolicy::class;
  protected $failoverPolicyDataType = '';
  /**
   * Fingerprint of this resource. A hash of the contents stored in this object.
   * This field is used in optimistic locking. This field will be ignored when
   * inserting a BackendService. An up-to-date fingerprint must be provided in
   * order to update the BackendService, otherwise the request will fail with
   * error 412 conditionNotMet.
   *
   * To see the latest fingerprint, make a get() request to retrieve a
   * BackendService.
   *
   * @var string
   */
  public $fingerprint;
  protected $haPolicyType = BackendServiceHAPolicy::class;
  protected $haPolicyDataType = '';
  /**
   * The list of URLs to the healthChecks, httpHealthChecks (legacy), or
   * httpsHealthChecks (legacy) resource for health checking this backend
   * service. Not all backend services support legacy health checks. See Load
   * balancer guide. Currently, at most one health check can be specified for
   * each backend service. Backend services with instance group or zonal NEG
   * backends must have a health check unless haPolicy is specified. Backend
   * services with internet or serverless NEG backends must not have a health
   * check.
   *
   * healthChecks[] cannot be specified with haPolicy.
   *
   * @var string[]
   */
  public $healthChecks;
  protected $iapType = BackendServiceIAP::class;
  protected $iapDataType = '';
  /**
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Specifies a preference for traffic sent from the proxy to the backend (or
   * from the client to the backend for proxyless gRPC). The possible values
   * are:        - IPV4_ONLY: Only send IPv4 traffic to the backends of the
   * backend service (Instance Group, Managed Instance Group, Network Endpoint
   * Group), regardless of traffic from the client to the proxy. Only IPv4
   * health checks are used to check the health of the backends. This is the
   * default setting.    - PREFER_IPV6: Prioritize the connection to the
   * endpoint's    IPv6 address over its IPv4 address (provided there is a
   * healthy IPv6    address).    - IPV6_ONLY: Only send IPv6 traffic to the
   * backends of the    backend service (Instance Group, Managed Instance Group,
   * Network Endpoint    Group), regardless of traffic from the client to the
   * proxy. Only IPv6    health checks are used to check the health of the
   * backends.
   *
   * This field is applicable to either:        -  Advanced global external
   * Application Load Balancer (load balancing    scheme EXTERNAL_MANAGED),
   * -  Regional external Application Load    Balancer,     -  Internal proxy
   * Network Load Balancer (load balancing    scheme INTERNAL_MANAGED),     -
   * Regional internal Application Load    Balancer (load balancing scheme
   * INTERNAL_MANAGED),     -  Traffic    Director with Envoy proxies and
   * proxyless gRPC (load balancing scheme    INTERNAL_SELF_MANAGED).
   *
   * @var string
   */
  public $ipAddressSelectionPolicy;
  /**
   * Output only. [Output Only] Type of resource. Always compute#backendService
   * for backend services.
   *
   * @var string
   */
  public $kind;
  /**
   * Specifies the load balancer type. A backend service created for one type of
   * load balancer cannot be used with another. For more information, refer
   * toChoosing a load balancer.
   *
   * @var string
   */
  public $loadBalancingScheme;
  protected $localityLbPoliciesType = BackendServiceLocalityLoadBalancingPolicyConfig::class;
  protected $localityLbPoliciesDataType = 'array';
  /**
   * The load balancing algorithm used within the scope of the locality. The
   * possible values are:        - ROUND_ROBIN: This is a simple policy in which
   * each healthy    backend is selected in round robin order. This is the
   * default.    - LEAST_REQUEST: An O(1) algorithm which    selects two random
   * healthy hosts and picks the host which has fewer active    requests.    -
   * RING_HASH: The ring/modulo hash load balancer implements    consistent
   * hashing to backends. The algorithm has the property that the
   * addition/removal of a host from a set of N hosts only affects 1/N of the
   * requests.    - RANDOM: The load balancer selects a random healthy    host.
   * - ORIGINAL_DESTINATION: Backend host is selected    based on the client
   * connection metadata, i.e., connections are opened to    the same address as
   * the destination address of the incoming connection    before the connection
   * was redirected to the load balancer.    - MAGLEV: used as a drop in
   * replacement for the ring hash    load balancer. Maglev is not as stable as
   * ring hash but has faster table    lookup build times and host selection
   * times. For more information about    Maglev, see Maglev:    A Fast and
   * Reliable Software Network Load Balancer.    - WEIGHTED_ROUND_ROBIN: Per-
   * endpoint Weighted Round Robin    Load Balancing using weights computed from
   * Backend reported Custom Metrics.    If set, the Backend Service responses
   * are expected to contain non-standard    HTTP response header field
   * Endpoint-Load-Metrics. The reported    metrics to use for computing the
   * weights are specified via thecustomMetrics field.        This field is
   * applicable to either:       - A regional backend service with the
   * service_protocol set to HTTP,       HTTPS, HTTP2 or H2C, and
   * load_balancing_scheme set to       INTERNAL_MANAGED.        - A global
   * backend service with the       load_balancing_scheme set to
   * INTERNAL_SELF_MANAGED, INTERNAL_MANAGED, or       EXTERNAL_MANAGED.
   * If sessionAffinity is not configured—that is, if session    affinity
   * remains at the default value of NONE—then the    default value for
   * localityLbPolicy    is ROUND_ROBIN. If session affinity is set to a value
   * other    than NONE,    then the default value for localityLbPolicy
   * isMAGLEV.        Only ROUND_ROBIN and RING_HASH are supported    when the
   * backend service is referenced by a URL map that is bound to    target gRPC
   * proxy that has validateForProxyless field set to true.
   * localityLbPolicy cannot be specified with haPolicy.
   *
   * @var string
   */
  public $localityLbPolicy;
  protected $logConfigType = BackendServiceLogConfig::class;
  protected $logConfigDataType = '';
  protected $maxStreamDurationType = Duration::class;
  protected $maxStreamDurationDataType = '';
  /**
   * Deployment metadata associated with the resource to be set by a GKE hub
   * controller and read by the backend RCTH
   *
   * @var string[]
   */
  public $metadatas;
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
   * The URL of the network to which this backend service belongs.
   *
   * This field must be set for Internal Passthrough Network Load Balancers when
   * the haPolicy is enabled, and for External Passthrough Network Load
   * Balancers when the haPolicy fastIpMove is enabled.
   *
   * This field can only be specified when the load balancing scheme is set
   * toINTERNAL, or when the load balancing scheme is set toEXTERNAL and
   * haPolicy fastIpMove is enabled.
   *
   * @var string
   */
  public $network;
  protected $outlierDetectionType = OutlierDetection::class;
  protected $outlierDetectionDataType = '';
  protected $paramsType = BackendServiceParams::class;
  protected $paramsDataType = '';
  /**
   * Deprecated in favor of portName. The TCP port to connect on the backend.
   * The default value is 80. For internal passthrough Network Load Balancers
   * and external passthrough Network Load Balancers, omit port.
   *
   * @deprecated
   * @var int
   */
  public $port;
  /**
   * A named port on a backend instance group representing the port for
   * communication to the backend VMs in that group. The named port must be
   * [defined on each backend instance group](https://cloud.google.com/load-
   * balancing/docs/backend-service#named_ports). This parameter has no meaning
   * if the backends are NEGs. For internal passthrough Network Load Balancers
   * and external passthrough Network Load Balancers, omit port_name.
   *
   * @var string
   */
  public $portName;
  /**
   * The protocol this BackendService uses to communicate with backends.
   *
   * Possible values are HTTP, HTTPS, HTTP2, H2C, TCP, SSL, UDP or GRPC.
   * depending on the chosen load balancer or Traffic Director configuration.
   * Refer to the documentation for the load balancers or for Traffic Director
   * for more information.
   *
   * Must be set to GRPC when the backend service is referenced by a URL map
   * that is bound to target gRPC proxy.
   *
   * @var string
   */
  public $protocol;
  /**
   * Output only. [Output Only] URL of the region where the regional backend
   * service resides. This field is not applicable to global backend services.
   * You must specify this field as part of the HTTP request URL. It is not
   * settable as a field in the request body.
   *
   * @var string
   */
  public $region;
  /**
   * [Output Only] The resource URL for the security policy associated with this
   * backend service.
   *
   * @var string
   */
  public $securityPolicy;
  protected $securitySettingsType = SecuritySettings::class;
  protected $securitySettingsDataType = '';
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * URLs of networkservices.ServiceBinding resources.
   *
   * Can only be set if load balancing scheme is INTERNAL_SELF_MANAGED. If set,
   * lists of backends and health checks must be both empty.
   *
   * @var string[]
   */
  public $serviceBindings;
  /**
   * URL to networkservices.ServiceLbPolicy resource.
   *
   * Can only be set if load balancing scheme is EXTERNAL_MANAGED,
   * INTERNAL_MANAGED or INTERNAL_SELF_MANAGED and the scope is global.
   *
   * @var string
   */
  public $serviceLbPolicy;
  /**
   * Type of session affinity to use. The default is NONE.
   *
   * Only NONE and HEADER_FIELD are supported when the backend service is
   * referenced by a URL map that is bound to target gRPC proxy that has
   * validateForProxyless field set to true.
   *
   * For more details, see: [Session Affinity](https://cloud.google.com/load-
   * balancing/docs/backend-service#session_affinity).
   *
   * sessionAffinity cannot be specified with haPolicy.
   *
   * @var string
   */
  public $sessionAffinity;
  protected $strongSessionAffinityCookieType = BackendServiceHttpCookie::class;
  protected $strongSessionAffinityCookieDataType = '';
  protected $subsettingType = Subsetting::class;
  protected $subsettingDataType = '';
  /**
   * The backend service timeout has a different meaning depending on the type
   * of load balancer. For more information see, Backend service settings. The
   * default is 30 seconds. The full range of timeout values allowed goes from 1
   * through 2,147,483,647 seconds.
   *
   * This value can be overridden in the PathMatcher configuration of the UrlMap
   * that references this backend service.
   *
   * Not supported when the backend service is referenced by a URL map that is
   * bound to target gRPC proxy that has validateForProxyless field set to true.
   * Instead, use maxStreamDuration.
   *
   * @var int
   */
  public $timeoutSec;
  protected $tlsSettingsType = BackendServiceTlsSettings::class;
  protected $tlsSettingsDataType = '';
  protected $usedByType = BackendServiceUsedBy::class;
  protected $usedByDataType = 'array';

  /**
   * Lifetime of cookies in seconds. This setting is applicable to Application
   * Load Balancers and Traffic Director and requires GENERATED_COOKIE or
   * HTTP_COOKIE session affinity.
   *
   * If set to 0, the cookie is non-persistent and lasts only until the end of
   * the browser session (or equivalent). The maximum allowed value is two weeks
   * (1,209,600).
   *
   * Not supported when the backend service is referenced by a URL map that is
   * bound to target gRPC proxy that has validateForProxyless field set to true.
   *
   * @param int $affinityCookieTtlSec
   */
  public function setAffinityCookieTtlSec($affinityCookieTtlSec)
  {
    $this->affinityCookieTtlSec = $affinityCookieTtlSec;
  }
  /**
   * @return int
   */
  public function getAffinityCookieTtlSec()
  {
    return $this->affinityCookieTtlSec;
  }
  /**
   * The list of backends that serve this BackendService.
   *
   * @param Backend[] $backends
   */
  public function setBackends($backends)
  {
    $this->backends = $backends;
  }
  /**
   * @return Backend[]
   */
  public function getBackends()
  {
    return $this->backends;
  }
  /**
   * Cloud CDN configuration for this BackendService. Only available for
   * specified load balancer types.
   *
   * @param BackendServiceCdnPolicy $cdnPolicy
   */
  public function setCdnPolicy(BackendServiceCdnPolicy $cdnPolicy)
  {
    $this->cdnPolicy = $cdnPolicy;
  }
  /**
   * @return BackendServiceCdnPolicy
   */
  public function getCdnPolicy()
  {
    return $this->cdnPolicy;
  }
  /**
   * @param CircuitBreakers $circuitBreakers
   */
  public function setCircuitBreakers(CircuitBreakers $circuitBreakers)
  {
    $this->circuitBreakers = $circuitBreakers;
  }
  /**
   * @return CircuitBreakers
   */
  public function getCircuitBreakers()
  {
    return $this->circuitBreakers;
  }
  /**
   * Compress text responses using Brotli or gzip compression, based on the
   * client's Accept-Encoding header.
   *
   * Accepted values: AUTOMATIC, DISABLED
   *
   * @param self::COMPRESSION_MODE_* $compressionMode
   */
  public function setCompressionMode($compressionMode)
  {
    $this->compressionMode = $compressionMode;
  }
  /**
   * @return self::COMPRESSION_MODE_*
   */
  public function getCompressionMode()
  {
    return $this->compressionMode;
  }
  /**
   * connectionDraining cannot be specified with haPolicy.
   *
   * @param ConnectionDraining $connectionDraining
   */
  public function setConnectionDraining(ConnectionDraining $connectionDraining)
  {
    $this->connectionDraining = $connectionDraining;
  }
  /**
   * @return ConnectionDraining
   */
  public function getConnectionDraining()
  {
    return $this->connectionDraining;
  }
  /**
   * Connection Tracking configuration for this BackendService. Connection
   * tracking policy settings are only available for external passthrough
   * Network Load Balancers and internal passthrough Network Load Balancers.
   *
   * connectionTrackingPolicy cannot be specified with haPolicy.
   *
   * @param BackendServiceConnectionTrackingPolicy $connectionTrackingPolicy
   */
  public function setConnectionTrackingPolicy(BackendServiceConnectionTrackingPolicy $connectionTrackingPolicy)
  {
    $this->connectionTrackingPolicy = $connectionTrackingPolicy;
  }
  /**
   * @return BackendServiceConnectionTrackingPolicy
   */
  public function getConnectionTrackingPolicy()
  {
    return $this->connectionTrackingPolicy;
  }
  /**
   * Consistent Hash-based load balancing can be used to provide soft session
   * affinity based on HTTP headers, cookies or other properties. This load
   * balancing policy is applicable only for HTTP connections. The affinity to a
   * particular destination host will be lost when one or more hosts are
   * added/removed from the destination service. This field specifies parameters
   * that control consistent hashing. This field is only applicable
   * whenlocalityLbPolicy is set to MAGLEV orRING_HASH.
   *
   * This field is applicable to either:        - A regional backend service
   * with the service_protocol set to HTTP,    HTTPS, HTTP2 or H2C, and
   * load_balancing_scheme set to    INTERNAL_MANAGED.     - A global backend
   * service with the    load_balancing_scheme set to INTERNAL_SELF_MANAGED.
   *
   * @param ConsistentHashLoadBalancerSettings $consistentHash
   */
  public function setConsistentHash(ConsistentHashLoadBalancerSettings $consistentHash)
  {
    $this->consistentHash = $consistentHash;
  }
  /**
   * @return ConsistentHashLoadBalancerSettings
   */
  public function getConsistentHash()
  {
    return $this->consistentHash;
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
   * List of custom metrics that are used for theWEIGHTED_ROUND_ROBIN
   * locality_lb_policy.
   *
   * @param BackendServiceCustomMetric[] $customMetrics
   */
  public function setCustomMetrics($customMetrics)
  {
    $this->customMetrics = $customMetrics;
  }
  /**
   * @return BackendServiceCustomMetric[]
   */
  public function getCustomMetrics()
  {
    return $this->customMetrics;
  }
  /**
   * Headers that the load balancer adds to proxied requests. See [Creating
   * custom headers](https://cloud.google.com/load-balancing/docs/custom-
   * headers).
   *
   * @param string[] $customRequestHeaders
   */
  public function setCustomRequestHeaders($customRequestHeaders)
  {
    $this->customRequestHeaders = $customRequestHeaders;
  }
  /**
   * @return string[]
   */
  public function getCustomRequestHeaders()
  {
    return $this->customRequestHeaders;
  }
  /**
   * Headers that the load balancer adds to proxied responses. See [Creating
   * custom headers](https://cloud.google.com/load-balancing/docs/custom-
   * headers).
   *
   * @param string[] $customResponseHeaders
   */
  public function setCustomResponseHeaders($customResponseHeaders)
  {
    $this->customResponseHeaders = $customResponseHeaders;
  }
  /**
   * @return string[]
   */
  public function getCustomResponseHeaders()
  {
    return $this->customResponseHeaders;
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
   * [Output Only] The resource URL for the edge security policy associated with
   * this backend service.
   *
   * @param string $edgeSecurityPolicy
   */
  public function setEdgeSecurityPolicy($edgeSecurityPolicy)
  {
    $this->edgeSecurityPolicy = $edgeSecurityPolicy;
  }
  /**
   * @return string
   */
  public function getEdgeSecurityPolicy()
  {
    return $this->edgeSecurityPolicy;
  }
  /**
   * If true, enables Cloud CDN for the backend service of a global external
   * Application Load Balancer.
   *
   * @param bool $enableCDN
   */
  public function setEnableCDN($enableCDN)
  {
    $this->enableCDN = $enableCDN;
  }
  /**
   * @return bool
   */
  public function getEnableCDN()
  {
    return $this->enableCDN;
  }
  /**
   * Specifies the canary migration state. Possible values are PREPARE,
   * TEST_BY_PERCENTAGE, and TEST_ALL_TRAFFIC.
   *
   * To begin the migration from EXTERNAL to EXTERNAL_MANAGED, the state must be
   * changed to PREPARE. The state must be changed to TEST_ALL_TRAFFIC before
   * the loadBalancingScheme can be changed to EXTERNAL_MANAGED. Optionally, the
   * TEST_BY_PERCENTAGE state can be used to migrate traffic by percentage using
   * externalManagedMigrationTestingPercentage.
   *
   * Rolling back a migration requires the states to be set in reverse order. So
   * changing the scheme from EXTERNAL_MANAGED to EXTERNAL requires the state to
   * be set to TEST_ALL_TRAFFIC at the same time. Optionally, the
   * TEST_BY_PERCENTAGE state can be used to migrate some traffic back to
   * EXTERNAL or PREPARE can be used to migrate all traffic back to EXTERNAL.
   *
   * Accepted values: PREPARE, TEST_ALL_TRAFFIC, TEST_BY_PERCENTAGE
   *
   * @param self::EXTERNAL_MANAGED_MIGRATION_STATE_* $externalManagedMigrationState
   */
  public function setExternalManagedMigrationState($externalManagedMigrationState)
  {
    $this->externalManagedMigrationState = $externalManagedMigrationState;
  }
  /**
   * @return self::EXTERNAL_MANAGED_MIGRATION_STATE_*
   */
  public function getExternalManagedMigrationState()
  {
    return $this->externalManagedMigrationState;
  }
  /**
   * Determines the fraction of requests that should be processed by the Global
   * external Application Load Balancer.
   *
   * The value of this field must be in the range [0, 100].
   *
   * Session affinity options will slightly affect this routing behavior, for
   * more details, see:Session Affinity.
   *
   * This value can only be set if the loadBalancingScheme in the BackendService
   * is set to EXTERNAL (when using the classic Application Load Balancer) and
   * the migration state is TEST_BY_PERCENTAGE.
   *
   * @param float $externalManagedMigrationTestingPercentage
   */
  public function setExternalManagedMigrationTestingPercentage($externalManagedMigrationTestingPercentage)
  {
    $this->externalManagedMigrationTestingPercentage = $externalManagedMigrationTestingPercentage;
  }
  /**
   * @return float
   */
  public function getExternalManagedMigrationTestingPercentage()
  {
    return $this->externalManagedMigrationTestingPercentage;
  }
  /**
   * Requires at least one backend instance group to be defined as a backup
   * (failover) backend. For load balancers that have configurable failover:
   * [Internal passthrough Network Load
   * Balancers](https://cloud.google.com/load-balancing/docs/internal/failover-
   * overview) and [external passthrough Network Load
   * Balancers](https://cloud.google.com/load-balancing/docs/network/networklb-
   * failover-overview).
   *
   * failoverPolicy cannot be specified with haPolicy.
   *
   * @param BackendServiceFailoverPolicy $failoverPolicy
   */
  public function setFailoverPolicy(BackendServiceFailoverPolicy $failoverPolicy)
  {
    $this->failoverPolicy = $failoverPolicy;
  }
  /**
   * @return BackendServiceFailoverPolicy
   */
  public function getFailoverPolicy()
  {
    return $this->failoverPolicy;
  }
  /**
   * Fingerprint of this resource. A hash of the contents stored in this object.
   * This field is used in optimistic locking. This field will be ignored when
   * inserting a BackendService. An up-to-date fingerprint must be provided in
   * order to update the BackendService, otherwise the request will fail with
   * error 412 conditionNotMet.
   *
   * To see the latest fingerprint, make a get() request to retrieve a
   * BackendService.
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
   * Configures self-managed High Availability (HA) for External and Internal
   * Protocol Forwarding.
   *
   * The backends of this regional backend service must only specify zonal
   * network endpoint groups (NEGs) of type GCE_VM_IP.
   *
   * When haPolicy is set for an Internal Passthrough Network Load Balancer, the
   * regional backend service must set the network field. All zonal NEGs must
   * belong to the same network. However, individual NEGs can belong to
   * different subnetworks of that network.
   *
   * When haPolicy is specified, the set of attached network endpoints across
   * all backends comprise an High Availability domain from which one endpoint
   * is selected as the active endpoint (the leader) that receives all traffic.
   *
   * haPolicy can be added only at backend service creation time. Once set up,
   * it cannot be deleted.
   *
   * Note that haPolicy is not for load balancing, and therefore cannot be
   * specified with sessionAffinity, connectionTrackingPolicy, and
   * failoverPolicy.
   *
   * haPolicy requires customers to be responsible for tracking backend endpoint
   * health and electing a leader among the healthy endpoints. Therefore,
   * haPolicy cannot be specified with healthChecks.
   *
   * haPolicy can only be specified for External Passthrough Network Load
   * Balancers and Internal Passthrough Network Load Balancers.
   *
   * @param BackendServiceHAPolicy $haPolicy
   */
  public function setHaPolicy(BackendServiceHAPolicy $haPolicy)
  {
    $this->haPolicy = $haPolicy;
  }
  /**
   * @return BackendServiceHAPolicy
   */
  public function getHaPolicy()
  {
    return $this->haPolicy;
  }
  /**
   * The list of URLs to the healthChecks, httpHealthChecks (legacy), or
   * httpsHealthChecks (legacy) resource for health checking this backend
   * service. Not all backend services support legacy health checks. See Load
   * balancer guide. Currently, at most one health check can be specified for
   * each backend service. Backend services with instance group or zonal NEG
   * backends must have a health check unless haPolicy is specified. Backend
   * services with internet or serverless NEG backends must not have a health
   * check.
   *
   * healthChecks[] cannot be specified with haPolicy.
   *
   * @param string[] $healthChecks
   */
  public function setHealthChecks($healthChecks)
  {
    $this->healthChecks = $healthChecks;
  }
  /**
   * @return string[]
   */
  public function getHealthChecks()
  {
    return $this->healthChecks;
  }
  /**
   * The configurations for Identity-Aware Proxy on this resource. Not available
   * for internal passthrough Network Load Balancers and external passthrough
   * Network Load Balancers.
   *
   * @param BackendServiceIAP $iap
   */
  public function setIap(BackendServiceIAP $iap)
  {
    $this->iap = $iap;
  }
  /**
   * @return BackendServiceIAP
   */
  public function getIap()
  {
    return $this->iap;
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
   * Specifies a preference for traffic sent from the proxy to the backend (or
   * from the client to the backend for proxyless gRPC). The possible values
   * are:        - IPV4_ONLY: Only send IPv4 traffic to the backends of the
   * backend service (Instance Group, Managed Instance Group, Network Endpoint
   * Group), regardless of traffic from the client to the proxy. Only IPv4
   * health checks are used to check the health of the backends. This is the
   * default setting.    - PREFER_IPV6: Prioritize the connection to the
   * endpoint's    IPv6 address over its IPv4 address (provided there is a
   * healthy IPv6    address).    - IPV6_ONLY: Only send IPv6 traffic to the
   * backends of the    backend service (Instance Group, Managed Instance Group,
   * Network Endpoint    Group), regardless of traffic from the client to the
   * proxy. Only IPv6    health checks are used to check the health of the
   * backends.
   *
   * This field is applicable to either:        -  Advanced global external
   * Application Load Balancer (load balancing    scheme EXTERNAL_MANAGED),
   * -  Regional external Application Load    Balancer,     -  Internal proxy
   * Network Load Balancer (load balancing    scheme INTERNAL_MANAGED),     -
   * Regional internal Application Load    Balancer (load balancing scheme
   * INTERNAL_MANAGED),     -  Traffic    Director with Envoy proxies and
   * proxyless gRPC (load balancing scheme    INTERNAL_SELF_MANAGED).
   *
   * Accepted values: IPV4_ONLY, IPV6_ONLY,
   * IP_ADDRESS_SELECTION_POLICY_UNSPECIFIED, PREFER_IPV6
   *
   * @param self::IP_ADDRESS_SELECTION_POLICY_* $ipAddressSelectionPolicy
   */
  public function setIpAddressSelectionPolicy($ipAddressSelectionPolicy)
  {
    $this->ipAddressSelectionPolicy = $ipAddressSelectionPolicy;
  }
  /**
   * @return self::IP_ADDRESS_SELECTION_POLICY_*
   */
  public function getIpAddressSelectionPolicy()
  {
    return $this->ipAddressSelectionPolicy;
  }
  /**
   * Output only. [Output Only] Type of resource. Always compute#backendService
   * for backend services.
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
   * Specifies the load balancer type. A backend service created for one type of
   * load balancer cannot be used with another. For more information, refer
   * toChoosing a load balancer.
   *
   * Accepted values: EXTERNAL, EXTERNAL_MANAGED, INTERNAL, INTERNAL_MANAGED,
   * INTERNAL_SELF_MANAGED, INVALID_LOAD_BALANCING_SCHEME
   *
   * @param self::LOAD_BALANCING_SCHEME_* $loadBalancingScheme
   */
  public function setLoadBalancingScheme($loadBalancingScheme)
  {
    $this->loadBalancingScheme = $loadBalancingScheme;
  }
  /**
   * @return self::LOAD_BALANCING_SCHEME_*
   */
  public function getLoadBalancingScheme()
  {
    return $this->loadBalancingScheme;
  }
  /**
   * A list of locality load-balancing policies to be used in order of
   * preference. When you use localityLbPolicies, you must set at least one
   * value for either the localityLbPolicies[].policy or the
   * localityLbPolicies[].customPolicy field. localityLbPolicies overrides any
   * value set in the localityLbPolicy field.
   *
   * For an example of how to use this field, seeDefine a list of preferred
   * policies.
   *
   * Caution: This field and its children are intended for use in a service mesh
   * that includes gRPC clients only. Envoy proxies can't use backend services
   * that have this configuration.
   *
   * @param BackendServiceLocalityLoadBalancingPolicyConfig[] $localityLbPolicies
   */
  public function setLocalityLbPolicies($localityLbPolicies)
  {
    $this->localityLbPolicies = $localityLbPolicies;
  }
  /**
   * @return BackendServiceLocalityLoadBalancingPolicyConfig[]
   */
  public function getLocalityLbPolicies()
  {
    return $this->localityLbPolicies;
  }
  /**
   * The load balancing algorithm used within the scope of the locality. The
   * possible values are:        - ROUND_ROBIN: This is a simple policy in which
   * each healthy    backend is selected in round robin order. This is the
   * default.    - LEAST_REQUEST: An O(1) algorithm which    selects two random
   * healthy hosts and picks the host which has fewer active    requests.    -
   * RING_HASH: The ring/modulo hash load balancer implements    consistent
   * hashing to backends. The algorithm has the property that the
   * addition/removal of a host from a set of N hosts only affects 1/N of the
   * requests.    - RANDOM: The load balancer selects a random healthy    host.
   * - ORIGINAL_DESTINATION: Backend host is selected    based on the client
   * connection metadata, i.e., connections are opened to    the same address as
   * the destination address of the incoming connection    before the connection
   * was redirected to the load balancer.    - MAGLEV: used as a drop in
   * replacement for the ring hash    load balancer. Maglev is not as stable as
   * ring hash but has faster table    lookup build times and host selection
   * times. For more information about    Maglev, see Maglev:    A Fast and
   * Reliable Software Network Load Balancer.    - WEIGHTED_ROUND_ROBIN: Per-
   * endpoint Weighted Round Robin    Load Balancing using weights computed from
   * Backend reported Custom Metrics.    If set, the Backend Service responses
   * are expected to contain non-standard    HTTP response header field
   * Endpoint-Load-Metrics. The reported    metrics to use for computing the
   * weights are specified via thecustomMetrics field.        This field is
   * applicable to either:       - A regional backend service with the
   * service_protocol set to HTTP,       HTTPS, HTTP2 or H2C, and
   * load_balancing_scheme set to       INTERNAL_MANAGED.        - A global
   * backend service with the       load_balancing_scheme set to
   * INTERNAL_SELF_MANAGED, INTERNAL_MANAGED, or       EXTERNAL_MANAGED.
   * If sessionAffinity is not configured—that is, if session    affinity
   * remains at the default value of NONE—then the    default value for
   * localityLbPolicy    is ROUND_ROBIN. If session affinity is set to a value
   * other    than NONE,    then the default value for localityLbPolicy
   * isMAGLEV.        Only ROUND_ROBIN and RING_HASH are supported    when the
   * backend service is referenced by a URL map that is bound to    target gRPC
   * proxy that has validateForProxyless field set to true.
   * localityLbPolicy cannot be specified with haPolicy.
   *
   * Accepted values: INVALID_LB_POLICY, LEAST_REQUEST, MAGLEV,
   * ORIGINAL_DESTINATION, RANDOM, RING_HASH, ROUND_ROBIN,
   * WEIGHTED_GCP_RENDEZVOUS, WEIGHTED_MAGLEV, WEIGHTED_ROUND_ROBIN
   *
   * @param self::LOCALITY_LB_POLICY_* $localityLbPolicy
   */
  public function setLocalityLbPolicy($localityLbPolicy)
  {
    $this->localityLbPolicy = $localityLbPolicy;
  }
  /**
   * @return self::LOCALITY_LB_POLICY_*
   */
  public function getLocalityLbPolicy()
  {
    return $this->localityLbPolicy;
  }
  /**
   * This field denotes the logging options for the load balancer traffic served
   * by this backend service. If logging is enabled, logs will be exported to
   * Stackdriver.
   *
   * @param BackendServiceLogConfig $logConfig
   */
  public function setLogConfig(BackendServiceLogConfig $logConfig)
  {
    $this->logConfig = $logConfig;
  }
  /**
   * @return BackendServiceLogConfig
   */
  public function getLogConfig()
  {
    return $this->logConfig;
  }
  /**
   * Specifies the default maximum duration (timeout) for streams to this
   * service. Duration is computed from the beginning of the stream until the
   * response has been completely processed, including all retries. A stream
   * that does not complete in this duration is closed.
   *
   * If not specified, there will be no timeout limit, i.e. the maximum duration
   * is infinite.
   *
   * This value can be overridden in the PathMatcher configuration of the UrlMap
   * that references this backend service.
   *
   * This field is only allowed when the loadBalancingScheme of the backend
   * service is INTERNAL_SELF_MANAGED.
   *
   * @param Duration $maxStreamDuration
   */
  public function setMaxStreamDuration(Duration $maxStreamDuration)
  {
    $this->maxStreamDuration = $maxStreamDuration;
  }
  /**
   * @return Duration
   */
  public function getMaxStreamDuration()
  {
    return $this->maxStreamDuration;
  }
  /**
   * Deployment metadata associated with the resource to be set by a GKE hub
   * controller and read by the backend RCTH
   *
   * @param string[] $metadatas
   */
  public function setMetadatas($metadatas)
  {
    $this->metadatas = $metadatas;
  }
  /**
   * @return string[]
   */
  public function getMetadatas()
  {
    return $this->metadatas;
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
   * The URL of the network to which this backend service belongs.
   *
   * This field must be set for Internal Passthrough Network Load Balancers when
   * the haPolicy is enabled, and for External Passthrough Network Load
   * Balancers when the haPolicy fastIpMove is enabled.
   *
   * This field can only be specified when the load balancing scheme is set
   * toINTERNAL, or when the load balancing scheme is set toEXTERNAL and
   * haPolicy fastIpMove is enabled.
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
   * Settings controlling the ejection of unhealthy backend endpoints from the
   * load balancing pool of each individual proxy instance that processes the
   * traffic for the given backend service. If not set, this feature is
   * considered disabled.
   *
   * Results of the outlier detection algorithm (ejection of endpoints from the
   * load balancing pool and returning them back to the pool) are executed
   * independently by each proxy instance of the load balancer. In most cases,
   * more than one proxy instance handles the traffic received by a backend
   * service. Thus, it is possible that an unhealthy endpoint is detected and
   * ejected by only some of the proxies, and while this happens, other proxies
   * may continue to send requests to the same unhealthy endpoint until they
   * detect and eject the unhealthy endpoint.
   *
   * Applicable backend endpoints can be:        - VM instances in an Instance
   * Group    - Endpoints in a Zonal NEG (GCE_VM_IP, GCE_VM_IP_PORT)    -
   * Endpoints in a Hybrid Connectivity NEG (NON_GCP_PRIVATE_IP_PORT)    -
   * Serverless NEGs, that resolve to Cloud Run, App Engine, or Cloud
   * Functions Services     - Private Service Connect NEGs, that resolve to
   * Google-managed regional API endpoints or managed services published using
   * Private Service Connect
   *
   * Applicable backend service types can be:        - A global backend service
   * with the loadBalancingScheme set to    INTERNAL_SELF_MANAGED or
   * EXTERNAL_MANAGED.     - A regional backend    service with the
   * serviceProtocol set to HTTP, HTTPS, HTTP2 or H2C, and
   * loadBalancingScheme set to INTERNAL_MANAGED or EXTERNAL_MANAGED. Not
   * supported for Serverless NEGs.
   *
   * Not supported when the backend service is referenced by a URL map that is
   * bound to target gRPC proxy that has validateForProxyless field set to true.
   *
   * @param OutlierDetection $outlierDetection
   */
  public function setOutlierDetection(OutlierDetection $outlierDetection)
  {
    $this->outlierDetection = $outlierDetection;
  }
  /**
   * @return OutlierDetection
   */
  public function getOutlierDetection()
  {
    return $this->outlierDetection;
  }
  /**
   * Input only. [Input Only] Additional params passed with the request, but not
   * persisted as part of resource payload.
   *
   * @param BackendServiceParams $params
   */
  public function setParams(BackendServiceParams $params)
  {
    $this->params = $params;
  }
  /**
   * @return BackendServiceParams
   */
  public function getParams()
  {
    return $this->params;
  }
  /**
   * Deprecated in favor of portName. The TCP port to connect on the backend.
   * The default value is 80. For internal passthrough Network Load Balancers
   * and external passthrough Network Load Balancers, omit port.
   *
   * @deprecated
   * @param int $port
   */
  public function setPort($port)
  {
    $this->port = $port;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getPort()
  {
    return $this->port;
  }
  /**
   * A named port on a backend instance group representing the port for
   * communication to the backend VMs in that group. The named port must be
   * [defined on each backend instance group](https://cloud.google.com/load-
   * balancing/docs/backend-service#named_ports). This parameter has no meaning
   * if the backends are NEGs. For internal passthrough Network Load Balancers
   * and external passthrough Network Load Balancers, omit port_name.
   *
   * @param string $portName
   */
  public function setPortName($portName)
  {
    $this->portName = $portName;
  }
  /**
   * @return string
   */
  public function getPortName()
  {
    return $this->portName;
  }
  /**
   * The protocol this BackendService uses to communicate with backends.
   *
   * Possible values are HTTP, HTTPS, HTTP2, H2C, TCP, SSL, UDP or GRPC.
   * depending on the chosen load balancer or Traffic Director configuration.
   * Refer to the documentation for the load balancers or for Traffic Director
   * for more information.
   *
   * Must be set to GRPC when the backend service is referenced by a URL map
   * that is bound to target gRPC proxy.
   *
   * Accepted values: GRPC, H2C, HTTP, HTTP2, HTTPS, SSL, TCP, UDP, UNSPECIFIED
   *
   * @param self::PROTOCOL_* $protocol
   */
  public function setProtocol($protocol)
  {
    $this->protocol = $protocol;
  }
  /**
   * @return self::PROTOCOL_*
   */
  public function getProtocol()
  {
    return $this->protocol;
  }
  /**
   * Output only. [Output Only] URL of the region where the regional backend
   * service resides. This field is not applicable to global backend services.
   * You must specify this field as part of the HTTP request URL. It is not
   * settable as a field in the request body.
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
   * [Output Only] The resource URL for the security policy associated with this
   * backend service.
   *
   * @param string $securityPolicy
   */
  public function setSecurityPolicy($securityPolicy)
  {
    $this->securityPolicy = $securityPolicy;
  }
  /**
   * @return string
   */
  public function getSecurityPolicy()
  {
    return $this->securityPolicy;
  }
  /**
   * This field specifies the security settings that apply to this backend
   * service. This field is applicable to a global backend service with the
   * load_balancing_scheme set to INTERNAL_SELF_MANAGED.
   *
   * @param SecuritySettings $securitySettings
   */
  public function setSecuritySettings(SecuritySettings $securitySettings)
  {
    $this->securitySettings = $securitySettings;
  }
  /**
   * @return SecuritySettings
   */
  public function getSecuritySettings()
  {
    return $this->securitySettings;
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
   * URLs of networkservices.ServiceBinding resources.
   *
   * Can only be set if load balancing scheme is INTERNAL_SELF_MANAGED. If set,
   * lists of backends and health checks must be both empty.
   *
   * @param string[] $serviceBindings
   */
  public function setServiceBindings($serviceBindings)
  {
    $this->serviceBindings = $serviceBindings;
  }
  /**
   * @return string[]
   */
  public function getServiceBindings()
  {
    return $this->serviceBindings;
  }
  /**
   * URL to networkservices.ServiceLbPolicy resource.
   *
   * Can only be set if load balancing scheme is EXTERNAL_MANAGED,
   * INTERNAL_MANAGED or INTERNAL_SELF_MANAGED and the scope is global.
   *
   * @param string $serviceLbPolicy
   */
  public function setServiceLbPolicy($serviceLbPolicy)
  {
    $this->serviceLbPolicy = $serviceLbPolicy;
  }
  /**
   * @return string
   */
  public function getServiceLbPolicy()
  {
    return $this->serviceLbPolicy;
  }
  /**
   * Type of session affinity to use. The default is NONE.
   *
   * Only NONE and HEADER_FIELD are supported when the backend service is
   * referenced by a URL map that is bound to target gRPC proxy that has
   * validateForProxyless field set to true.
   *
   * For more details, see: [Session Affinity](https://cloud.google.com/load-
   * balancing/docs/backend-service#session_affinity).
   *
   * sessionAffinity cannot be specified with haPolicy.
   *
   * Accepted values: CLIENT_IP, CLIENT_IP_NO_DESTINATION, CLIENT_IP_PORT_PROTO,
   * CLIENT_IP_PROTO, GENERATED_COOKIE, HEADER_FIELD, HTTP_COOKIE, NONE,
   * STRONG_COOKIE_AFFINITY
   *
   * @param self::SESSION_AFFINITY_* $sessionAffinity
   */
  public function setSessionAffinity($sessionAffinity)
  {
    $this->sessionAffinity = $sessionAffinity;
  }
  /**
   * @return self::SESSION_AFFINITY_*
   */
  public function getSessionAffinity()
  {
    return $this->sessionAffinity;
  }
  /**
   * Describes the HTTP cookie used for stateful session affinity. This field is
   * applicable and required if the sessionAffinity is set
   * toSTRONG_COOKIE_AFFINITY.
   *
   * @param BackendServiceHttpCookie $strongSessionAffinityCookie
   */
  public function setStrongSessionAffinityCookie(BackendServiceHttpCookie $strongSessionAffinityCookie)
  {
    $this->strongSessionAffinityCookie = $strongSessionAffinityCookie;
  }
  /**
   * @return BackendServiceHttpCookie
   */
  public function getStrongSessionAffinityCookie()
  {
    return $this->strongSessionAffinityCookie;
  }
  /**
   * subsetting cannot be specified with haPolicy.
   *
   * @param Subsetting $subsetting
   */
  public function setSubsetting(Subsetting $subsetting)
  {
    $this->subsetting = $subsetting;
  }
  /**
   * @return Subsetting
   */
  public function getSubsetting()
  {
    return $this->subsetting;
  }
  /**
   * The backend service timeout has a different meaning depending on the type
   * of load balancer. For more information see, Backend service settings. The
   * default is 30 seconds. The full range of timeout values allowed goes from 1
   * through 2,147,483,647 seconds.
   *
   * This value can be overridden in the PathMatcher configuration of the UrlMap
   * that references this backend service.
   *
   * Not supported when the backend service is referenced by a URL map that is
   * bound to target gRPC proxy that has validateForProxyless field set to true.
   * Instead, use maxStreamDuration.
   *
   * @param int $timeoutSec
   */
  public function setTimeoutSec($timeoutSec)
  {
    $this->timeoutSec = $timeoutSec;
  }
  /**
   * @return int
   */
  public function getTimeoutSec()
  {
    return $this->timeoutSec;
  }
  /**
   * Configuration for Backend Authenticated TLS and mTLS. May only be specified
   * when the backend protocol is SSL, HTTPS or HTTP2.
   *
   * @param BackendServiceTlsSettings $tlsSettings
   */
  public function setTlsSettings(BackendServiceTlsSettings $tlsSettings)
  {
    $this->tlsSettings = $tlsSettings;
  }
  /**
   * @return BackendServiceTlsSettings
   */
  public function getTlsSettings()
  {
    return $this->tlsSettings;
  }
  /**
   * Output only. [Output Only] List of resources referencing given backend
   * service.
   *
   * @param BackendServiceUsedBy[] $usedBy
   */
  public function setUsedBy($usedBy)
  {
    $this->usedBy = $usedBy;
  }
  /**
   * @return BackendServiceUsedBy[]
   */
  public function getUsedBy()
  {
    return $this->usedBy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackendService::class, 'Google_Service_Compute_BackendService');
