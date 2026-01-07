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

use Google\Service\VMwareEngine\FetchNetworkPolicyExternalAddressesResponse;
use Google\Service\VMwareEngine\ListNetworkPoliciesResponse;
use Google\Service\VMwareEngine\NetworkPolicy;
use Google\Service\VMwareEngine\Operation;

/**
 * The "networkPolicies" collection of methods.
 * Typical usage is:
 *  <code>
 *   $vmwareengineService = new Google\Service\VMwareEngine(...);
 *   $networkPolicies = $vmwareengineService->projects_locations_networkPolicies;
 *  </code>
 */
class ProjectsLocationsNetworkPolicies extends \Google\Service\Resource
{
  /**
   * Creates a new network policy in a given VMware Engine network of a project
   * and location (region). A new network policy cannot be created if another
   * network policy already exists in the same scope. (networkPolicies.create)
   *
   * @param string $parent Required. The resource name of the location (region) to
   * create the new network policy in. Resource names are schemeless URIs that
   * follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1`
   * @param NetworkPolicy $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string networkPolicyId Required. The user-provided identifier of
   * the network policy to be created. This identifier must be unique within
   * parent `projects/{my-project}/locations/{us-central1}/networkPolicies` and
   * becomes the final token in the name URI. The identifier must meet the
   * following requirements: * Only contains 1-63 alphanumeric characters and
   * hyphens * Begins with an alphabetical character * Ends with a non-hyphen
   * character * Not formatted as a UUID * Complies with [RFC
   * 1034](https://datatracker.ietf.org/doc/html/rfc1034) (section 3.5)
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server will know to ignore the request if it has already been completed. The
   * server guarantees that a request doesn't result in creation of duplicate
   * commitments for at least 60 minutes. For example, consider a situation where
   * you make an initial request and the request times out. If you make the
   * request again with the same request ID, the server can check if original
   * operation with the same request ID was received, and if so, will ignore the
   * second request. This prevents clients from accidentally creating duplicate
   * commitments. The request ID must be a valid UUID with the exception that zero
   * UUID is not supported (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, NetworkPolicy $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a `NetworkPolicy` resource. A network policy cannot be deleted when
   * `NetworkService.state` is set to `RECONCILING` for either its external IP or
   * internet access service. (networkPolicies.delete)
   *
   * @param string $name Required. The resource name of the network policy to
   * delete. Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1/networkPolicies/my-network-policy`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server will know to ignore the request if it has already been completed. The
   * server guarantees that a request doesn't result in creation of duplicate
   * commitments for at least 60 minutes. For example, consider a situation where
   * you make an initial request and the request times out. If you make the
   * request again with the same request ID, the server can check if original
   * operation with the same request ID was received, and if so, will ignore the
   * second request. This prevents clients from accidentally creating duplicate
   * commitments. The request ID must be a valid UUID with the exception that zero
   * UUID is not supported (00000000-0000-0000-0000-000000000000).
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
   * Lists external IP addresses assigned to VMware workload VMs within the scope
   * of the given network policy. (networkPolicies.fetchExternalAddresses)
   *
   * @param string $networkPolicy Required. The resource name of the network
   * policy to query for assigned external IP addresses. Resource names are
   * schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1/networkPolicies/my-policy`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of external IP addresses to return
   * in one page. The service may return fewer than this value. The maximum value
   * is coerced to 1000. The default value of this field is 500.
   * @opt_param string pageToken A page token, received from a previous
   * `FetchNetworkPolicyExternalAddresses` call. Provide this to retrieve the
   * subsequent page. When paginating, all parameters provided to
   * `FetchNetworkPolicyExternalAddresses`, except for `page_size` and
   * `page_token`, must match the call that provided the page token.
   * @return FetchNetworkPolicyExternalAddressesResponse
   * @throws \Google\Service\Exception
   */
  public function fetchExternalAddresses($networkPolicy, $optParams = [])
  {
    $params = ['networkPolicy' => $networkPolicy];
    $params = array_merge($params, $optParams);
    return $this->call('fetchExternalAddresses', [$params], FetchNetworkPolicyExternalAddressesResponse::class);
  }
  /**
   * Retrieves a `NetworkPolicy` resource by its resource name.
   * (networkPolicies.get)
   *
   * @param string $name Required. The resource name of the network policy to
   * retrieve. Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1/networkPolicies/my-network-policy`
   * @param array $optParams Optional parameters.
   * @return NetworkPolicy
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], NetworkPolicy::class);
  }
  /**
   * Lists `NetworkPolicy` resources in a specified project and location.
   * (networkPolicies.listProjectsLocationsNetworkPolicies)
   *
   * @param string $parent Required. The resource name of the location (region) to
   * query for network policies. Resource names are schemeless URIs that follow
   * the conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/us-central1`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter A filter expression that matches resources returned
   * in the response. The expression must specify the field name, a comparison
   * operator, and the value that you want to use for filtering. The value must be
   * a string, a number, or a boolean. The comparison operator must be `=`, `!=`,
   * `>`, or `<`. For example, if you are filtering a list of network policies,
   * you can exclude the ones named `example-policy` by specifying `name !=
   * "example-policy"`. To filter on multiple expressions, provide each separate
   * expression within parentheses. For example: ``` (name = "example-policy")
   * (createTime > "2021-04-12T08:15:10.40Z") ``` By default, each expression is
   * an `AND` expression. However, you can include `AND` and `OR` expressions
   * explicitly. For example: ``` (name = "example-policy-1") AND (createTime >
   * "2021-04-12T08:15:10.40Z") OR (name = "example-policy-2") ```
   * @opt_param string orderBy Sorts list results by a certain order. By default,
   * returned results are ordered by `name` in ascending order. You can also sort
   * results in descending order based on the `name` value using `orderBy="name
   * desc"`. Currently, only ordering by `name` is supported.
   * @opt_param int pageSize The maximum number of network policies to return in
   * one page. The service may return fewer than this value. The maximum value is
   * coerced to 1000. The default value of this field is 500.
   * @opt_param string pageToken A page token, received from a previous
   * `ListNetworkPolicies` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListNetworkPolicies` must
   * match the call that provided the page token.
   * @return ListNetworkPoliciesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsNetworkPolicies($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListNetworkPoliciesResponse::class);
  }
  /**
   * Modifies a `NetworkPolicy` resource. Only the following fields can be
   * updated: `internet_access`, `external_ip`, `edge_services_cidr`. Only fields
   * specified in `updateMask` are applied. When updating a network policy, the
   * external IP network service can only be disabled if there are no external IP
   * addresses present in the scope of the policy. Also, a `NetworkService` cannot
   * be updated when `NetworkService.state` is set to `RECONCILING`. During
   * operation processing, the resource is temporarily in the `ACTIVE` state
   * before the operation fully completes. For that period of time, you can't
   * update the resource. Use the operation status to determine when the
   * processing fully completes. (networkPolicies.patch)
   *
   * @param string $name Output only. Identifier. The resource name of this
   * network policy. Resource names are schemeless URIs that follow the
   * conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/us-central1/networkPolicies/my-
   * network-policy`
   * @param NetworkPolicy $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server will know to ignore the request if it has already been completed. The
   * server guarantees that a request doesn't result in creation of duplicate
   * commitments for at least 60 minutes. For example, consider a situation where
   * you make an initial request and the request times out. If you make the
   * request again with the same request ID, the server can check if original
   * operation with the same request ID was received, and if so, will ignore the
   * second request. This prevents clients from accidentally creating duplicate
   * commitments. The request ID must be a valid UUID with the exception that zero
   * UUID is not supported (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the `NetworkPolicy` resource by the update. The
   * fields specified in the `update_mask` are relative to the resource, not the
   * full request. A field will be overwritten if it is in the mask. If the user
   * does not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, NetworkPolicy $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsNetworkPolicies::class, 'Google_Service_VMwareEngine_Resource_ProjectsLocationsNetworkPolicies');
