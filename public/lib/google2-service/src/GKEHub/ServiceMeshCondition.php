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

namespace Google\Service\GKEHub;

class ServiceMeshCondition extends \Google\Model
{
  /**
   * Default Unspecified code
   */
  public const CODE_CODE_UNSPECIFIED = 'CODE_UNSPECIFIED';
  /**
   * Mesh IAM permission denied error code
   */
  public const CODE_MESH_IAM_PERMISSION_DENIED = 'MESH_IAM_PERMISSION_DENIED';
  /**
   * Permission denied error code for cross-project
   */
  public const CODE_MESH_IAM_CROSS_PROJECT_PERMISSION_DENIED = 'MESH_IAM_CROSS_PROJECT_PERMISSION_DENIED';
  /**
   * CNI config unsupported error code
   */
  public const CODE_CNI_CONFIG_UNSUPPORTED = 'CNI_CONFIG_UNSUPPORTED';
  /**
   * GKE sandbox unsupported error code
   */
  public const CODE_GKE_SANDBOX_UNSUPPORTED = 'GKE_SANDBOX_UNSUPPORTED';
  /**
   * Nodepool workload identity federation required error code
   */
  public const CODE_NODEPOOL_WORKLOAD_IDENTITY_FEDERATION_REQUIRED = 'NODEPOOL_WORKLOAD_IDENTITY_FEDERATION_REQUIRED';
  /**
   * CNI installation failed error code
   */
  public const CODE_CNI_INSTALLATION_FAILED = 'CNI_INSTALLATION_FAILED';
  /**
   * CNI pod unschedulable error code
   */
  public const CODE_CNI_POD_UNSCHEDULABLE = 'CNI_POD_UNSCHEDULABLE';
  /**
   * Cluster has zero node code
   */
  public const CODE_CLUSTER_HAS_ZERO_NODES = 'CLUSTER_HAS_ZERO_NODES';
  /**
   * Failure to reconcile CanonicalServices
   */
  public const CODE_CANONICAL_SERVICE_ERROR = 'CANONICAL_SERVICE_ERROR';
  /**
   * Multiple control planes unsupported error code
   */
  public const CODE_UNSUPPORTED_MULTIPLE_CONTROL_PLANES = 'UNSUPPORTED_MULTIPLE_CONTROL_PLANES';
  /**
   * VPC-SC GA is supported for this control plane.
   */
  public const CODE_VPCSC_GA_SUPPORTED = 'VPCSC_GA_SUPPORTED';
  /**
   * User is using deprecated ControlPlaneManagement and they have not yet set
   * Management.
   */
  public const CODE_DEPRECATED_SPEC_CONTROL_PLANE_MANAGEMENT = 'DEPRECATED_SPEC_CONTROL_PLANE_MANAGEMENT';
  /**
   * User is using deprecated ControlPlaneManagement and they have already set
   * Management.
   */
  public const CODE_DEPRECATED_SPEC_CONTROL_PLANE_MANAGEMENT_SAFE = 'DEPRECATED_SPEC_CONTROL_PLANE_MANAGEMENT_SAFE';
  /**
   * Configuration (Istio/k8s resources) failed to apply due to internal error.
   */
  public const CODE_CONFIG_APPLY_INTERNAL_ERROR = 'CONFIG_APPLY_INTERNAL_ERROR';
  /**
   * Configuration failed to be applied due to being invalid.
   */
  public const CODE_CONFIG_VALIDATION_ERROR = 'CONFIG_VALIDATION_ERROR';
  /**
   * Encountered configuration(s) with possible unintended behavior or invalid
   * configuration. These configs may not have been applied.
   */
  public const CODE_CONFIG_VALIDATION_WARNING = 'CONFIG_VALIDATION_WARNING';
  /**
   * BackendService quota exceeded error code.
   */
  public const CODE_QUOTA_EXCEEDED_BACKEND_SERVICES = 'QUOTA_EXCEEDED_BACKEND_SERVICES';
  /**
   * HealthCheck quota exceeded error code.
   */
  public const CODE_QUOTA_EXCEEDED_HEALTH_CHECKS = 'QUOTA_EXCEEDED_HEALTH_CHECKS';
  /**
   * HTTPRoute quota exceeded error code.
   */
  public const CODE_QUOTA_EXCEEDED_HTTP_ROUTES = 'QUOTA_EXCEEDED_HTTP_ROUTES';
  /**
   * TCPRoute quota exceeded error code.
   */
  public const CODE_QUOTA_EXCEEDED_TCP_ROUTES = 'QUOTA_EXCEEDED_TCP_ROUTES';
  /**
   * TLS routes quota exceeded error code.
   */
  public const CODE_QUOTA_EXCEEDED_TLS_ROUTES = 'QUOTA_EXCEEDED_TLS_ROUTES';
  /**
   * TrafficPolicy quota exceeded error code.
   */
  public const CODE_QUOTA_EXCEEDED_TRAFFIC_POLICIES = 'QUOTA_EXCEEDED_TRAFFIC_POLICIES';
  /**
   * EndpointPolicy quota exceeded error code.
   */
  public const CODE_QUOTA_EXCEEDED_ENDPOINT_POLICIES = 'QUOTA_EXCEEDED_ENDPOINT_POLICIES';
  /**
   * Gateway quota exceeded error code.
   */
  public const CODE_QUOTA_EXCEEDED_GATEWAYS = 'QUOTA_EXCEEDED_GATEWAYS';
  /**
   * Mesh quota exceeded error code.
   */
  public const CODE_QUOTA_EXCEEDED_MESHES = 'QUOTA_EXCEEDED_MESHES';
  /**
   * ServerTLSPolicy quota exceeded error code.
   */
  public const CODE_QUOTA_EXCEEDED_SERVER_TLS_POLICIES = 'QUOTA_EXCEEDED_SERVER_TLS_POLICIES';
  /**
   * ClientTLSPolicy quota exceeded error code.
   */
  public const CODE_QUOTA_EXCEEDED_CLIENT_TLS_POLICIES = 'QUOTA_EXCEEDED_CLIENT_TLS_POLICIES';
  /**
   * ServiceLBPolicy quota exceeded error code.
   */
  public const CODE_QUOTA_EXCEEDED_SERVICE_LB_POLICIES = 'QUOTA_EXCEEDED_SERVICE_LB_POLICIES';
  /**
   * HTTPFilter quota exceeded error code.
   */
  public const CODE_QUOTA_EXCEEDED_HTTP_FILTERS = 'QUOTA_EXCEEDED_HTTP_FILTERS';
  /**
   * TCPFilter quota exceeded error code.
   */
  public const CODE_QUOTA_EXCEEDED_TCP_FILTERS = 'QUOTA_EXCEEDED_TCP_FILTERS';
  /**
   * NetworkEndpointGroup quota exceeded error code.
   */
  public const CODE_QUOTA_EXCEEDED_NETWORK_ENDPOINT_GROUPS = 'QUOTA_EXCEEDED_NETWORK_ENDPOINT_GROUPS';
  /**
   * Legacy istio secrets found for multicluster error code
   */
  public const CODE_LEGACY_MC_SECRETS = 'LEGACY_MC_SECRETS';
  /**
   * Workload identity required error code
   */
  public const CODE_WORKLOAD_IDENTITY_REQUIRED = 'WORKLOAD_IDENTITY_REQUIRED';
  /**
   * Non-standard binary usage error code
   */
  public const CODE_NON_STANDARD_BINARY_USAGE = 'NON_STANDARD_BINARY_USAGE';
  /**
   * Unsupported gateway class error code
   */
  public const CODE_UNSUPPORTED_GATEWAY_CLASS = 'UNSUPPORTED_GATEWAY_CLASS';
  /**
   * Managed CNI not enabled error code
   */
  public const CODE_MANAGED_CNI_NOT_ENABLED = 'MANAGED_CNI_NOT_ENABLED';
  /**
   * Modernization is scheduled for a cluster.
   */
  public const CODE_MODERNIZATION_SCHEDULED = 'MODERNIZATION_SCHEDULED';
  /**
   * Modernization is in progress for a cluster.
   */
  public const CODE_MODERNIZATION_IN_PROGRESS = 'MODERNIZATION_IN_PROGRESS';
  /**
   * Modernization is completed for a cluster.
   */
  public const CODE_MODERNIZATION_COMPLETED = 'MODERNIZATION_COMPLETED';
  /**
   * Modernization is aborted for a cluster.
   */
  public const CODE_MODERNIZATION_ABORTED = 'MODERNIZATION_ABORTED';
  /**
   * Preparing cluster so that its workloads can be migrated.
   */
  public const CODE_MODERNIZATION_PREPARING = 'MODERNIZATION_PREPARING';
  /**
   * Modernization is stalled for a cluster.
   */
  public const CODE_MODERNIZATION_STALLED = 'MODERNIZATION_STALLED';
  /**
   * Cluster has been prepared for its workloads to be migrated.
   */
  public const CODE_MODERNIZATION_PREPARED = 'MODERNIZATION_PREPARED';
  /**
   * Migrating the cluster's workloads to the new implementation.
   */
  public const CODE_MODERNIZATION_MIGRATING_WORKLOADS = 'MODERNIZATION_MIGRATING_WORKLOADS';
  /**
   * Rollback is in progress for modernization of a cluster.
   */
  public const CODE_MODERNIZATION_ROLLING_BACK_CLUSTER = 'MODERNIZATION_ROLLING_BACK_CLUSTER';
  /**
   * Modernization will be scheduled for a fleet.
   */
  public const CODE_MODERNIZATION_WILL_BE_SCHEDULED = 'MODERNIZATION_WILL_BE_SCHEDULED';
  /**
   * Fleet is opted out from automated modernization.
   */
  public const CODE_MODERNIZATION_MANUAL = 'MODERNIZATION_MANUAL';
  /**
   * Fleet is eligible for modernization.
   */
  public const CODE_MODERNIZATION_ELIGIBLE = 'MODERNIZATION_ELIGIBLE';
  /**
   * Modernization of one or more clusters in a fleet is in progress.
   */
  public const CODE_MODERNIZATION_MODERNIZING = 'MODERNIZATION_MODERNIZING';
  /**
   * Modernization of all the fleet's clusters is complete. Soaking before
   * finalizing the modernization.
   */
  public const CODE_MODERNIZATION_MODERNIZED_SOAKING = 'MODERNIZATION_MODERNIZED_SOAKING';
  /**
   * Modernization is finalized for all clusters in a fleet. Rollback is no
   * longer allowed.
   */
  public const CODE_MODERNIZATION_FINALIZED = 'MODERNIZATION_FINALIZED';
  /**
   * Rollback is in progress for modernization of all clusters in a fleet.
   */
  public const CODE_MODERNIZATION_ROLLING_BACK_FLEET = 'MODERNIZATION_ROLLING_BACK_FLEET';
  /**
   * Unspecified severity
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * Indicates an issue that prevents the mesh from operating correctly
   */
  public const SEVERITY_ERROR = 'ERROR';
  /**
   * Indicates a setting is likely wrong, but the mesh is still able to operate
   */
  public const SEVERITY_WARNING = 'WARNING';
  /**
   * An informational message, not requiring any action
   */
  public const SEVERITY_INFO = 'INFO';
  /**
   * Unique identifier of the condition which describes the condition
   * recognizable to the user.
   *
   * @var string
   */
  public $code;
  /**
   * A short summary about the issue.
   *
   * @var string
   */
  public $details;
  /**
   * Links contains actionable information.
   *
   * @var string
   */
  public $documentationLink;
  /**
   * Severity level of the condition.
   *
   * @var string
   */
  public $severity;

