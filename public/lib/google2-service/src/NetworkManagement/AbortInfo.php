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

class AbortInfo extends \Google\Collection
{
  /**
   * Cause is unspecified.
   */
  public const CAUSE_CAUSE_UNSPECIFIED = 'CAUSE_UNSPECIFIED';
  /**
   * Aborted due to unknown network. Deprecated, not used in the new tests.
   *
   * @deprecated
   */
  public const CAUSE_UNKNOWN_NETWORK = 'UNKNOWN_NETWORK';
  /**
   * Aborted because no project information can be derived from the test input.
   * Deprecated, not used in the new tests.
   *
   * @deprecated
   */
  public const CAUSE_UNKNOWN_PROJECT = 'UNKNOWN_PROJECT';
  /**
   * Aborted because traffic is sent from a public IP to an instance without an
   * external IP. Deprecated, not used in the new tests.
   *
   * @deprecated
   */
  public const CAUSE_NO_EXTERNAL_IP = 'NO_EXTERNAL_IP';
  /**
   * Aborted because none of the traces matches destination information
   * specified in the input test request. Deprecated, not used in the new tests.
   *
   * @deprecated
   */
  public const CAUSE_UNINTENDED_DESTINATION = 'UNINTENDED_DESTINATION';
  /**
   * Aborted because the source endpoint could not be found. Deprecated, not
   * used in the new tests.
   *
   * @deprecated
   */
  public const CAUSE_SOURCE_ENDPOINT_NOT_FOUND = 'SOURCE_ENDPOINT_NOT_FOUND';
  /**
   * Aborted because the source network does not match the source endpoint.
   * Deprecated, not used in the new tests.
   *
   * @deprecated
   */
  public const CAUSE_MISMATCHED_SOURCE_NETWORK = 'MISMATCHED_SOURCE_NETWORK';
  /**
   * Aborted because the destination endpoint could not be found. Deprecated,
   * not used in the new tests.
   *
   * @deprecated
   */
  public const CAUSE_DESTINATION_ENDPOINT_NOT_FOUND = 'DESTINATION_ENDPOINT_NOT_FOUND';
  /**
   * Aborted because the destination network does not match the destination
   * endpoint. Deprecated, not used in the new tests.
   *
   * @deprecated
   */
  public const CAUSE_MISMATCHED_DESTINATION_NETWORK = 'MISMATCHED_DESTINATION_NETWORK';
  /**
   * Aborted because no endpoint with the packet's destination IP address is
   * found.
   */
  public const CAUSE_UNKNOWN_IP = 'UNKNOWN_IP';
  /**
   * Aborted because no endpoint with the packet's destination IP is found in
   * the Google-managed project.
   */
  public const CAUSE_GOOGLE_MANAGED_SERVICE_UNKNOWN_IP = 'GOOGLE_MANAGED_SERVICE_UNKNOWN_IP';
  /**
   * Aborted because the source IP address doesn't belong to any of the subnets
   * of the source VPC network.
   */
  public const CAUSE_SOURCE_IP_ADDRESS_NOT_IN_SOURCE_NETWORK = 'SOURCE_IP_ADDRESS_NOT_IN_SOURCE_NETWORK';
  /**
   * Aborted because user lacks permission to access all or part of the network
   * configurations required to run the test.
   */
  public const CAUSE_PERMISSION_DENIED = 'PERMISSION_DENIED';
  /**
   * Aborted because user lacks permission to access Cloud NAT configs required
   * to run the test.
   */
  public const CAUSE_PERMISSION_DENIED_NO_CLOUD_NAT_CONFIGS = 'PERMISSION_DENIED_NO_CLOUD_NAT_CONFIGS';
  /**
   * Aborted because user lacks permission to access Network endpoint group
   * endpoint configs required to run the test.
   */
  public const CAUSE_PERMISSION_DENIED_NO_NEG_ENDPOINT_CONFIGS = 'PERMISSION_DENIED_NO_NEG_ENDPOINT_CONFIGS';
  /**
   * Aborted because user lacks permission to access Cloud Router configs
   * required to run the test.
   */
  public const CAUSE_PERMISSION_DENIED_NO_CLOUD_ROUTER_CONFIGS = 'PERMISSION_DENIED_NO_CLOUD_ROUTER_CONFIGS';
  /**
   * Aborted because no valid source or destination endpoint is derived from the
   * input test request.
   */
  public const CAUSE_NO_SOURCE_LOCATION = 'NO_SOURCE_LOCATION';
  /**
   * Aborted because the source or destination endpoint specified in the request
   * is invalid. Some examples: - The request might contain malformed resource
   * URI, project ID, or IP address. - The request might contain inconsistent
   * information (for example, the request might include both the instance and
   * the network, but the instance might not have a NIC in that network).
   */
  public const CAUSE_INVALID_ARGUMENT = 'INVALID_ARGUMENT';
  /**
   * Aborted because the number of steps in the trace exceeds a certain limit.
   * It might be caused by a routing loop.
   */
  public const CAUSE_TRACE_TOO_LONG = 'TRACE_TOO_LONG';
  /**
   * Aborted due to internal server error.
   */
  public const CAUSE_INTERNAL_ERROR = 'INTERNAL_ERROR';
  /**
   * Aborted because the test scenario is not supported.
   */
  public const CAUSE_UNSUPPORTED = 'UNSUPPORTED';
  /**
   * Aborted because the source and destination resources have no common IP
   * version.
   */
  public const CAUSE_MISMATCHED_IP_VERSION = 'MISMATCHED_IP_VERSION';
  /**
   * Aborted because the connection between the control plane and the node of
   * the source cluster is initiated by the node and managed by the Konnectivity
   * proxy.
   */
  public const CAUSE_GKE_KONNECTIVITY_PROXY_UNSUPPORTED = 'GKE_KONNECTIVITY_PROXY_UNSUPPORTED';
  /**
   * Aborted because expected resource configuration was missing.
   */
  public const CAUSE_RESOURCE_CONFIG_NOT_FOUND = 'RESOURCE_CONFIG_NOT_FOUND';
  /**
   * Aborted because expected VM instance configuration was missing.
   */
  public const CAUSE_VM_INSTANCE_CONFIG_NOT_FOUND = 'VM_INSTANCE_CONFIG_NOT_FOUND';
  /**
   * Aborted because expected network configuration was missing.
   */
  public const CAUSE_NETWORK_CONFIG_NOT_FOUND = 'NETWORK_CONFIG_NOT_FOUND';
  /**
   * Aborted because expected firewall configuration was missing.
   */
  public const CAUSE_FIREWALL_CONFIG_NOT_FOUND = 'FIREWALL_CONFIG_NOT_FOUND';
  /**
   * Aborted because expected route configuration was missing.
   */
  public const CAUSE_ROUTE_CONFIG_NOT_FOUND = 'ROUTE_CONFIG_NOT_FOUND';
  /**
   * Aborted because PSC endpoint selection for the Google-managed service is
   * ambiguous (several PSC endpoints satisfy test input).
   */
  public const CAUSE_GOOGLE_MANAGED_SERVICE_AMBIGUOUS_PSC_ENDPOINT = 'GOOGLE_MANAGED_SERVICE_AMBIGUOUS_PSC_ENDPOINT';
  /**
   * Aborted because endpoint selection for the Google-managed service is
   * ambiguous (several endpoints satisfy test input).
   */
  public const CAUSE_GOOGLE_MANAGED_SERVICE_AMBIGUOUS_ENDPOINT = 'GOOGLE_MANAGED_SERVICE_AMBIGUOUS_ENDPOINT';
  /**
   * Aborted because tests with a PSC-based Cloud SQL instance as a source are
   * not supported.
   */
  public const CAUSE_SOURCE_PSC_CLOUD_SQL_UNSUPPORTED = 'SOURCE_PSC_CLOUD_SQL_UNSUPPORTED';
  /**
   * Aborted because tests with a Redis Cluster as a source are not supported.
   */
  public const CAUSE_SOURCE_REDIS_CLUSTER_UNSUPPORTED = 'SOURCE_REDIS_CLUSTER_UNSUPPORTED';
  /**
   * Aborted because tests with a Redis Instance as a source are not supported.
   */
  public const CAUSE_SOURCE_REDIS_INSTANCE_UNSUPPORTED = 'SOURCE_REDIS_INSTANCE_UNSUPPORTED';
  /**
   * Aborted because tests with a forwarding rule as a source are not supported.
   */
  public const CAUSE_SOURCE_FORWARDING_RULE_UNSUPPORTED = 'SOURCE_FORWARDING_RULE_UNSUPPORTED';
  /**
   * Aborted because one of the endpoints is a non-routable IP address
   * (loopback, link-local, etc).
   */
  public const CAUSE_NON_ROUTABLE_IP_ADDRESS = 'NON_ROUTABLE_IP_ADDRESS';
  /**
   * Aborted due to an unknown issue in the Google-managed project.
   */
  public const CAUSE_UNKNOWN_ISSUE_IN_GOOGLE_MANAGED_PROJECT = 'UNKNOWN_ISSUE_IN_GOOGLE_MANAGED_PROJECT';
  /**
   * Aborted due to an unsupported configuration of the Google-managed project.
   */
  public const CAUSE_UNSUPPORTED_GOOGLE_MANAGED_PROJECT_CONFIG = 'UNSUPPORTED_GOOGLE_MANAGED_PROJECT_CONFIG';
  /**
   * Aborted because the source endpoint is a Cloud Run revision with direct VPC
   * access enabled, but there are no reserved serverless IP ranges.
   */
  public const CAUSE_NO_SERVERLESS_IP_RANGES = 'NO_SERVERLESS_IP_RANGES';
  /**
   * Aborted because the used protocol is not supported for the used IP version.
   */
  public const CAUSE_IP_VERSION_PROTOCOL_MISMATCH = 'IP_VERSION_PROTOCOL_MISMATCH';
  protected $collection_key = 'projectsMissingPermission';
  /**
   * Causes that the analysis is aborted.
   *
   * @var string
   */
  public $cause;
  /**
   * IP address that caused the abort.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * List of project IDs the user specified in the request but lacks access to.
   * In this case, analysis is aborted with the PERMISSION_DENIED cause.
   *
   * @var string[]
   */
  public $projectsMissingPermission;
  /**
   * URI of the resource that caused the abort.
   *
   * @var string
   */
  public $resourceUri;

