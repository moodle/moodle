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

class DropInfo extends \Google\Model
{
  /**
   * Cause is unspecified.
   */
  public const CAUSE_CAUSE_UNSPECIFIED = 'CAUSE_UNSPECIFIED';
  /**
   * Destination external address cannot be resolved to a known target. If the
   * address is used in a Google Cloud project, provide the project ID as test
   * input.
   */
  public const CAUSE_UNKNOWN_EXTERNAL_ADDRESS = 'UNKNOWN_EXTERNAL_ADDRESS';
  /**
   * A Compute Engine instance can only send or receive a packet with a foreign
   * IP address if ip_forward is enabled.
   */
  public const CAUSE_FOREIGN_IP_DISALLOWED = 'FOREIGN_IP_DISALLOWED';
  /**
   * Dropped due to a firewall rule, unless allowed due to connection tracking.
   */
  public const CAUSE_FIREWALL_RULE = 'FIREWALL_RULE';
  /**
   * Dropped due to no matching routes.
   */
  public const CAUSE_NO_ROUTE = 'NO_ROUTE';
  /**
   * Dropped due to invalid route. Route's next hop is a blackhole.
   */
  public const CAUSE_ROUTE_BLACKHOLE = 'ROUTE_BLACKHOLE';
  /**
   * Packet is sent to a wrong (unintended) network. Example: you trace a packet
   * from VM1:Network1 to VM2:Network2, however, the route configured in
   * Network1 sends the packet destined for VM2's IP address to Network3.
   */
  public const CAUSE_ROUTE_WRONG_NETWORK = 'ROUTE_WRONG_NETWORK';
  /**
   * Route's next hop IP address cannot be resolved to a GCP resource.
   */
  public const CAUSE_ROUTE_NEXT_HOP_IP_ADDRESS_NOT_RESOLVED = 'ROUTE_NEXT_HOP_IP_ADDRESS_NOT_RESOLVED';
  /**
   * Route's next hop resource is not found.
   */
  public const CAUSE_ROUTE_NEXT_HOP_RESOURCE_NOT_FOUND = 'ROUTE_NEXT_HOP_RESOURCE_NOT_FOUND';
  /**
   * Route's next hop instance doesn't have a NIC in the route's network.
   */
  public const CAUSE_ROUTE_NEXT_HOP_INSTANCE_WRONG_NETWORK = 'ROUTE_NEXT_HOP_INSTANCE_WRONG_NETWORK';
  /**
   * Route's next hop IP address is not a primary IP address of the next hop
   * instance.
   */
  public const CAUSE_ROUTE_NEXT_HOP_INSTANCE_NON_PRIMARY_IP = 'ROUTE_NEXT_HOP_INSTANCE_NON_PRIMARY_IP';
  /**
   * Route's next hop forwarding rule doesn't match next hop IP address.
   */
  public const CAUSE_ROUTE_NEXT_HOP_FORWARDING_RULE_IP_MISMATCH = 'ROUTE_NEXT_HOP_FORWARDING_RULE_IP_MISMATCH';
  /**
   * Route's next hop VPN tunnel is down (does not have valid IKE SAs).
   */
  public const CAUSE_ROUTE_NEXT_HOP_VPN_TUNNEL_NOT_ESTABLISHED = 'ROUTE_NEXT_HOP_VPN_TUNNEL_NOT_ESTABLISHED';
  /**
   * Route's next hop forwarding rule type is invalid (it's not a forwarding
   * rule of the internal passthrough load balancer).
   */
  public const CAUSE_ROUTE_NEXT_HOP_FORWARDING_RULE_TYPE_INVALID = 'ROUTE_NEXT_HOP_FORWARDING_RULE_TYPE_INVALID';
  /**
   * Packet is sent from the Internet or Google service to the private IPv6
   * address.
   */
  public const CAUSE_NO_ROUTE_FROM_INTERNET_TO_PRIVATE_IPV6_ADDRESS = 'NO_ROUTE_FROM_INTERNET_TO_PRIVATE_IPV6_ADDRESS';
  /**
   * Packet is sent from the external IPv6 source address of an instance to the
   * private IPv6 address of an instance.
   */
  public const CAUSE_NO_ROUTE_FROM_EXTERNAL_IPV6_SOURCE_TO_PRIVATE_IPV6_ADDRESS = 'NO_ROUTE_FROM_EXTERNAL_IPV6_SOURCE_TO_PRIVATE_IPV6_ADDRESS';
  /**
   * The packet does not match a policy-based VPN tunnel local selector.
   */
  public const CAUSE_VPN_TUNNEL_LOCAL_SELECTOR_MISMATCH = 'VPN_TUNNEL_LOCAL_SELECTOR_MISMATCH';
  /**
   * The packet does not match a policy-based VPN tunnel remote selector.
   */
  public const CAUSE_VPN_TUNNEL_REMOTE_SELECTOR_MISMATCH = 'VPN_TUNNEL_REMOTE_SELECTOR_MISMATCH';
  /**
   * Packet with internal destination address sent to the internet gateway.
   */
  public const CAUSE_PRIVATE_TRAFFIC_TO_INTERNET = 'PRIVATE_TRAFFIC_TO_INTERNET';
  /**
   * Endpoint with only an internal IP address tries to access Google API and
   * services, but Private Google Access is not enabled in the subnet or is not
   * applicable.
   */
  public const CAUSE_PRIVATE_GOOGLE_ACCESS_DISALLOWED = 'PRIVATE_GOOGLE_ACCESS_DISALLOWED';
  /**
   * Source endpoint tries to access Google API and services through the VPN
   * tunnel to another network, but Private Google Access needs to be enabled in
   * the source endpoint network.
   */
  public const CAUSE_PRIVATE_GOOGLE_ACCESS_VIA_VPN_TUNNEL_UNSUPPORTED = 'PRIVATE_GOOGLE_ACCESS_VIA_VPN_TUNNEL_UNSUPPORTED';
  /**
   * Endpoint with only an internal IP address tries to access external hosts,
   * but there is no matching Cloud NAT gateway in the subnet.
   */
  public const CAUSE_NO_EXTERNAL_ADDRESS = 'NO_EXTERNAL_ADDRESS';
  /**
   * Destination internal address cannot be resolved to a known target. If this
   * is a shared VPC scenario, verify if the service project ID is provided as
   * test input. Otherwise, verify if the IP address is being used in the
   * project.
   */
  public const CAUSE_UNKNOWN_INTERNAL_ADDRESS = 'UNKNOWN_INTERNAL_ADDRESS';
  /**
   * Forwarding rule's protocol and ports do not match the packet header.
   */
  public const CAUSE_FORWARDING_RULE_MISMATCH = 'FORWARDING_RULE_MISMATCH';
  /**
   * Forwarding rule does not have backends configured.
   */
  public const CAUSE_FORWARDING_RULE_NO_INSTANCES = 'FORWARDING_RULE_NO_INSTANCES';
  /**
   * Firewalls block the health check probes to the backends and cause the
   * backends to be unavailable for traffic from the load balancer. For more
   * details, see [Health check firewall rules](https://cloud.google.com/load-
   * balancing/docs/health-checks#firewall_rules).
   */
  public const CAUSE_FIREWALL_BLOCKING_LOAD_BALANCER_BACKEND_HEALTH_CHECK = 'FIREWALL_BLOCKING_LOAD_BALANCER_BACKEND_HEALTH_CHECK';
  /**
   * Matching ingress firewall rules by network tags for packets sent via
   * serverless VPC direct egress is unsupported. Behavior is undefined.
   * https://cloud.google.com/run/docs/configuring/vpc-direct-vpc#limitations
   */
  public const CAUSE_INGRESS_FIREWALL_TAGS_UNSUPPORTED_BY_DIRECT_VPC_EGRESS = 'INGRESS_FIREWALL_TAGS_UNSUPPORTED_BY_DIRECT_VPC_EGRESS';
  /**
   * Packet is sent from or to a Compute Engine instance that is not in a
   * running state.
   */
  public const CAUSE_INSTANCE_NOT_RUNNING = 'INSTANCE_NOT_RUNNING';
  /**
   * Packet sent from or to a GKE cluster that is not in running state.
   */
  public const CAUSE_GKE_CLUSTER_NOT_RUNNING = 'GKE_CLUSTER_NOT_RUNNING';
  /**
   * Packet sent from or to a Cloud SQL instance that is not in running state.
   */
  public const CAUSE_CLOUD_SQL_INSTANCE_NOT_RUNNING = 'CLOUD_SQL_INSTANCE_NOT_RUNNING';
  /**
   * Packet sent from or to a Redis Instance that is not in running state.
   */
  public const CAUSE_REDIS_INSTANCE_NOT_RUNNING = 'REDIS_INSTANCE_NOT_RUNNING';
  /**
   * Packet sent from or to a Redis Cluster that is not in running state.
   */
  public const CAUSE_REDIS_CLUSTER_NOT_RUNNING = 'REDIS_CLUSTER_NOT_RUNNING';
  /**
   * The type of traffic is blocked and the user cannot configure a firewall
   * rule to enable it. See [Always blocked
   * traffic](https://cloud.google.com/vpc/docs/firewalls#blockedtraffic) for
   * more details.
   */
  public const CAUSE_TRAFFIC_TYPE_BLOCKED = 'TRAFFIC_TYPE_BLOCKED';
  /**
   * Access to Google Kubernetes Engine cluster master's endpoint is not
   * authorized. See [Access to the cluster
   * endpoints](https://cloud.google.com/kubernetes-engine/docs/how-to/private-
   * clusters#access_to_the_cluster_endpoints) for more details.
   */
  public const CAUSE_GKE_MASTER_UNAUTHORIZED_ACCESS = 'GKE_MASTER_UNAUTHORIZED_ACCESS';
  /**
   * Access to the Cloud SQL instance endpoint is not authorized. See
   * [Authorizing with authorized
   * networks](https://cloud.google.com/sql/docs/mysql/authorize-networks) for
   * more details.
   */
  public const CAUSE_CLOUD_SQL_INSTANCE_UNAUTHORIZED_ACCESS = 'CLOUD_SQL_INSTANCE_UNAUTHORIZED_ACCESS';
  /**
   * Packet was dropped inside Google Kubernetes Engine Service.
   */
  public const CAUSE_DROPPED_INSIDE_GKE_SERVICE = 'DROPPED_INSIDE_GKE_SERVICE';
  /**
   * Packet was dropped inside Cloud SQL Service.
   */
  public const CAUSE_DROPPED_INSIDE_CLOUD_SQL_SERVICE = 'DROPPED_INSIDE_CLOUD_SQL_SERVICE';
  /**
   * Packet was dropped because there is no peering between the originating
   * network and the Google Managed Services Network.
   */
  public const CAUSE_GOOGLE_MANAGED_SERVICE_NO_PEERING = 'GOOGLE_MANAGED_SERVICE_NO_PEERING';
  /**
   * Packet was dropped because the Google-managed service uses Private Service
   * Connect (PSC), but the PSC endpoint is not found in the project.
   */
  public const CAUSE_GOOGLE_MANAGED_SERVICE_NO_PSC_ENDPOINT = 'GOOGLE_MANAGED_SERVICE_NO_PSC_ENDPOINT';
  /**
   * Packet was dropped because the GKE cluster uses Private Service Connect
   * (PSC), but the PSC endpoint is not found in the project.
   */
  public const CAUSE_GKE_PSC_ENDPOINT_MISSING = 'GKE_PSC_ENDPOINT_MISSING';
  /**
   * Packet was dropped because the Cloud SQL instance has neither a private nor
   * a public IP address.
   */
  public const CAUSE_CLOUD_SQL_INSTANCE_NO_IP_ADDRESS = 'CLOUD_SQL_INSTANCE_NO_IP_ADDRESS';
  /**
   * Packet was dropped because a GKE cluster private endpoint is unreachable
   * from a region different from the cluster's region.
   */
  public const CAUSE_GKE_CONTROL_PLANE_REGION_MISMATCH = 'GKE_CONTROL_PLANE_REGION_MISMATCH';
  /**
   * Packet sent from a public GKE cluster control plane to a private IP
   * address.
   */
  public const CAUSE_PUBLIC_GKE_CONTROL_PLANE_TO_PRIVATE_DESTINATION = 'PUBLIC_GKE_CONTROL_PLANE_TO_PRIVATE_DESTINATION';
  /**
   * Packet was dropped because there is no route from a GKE cluster control
   * plane to a destination network.
   */
  public const CAUSE_GKE_CONTROL_PLANE_NO_ROUTE = 'GKE_CONTROL_PLANE_NO_ROUTE';
  /**
   * Packet sent from a Cloud SQL instance to an external IP address is not
   * allowed. The Cloud SQL instance is not configured to send packets to
   * external IP addresses.
   */
  public const CAUSE_CLOUD_SQL_INSTANCE_NOT_CONFIGURED_FOR_EXTERNAL_TRAFFIC = 'CLOUD_SQL_INSTANCE_NOT_CONFIGURED_FOR_EXTERNAL_TRAFFIC';
  /**
   * Packet sent from a Cloud SQL instance with only a public IP address to a
   * private IP address.
   */
  public const CAUSE_PUBLIC_CLOUD_SQL_INSTANCE_TO_PRIVATE_DESTINATION = 'PUBLIC_CLOUD_SQL_INSTANCE_TO_PRIVATE_DESTINATION';
  /**
   * Packet was dropped because there is no route from a Cloud SQL instance to a
   * destination network.
   */
  public const CAUSE_CLOUD_SQL_INSTANCE_NO_ROUTE = 'CLOUD_SQL_INSTANCE_NO_ROUTE';
  /**
   * Packet was dropped because the Cloud SQL instance requires all connections
   * to use Cloud SQL connectors and to target the Cloud SQL proxy port (3307).
   */
  public const CAUSE_CLOUD_SQL_CONNECTOR_REQUIRED = 'CLOUD_SQL_CONNECTOR_REQUIRED';
  /**
   * Packet could be dropped because the Cloud Function is not in an active
   * status.
   */
  public const CAUSE_CLOUD_FUNCTION_NOT_ACTIVE = 'CLOUD_FUNCTION_NOT_ACTIVE';
  /**
   * Packet could be dropped because no VPC connector is set.
   */
  public const CAUSE_VPC_CONNECTOR_NOT_SET = 'VPC_CONNECTOR_NOT_SET';
  /**
   * Packet could be dropped because the VPC connector is not in a running
   * state.
   */
  public const CAUSE_VPC_CONNECTOR_NOT_RUNNING = 'VPC_CONNECTOR_NOT_RUNNING';
  /**
   * Packet could be dropped because the traffic from the serverless service to
   * the VPC connector is not allowed.
   */
  public const CAUSE_VPC_CONNECTOR_SERVERLESS_TRAFFIC_BLOCKED = 'VPC_CONNECTOR_SERVERLESS_TRAFFIC_BLOCKED';
  /**
   * Packet could be dropped because the health check traffic to the VPC
   * connector is not allowed.
   */
  public const CAUSE_VPC_CONNECTOR_HEALTH_CHECK_TRAFFIC_BLOCKED = 'VPC_CONNECTOR_HEALTH_CHECK_TRAFFIC_BLOCKED';
  /**
   * Packet could be dropped because it was sent from a different region to a
   * regional forwarding without global access.
   */
  public const CAUSE_FORWARDING_RULE_REGION_MISMATCH = 'FORWARDING_RULE_REGION_MISMATCH';
  /**
   * The Private Service Connect endpoint is in a project that is not approved
   * to connect to the service.
   */
  public const CAUSE_PSC_CONNECTION_NOT_ACCEPTED = 'PSC_CONNECTION_NOT_ACCEPTED';
  /**
   * The packet is sent to the Private Service Connect endpoint over the
   * peering, but [it's not
   * supported](https://cloud.google.com/vpc/docs/configure-private-service-
   * connect-services#on-premises).
   */
  public const CAUSE_PSC_ENDPOINT_ACCESSED_FROM_PEERED_NETWORK = 'PSC_ENDPOINT_ACCESSED_FROM_PEERED_NETWORK';
  /**
   * The packet is sent to the Private Service Connect backend (network endpoint
   * group), but the producer PSC forwarding rule does not have global access
   * enabled.
   */
  public const CAUSE_PSC_NEG_PRODUCER_ENDPOINT_NO_GLOBAL_ACCESS = 'PSC_NEG_PRODUCER_ENDPOINT_NO_GLOBAL_ACCESS';
  /**
   * The packet is sent to the Private Service Connect backend (network endpoint
   * group), but the producer PSC forwarding rule has multiple ports specified.
   */
  public const CAUSE_PSC_NEG_PRODUCER_FORWARDING_RULE_MULTIPLE_PORTS = 'PSC_NEG_PRODUCER_FORWARDING_RULE_MULTIPLE_PORTS';
  /**
   * The packet is sent to the Private Service Connect backend (network endpoint
   * group) targeting a Cloud SQL service attachment, but this configuration is
   * not supported.
   */
  public const CAUSE_CLOUD_SQL_PSC_NEG_UNSUPPORTED = 'CLOUD_SQL_PSC_NEG_UNSUPPORTED';
  /**
   * No NAT subnets are defined for the PSC service attachment.
   */
  public const CAUSE_NO_NAT_SUBNETS_FOR_PSC_SERVICE_ATTACHMENT = 'NO_NAT_SUBNETS_FOR_PSC_SERVICE_ATTACHMENT';
  /**
   * PSC endpoint is accessed via NCC, but PSC transitivity configuration is not
   * yet propagated.
   */
  public const CAUSE_PSC_TRANSITIVITY_NOT_PROPAGATED = 'PSC_TRANSITIVITY_NOT_PROPAGATED';
  /**
   * The packet sent from the hybrid NEG proxy matches a non-dynamic route, but
   * such a configuration is not supported.
   */
  public const CAUSE_HYBRID_NEG_NON_DYNAMIC_ROUTE_MATCHED = 'HYBRID_NEG_NON_DYNAMIC_ROUTE_MATCHED';
  /**
   * The packet sent from the hybrid NEG proxy matches a dynamic route with a
   * next hop in a different region, but such a configuration is not supported.
   */
  public const CAUSE_HYBRID_NEG_NON_LOCAL_DYNAMIC_ROUTE_MATCHED = 'HYBRID_NEG_NON_LOCAL_DYNAMIC_ROUTE_MATCHED';
  /**
   * Packet sent from a Cloud Run revision that is not ready.
   */
  public const CAUSE_CLOUD_RUN_REVISION_NOT_READY = 'CLOUD_RUN_REVISION_NOT_READY';
  /**
   * Packet was dropped inside Private Service Connect service producer.
   */
  public const CAUSE_DROPPED_INSIDE_PSC_SERVICE_PRODUCER = 'DROPPED_INSIDE_PSC_SERVICE_PRODUCER';
  /**
   * Packet sent to a load balancer, which requires a proxy-only subnet and the
   * subnet is not found.
   */
  public const CAUSE_LOAD_BALANCER_HAS_NO_PROXY_SUBNET = 'LOAD_BALANCER_HAS_NO_PROXY_SUBNET';
  /**
   * Packet sent to Cloud Nat without active NAT IPs.
   */
  public const CAUSE_CLOUD_NAT_NO_ADDRESSES = 'CLOUD_NAT_NO_ADDRESSES';
  /**
   * Packet is stuck in a routing loop.
   */
  public const CAUSE_ROUTING_LOOP = 'ROUTING_LOOP';
  /**
   * Packet is dropped inside a Google-managed service due to being delivered in
   * return trace to an endpoint that doesn't match the endpoint the packet was
   * sent from in forward trace. Used only for return traces.
   */
  public const CAUSE_DROPPED_INSIDE_GOOGLE_MANAGED_SERVICE = 'DROPPED_INSIDE_GOOGLE_MANAGED_SERVICE';
  /**
   * Packet is dropped due to a load balancer backend instance not having a
   * network interface in the network expected by the load balancer.
   */
  public const CAUSE_LOAD_BALANCER_BACKEND_INVALID_NETWORK = 'LOAD_BALANCER_BACKEND_INVALID_NETWORK';
  /**
   * Packet is dropped due to a backend service named port not being defined on
   * the instance group level.
   */
  public const CAUSE_BACKEND_SERVICE_NAMED_PORT_NOT_DEFINED = 'BACKEND_SERVICE_NAMED_PORT_NOT_DEFINED';
  /**
   * Packet is dropped due to a destination IP range being part of a Private NAT
   * IP range.
   */
  public const CAUSE_DESTINATION_IS_PRIVATE_NAT_IP_RANGE = 'DESTINATION_IS_PRIVATE_NAT_IP_RANGE';
  /**
   * Generic drop cause for a packet being dropped inside a Redis Instance
   * service project.
   */
  public const CAUSE_DROPPED_INSIDE_REDIS_INSTANCE_SERVICE = 'DROPPED_INSIDE_REDIS_INSTANCE_SERVICE';
  /**
   * Packet is dropped due to an unsupported port being used to connect to a
   * Redis Instance. Port 6379 should be used to connect to a Redis Instance.
   */
  public const CAUSE_REDIS_INSTANCE_UNSUPPORTED_PORT = 'REDIS_INSTANCE_UNSUPPORTED_PORT';
  /**
   * Packet is dropped due to connecting from PUPI address to a PSA based Redis
   * Instance.
   */
  public const CAUSE_REDIS_INSTANCE_CONNECTING_FROM_PUPI_ADDRESS = 'REDIS_INSTANCE_CONNECTING_FROM_PUPI_ADDRESS';
  /**
   * Packet is dropped due to no route to the destination network.
   */
  public const CAUSE_REDIS_INSTANCE_NO_ROUTE_TO_DESTINATION_NETWORK = 'REDIS_INSTANCE_NO_ROUTE_TO_DESTINATION_NETWORK';
  /**
   * Redis Instance does not have an external IP address.
   */
  public const CAUSE_REDIS_INSTANCE_NO_EXTERNAL_IP = 'REDIS_INSTANCE_NO_EXTERNAL_IP';
  /**
   * Packet is dropped due to an unsupported protocol being used to connect to a
   * Redis Instance. Only TCP connections are accepted by a Redis Instance.
   */
  public const CAUSE_REDIS_INSTANCE_UNSUPPORTED_PROTOCOL = 'REDIS_INSTANCE_UNSUPPORTED_PROTOCOL';
  /**
   * Generic drop cause for a packet being dropped inside a Redis Cluster
   * service project.
   */
  public const CAUSE_DROPPED_INSIDE_REDIS_CLUSTER_SERVICE = 'DROPPED_INSIDE_REDIS_CLUSTER_SERVICE';
  /**
   * Packet is dropped due to an unsupported port being used to connect to a
   * Redis Cluster. Ports 6379 and 11000 to 13047 should be used to connect to a
   * Redis Cluster.
   */
  public const CAUSE_REDIS_CLUSTER_UNSUPPORTED_PORT = 'REDIS_CLUSTER_UNSUPPORTED_PORT';
  /**
   * Redis Cluster does not have an external IP address.
   */
  public const CAUSE_REDIS_CLUSTER_NO_EXTERNAL_IP = 'REDIS_CLUSTER_NO_EXTERNAL_IP';
  /**
   * Packet is dropped due to an unsupported protocol being used to connect to a
   * Redis Cluster. Only TCP connections are accepted by a Redis Cluster.
   */
  public const CAUSE_REDIS_CLUSTER_UNSUPPORTED_PROTOCOL = 'REDIS_CLUSTER_UNSUPPORTED_PROTOCOL';
  /**
   * Packet from the non-GCP (on-prem) or unknown GCP network is dropped due to
   * the destination IP address not belonging to any IP prefix advertised via
   * BGP by the Cloud Router.
   */
  public const CAUSE_NO_ADVERTISED_ROUTE_TO_GCP_DESTINATION = 'NO_ADVERTISED_ROUTE_TO_GCP_DESTINATION';
  /**
   * Packet from the non-GCP (on-prem) or unknown GCP network is dropped due to
   * the destination IP address not belonging to any IP prefix included to the
   * local traffic selector of the VPN tunnel.
   */
  public const CAUSE_NO_TRAFFIC_SELECTOR_TO_GCP_DESTINATION = 'NO_TRAFFIC_SELECTOR_TO_GCP_DESTINATION';
  /**
   * Packet from the unknown peered network is dropped due to no known route
   * from the source network to the destination IP address.
   */
  public const CAUSE_NO_KNOWN_ROUTE_FROM_PEERED_NETWORK_TO_DESTINATION = 'NO_KNOWN_ROUTE_FROM_PEERED_NETWORK_TO_DESTINATION';
  /**
   * Sending packets processed by the Private NAT Gateways to the Private
   * Service Connect endpoints is not supported.
   */
  public const CAUSE_PRIVATE_NAT_TO_PSC_ENDPOINT_UNSUPPORTED = 'PRIVATE_NAT_TO_PSC_ENDPOINT_UNSUPPORTED';
  /**
   * Packet is sent to the PSC port mapping service, but its destination port
   * does not match any port mapping rules.
   */
  public const CAUSE_PSC_PORT_MAPPING_PORT_MISMATCH = 'PSC_PORT_MAPPING_PORT_MISMATCH';
  /**
   * Sending packets directly to the PSC port mapping service without going
   * through the PSC connection is not supported.
   */
  public const CAUSE_PSC_PORT_MAPPING_WITHOUT_PSC_CONNECTION_UNSUPPORTED = 'PSC_PORT_MAPPING_WITHOUT_PSC_CONNECTION_UNSUPPORTED';
  /**
   * Packet with destination IP address within the reserved NAT64 range is
   * dropped due to matching a route of an unsupported type.
   */
  public const CAUSE_UNSUPPORTED_ROUTE_MATCHED_FOR_NAT64_DESTINATION = 'UNSUPPORTED_ROUTE_MATCHED_FOR_NAT64_DESTINATION';
  /**
   * Packet could be dropped because hybrid endpoint like a VPN gateway or
   * Interconnect is not allowed to send traffic to the Internet.
   */
  public const CAUSE_TRAFFIC_FROM_HYBRID_ENDPOINT_TO_INTERNET_DISALLOWED = 'TRAFFIC_FROM_HYBRID_ENDPOINT_TO_INTERNET_DISALLOWED';
  /**
   * Packet with destination IP address within the reserved NAT64 range is
   * dropped due to no matching NAT gateway in the subnet.
   */
  public const CAUSE_NO_MATCHING_NAT64_GATEWAY = 'NO_MATCHING_NAT64_GATEWAY';
  /**
   * Packet is dropped due to being sent to a backend of a passthrough load
   * balancer that doesn't use the same IP version as the frontend.
   */
  public const CAUSE_LOAD_BALANCER_BACKEND_IP_VERSION_MISMATCH = 'LOAD_BALANCER_BACKEND_IP_VERSION_MISMATCH';
  /**
   * Packet from the unknown NCC network is dropped due to no known route from
   * the source network to the destination IP address.
   */
  public const CAUSE_NO_KNOWN_ROUTE_FROM_NCC_NETWORK_TO_DESTINATION = 'NO_KNOWN_ROUTE_FROM_NCC_NETWORK_TO_DESTINATION';
  /**
   * Packet is dropped by Cloud NAT due to using an unsupported protocol.
   */
  public const CAUSE_CLOUD_NAT_PROTOCOL_UNSUPPORTED = 'CLOUD_NAT_PROTOCOL_UNSUPPORTED';
  /**
   * Packet is dropped due to using an unsupported protocol (any other than UDP)
   * for L2 Interconnect.
   */
  public const CAUSE_L2_INTERCONNECT_UNSUPPORTED_PROTOCOL = 'L2_INTERCONNECT_UNSUPPORTED_PROTOCOL';
  /**
   * Packet is dropped due to using an unsupported port (any other than 6081)
   * for L2 Interconnect.
   */
  public const CAUSE_L2_INTERCONNECT_UNSUPPORTED_PORT = 'L2_INTERCONNECT_UNSUPPORTED_PORT';
  /**
   * Packet is dropped due to destination IP not matching the appliance mapping
   * IPs configured on the L2 Interconnect attachment.
   */
  public const CAUSE_L2_INTERCONNECT_DESTINATION_IP_MISMATCH = 'L2_INTERCONNECT_DESTINATION_IP_MISMATCH';
  /**
   * Packet could be dropped because it matches a route associated with an NCC
   * spoke in the hybrid subnet context, but such a configuration is not
   * supported.
   */
  public const CAUSE_NCC_ROUTE_WITHIN_HYBRID_SUBNET_UNSUPPORTED = 'NCC_ROUTE_WITHIN_HYBRID_SUBNET_UNSUPPORTED';
  /**
   * Packet is dropped because the region of the hybrid subnet is different from
   * the region of the next hop of the route matched within this hybrid subnet.
   */
  public const CAUSE_HYBRID_SUBNET_REGION_MISMATCH = 'HYBRID_SUBNET_REGION_MISMATCH';
  /**
   * Packet is dropped because no matching route was found in the hybrid subnet.
   */
  public const CAUSE_HYBRID_SUBNET_NO_ROUTE = 'HYBRID_SUBNET_NO_ROUTE';
  /**
   * Cause that the packet is dropped.
   *
   * @var string
   */
  public $cause;
  /**
   * Geolocation (region code) of the destination IP address (if relevant).
   *
   * @var string
   */
  public $destinationGeolocationCode;
  /**
   * Destination IP address of the dropped packet (if relevant).
   *
   * @var string
   */
  public $destinationIp;
  /**
   * Region of the dropped packet (if relevant).
   *
   * @var string
   */
  public $region;
  /**
   * URI of the resource that caused the drop.
   *
   * @var string
   */
  public $resourceUri;
  /**
   * Geolocation (region code) of the source IP address (if relevant).
   *
   * @var string
   */
  public $sourceGeolocationCode;
  /**
   * Source IP address of the dropped packet (if relevant).
   *
   * @var string
   */
  public $sourceIp;

