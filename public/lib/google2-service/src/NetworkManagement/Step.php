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

class Step extends \Google\Model
{
  /**
   * Unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Initial state: packet originating from a Compute Engine instance. An
   * InstanceInfo is populated with starting instance information.
   */
  public const STATE_START_FROM_INSTANCE = 'START_FROM_INSTANCE';
  /**
   * Initial state: packet originating from the internet. The endpoint
   * information is populated.
   */
  public const STATE_START_FROM_INTERNET = 'START_FROM_INTERNET';
  /**
   * Initial state: packet originating from a Google service. The google_service
   * information is populated.
   */
  public const STATE_START_FROM_GOOGLE_SERVICE = 'START_FROM_GOOGLE_SERVICE';
  /**
   * Initial state: packet originating from a VPC or on-premises network with
   * internal source IP. If the source is a VPC network visible to the user, a
   * NetworkInfo is populated with details of the network.
   */
  public const STATE_START_FROM_PRIVATE_NETWORK = 'START_FROM_PRIVATE_NETWORK';
  /**
   * Initial state: packet originating from a Google Kubernetes Engine cluster
   * master. A GKEMasterInfo is populated with starting instance information.
   */
  public const STATE_START_FROM_GKE_MASTER = 'START_FROM_GKE_MASTER';
  /**
   * Initial state: packet originating from a Cloud SQL instance. A
   * CloudSQLInstanceInfo is populated with starting instance information.
   */
  public const STATE_START_FROM_CLOUD_SQL_INSTANCE = 'START_FROM_CLOUD_SQL_INSTANCE';
  /**
   * Initial state: packet originating from a Redis instance. A
   * RedisInstanceInfo is populated with starting instance information.
   */
  public const STATE_START_FROM_REDIS_INSTANCE = 'START_FROM_REDIS_INSTANCE';
  /**
   * Initial state: packet originating from a Redis Cluster. A RedisClusterInfo
   * is populated with starting Cluster information.
   */
  public const STATE_START_FROM_REDIS_CLUSTER = 'START_FROM_REDIS_CLUSTER';
  /**
   * Initial state: packet originating from a Cloud Function. A
   * CloudFunctionInfo is populated with starting function information.
   */
  public const STATE_START_FROM_CLOUD_FUNCTION = 'START_FROM_CLOUD_FUNCTION';
  /**
   * Initial state: packet originating from an App Engine service version. An
   * AppEngineVersionInfo is populated with starting version information.
   */
  public const STATE_START_FROM_APP_ENGINE_VERSION = 'START_FROM_APP_ENGINE_VERSION';
  /**
   * Initial state: packet originating from a Cloud Run revision. A
   * CloudRunRevisionInfo is populated with starting revision information.
   */
  public const STATE_START_FROM_CLOUD_RUN_REVISION = 'START_FROM_CLOUD_RUN_REVISION';
  /**
   * Initial state: packet originating from a Storage Bucket. Used only for
   * return traces. The storage_bucket information is populated.
   */
  public const STATE_START_FROM_STORAGE_BUCKET = 'START_FROM_STORAGE_BUCKET';
  /**
   * Initial state: packet originating from a published service that uses
   * Private Service Connect. Used only for return traces.
   */
  public const STATE_START_FROM_PSC_PUBLISHED_SERVICE = 'START_FROM_PSC_PUBLISHED_SERVICE';
  /**
   * Initial state: packet originating from a serverless network endpoint group
   * backend. Used only for return traces. The serverless_neg information is
   * populated.
   */
  public const STATE_START_FROM_SERVERLESS_NEG = 'START_FROM_SERVERLESS_NEG';
  /**
   * Config checking state: verify ingress firewall rule.
   */
  public const STATE_APPLY_INGRESS_FIREWALL_RULE = 'APPLY_INGRESS_FIREWALL_RULE';
  /**
   * Config checking state: verify egress firewall rule.
   */
  public const STATE_APPLY_EGRESS_FIREWALL_RULE = 'APPLY_EGRESS_FIREWALL_RULE';
  /**
   * Config checking state: verify route.
   */
  public const STATE_APPLY_ROUTE = 'APPLY_ROUTE';
  /**
   * Config checking state: match forwarding rule.
   */
  public const STATE_APPLY_FORWARDING_RULE = 'APPLY_FORWARDING_RULE';
  /**
   * Config checking state: verify load balancer backend configuration.
   */
  public const STATE_ANALYZE_LOAD_BALANCER_BACKEND = 'ANALYZE_LOAD_BALANCER_BACKEND';
  /**
   * Config checking state: packet sent or received under foreign IP address and
   * allowed.
   */
  public const STATE_SPOOFING_APPROVED = 'SPOOFING_APPROVED';
  /**
   * Forwarding state: arriving at a Compute Engine instance.
   */
  public const STATE_ARRIVE_AT_INSTANCE = 'ARRIVE_AT_INSTANCE';
  /**
   * Forwarding state: arriving at a Compute Engine internal load balancer.
   *
   * @deprecated
   */
  public const STATE_ARRIVE_AT_INTERNAL_LOAD_BALANCER = 'ARRIVE_AT_INTERNAL_LOAD_BALANCER';
  /**
   * Forwarding state: arriving at a Compute Engine external load balancer.
   *
   * @deprecated
   */
  public const STATE_ARRIVE_AT_EXTERNAL_LOAD_BALANCER = 'ARRIVE_AT_EXTERNAL_LOAD_BALANCER';
  /**
   * Forwarding state: arriving at a hybrid subnet. Appropriate routing
   * configuration will be determined here.
   */
  public const STATE_ARRIVE_AT_HYBRID_SUBNET = 'ARRIVE_AT_HYBRID_SUBNET';
  /**
   * Forwarding state: arriving at a Cloud VPN gateway.
   */
  public const STATE_ARRIVE_AT_VPN_GATEWAY = 'ARRIVE_AT_VPN_GATEWAY';
  /**
   * Forwarding state: arriving at a Cloud VPN tunnel.
   */
  public const STATE_ARRIVE_AT_VPN_TUNNEL = 'ARRIVE_AT_VPN_TUNNEL';
  /**
   * Forwarding state: arriving at an interconnect attachment.
   */
  public const STATE_ARRIVE_AT_INTERCONNECT_ATTACHMENT = 'ARRIVE_AT_INTERCONNECT_ATTACHMENT';
  /**
   * Forwarding state: arriving at a VPC connector.
   */
  public const STATE_ARRIVE_AT_VPC_CONNECTOR = 'ARRIVE_AT_VPC_CONNECTOR';
  /**
   * Forwarding state: for packets originating from a serverless endpoint
   * forwarded through Direct VPC egress.
   */
  public const STATE_DIRECT_VPC_EGRESS_CONNECTION = 'DIRECT_VPC_EGRESS_CONNECTION';
  /**
   * Forwarding state: for packets originating from a serverless endpoint
   * forwarded through public (external) connectivity.
   */
  public const STATE_SERVERLESS_EXTERNAL_CONNECTION = 'SERVERLESS_EXTERNAL_CONNECTION';
  /**
   * Transition state: packet header translated. The `nat` field is populated
   * with the translation information.
   */
  public const STATE_NAT = 'NAT';
  /**
   * Transition state: original connection is terminated and a new proxied
   * connection is initiated.
   */
  public const STATE_PROXY_CONNECTION = 'PROXY_CONNECTION';
  /**
   * Final state: packet could be delivered.
   */
  public const STATE_DELIVER = 'DELIVER';
  /**
   * Final state: packet could be dropped.
   */
  public const STATE_DROP = 'DROP';
  /**
   * Final state: packet could be forwarded to a network with an unknown
   * configuration.
   */
  public const STATE_FORWARD = 'FORWARD';
  /**
   * Final state: analysis is aborted.
   */
  public const STATE_ABORT = 'ABORT';
  /**
   * Special state: viewer of the test result does not have permission to see
   * the configuration in this step.
   */
  public const STATE_VIEWER_PERMISSION_MISSING = 'VIEWER_PERMISSION_MISSING';
  protected $abortType = AbortInfo::class;
  protected $abortDataType = '';
  protected $appEngineVersionType = AppEngineVersionInfo::class;
  protected $appEngineVersionDataType = '';
  /**
   * This is a step that leads to the final state Drop.
   *
   * @var bool
   */
  public $causesDrop;
  protected $cloudFunctionType = CloudFunctionInfo::class;
  protected $cloudFunctionDataType = '';
  protected $cloudRunRevisionType = CloudRunRevisionInfo::class;
  protected $cloudRunRevisionDataType = '';
  protected $cloudSqlInstanceType = CloudSQLInstanceInfo::class;
  protected $cloudSqlInstanceDataType = '';
  protected $deliverType = DeliverInfo::class;
  protected $deliverDataType = '';
  /**
   * A description of the step. Usually this is a summary of the state.
   *
   * @var string
   */
  public $description;
  protected $directVpcEgressConnectionType = DirectVpcEgressConnectionInfo::class;
  protected $directVpcEgressConnectionDataType = '';
  protected $dropType = DropInfo::class;
  protected $dropDataType = '';
  protected $endpointType = EndpointInfo::class;
  protected $endpointDataType = '';
  protected $firewallType = FirewallInfo::class;
  protected $firewallDataType = '';
  protected $forwardType = ForwardInfo::class;
  protected $forwardDataType = '';
  protected $forwardingRuleType = ForwardingRuleInfo::class;
  protected $forwardingRuleDataType = '';
  protected $gkeMasterType = GKEMasterInfo::class;
  protected $gkeMasterDataType = '';
  protected $googleServiceType = GoogleServiceInfo::class;
  protected $googleServiceDataType = '';
  protected $hybridSubnetType = HybridSubnetInfo::class;
  protected $hybridSubnetDataType = '';
  protected $instanceType = InstanceInfo::class;
  protected $instanceDataType = '';
  protected $interconnectAttachmentType = InterconnectAttachmentInfo::class;
  protected $interconnectAttachmentDataType = '';
  protected $loadBalancerType = LoadBalancerInfo::class;
  protected $loadBalancerDataType = '';
  protected $loadBalancerBackendInfoType = LoadBalancerBackendInfo::class;
  protected $loadBalancerBackendInfoDataType = '';
  protected $natType = NatInfo::class;
  protected $natDataType = '';
  protected $networkType = NetworkInfo::class;
  protected $networkDataType = '';
  /**
   * Project ID that contains the configuration this step is validating.
   *
   * @var string
   */
  public $projectId;
  protected $proxyConnectionType = ProxyConnectionInfo::class;
  protected $proxyConnectionDataType = '';
  protected $redisClusterType = RedisClusterInfo::class;
  protected $redisClusterDataType = '';
  protected $redisInstanceType = RedisInstanceInfo::class;
  protected $redisInstanceDataType = '';
  protected $routeType = RouteInfo::class;
  protected $routeDataType = '';
  protected $serverlessExternalConnectionType = ServerlessExternalConnectionInfo::class;
  protected $serverlessExternalConnectionDataType = '';
  protected $serverlessNegType = ServerlessNegInfo::class;
  protected $serverlessNegDataType = '';
  /**
   * Each step is in one of the pre-defined states.
   *
   * @var string
   */
  public $state;
  protected $storageBucketType = StorageBucketInfo::class;
  protected $storageBucketDataType = '';
  protected $vpcConnectorType = VpcConnectorInfo::class;
  protected $vpcConnectorDataType = '';
  protected $vpnGatewayType = VpnGatewayInfo::class;
  protected $vpnGatewayDataType = '';
  protected $vpnTunnelType = VpnTunnelInfo::class;
  protected $vpnTunnelDataType = '';