  /**
   * Unique identifier of the condition which describes the condition
   * recognizable to the user.
   *
   * Accepted values: CODE_UNSPECIFIED, MESH_IAM_PERMISSION_DENIED,
   * MESH_IAM_CROSS_PROJECT_PERMISSION_DENIED, CNI_CONFIG_UNSUPPORTED,
   * GKE_SANDBOX_UNSUPPORTED, NODEPOOL_WORKLOAD_IDENTITY_FEDERATION_REQUIRED,
   * CNI_INSTALLATION_FAILED, CNI_POD_UNSCHEDULABLE, CLUSTER_HAS_ZERO_NODES,
   * CANONICAL_SERVICE_ERROR, UNSUPPORTED_MULTIPLE_CONTROL_PLANES,
   * VPCSC_GA_SUPPORTED, DEPRECATED_SPEC_CONTROL_PLANE_MANAGEMENT,
   * DEPRECATED_SPEC_CONTROL_PLANE_MANAGEMENT_SAFE, CONFIG_APPLY_INTERNAL_ERROR,
   * CONFIG_VALIDATION_ERROR, CONFIG_VALIDATION_WARNING,
   * QUOTA_EXCEEDED_BACKEND_SERVICES, QUOTA_EXCEEDED_HEALTH_CHECKS,
   * QUOTA_EXCEEDED_HTTP_ROUTES, QUOTA_EXCEEDED_TCP_ROUTES,
   * QUOTA_EXCEEDED_TLS_ROUTES, QUOTA_EXCEEDED_TRAFFIC_POLICIES,
   * QUOTA_EXCEEDED_ENDPOINT_POLICIES, QUOTA_EXCEEDED_GATEWAYS,
   * QUOTA_EXCEEDED_MESHES, QUOTA_EXCEEDED_SERVER_TLS_POLICIES,
   * QUOTA_EXCEEDED_CLIENT_TLS_POLICIES, QUOTA_EXCEEDED_SERVICE_LB_POLICIES,
   * QUOTA_EXCEEDED_HTTP_FILTERS, QUOTA_EXCEEDED_TCP_FILTERS,
   * QUOTA_EXCEEDED_NETWORK_ENDPOINT_GROUPS, LEGACY_MC_SECRETS,
   * WORKLOAD_IDENTITY_REQUIRED, NON_STANDARD_BINARY_USAGE,
   * UNSUPPORTED_GATEWAY_CLASS, MANAGED_CNI_NOT_ENABLED,
   * MODERNIZATION_SCHEDULED, MODERNIZATION_IN_PROGRESS,
   * MODERNIZATION_COMPLETED, MODERNIZATION_ABORTED, MODERNIZATION_PREPARING,
   * MODERNIZATION_STALLED, MODERNIZATION_PREPARED,
   * MODERNIZATION_MIGRATING_WORKLOADS, MODERNIZATION_ROLLING_BACK_CLUSTER,
   * MODERNIZATION_WILL_BE_SCHEDULED, MODERNIZATION_MANUAL,
   * MODERNIZATION_ELIGIBLE, MODERNIZATION_MODERNIZING,
   * MODERNIZATION_MODERNIZED_SOAKING, MODERNIZATION_FINALIZED,
   * MODERNIZATION_ROLLING_BACK_FLEET
   *
   * @param self::CODE_* $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return self::CODE_*
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * A short summary about the issue.
   *
   * @param string $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return string
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * Links contains actionable information.
   *
   * @param string $documentationLink
   */
  public function setDocumentationLink($documentationLink)
  {
    $this->documentationLink = $documentationLink;
  }
  /**
   * @return string
   */
  public function getDocumentationLink()
  {
    return $this->documentationLink;
  }
  /**
   * Severity level of the condition.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, ERROR, WARNING, INFO
   *
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceMeshCondition::class, 'Google_Service_GKEHub_ServiceMeshCondition');
