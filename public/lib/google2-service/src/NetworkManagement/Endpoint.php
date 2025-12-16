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

class Endpoint extends \Google\Model
{
  /**
   * Forwarding rule target is unknown.
   */
  public const FORWARDING_RULE_TARGET_FORWARDING_RULE_TARGET_UNSPECIFIED = 'FORWARDING_RULE_TARGET_UNSPECIFIED';
  /**
   * Compute Engine instance for protocol forwarding.
   */
  public const FORWARDING_RULE_TARGET_INSTANCE = 'INSTANCE';
  /**
   * Load Balancer. The specific type can be found from load_balancer_type.
   */
  public const FORWARDING_RULE_TARGET_LOAD_BALANCER = 'LOAD_BALANCER';
  /**
   * Classic Cloud VPN Gateway.
   */
  public const FORWARDING_RULE_TARGET_VPN_GATEWAY = 'VPN_GATEWAY';
  /**
   * Forwarding Rule is a Private Service Connect endpoint.
   */
  public const FORWARDING_RULE_TARGET_PSC = 'PSC';
  /**
   * Forwarding rule points to a different target than a load balancer or a load
   * balancer type is unknown.
   */
  public const LOAD_BALANCER_TYPE_LOAD_BALANCER_TYPE_UNSPECIFIED = 'LOAD_BALANCER_TYPE_UNSPECIFIED';
  /**
   * Global external HTTP(S) load balancer.
   */
  public const LOAD_BALANCER_TYPE_HTTPS_ADVANCED_LOAD_BALANCER = 'HTTPS_ADVANCED_LOAD_BALANCER';
  /**
   * Global external HTTP(S) load balancer (classic)
   */
  public const LOAD_BALANCER_TYPE_HTTPS_LOAD_BALANCER = 'HTTPS_LOAD_BALANCER';
  /**
   * Regional external HTTP(S) load balancer.
   */
  public const LOAD_BALANCER_TYPE_REGIONAL_HTTPS_LOAD_BALANCER = 'REGIONAL_HTTPS_LOAD_BALANCER';
  /**
   * Internal HTTP(S) load balancer.
   */
  public const LOAD_BALANCER_TYPE_INTERNAL_HTTPS_LOAD_BALANCER = 'INTERNAL_HTTPS_LOAD_BALANCER';
  /**
   * External SSL proxy load balancer.
   */
  public const LOAD_BALANCER_TYPE_SSL_PROXY_LOAD_BALANCER = 'SSL_PROXY_LOAD_BALANCER';
  /**
   * External TCP proxy load balancer.
   */
  public const LOAD_BALANCER_TYPE_TCP_PROXY_LOAD_BALANCER = 'TCP_PROXY_LOAD_BALANCER';
  /**
   * Internal regional TCP proxy load balancer.
   */
  public const LOAD_BALANCER_TYPE_INTERNAL_TCP_PROXY_LOAD_BALANCER = 'INTERNAL_TCP_PROXY_LOAD_BALANCER';
  /**
   * External TCP/UDP Network load balancer.
   */
  public const LOAD_BALANCER_TYPE_NETWORK_LOAD_BALANCER = 'NETWORK_LOAD_BALANCER';
  /**
   * Target-pool based external TCP/UDP Network load balancer.
   */
  public const LOAD_BALANCER_TYPE_LEGACY_NETWORK_LOAD_BALANCER = 'LEGACY_NETWORK_LOAD_BALANCER';
  /**
   * Internal TCP/UDP load balancer.
   */
  public const LOAD_BALANCER_TYPE_TCP_UDP_INTERNAL_LOAD_BALANCER = 'TCP_UDP_INTERNAL_LOAD_BALANCER';
  /**
   * Default type if unspecified.
   */
  public const NETWORK_TYPE_NETWORK_TYPE_UNSPECIFIED = 'NETWORK_TYPE_UNSPECIFIED';
  /**
   * A network hosted within Google Cloud. To receive more detailed output,
   * specify the URI for the source or destination network.
   */
  public const NETWORK_TYPE_GCP_NETWORK = 'GCP_NETWORK';
  /**
   * A network hosted outside of Google Cloud. This can be an on-premises
   * network, an internet resource or a network hosted by another cloud
   * provider.
   */
  public const NETWORK_TYPE_NON_GCP_NETWORK = 'NON_GCP_NETWORK';
  protected $appEngineVersionType = AppEngineVersionEndpoint::class;
  protected $appEngineVersionDataType = '';
  protected $cloudFunctionType = CloudFunctionEndpoint::class;
  protected $cloudFunctionDataType = '';
  protected $cloudRunRevisionType = CloudRunRevisionEndpoint::class;
  protected $cloudRunRevisionDataType = '';
  /**
   * A [Cloud SQL](https://cloud.google.com/sql) instance URI.
   *
   * @var string
   */
  public $cloudSqlInstance;
  /**
   * A forwarding rule and its corresponding IP address represent the frontend
   * configuration of a Google Cloud load balancer. Forwarding rules are also
   * used for protocol forwarding, Private Service Connect and other network
   * services to provide forwarding information in the control plane. Applicable
   * only to destination endpoint. Format:
   * `projects/{project}/global/forwardingRules/{id}` or
   * `projects/{project}/regions/{region}/forwardingRules/{id}`
   *
   * @var string
   */
  public $forwardingRule;
  /**
   * Output only. Specifies the type of the target of the forwarding rule.
   *
   * @var string
   */
  public $forwardingRuleTarget;
  /**
   * DNS endpoint of [Google Kubernetes Engine cluster control
   * plane](https://cloud.google.com/kubernetes-engine/docs/concepts/cluster-
   * architecture). Requires gke_master_cluster to be set, can't be used
   * simultaneoulsly with ip_address or network. Applicable only to destination
   * endpoint.
   *
   * @var string
   */
  public $fqdn;
  /**
   * A cluster URI for [Google Kubernetes Engine cluster control
   * plane](https://cloud.google.com/kubernetes-engine/docs/concepts/cluster-
   * architecture).
   *
   * @var string
   */
  public $gkeMasterCluster;
  /**
   * A Compute Engine instance URI.
   *
   * @var string
   */
  public $instance;
  /**
   * The IP address of the endpoint, which can be an external or internal IP.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * Output only. ID of the load balancer the forwarding rule points to. Empty
   * for forwarding rules not related to load balancers.
   *
   * @var string
   */
  public $loadBalancerId;
  /**
   * Output only. Type of the load balancer the forwarding rule points to.
   *
   * @var string
   */
  public $loadBalancerType;
  /**
   * A VPC network URI.
   *
   * @var string
   */
  public $network;
  /**
   * Type of the network where the endpoint is located. Applicable only to
   * source endpoint, as destination network type can be inferred from the
   * source.
   *
   * @var string
   */
  public $networkType;
  /**
   * The IP protocol port of the endpoint. Only applicable when protocol is TCP
   * or UDP.
   *
   * @var int
   */
  public $port;
  /**
   * Project ID where the endpoint is located. The project ID can be derived
   * from the URI if you provide a endpoint or network URI. The following are
   * two cases where you may need to provide the project ID: 1. Only the IP
   * address is specified, and the IP address is within a Google Cloud project.
   * 2. When you are using Shared VPC and the IP address that you provide is
   * from the service project. In this case, the network that the IP address
   * resides in is defined in the host project.
   *
   * @var string
   */
  public $projectId;
  /**
   * A [Redis Cluster](https://cloud.google.com/memorystore/docs/cluster) URI.
   * Applicable only to destination endpoint.
   *
   * @var string
   */
  public $redisCluster;
  /**
   * A [Redis Instance](https://cloud.google.com/memorystore/docs/redis) URI.
   * Applicable only to destination endpoint.
   *
   * @var string
   */
  public $redisInstance;