  /**
   * Display information of the final state "abort" and reason.
   *
   * @param AbortInfo $abort
   */
  public function setAbort(AbortInfo $abort)
  {
    $this->abort = $abort;
  }
  /**
   * @return AbortInfo
   */
  public function getAbort()
  {
    return $this->abort;
  }
  /**
   * Display information of an App Engine service version.
   *
   * @param AppEngineVersionInfo $appEngineVersion
   */
  public function setAppEngineVersion(AppEngineVersionInfo $appEngineVersion)
  {
    $this->appEngineVersion = $appEngineVersion;
  }
  /**
   * @return AppEngineVersionInfo
   */
  public function getAppEngineVersion()
  {
    return $this->appEngineVersion;
  }
  /**
   * This is a step that leads to the final state Drop.
   *
   * @param bool $causesDrop
   */
  public function setCausesDrop($causesDrop)
  {
    $this->causesDrop = $causesDrop;
  }
  /**
   * @return bool
   */
  public function getCausesDrop()
  {
    return $this->causesDrop;
  }
  /**
   * Display information of a Cloud Function.
   *
   * @param CloudFunctionInfo $cloudFunction
   */
  public function setCloudFunction(CloudFunctionInfo $cloudFunction)
  {
    $this->cloudFunction = $cloudFunction;
  }
  /**
   * @return CloudFunctionInfo
   */
  public function getCloudFunction()
  {
    return $this->cloudFunction;
  }
  /**
   * Display information of a Cloud Run revision.
   *
   * @param CloudRunRevisionInfo $cloudRunRevision
   */
  public function setCloudRunRevision(CloudRunRevisionInfo $cloudRunRevision)
  {
    $this->cloudRunRevision = $cloudRunRevision;
  }
  /**
   * @return CloudRunRevisionInfo
   */
  public function getCloudRunRevision()
  {
    return $this->cloudRunRevision;
  }
  /**
   * Display information of a Cloud SQL instance.
   *
   * @param CloudSQLInstanceInfo $cloudSqlInstance
   */
  public function setCloudSqlInstance(CloudSQLInstanceInfo $cloudSqlInstance)
  {
    $this->cloudSqlInstance = $cloudSqlInstance;
  }
  /**
   * @return CloudSQLInstanceInfo
   */
  public function getCloudSqlInstance()
  {
    return $this->cloudSqlInstance;
  }
  /**
   * Display information of the final state "deliver" and reason.
   *
   * @param DeliverInfo $deliver
   */
  public function setDeliver(DeliverInfo $deliver)
  {
    $this->deliver = $deliver;
  }
  /**
   * @return DeliverInfo
   */
  public function getDeliver()
  {
    return $this->deliver;
  }
  /**
   * A description of the step. Usually this is a summary of the state.
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
   * Display information of a serverless direct VPC egress connection.
   *
   * @param DirectVpcEgressConnectionInfo $directVpcEgressConnection
   */
  public function setDirectVpcEgressConnection(DirectVpcEgressConnectionInfo $directVpcEgressConnection)
  {
    $this->directVpcEgressConnection = $directVpcEgressConnection;
  }
  /**
   * @return DirectVpcEgressConnectionInfo
   */
  public function getDirectVpcEgressConnection()
  {
    return $this->directVpcEgressConnection;
  }
  /**
   * Display information of the final state "drop" and reason.
   *
   * @param DropInfo $drop
   */
  public function setDrop(DropInfo $drop)
  {
    $this->drop = $drop;
  }
  /**
   * @return DropInfo
   */
  public function getDrop()
  {
    return $this->drop;
  }
  /**
   * Display information of the source and destination under analysis. The
   * endpoint information in an intermediate state may differ with the initial
   * input, as it might be modified by state like NAT, or Connection Proxy.
   *
   * @param EndpointInfo $endpoint
   */
  public function setEndpoint(EndpointInfo $endpoint)
  {
    $this->endpoint = $endpoint;
  }
  /**
   * @return EndpointInfo
   */
  public function getEndpoint()
  {
    return $this->endpoint;
  }
  /**
   * Display information of a Compute Engine firewall rule.
   *
   * @param FirewallInfo $firewall
   */
  public function setFirewall(FirewallInfo $firewall)
  {
    $this->firewall = $firewall;
  }
  /**
   * @return FirewallInfo
   */
  public function getFirewall()
  {
    return $this->firewall;
  }
  /**
   * Display information of the final state "forward" and reason.
   *
   * @param ForwardInfo $forward
   */
  public function setForward(ForwardInfo $forward)
  {
    $this->forward = $forward;
  }
  /**
   * @return ForwardInfo
   */
  public function getForward()
  {
    return $this->forward;
  }
  /**
   * Display information of a Compute Engine forwarding rule.
   *
   * @param ForwardingRuleInfo $forwardingRule
   */
  public function setForwardingRule(ForwardingRuleInfo $forwardingRule)
  {
    $this->forwardingRule = $forwardingRule;
  }
  /**
   * @return ForwardingRuleInfo
   */
  public function getForwardingRule()
  {
    return $this->forwardingRule;
  }
  /**
   * Display information of a Google Kubernetes Engine cluster master.
   *
   * @param GKEMasterInfo $gkeMaster
   */
  public function setGkeMaster(GKEMasterInfo $gkeMaster)
  {
    $this->gkeMaster = $gkeMaster;
  }
  /**
   * @return GKEMasterInfo
   */
  public function getGkeMaster()
  {
    return $this->gkeMaster;
  }
  /**
   * Display information of a Google service
   *
   * @param GoogleServiceInfo $googleService
   */
  public function setGoogleService(GoogleServiceInfo $googleService)
  {
    $this->googleService = $googleService;
  }
  /**
   * @return GoogleServiceInfo
   */
  public function getGoogleService()
  {
    return $this->googleService;
  }
  /**
   * Display information of a hybrid subnet.
   *
   * @param HybridSubnetInfo $hybridSubnet
   */
  public function setHybridSubnet(HybridSubnetInfo $hybridSubnet)
  {
    $this->hybridSubnet = $hybridSubnet;
  }
  /**
   * @return HybridSubnetInfo
   */
  public function getHybridSubnet()
  {
    return $this->hybridSubnet;
  }
  /**
   * Display information of a Compute Engine instance.
   *
   * @param InstanceInfo $instance
   */
  public function setInstance(InstanceInfo $instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return InstanceInfo
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * Display information of an interconnect attachment.
   *
   * @param InterconnectAttachmentInfo $interconnectAttachment
   */
  public function setInterconnectAttachment(InterconnectAttachmentInfo $interconnectAttachment)
  {
    $this->interconnectAttachment = $interconnectAttachment;
  }
  /**
   * @return InterconnectAttachmentInfo
   */
  public function getInterconnectAttachment()
  {
    return $this->interconnectAttachment;
  }
  /**
   * Display information of the load balancers. Deprecated in favor of the
   * `load_balancer_backend_info` field, not used in new tests.
   *
   * @deprecated
   * @param LoadBalancerInfo $loadBalancer
   */
  public function setLoadBalancer(LoadBalancerInfo $loadBalancer)
  {
    $this->loadBalancer = $loadBalancer;
  }
  /**
   * @deprecated
   * @return LoadBalancerInfo
   */
  public function getLoadBalancer()
  {
    return $this->loadBalancer;
  }
  /**
   * Display information of a specific load balancer backend.
   *
   * @param LoadBalancerBackendInfo $loadBalancerBackendInfo
   */
  public function setLoadBalancerBackendInfo(LoadBalancerBackendInfo $loadBalancerBackendInfo)
  {
    $this->loadBalancerBackendInfo = $loadBalancerBackendInfo;
  }
  /**
   * @return LoadBalancerBackendInfo
   */
  public function getLoadBalancerBackendInfo()
  {
    return $this->loadBalancerBackendInfo;
  }
  /**
   * Display information of a NAT.
   *
   * @param NatInfo $nat
   */
  public function setNat(NatInfo $nat)
  {
    $this->nat = $nat;
  }
  /**
   * @return NatInfo
   */
  public function getNat()
  {
    return $this->nat;
  }
  /**
   * Display information of a Google Cloud network.
   *
   * @param NetworkInfo $network
   */
  public function setNetwork(NetworkInfo $network)
  {
    $this->network = $network;
  }
  /**
   * @return NetworkInfo
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Project ID that contains the configuration this step is validating.
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
   * Display information of a ProxyConnection.
   *
   * @param ProxyConnectionInfo $proxyConnection
   */
  public function setProxyConnection(ProxyConnectionInfo $proxyConnection)
  {
    $this->proxyConnection = $proxyConnection;
  }
  /**
   * @return ProxyConnectionInfo
   */
  public function getProxyConnection()
  {
    return $this->proxyConnection;
  }
  /**
   * Display information of a Redis Cluster.
   *
   * @param RedisClusterInfo $redisCluster
   */
  public function setRedisCluster(RedisClusterInfo $redisCluster)
  {
    $this->redisCluster = $redisCluster;
  }
  /**
   * @return RedisClusterInfo
   */
  public function getRedisCluster()
  {
    return $this->redisCluster;
  }
  /**
   * Display information of a Redis Instance.
   *
   * @param RedisInstanceInfo $redisInstance
   */
  public function setRedisInstance(RedisInstanceInfo $redisInstance)
  {
    $this->redisInstance = $redisInstance;
  }
  /**
   * @return RedisInstanceInfo
   */
  public function getRedisInstance()
  {
    return $this->redisInstance;
  }
  /**
   * Display information of a Compute Engine route.
   *
   * @param RouteInfo $route
   */
  public function setRoute(RouteInfo $route)
  {
    $this->route = $route;
  }
  /**
   * @return RouteInfo
   */
  public function getRoute()
  {
    return $this->route;
  }
  /**
   * Display information of a serverless public (external) connection.
   *
   * @param ServerlessExternalConnectionInfo $serverlessExternalConnection
   */
  public function setServerlessExternalConnection(ServerlessExternalConnectionInfo $serverlessExternalConnection)
  {
    $this->serverlessExternalConnection = $serverlessExternalConnection;
  }
  /**
   * @return ServerlessExternalConnectionInfo
   */
  public function getServerlessExternalConnection()
  {
    return $this->serverlessExternalConnection;
  }
  /**
   * Display information of a Serverless network endpoint group backend. Used
   * only for return traces.
   *
   * @param ServerlessNegInfo $serverlessNeg
   */
  public function setServerlessNeg(ServerlessNegInfo $serverlessNeg)
  {
    $this->serverlessNeg = $serverlessNeg;
  }
  /**
   * @return ServerlessNegInfo
   */
  public function getServerlessNeg()
  {
    return $this->serverlessNeg;
  }
  /**
   * Each step is in one of the pre-defined states.
   *
   * Accepted values: STATE_UNSPECIFIED, START_FROM_INSTANCE,
   * START_FROM_INTERNET, START_FROM_GOOGLE_SERVICE, START_FROM_PRIVATE_NETWORK,
   * START_FROM_GKE_MASTER, START_FROM_CLOUD_SQL_INSTANCE,
   * START_FROM_REDIS_INSTANCE, START_FROM_REDIS_CLUSTER,
   * START_FROM_CLOUD_FUNCTION, START_FROM_APP_ENGINE_VERSION,
   * START_FROM_CLOUD_RUN_REVISION, START_FROM_STORAGE_BUCKET,
   * START_FROM_PSC_PUBLISHED_SERVICE, START_FROM_SERVERLESS_NEG,
   * APPLY_INGRESS_FIREWALL_RULE, APPLY_EGRESS_FIREWALL_RULE, APPLY_ROUTE,
   * APPLY_FORWARDING_RULE, ANALYZE_LOAD_BALANCER_BACKEND, SPOOFING_APPROVED,
   * ARRIVE_AT_INSTANCE, ARRIVE_AT_INTERNAL_LOAD_BALANCER,
   * ARRIVE_AT_EXTERNAL_LOAD_BALANCER, ARRIVE_AT_HYBRID_SUBNET,
   * ARRIVE_AT_VPN_GATEWAY, ARRIVE_AT_VPN_TUNNEL,
   * ARRIVE_AT_INTERCONNECT_ATTACHMENT, ARRIVE_AT_VPC_CONNECTOR,
   * DIRECT_VPC_EGRESS_CONNECTION, SERVERLESS_EXTERNAL_CONNECTION, NAT,
   * PROXY_CONNECTION, DELIVER, DROP, FORWARD, ABORT, VIEWER_PERMISSION_MISSING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Display information of a Storage Bucket. Used only for return traces.
   *
   * @param StorageBucketInfo $storageBucket
   */
  public function setStorageBucket(StorageBucketInfo $storageBucket)
  {
    $this->storageBucket = $storageBucket;
  }
  /**
   * @return StorageBucketInfo
   */
  public function getStorageBucket()
  {
    return $this->storageBucket;
  }
  /**
   * Display information of a VPC connector.
   *
   * @param VpcConnectorInfo $vpcConnector
   */
  public function setVpcConnector(VpcConnectorInfo $vpcConnector)
  {
    $this->vpcConnector = $vpcConnector;
  }
  /**
   * @return VpcConnectorInfo
   */
  public function getVpcConnector()
  {
    return $this->vpcConnector;
  }
  /**
   * Display information of a Compute Engine VPN gateway.
   *
   * @param VpnGatewayInfo $vpnGateway
   */
  public function setVpnGateway(VpnGatewayInfo $vpnGateway)
  {
    $this->vpnGateway = $vpnGateway;
  }
  /**
   * @return VpnGatewayInfo
   */
  public function getVpnGateway()
  {
    return $this->vpnGateway;
  }
  /**
   * Display information of a Compute Engine VPN tunnel.
   *
   * @param VpnTunnelInfo $vpnTunnel
   */
  public function setVpnTunnel(VpnTunnelInfo $vpnTunnel)
  {
    $this->vpnTunnel = $vpnTunnel;
  }
  /**
   * @return VpnTunnelInfo
   */
  public function getVpnTunnel()
  {
    return $this->vpnTunnel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Step::class, 'Google_Service_NetworkManagement_Step');