  /**
   * Causes that the analysis is aborted.
   *
   * Accepted values: CAUSE_UNSPECIFIED, UNKNOWN_NETWORK, UNKNOWN_PROJECT,
   * NO_EXTERNAL_IP, UNINTENDED_DESTINATION, SOURCE_ENDPOINT_NOT_FOUND,
   * MISMATCHED_SOURCE_NETWORK, DESTINATION_ENDPOINT_NOT_FOUND,
   * MISMATCHED_DESTINATION_NETWORK, UNKNOWN_IP,
   * GOOGLE_MANAGED_SERVICE_UNKNOWN_IP, SOURCE_IP_ADDRESS_NOT_IN_SOURCE_NETWORK,
   * PERMISSION_DENIED, PERMISSION_DENIED_NO_CLOUD_NAT_CONFIGS,
   * PERMISSION_DENIED_NO_NEG_ENDPOINT_CONFIGS,
   * PERMISSION_DENIED_NO_CLOUD_ROUTER_CONFIGS, NO_SOURCE_LOCATION,
   * INVALID_ARGUMENT, TRACE_TOO_LONG, INTERNAL_ERROR, UNSUPPORTED,
   * MISMATCHED_IP_VERSION, GKE_KONNECTIVITY_PROXY_UNSUPPORTED,
   * RESOURCE_CONFIG_NOT_FOUND, VM_INSTANCE_CONFIG_NOT_FOUND,
   * NETWORK_CONFIG_NOT_FOUND, FIREWALL_CONFIG_NOT_FOUND,
   * ROUTE_CONFIG_NOT_FOUND, GOOGLE_MANAGED_SERVICE_AMBIGUOUS_PSC_ENDPOINT,
   * GOOGLE_MANAGED_SERVICE_AMBIGUOUS_ENDPOINT,
   * SOURCE_PSC_CLOUD_SQL_UNSUPPORTED, SOURCE_REDIS_CLUSTER_UNSUPPORTED,
   * SOURCE_REDIS_INSTANCE_UNSUPPORTED, SOURCE_FORWARDING_RULE_UNSUPPORTED,
   * NON_ROUTABLE_IP_ADDRESS, UNKNOWN_ISSUE_IN_GOOGLE_MANAGED_PROJECT,
   * UNSUPPORTED_GOOGLE_MANAGED_PROJECT_CONFIG, NO_SERVERLESS_IP_RANGES,
   * IP_VERSION_PROTOCOL_MISMATCH
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
   * IP address that caused the abort.
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
   * List of project IDs the user specified in the request but lacks access to.
   * In this case, analysis is aborted with the PERMISSION_DENIED cause.
   *
   * @param string[] $projectsMissingPermission
   */
  public function setProjectsMissingPermission($projectsMissingPermission)
  {
    $this->projectsMissingPermission = $projectsMissingPermission;
  }
  /**
   * @return string[]
   */
  public function getProjectsMissingPermission()
  {
    return $this->projectsMissingPermission;
  }
  /**
   * URI of the resource that caused the abort.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AbortInfo::class, 'Google_Service_NetworkManagement_AbortInfo');
