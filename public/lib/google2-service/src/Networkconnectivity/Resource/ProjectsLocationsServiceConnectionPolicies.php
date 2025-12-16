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

namespace Google\Service\Networkconnectivity\Resource;

use Google\Service\Networkconnectivity\GoogleLongrunningOperation;
use Google\Service\Networkconnectivity\ListServiceConnectionPoliciesResponse;
use Google\Service\Networkconnectivity\Policy;
use Google\Service\Networkconnectivity\ServiceConnectionPolicy;
use Google\Service\Networkconnectivity\SetIamPolicyRequest;
use Google\Service\Networkconnectivity\TestIamPermissionsRequest;
use Google\Service\Networkconnectivity\TestIamPermissionsResponse;

/**
 * The "serviceConnectionPolicies" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networkconnectivityService = new Google\Service\Networkconnectivity(...);
 *   $serviceConnectionPolicies = $networkconnectivityService->projects_locations_serviceConnectionPolicies;
 *  </code>
 */
class ProjectsLocationsServiceConnectionPolicies extends \Google\Service\Resource
{
  /**
   * Creates a new ServiceConnectionPolicy in a given project and location.
   * (serviceConnectionPolicies.create)
   *
   * @param string $parent Required. The parent resource's name of the
   * ServiceConnectionPolicy. ex. projects/123/locations/us-east1
   * @param ServiceConnectionPolicy $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string autoSubnetworkConfig.allocRangeSpace Optional. The space
   * where we search for a free range to create a subnetwork. It can be narrow
   * down or pick a different space. This is in standard CIDR format. If not
   * specified, “10.0.0.0/8” is used. Only eligible for IPV4_ONLY and IPV4_IPV6
   * subnetwork.
   * @opt_param string autoSubnetworkConfig.ipStack Optional. The requested IP
   * stack for the subnetwork. If not specified, IPv4 is used.
   * @opt_param int autoSubnetworkConfig.prefixLength Optional. The desired prefix
   * length for the subnet's IP address range. E.g., 24 for a /24. The actual
   * range is allocated from available space. If not specified, 24 is used. Only
   * eligible for IPV4_ONLY and IPV4_IPV6 subnetwork.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes since the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string serviceConnectionPolicyId Optional. Resource ID (i.e. 'foo'
   * in '[...]/projects/p/locations/l/serviceConnectionPolicies/foo') See
   * https://google.aip.dev/122#resource-id-segments Unique per location.
   * @opt_param string subnetworkMode Optional. If this field is not set,
   * USER_PROVIDED is the inferred value to use.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, ServiceConnectionPolicy $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a single ServiceConnectionPolicy. (serviceConnectionPolicies.delete)
   *
   * @param string $name Required. The name of the ServiceConnectionPolicy to
   * delete.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. The etag is computed by the server, and may
   * be sent on update and delete requests to ensure the client has an up-to-date
   * value before proceeding.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes after the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Gets details of a single ServiceConnectionPolicy.
   * (serviceConnectionPolicies.get)
   *
   * @param string $name Required. Name of the ServiceConnectionPolicy to get.
   * @param array $optParams Optional parameters.
   * @return ServiceConnectionPolicy
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ServiceConnectionPolicy::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set.
   * (serviceConnectionPolicies.getIamPolicy)
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
   * Lists ServiceConnectionPolicies in a given project and location.
   * (serviceConnectionPolicies.listProjectsLocationsServiceConnectionPolicies)
   *
   * @param string $parent Required. The parent resource's name. ex.
   * projects/123/locations/us-east1
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter A filter expression that filters the results listed
   * in the response.
   * @opt_param string orderBy Sort the results by a certain order.
   * @opt_param int pageSize The maximum number of results per page that should be
   * returned.
   * @opt_param string pageToken The page token.
   * @return ListServiceConnectionPoliciesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsServiceConnectionPolicies($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListServiceConnectionPoliciesResponse::class);
  }
  /**
   * Updates the parameters of a single ServiceConnectionPolicy.
   * (serviceConnectionPolicies.patch)
   *
   * @param string $name Immutable. The name of a ServiceConnectionPolicy. Format:
   * projects/{project}/locations/{location}/serviceConnectionPolicies/{service_co
   * nnection_policy} See: https://google.aip.dev/122#fields-representing-
   * resource-names
   * @param ServiceConnectionPolicy $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes since the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Optional. Field mask is used to specify the
   * fields to be overwritten in the ServiceConnectionPolicy resource by the
   * update. The fields specified in the update_mask are relative to the resource,
   * not the full request. A field will be overwritten if it is in the mask. If
   * the user does not provide a mask then all fields will be overwritten.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, ServiceConnectionPolicy $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (serviceConnectionPolicies.setIamPolicy)
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
   * (serviceConnectionPolicies.testIamPermissions)
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsServiceConnectionPolicies::class, 'Google_Service_Networkconnectivity_Resource_ProjectsLocationsServiceConnectionPolicies');