  /**
   * Cause that the packet is dropped.
   *
   * Accepted values: CAUSE_UNSPECIFIED, UNKNOWN_EXTERNAL_ADDRESS,
   * FOREIGN_IP_DISALLOWED, FIREWALL_RULE, NO_ROUTE, ROUTE_BLACKHOLE,
   * ROUTE_WRONG_NETWORK, ROUTE_NEXT_HOP_IP_ADDRESS_NOT_RESOLVED,
   * ROUTE_NEXT_HOP_RESOURCE_NOT_FOUND, ROUTE_NEXT_HOP_INSTANCE_WRONG_NETWORK,
   * ROUTE_NEXT_HOP_INSTANCE_NON_PRIMARY_IP,
   * ROUTE_NEXT_HOP_FORWARDING_RULE_IP_MISMATCH,
   * ROUTE_NEXT_HOP_VPN_TUNNEL_NOT_ESTABLISHED,
   * ROUTE_NEXT_HOP_FORWARDING_RULE_TYPE_INVALID,
   * NO_ROUTE_FROM_INTERNET_TO_PRIVATE_IPV6_ADDRESS,
   * NO_ROUTE_FROM_EXTERNAL_IPV6_SOURCE_TO_PRIVATE_IPV6_ADDRESS,
   * VPN_TUNNEL_LOCAL_SELECTOR_MISMATCH, VPN_TUNNEL_REMOTE_SELECTOR_MISMATCH,
   * PRIVATE_TRAFFIC_TO_INTERNET, PRIVATE_GOOGLE_ACCESS_DISALLOWED,
   * PRIVATE_GOOGLE_ACCESS_VIA_VPN_TUNNEL_UNSUPPORTED, NO_EXTERNAL_ADDRESS,
   * UNKNOWN_INTERNAL_ADDRESS, FORWARDING_RULE_MISMATCH,
   * FORWARDING_RULE_NO_INSTANCES,
   * FIREWALL_BLOCKING_LOAD_BALANCER_BACKEND_HEALTH_CHECK,
   * INGRESS_FIREWALL_TAGS_UNSUPPORTED_BY_DIRECT_VPC_EGRESS,
   * INSTANCE_NOT_RUNNING, GKE_CLUSTER_NOT_RUNNING,
   * CLOUD_SQL_INSTANCE_NOT_RUNNING, REDIS_INSTANCE_NOT_RUNNING,
   * REDIS_CLUSTER_NOT_RUNNING, TRAFFIC_TYPE_BLOCKED,
   * GKE_MASTER_UNAUTHORIZED_ACCESS, CLOUD_SQL_INSTANCE_UNAUTHORIZED_ACCESS,
   * DROPPED_INSIDE_GKE_SERVICE, DROPPED_INSIDE_CLOUD_SQL_SERVICE,
   * GOOGLE_MANAGED_SERVICE_NO_PEERING, GOOGLE_MANAGED_SERVICE_NO_PSC_ENDPOINT,
   * GKE_PSC_ENDPOINT_MISSING, CLOUD_SQL_INSTANCE_NO_IP_ADDRESS,
   * GKE_CONTROL_PLANE_REGION_MISMATCH,
   * PUBLIC_GKE_CONTROL_PLANE_TO_PRIVATE_DESTINATION,
   * GKE_CONTROL_PLANE_NO_ROUTE,
   * CLOUD_SQL_INSTANCE_NOT_CONFIGURED_FOR_EXTERNAL_TRAFFIC,
   * PUBLIC_CLOUD_SQL_INSTANCE_TO_PRIVATE_DESTINATION,
   * CLOUD_SQL_INSTANCE_NO_ROUTE, CLOUD_SQL_CONNECTOR_REQUIRED,
   * CLOUD_FUNCTION_NOT_ACTIVE, VPC_CONNECTOR_NOT_SET,
   * VPC_CONNECTOR_NOT_RUNNING, VPC_CONNECTOR_SERVERLESS_TRAFFIC_BLOCKED,
   * VPC_CONNECTOR_HEALTH_CHECK_TRAFFIC_BLOCKED,
   * FORWARDING_RULE_REGION_MISMATCH, PSC_CONNECTION_NOT_ACCEPTED,
   * PSC_ENDPOINT_ACCESSED_FROM_PEERED_NETWORK,
   * PSC_NEG_PRODUCER_ENDPOINT_NO_GLOBAL_ACCESS,
   * PSC_NEG_PRODUCER_FORWARDING_RULE_MULTIPLE_PORTS,
   * CLOUD_SQL_PSC_NEG_UNSUPPORTED, NO_NAT_SUBNETS_FOR_PSC_SERVICE_ATTACHMENT,
   * PSC_TRANSITIVITY_NOT_PROPAGATED, HYBRID_NEG_NON_DYNAMIC_ROUTE_MATCHED,
   * HYBRID_NEG_NON_LOCAL_DYNAMIC_ROUTE_MATCHED, CLOUD_RUN_REVISION_NOT_READY,
   * DROPPED_INSIDE_PSC_SERVICE_PRODUCER, LOAD_BALANCER_HAS_NO_PROXY_SUBNET,
   * CLOUD_NAT_NO_ADDRESSES, ROUTING_LOOP,
   * DROPPED_INSIDE_GOOGLE_MANAGED_SERVICE,
   * LOAD_BALANCER_BACKEND_INVALID_NETWORK,
   * BACKEND_SERVICE_NAMED_PORT_NOT_DEFINED,
   * DESTINATION_IS_PRIVATE_NAT_IP_RANGE, DROPPED_INSIDE_REDIS_INSTANCE_SERVICE,
   * REDIS_INSTANCE_UNSUPPORTED_PORT,
   * REDIS_INSTANCE_CONNECTING_FROM_PUPI_ADDRESS,
   * REDIS_INSTANCE_NO_ROUTE_TO_DESTINATION_NETWORK,
   * REDIS_INSTANCE_NO_EXTERNAL_IP, REDIS_INSTANCE_UNSUPPORTED_PROTOCOL,
   * DROPPED_INSIDE_REDIS_CLUSTER_SERVICE, REDIS_CLUSTER_UNSUPPORTED_PORT,
   * REDIS_CLUSTER_NO_EXTERNAL_IP, REDIS_CLUSTER_UNSUPPORTED_PROTOCOL,
   * NO_ADVERTISED_ROUTE_TO_GCP_DESTINATION,
   * NO_TRAFFIC_SELECTOR_TO_GCP_DESTINATION,
   * NO_KNOWN_ROUTE_FROM_PEERED_NETWORK_TO_DESTINATION,
   * PRIVATE_NAT_TO_PSC_ENDPOINT_UNSUPPORTED, PSC_PORT_MAPPING_PORT_MISMATCH,
   * PSC_PORT_MAPPING_WITHOUT_PSC_CONNECTION_UNSUPPORTED,
   * UNSUPPORTED_ROUTE_MATCHED_FOR_NAT64_DESTINATION,
   * TRAFFIC_FROM_HYBRID_ENDPOINT_TO_INTERNET_DISALLOWED,
   * NO_MATCHING_NAT64_GATEWAY, LOAD_BALANCER_BACKEND_IP_VERSION_MISMATCH,
   * NO_KNOWN_ROUTE_FROM_NCC_NETWORK_TO_DESTINATION,
   * CLOUD_NAT_PROTOCOL_UNSUPPORTED, L2_INTERCONNECT_UNSUPPORTED_PROTOCOL,
   * L2_INTERCONNECT_UNSUPPORTED_PORT, L2_INTERCONNECT_DESTINATION_IP_MISMATCH,
   * NCC_ROUTE_WITHIN_HYBRID_SUBNET_UNSUPPORTED, HYBRID_SUBNET_REGION_MISMATCH,
   * HYBRID_SUBNET_NO_ROUTE
   *
   * @param self::CAUSE_* $cause
   */
  public function setCause($cause)
  {
    $this->cause = $cause;
  }
  /**
   * @return self::CAUSE_*
   */
  public function getCause()
  {
    return $this->cause;
  }
  /**
   * Geolocation (region code) of the destination IP address (if relevant).
   *
   * @param string $destinationGeolocationCode
   */
  public function setDestinationGeolocationCode($destinationGeolocationCode)
  {
    $this->destinationGeolocationCode = $destinationGeolocationCode;
  }
  /**
   * @return string
   */
  public function getDestinationGeolocationCode()
  {
    return $this->destinationGeolocationCode;
  }
  /**
   * Destination IP address of the dropped packet (if relevant).
   *
   * @param string $destinationIp
   */
  public function setDestinationIp($destinationIp)
  {
    $this->destinationIp = $destinationIp;
  }
  /**
   * @return string
   */
  public function getDestinationIp()
  {
    return $this->destinationIp;
  }
  /**
   * Region of the dropped packet (if relevant).
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
   * URI of the resource that caused the drop.
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
   * Geolocation (region code) of the source IP address (if relevant).
   *
   * @param string $sourceGeolocationCode
   */
  public function setSourceGeolocationCode($sourceGeolocationCode)
  {
    $this->sourceGeolocationCode = $sourceGeolocationCode;
  }
  /**
   * @return string
   */
  public function getSourceGeolocationCode()
  {
    return $this->sourceGeolocationCode;
  }
  /**
   * Source IP address of the dropped packet (if relevant).
   *
   * @param string $sourceIp
   */
  public function setSourceIp($sourceIp)
  {
    $this->sourceIp = $sourceIp;
  }
  /**
   * @return string
   */
  public function getSourceIp()
  {
    return $this->sourceIp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DropInfo::class, 'Google_Service_NetworkManagement_DropInfo');