  /**
   * An [App Engine](https://cloud.google.com/appengine) [service
   * version](https://cloud.google.com/appengine/docs/admin-
   * api/reference/rest/v1/apps.services.versions). Applicable only to source
   * endpoint.
   *
   * @param AppEngineVersionEndpoint $appEngineVersion
   */
  public function setAppEngineVersion(AppEngineVersionEndpoint $appEngineVersion)
  {
    $this->appEngineVersion = $appEngineVersion;
  }
  /**
   * @return AppEngineVersionEndpoint
   */
  public function getAppEngineVersion()
  {
    return $this->appEngineVersion;
  }
  /**
   * A [Cloud Function](https://cloud.google.com/functions). Applicable only to
   * source endpoint.
   *
   * @param CloudFunctionEndpoint $cloudFunction
   */
  public function setCloudFunction(CloudFunctionEndpoint $cloudFunction)
  {
    $this->cloudFunction = $cloudFunction;
  }
  /**
   * @return CloudFunctionEndpoint
   */
  public function getCloudFunction()
  {
    return $this->cloudFunction;
  }
  /**
   * A [Cloud Run](https://cloud.google.com/run) [revision](https://cloud.google
   * .com/run/docs/reference/rest/v1/namespaces.revisions/get) Applicable only
   * to source endpoint.
   *
   * @param CloudRunRevisionEndpoint $cloudRunRevision
   */
  public function setCloudRunRevision(CloudRunRevisionEndpoint $cloudRunRevision)
  {
    $this->cloudRunRevision = $cloudRunRevision;
  }
  /**
   * @return CloudRunRevisionEndpoint
   */
  public function getCloudRunRevision()
  {
    return $this->cloudRunRevision;
  }
  /**
   * A [Cloud SQL](https://cloud.google.com/sql) instance URI.
   *
   * @param string $cloudSqlInstance
   */
  public function setCloudSqlInstance($cloudSqlInstance)
  {
    $this->cloudSqlInstance = $cloudSqlInstance;
  }
  /**
   * @return string
   */
  public function getCloudSqlInstance()
  {
    return $this->cloudSqlInstance;
  }
  /**
   * A forwarding rule and its corresponding IP address represent the frontend
   * configuration of a Google Cloud load balancer. Forwarding rules are also
   * used for protocol forwarding, Private Service Connect and other network
   * services to provide forwarding information in the control plane. Applicable
   * only to destination endpoint. Format:
   * `projects/{project}/global/forwardingRules/{id}` or
   * `projects/{project}/regions/{region}/forwardingRules/{id}`
   *
   * @param string $forwardingRule
   */
  public function setForwardingRule($forwardingRule)
  {
    $this->forwardingRule = $forwardingRule;
  }
  /**
   * @return string
   */
  public function getForwardingRule()
  {
    return $this->forwardingRule;
  }
  /**
   * Output only. Specifies the type of the target of the forwarding rule.
   *
   * Accepted values: FORWARDING_RULE_TARGET_UNSPECIFIED, INSTANCE,
   * LOAD_BALANCER, VPN_GATEWAY, PSC
   *
   * @param self::FORWARDING_RULE_TARGET_* $forwardingRuleTarget
   */
  public function setForwardingRuleTarget($forwardingRuleTarget)
  {
    $this->forwardingRuleTarget = $forwardingRuleTarget;
  }
  /**
   * @return self::FORWARDING_RULE_TARGET_*
   */
  public function getForwardingRuleTarget()
  {
    return $this->forwardingRuleTarget;
  }
  /**
   * DNS endpoint of [Google Kubernetes Engine cluster control
   * plane](https://cloud.google.com/kubernetes-engine/docs/concepts/cluster-
   * architecture). Requires gke_master_cluster to be set, can't be used
   * simultaneoulsly with ip_address or network. Applicable only to destination
   * endpoint.
   *
   * @param string $fqdn
   */
  public function setFqdn($fqdn)
  {
    $this->fqdn = $fqdn;
  }
  /**
   * @return string
   */
  public function getFqdn()
  {
    return $this->fqdn;
  }
  /**
   * A cluster URI for [Google Kubernetes Engine cluster control
   * plane](https://cloud.google.com/kubernetes-engine/docs/concepts/cluster-
   * architecture).
   *
   * @param string $gkeMasterCluster
   */
  public function setGkeMasterCluster($gkeMasterCluster)
  {
    $this->gkeMasterCluster = $gkeMasterCluster;
  }
  /**
   * @return string
   */
  public function getGkeMasterCluster()
  {
    return $this->gkeMasterCluster;
  }
  /**
   * A Compute Engine instance URI.
   *
   * @param string $instance
   */
  public function setInstance($instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return string
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * The IP address of the endpoint, which can be an external or internal IP.
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
   * Output only. ID of the load balancer the forwarding rule points to. Empty
   * for forwarding rules not related to load balancers.
   *
   * @param string $loadBalancerId
   */
  public function setLoadBalancerId($loadBalancerId)
  {
    $this->loadBalancerId = $loadBalancerId;
  }
  /**
   * @return string
   */
  public function getLoadBalancerId()
  {
    return $this->loadBalancerId;
  }
  /**
   * Output only. Type of the load balancer the forwarding rule points to.
   *
   * Accepted values: LOAD_BALANCER_TYPE_UNSPECIFIED,
   * HTTPS_ADVANCED_LOAD_BALANCER, HTTPS_LOAD_BALANCER,
   * REGIONAL_HTTPS_LOAD_BALANCER, INTERNAL_HTTPS_LOAD_BALANCER,
   * SSL_PROXY_LOAD_BALANCER, TCP_PROXY_LOAD_BALANCER,
   * INTERNAL_TCP_PROXY_LOAD_BALANCER, NETWORK_LOAD_BALANCER,
   * LEGACY_NETWORK_LOAD_BALANCER, TCP_UDP_INTERNAL_LOAD_BALANCER
   *
   * @param self::LOAD_BALANCER_TYPE_* $loadBalancerType
   */
  public function setLoadBalancerType($loadBalancerType)
  {
    $this->loadBalancerType = $loadBalancerType;
  }
  /**
   * @return self::LOAD_BALANCER_TYPE_*
   */
  public function getLoadBalancerType()
  {
    return $this->loadBalancerType;
  }
  /**
   * A VPC network URI.
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
   * Type of the network where the endpoint is located. Applicable only to
   * source endpoint, as destination network type can be inferred from the
   * source.
   *
   * Accepted values: NETWORK_TYPE_UNSPECIFIED, GCP_NETWORK, NON_GCP_NETWORK
   *
   * @param self::NETWORK_TYPE_* $networkType
   */
  public function setNetworkType($networkType)
  {
    $this->networkType = $networkType;
  }
  /**
   * @return self::NETWORK_TYPE_*
   */
  public function getNetworkType()
  {
    return $this->networkType;
  }
  /**
   * The IP protocol port of the endpoint. Only applicable when protocol is TCP
   * or UDP.
   *
   * @param int $port
   */
  public function setPort($port)
  {
    $this->port = $port;
  }
  /**
   * @return int
   */
  public function getPort()
  {
    return $this->port;
  }
  /**
   * Project ID where the endpoint is located. The project ID can be derived
   * from the URI if you provide a endpoint or network URI. The following are
   * two cases where you may need to provide the project ID: 1. Only the IP
   * address is specified, and the IP address is within a Google Cloud project.
   * 2. When you are using Shared VPC and the IP address that you provide is
   * from the service project. In this case, the network that the IP address
   * resides in is defined in the host project.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * A [Redis Cluster](https://cloud.google.com/memorystore/docs/cluster) URI.
   * Applicable only to destination endpoint.
   *
   * @param string $redisCluster
   */
  public function setRedisCluster($redisCluster)
  {
    $this->redisCluster = $redisCluster;
  }
  /**
   * @return string
   */
  public function getRedisCluster()
  {
    return $this->redisCluster;
  }
  /**
   * A [Redis Instance](https://cloud.google.com/memorystore/docs/redis) URI.
   * Applicable only to destination endpoint.
   *
   * @param string $redisInstance
   */
  public function setRedisInstance($redisInstance)
  {
    $this->redisInstance = $redisInstance;
  }
  /**
   * @return string
   */
  public function getRedisInstance()
  {
    return $this->redisInstance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Endpoint::class, 'Google_Service_NetworkManagement_Endpoint');
