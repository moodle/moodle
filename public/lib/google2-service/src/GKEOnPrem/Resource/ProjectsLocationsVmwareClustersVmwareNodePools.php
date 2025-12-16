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

use Google\Service\GKEOnPrem\EnrollVmwareNodePoolRequest;
use Google\Service\GKEOnPrem\ListVmwareNodePoolsResponse;
use Google\Service\GKEOnPrem\Operation;
use Google\Service\GKEOnPrem\Policy;
use Google\Service\GKEOnPrem\SetIamPolicyRequest;
use Google\Service\GKEOnPrem\TestIamPermissionsRequest;
use Google\Service\GKEOnPrem\TestIamPermissionsResponse;
use Google\Service\GKEOnPrem\VmwareNodePool;

/**
 * The "vmwareNodePools" collection of methods.
 * Typical usage is:
 *  <code>
 *   $gkeonpremService = new Google\Service\GKEOnPrem(...);
 *   $vmwareNodePools = $gkeonpremService->projects_locations_vmwareClusters_vmwareNodePools;
 *  </code>
 */
class ProjectsLocationsVmwareClustersVmwareNodePools extends \Google\Service\Resource
{
  /**
   * Creates a new VMware node pool in a given project, location and VMWare
   * cluster. (vmwareNodePools.create)
   *
   * @param string $parent Required. The parent resource where this node pool will
   * be created. projects/{project}/locations/{location}/vmwareClusters/{cluster}
   * @param VmwareNodePool $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool validateOnly If set, only validate the request, but do not
   * actually create the node pool.
   * @opt_param string vmwareNodePoolId The ID to use for the node pool, which
   * will become the final component of the node pool's resource name. This value
   * must be up to 40 characters and follow RFC-1123
   * (https://tools.ietf.org/html/rfc1123) format. The value must not be permitted
   * to be a UUID (or UUID-like: anything matching
   * /^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/i).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, VmwareNodePool $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single VMware node pool. (vmwareNodePools.delete)
   *
   * @param string $name Required. The name of the node pool to delete. Format: pr
   * ojects/{project}/locations/{location}/vmwareClusters/{cluster}/vmwareNodePool
   * s/{nodepool}
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing If set to true, and the VMware node pool is not
   * found, the request will succeed but no action will be taken on the server and
   * return a completed LRO.
   * @opt_param string etag The current etag of the VmwareNodePool. If an etag is
   * provided and does not match the current etag of the node pool, deletion will
   * be blocked and an ABORTED error will be returned.
   * @opt_param bool ignoreErrors If set to true, the deletion of a VMware node
   * pool resource will succeed even if errors occur during deletion. This
   * parameter can be used when you want to delete GCP's node pool resource and
   * you've already deleted the on-prem admin cluster that hosted your node pool.
   * WARNING: Using this parameter when your user cluster still exists may result
   * in a deleted GCP node pool but an existing on-prem node pool.
   * @opt_param bool validateOnly If set, only validate the request, but do not
   * actually delete the node pool.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Enrolls a VMware node pool to Anthos On-Prem API (vmwareNodePools.enroll)
   *
   * @param string $parent Required. The parent resource where the node pool is
   * enrolled in.
   * @param EnrollVmwareNodePoolRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function enroll($parent, EnrollVmwareNodePoolRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('enroll', [$params], Operation::class);
  }
  /**
   * Gets details of a single VMware node pool. (vmwareNodePools.get)
   *
   * @param string $name Required. The name of the node pool to retrieve. projects
   * /{project}/locations/{location}/vmwareClusters/{cluster}/vmwareNodePools/{nod
   * epool}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string view View for VMware node pool. When `BASIC` is specified,
   * only the node pool resource name is returned. The default/unset value
   * `NODE_POOL_VIEW_UNSPECIFIED` is the same as `FULL', which returns the
   * complete node pool configuration details.
   * @return VmwareNodePool
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], VmwareNodePool::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set.
   * (vmwareNodePools.getIamPolicy)
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
   * Lists VMware node pools in a given project, location and VMWare cluster.
   * (vmwareNodePools.listProjectsLocationsVmwareClustersVmwareNodePools)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * node pools. Format:
   * projects/{project}/locations/{location}/vmwareClusters/{vmwareCluster}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of node pools to return. The
   * service may return fewer than this value. If unspecified, at most 50 node
   * pools will be returned. The maximum value is 1000; values above 1000 will be
   * coerced to 1000.
   * @opt_param string pageToken A page token, received from a previous
   * `ListVmwareNodePools` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListVmwareNodePools` must
   * match the call that provided the page token.
   * @opt_param string view View for VMware node pools. When `BASIC` is specified,
   * only the node pool resource name is returned. The default/unset value
   * `NODE_POOL_VIEW_UNSPECIFIED` is the same as `FULL', which returns the
   * complete node pool configuration details.
   * @return ListVmwareNodePoolsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsVmwareClustersVmwareNodePools($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListVmwareNodePoolsResponse::class);
  }
  /**
   * Updates the parameters of a single VMware node pool. (vmwareNodePools.patch)
   *
   * @param string $name Immutable. The resource name of this node pool.
   * @param VmwareNodePool $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the VMwareNodePool resource by the update. The
   * fields specified in the update_mask are relative to the resource, not the
   * full request. A field will be overwritten if it is in the mask. If the user
   * does not provide a mask then all populated fields in the VMwareNodePool
   * message will be updated. Empty fields will be ignored unless a field mask is
   * used.
   * @opt_param bool validateOnly Validate the request without actually doing any
   * updates.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, VmwareNodePool $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (vmwareNodePools.setIamPolicy)
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
   * (vmwareNodePools.testIamPermissions)
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
   * Unenrolls a VMware node pool to Anthos On-Prem API (vmwareNodePools.unenroll)
   *
   * @param string $name Required. The name of the node pool to unenroll. Format:
   * projects/{project}/locations/{location}/vmwareClusters/{cluster}/vmwareNodePo
   * ols/{nodepool}
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing If set to true, and the VMware node pool is not
   * found, the request will succeed but no action will be taken on the server and
   * return a completed LRO.
   * @opt_param string etag The current etag of the VMware node pool. If an etag
   * is provided and does not match the current etag of node pool, deletion will
   * be blocked and an ABORTED error will be returned.
   * @opt_param bool validateOnly If set, only validate the request, but do not
   * actually unenroll the node pool.
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
class_alias(ProjectsLocationsVmwareClustersVmwareNodePools::class, 'Google_Service_GKEOnPrem_Resource_ProjectsLocationsVmwareClustersVmwareNodePools');
