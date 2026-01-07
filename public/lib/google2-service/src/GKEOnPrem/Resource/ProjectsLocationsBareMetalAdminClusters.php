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

namespace Google\Service\GKEOnPrem\Resource;

use Google\Service\GKEOnPrem\BareMetalAdminCluster;
use Google\Service\GKEOnPrem\EnrollBareMetalAdminClusterRequest;
use Google\Service\GKEOnPrem\ListBareMetalAdminClustersResponse;
use Google\Service\GKEOnPrem\Operation;
use Google\Service\GKEOnPrem\Policy;
use Google\Service\GKEOnPrem\QueryBareMetalAdminVersionConfigResponse;
use Google\Service\GKEOnPrem\SetIamPolicyRequest;
use Google\Service\GKEOnPrem\TestIamPermissionsRequest;
use Google\Service\GKEOnPrem\TestIamPermissionsResponse;

/**
 * The "bareMetalAdminClusters" collection of methods.
 * Typical usage is:
 *  <code>
 *   $gkeonpremService = new Google\Service\GKEOnPrem(...);
 *   $bareMetalAdminClusters = $gkeonpremService->projects_locations_bareMetalAdminClusters;
 *  </code>
 */
class ProjectsLocationsBareMetalAdminClusters extends \Google\Service\Resource
{
  /**
   * Creates a new bare metal admin cluster in a given project and location. The
   * API needs to be combined with creating a bootstrap cluster to work. See:
   * https://cloud.google.com/anthos/clusters/docs/bare-
   * metal/latest/installing/creating-clusters/create-admin-cluster-
   * api#prepare_bootstrap_environment (bareMetalAdminClusters.create)
   *
   * @param string $parent Required. The parent of the project and location where
   * the cluster is created in. Format: "projects/{project}/locations/{location}"
   * @param BareMetalAdminCluster $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowPreflightFailure Optional. If set to true, CLM will
   * force CCFE to persist the cluster resource in RMS when the creation fails
   * during standalone preflight checks. In that case the subsequent create call
   * will fail with "cluster already exists" error and hence a update cluster is
   * required to fix the cluster.
   * @opt_param string bareMetalAdminClusterId Required. User provided identifier
   * that is used as part of the resource name; must conform to RFC-1034 and
   * additionally restrict to lower-cased letters. This comes out roughly to:
   * /^a-z+[a-z0-9]$/
   * @opt_param bool validateOnly Validate the request without actually doing any
   * updates.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, BareMetalAdminCluster $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Enrolls an existing bare metal admin cluster to the Anthos On-Prem API within
   * a given project and location. Through enrollment, an existing admin cluster
   * will become Anthos On-Prem API managed. The corresponding GCP resources will
   * be created and all future modifications to the cluster will be expected to be
   * performed through the API. (bareMetalAdminClusters.enroll)
   *
   * @param string $parent Required. The parent of the project and location where
   * the cluster is enrolled in. Format: "projects/{project}/locations/{location}"
   * @param EnrollBareMetalAdminClusterRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function enroll($parent, EnrollBareMetalAdminClusterRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('enroll', [$params], Operation::class);
  }
  /**
   * Gets details of a single bare metal admin cluster.
   * (bareMetalAdminClusters.get)
   *
   * @param string $name Required. Name of the bare metal admin cluster to get.
   * Format: "projects/{project}/locations/{location}/bareMetalAdminClusters/{bare
   * _metal_admin_cluster}"
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If true, return BareMetal Admin
   * Cluster including the one that only exists in RMS.
   * @opt_param string view View for bare metal admin cluster. When `BASIC` is
   * specified, only the cluster resource name and membership are returned. The
   * default/unset value `CLUSTER_VIEW_UNSPECIFIED` is the same as `FULL', which
   * returns the complete cluster configuration details.
   * @return BareMetalAdminCluster
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], BareMetalAdminCluster::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set.
   * (bareMetalAdminClusters.getIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int options.requestedPolicyVersion Optional. The maximum policy
   * version that will be used to format the policy. Valid values are 0, 1, and 3.
   * Requests specifying an invalid value will be rejected. Requests for policies
   * with any conditional role bindings must specify version 3. Policies with no
   * conditional role bindings may specify any valid value or leave the field
   * unset. The policy in the response might use the policy version that you
   * specified, or it might use a lower policy version. For example, if you
   * specify version 3, but the policy has no conditional role bindings, the
   * response uses version 1. To learn which resources support conditions in their
   * IAM policies, see the [IAM
   * documentation](https://cloud.google.com/iam/help/conditions/resource-
   * policies).
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function getIamPolicy($resource, $optParams = [])
  {
    $params = ['resource' => $resource];
    $params = array_merge($params, $optParams);
    return $this->call('getIamPolicy', [$params], Policy::class);
  }
  /**
   * Lists bare metal admin clusters in a given project and location.
   * (bareMetalAdminClusters.listProjectsLocationsBareMetalAdminClusters)
   *
   * @param string $parent Required. The parent of the project and location where
   * the clusters are listed in. Format: "projects/{project}/locations/{location}"
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If true, return list of BareMetal
   * Admin Clusters including the ones that only exists in RMS.
   * @opt_param int pageSize Requested page size. Server may return fewer items
   * than requested. If unspecified, at most 50 clusters will be returned. The
   * maximum value is 1000; values above 1000 will be coerced to 1000.
   * @opt_param string pageToken A token identifying a page of results the server
   * should return.
   * @opt_param string view View for bare metal admin clusters. When `BASIC` is
   * specified, only the admin cluster resource name and membership are returned.
   * The default/unset value `CLUSTER_VIEW_UNSPECIFIED` is the same as `FULL',
   * which returns the complete admin cluster configuration details.
   * @return ListBareMetalAdminClustersResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsBareMetalAdminClusters($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListBareMetalAdminClustersResponse::class);
  }
  /**
   * Updates the parameters of a single bare metal admin cluster.
   * (bareMetalAdminClusters.patch)
   *
   * @param string $name Immutable. The bare metal admin cluster resource name.
   * @param BareMetalAdminCluster $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the BareMetalAdminCluster resource by the update.
   * The fields specified in the update_mask are relative to the resource, not the
   * full request. A field will be overwritten if it is in the mask. If the user
   * does not provide a mask then all populated fields in the
   * BareMetalAdminCluster message will be updated. Empty fields will be ignored
   * unless a field mask is used.
   * @opt_param bool validateOnly Validate the request without actually doing any
   * updates.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, BareMetalAdminCluster $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Queries the bare metal admin cluster version config.
   * (bareMetalAdminClusters.queryVersionConfig)
   *
   * @param string $parent Required. The parent of the project and location to
   * query for version config. Format: "projects/{project}/locations/{location}"
   * @param array $optParams Optional parameters.
   *
   * @opt_param string upgradeConfig.clusterName The admin cluster resource name.
   * This is the full resource name of the admin cluster resource. Format: "projec
   * ts/{project}/locations/{location}/bareMetalAdminClusters/{bare_metal_admin_cl
   * uster}"
   * @return QueryBareMetalAdminVersionConfigResponse
   * @throws \Google\Service\Exception
   */
  public function queryVersionConfig($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('queryVersionConfig', [$params], QueryBareMetalAdminVersionConfigResponse::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (bareMetalAdminClusters.setIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * specified. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param SetIamPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function setIamPolicy($resource, SetIamPolicyRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setIamPolicy', [$params], Policy::class);
  }
  /**
   * Returns permissions that a caller has on the specified resource. If the
   * resource does not exist, this will return an empty set of permissions, not a
   * `NOT_FOUND` error. Note: This operation is designed to be used for building
   * permission-aware UIs and command-line tools, not for authorization checking.
   * This operation may "fail open" without warning.
   * (bareMetalAdminClusters.testIamPermissions)
   *
   * @param string $resource REQUIRED: The resource for which the policy detail is
   * being requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param TestIamPermissionsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return TestIamPermissionsResponse
   * @throws \Google\Service\Exception
   */
  public function testIamPermissions($resource, TestIamPermissionsRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('testIamPermissions', [$params], TestIamPermissionsResponse::class);
  }
  /**
   * Unenrolls an existing bare metal admin cluster from the Anthos On-Prem API
   * within a given project and location. Unenrollment removes the Cloud reference
   * to the cluster without modifying the underlying OnPrem Resources. Clusters
   * will continue to run; however, they will no longer be accessible through the
   * Anthos On-Prem API or its clients. (bareMetalAdminClusters.unenroll)
   *
   * @param string $name Required. Name of the bare metal admin cluster to be
   * unenrolled. Format:
   * "projects/{project}/locations/{location}/bareMetalAdminClusters/{cluster}"
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing If set to true, and the bare metal admin cluster
   * is not found, the request will succeed but no action will be taken on the
   * server and return a completed LRO.
   * @opt_param string etag The current etag of the bare metal admin cluster. If
   * an etag is provided and does not match the current etag of the cluster,
   * deletion will be blocked and an ABORTED error will be returned.
   * @opt_param bool ignoreErrors If set to true, the unenrollment of a bare metal
   * admin cluster resource will succeed even if errors occur during unenrollment.
   * This parameter can be used when you want to unenroll admin cluster resource
   * and the on-prem admin cluster is disconnected / unreachable. WARNING: Using
   * this parameter when your admin cluster still exists may result in a deleted
   * GCP admin cluster but existing resourcelink in on-prem admin cluster and
   * membership.
   * @opt_param bool validateOnly Validate the request without actually doing any
   * updates.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function unenroll($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('unenroll', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsBareMetalAdminClusters::class, 'Google_Service_GKEOnPrem_Resource_ProjectsLocationsBareMetalAdminClusters');
