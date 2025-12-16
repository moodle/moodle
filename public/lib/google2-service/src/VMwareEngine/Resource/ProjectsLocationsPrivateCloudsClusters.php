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

namespace Google\Service\VMwareEngine\Resource;

use Google\Service\VMwareEngine\Cluster;
use Google\Service\VMwareEngine\ListClustersResponse;
use Google\Service\VMwareEngine\MountDatastoreRequest;
use Google\Service\VMwareEngine\Operation;
use Google\Service\VMwareEngine\Policy;
use Google\Service\VMwareEngine\SetIamPolicyRequest;
use Google\Service\VMwareEngine\TestIamPermissionsRequest;
use Google\Service\VMwareEngine\TestIamPermissionsResponse;
use Google\Service\VMwareEngine\UnmountDatastoreRequest;

/**
 * The "clusters" collection of methods.
 * Typical usage is:
 *  <code>
 *   $vmwareengineService = new Google\Service\VMwareEngine(...);
 *   $clusters = $vmwareengineService->projects_locations_privateClouds_clusters;
 *  </code>
 */
class ProjectsLocationsPrivateCloudsClusters extends \Google\Service\Resource
{
  /**
   * Creates a new cluster in a given private cloud. Creating a new cluster
   * provides additional nodes for use in the parent private cloud and requires
   * sufficient [node quota](https://cloud.google.com/vmware-engine/quotas).
   * (clusters.create)
   *
   * @param string $parent Required. The resource name of the private cloud to
   * create a new cluster in. Resource names are schemeless URIs that follow the
   * conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/us-central1-a/privateClouds/my-cloud`
   * @param Cluster $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string clusterId Required. The user-provided identifier of the new
   * `Cluster`. This identifier must be unique among clusters within the parent
   * and becomes the final token in the name URI. The identifier must meet the
   * following requirements: * Only contains 1-63 alphanumeric characters and
   * hyphens * Begins with an alphabetical character * Ends with a non-hyphen
   * character * Not formatted as a UUID * Complies with [RFC
   * 1034](https://datatracker.ietf.org/doc/html/rfc1034) (section 3.5)
   * @opt_param string requestId Optional. The request ID must be a valid UUID
   * with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param bool validateOnly Optional. True if you want the request to be
   * validated and not executed; false otherwise.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Cluster $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a `Cluster` resource. To avoid unintended data loss, migrate or
   * gracefully shut down any workloads running on the cluster before deletion.
   * You cannot delete the management cluster of a private cloud using this
   * method. (clusters.delete)
   *
   * @param string $name Required. The resource name of the cluster to delete.
   * Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/clusters/my-cluster`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. The request ID must be a valid UUID
   * with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
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
   * Retrieves a `Cluster` resource by its resource name. (clusters.get)
   *
   * @param string $name Required. The cluster resource name to retrieve. Resource
   * names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/clusters/my-cluster`
   * @param array $optParams Optional parameters.
   * @return Cluster
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Cluster::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set. (clusters.getIamPolicy)
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
   * Lists `Cluster` resources in a given private cloud.
   * (clusters.listProjectsLocationsPrivateCloudsClusters)
   *
   * @param string $parent Required. The resource name of the private cloud to
   * query for clusters. Resource names are schemeless URIs that follow the
   * conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/us-central1-a/privateClouds/my-cloud`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter To filter on multiple expressions, provide each
   * separate expression within parentheses. For example: ``` (name = "example-
   * cluster") (nodeCount = "3") ``` By default, each expression is an `AND`
   * expression. However, you can include `AND` and `OR` expressions explicitly.
   * For example: ``` (name = "example-cluster-1") AND (createTime >
   * "2021-04-12T08:15:10.40Z") OR (name = "example-cluster-2") ```
   * @opt_param string orderBy Sorts list results by a certain order. By default,
   * returned results are ordered by `name` in ascending order. You can also sort
   * results in descending order based on the `name` value using `orderBy="name
   * desc"`. Currently, only ordering by `name` is supported.
   * @opt_param int pageSize The maximum number of clusters to return in one page.
   * The service may return fewer than this value. The maximum value is coerced to
   * 1000. The default value of this field is 500.
   * @opt_param string pageToken A page token, received from a previous
   * `ListClusters` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListClusters` must match the
   * call that provided the page token.
   * @return ListClustersResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsPrivateCloudsClusters($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListClustersResponse::class);
  }
  /**
   * Mounts a `Datastore` on a cluster resource Datastores are zonal resources
   * (clusters.mountDatastore)
   *
   * @param string $name Required. The resource name of the cluster to mount the
   * datastore. Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/clusters/my-cluster`
   * @param MountDatastoreRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function mountDatastore($name, MountDatastoreRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('mountDatastore', [$params], Operation::class);
  }
  /**
   * Modifies a `Cluster` resource. Only fields specified in `updateMask` are
   * applied. During operation processing, the resource is temporarily in the
   * `ACTIVE` state before the operation fully completes. For that period of time,
   * you can't update the resource. Use the operation status to determine when the
   * processing fully completes. (clusters.patch)
   *
   * @param string $name Output only. Identifier. The resource name of this
   * cluster. Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/clusters/my-cluster`
   * @param Cluster $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. The request ID must be a valid UUID
   * with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the `Cluster` resource by the update. The fields
   * specified in the `updateMask` are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then all fields will be overwritten.
   * @opt_param bool validateOnly Optional. True if you want the request to be
   * validated and not executed; false otherwise.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Cluster $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (clusters.setIamPolicy)
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
   * This operation may "fail open" without warning. (clusters.testIamPermissions)
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
   * Mounts a `Datastore` on a cluster resource Datastores are zonal resources
   * (clusters.unmountDatastore)
   *
   * @param string $name Required. The resource name of the cluster to unmount the
   * datastore. Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/clusters/my-cluster`
   * @param UnmountDatastoreRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function unmountDatastore($name, UnmountDatastoreRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('unmountDatastore', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsPrivateCloudsClusters::class, 'Google_Service_VMwareEngine_Resource_ProjectsLocationsPrivateCloudsClusters');
