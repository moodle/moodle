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

use Google\Service\VMwareEngine\ExternalAccessRule;
use Google\Service\VMwareEngine\ListExternalAccessRulesResponse;
use Google\Service\VMwareEngine\Operation;

/**
 * The "externalAccessRules" collection of methods.
 * Typical usage is:
 *  <code>
 *   $vmwareengineService = new Google\Service\VMwareEngine(...);
 *   $externalAccessRules = $vmwareengineService->projects_locations_networkPolicies_externalAccessRules;
 *  </code>
 */
class ProjectsLocationsNetworkPoliciesExternalAccessRules extends \Google\Service\Resource
{
  /**
   * Creates a new external access rule in a given network policy.
   * (externalAccessRules.create)
   *
   * @param string $parent Required. The resource name of the network policy to
   * create a new external access firewall rule in. Resource names are schemeless
   * URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1/networkPolicies/my-policy`
   * @param ExternalAccessRule $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string externalAccessRuleId Required. The user-provided identifier
   * of the `ExternalAccessRule` to be created. This identifier must be unique
   * among `ExternalAccessRule` resources within the parent and becomes the final
   * token in the name URI. The identifier must meet the following requirements: *
   * Only contains 1-63 alphanumeric characters and hyphens * Begins with an
   * alphabetical character * Ends with a non-hyphen character * Not formatted as
   * a UUID * Complies with [RFC
   * 1034](https://datatracker.ietf.org/doc/html/rfc1034) (section 3.5)
   * @opt_param string requestId A request ID to identify requests. Specify a
   * unique request ID so that if you must retry your request, the server will
   * know to ignore the request if it has already been completed. The server
   * guarantees that a request doesn't result in creation of duplicate commitments
   * for at least 60 minutes. For example, consider a situation where you make an
   * initial request and the request times out. If you make the request again with
   * the same request ID, the server can check if the original operation with the
   * same request ID was received, and if so, will ignore the second request. This
   * prevents clients from accidentally creating duplicate commitments. The
   * request ID must be a valid UUID with the exception that zero UUID is not
   * supported (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, ExternalAccessRule $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single external access rule. (externalAccessRules.delete)
   *
   * @param string $name Required. The resource name of the external access
   * firewall rule to delete. Resource names are schemeless URIs that follow the
   * conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/us-central1/networkPolicies/my-
   * policy/externalAccessRules/my-rule`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server will know to ignore the request if it has already been completed. The
   * server guarantees that a request doesn't result in creation of duplicate
   * commitments for at least 60 minutes. For example, consider a situation where
   * you make an initial request and the request times out. If you make the
   * request again with the same request ID, the server can check if the original
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
   * Gets details of a single external access rule. (externalAccessRules.get)
   *
   * @param string $name Required. The resource name of the external access
   * firewall rule to retrieve. Resource names are schemeless URIs that follow the
   * conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/us-central1/networkPolicies/my-
   * policy/externalAccessRules/my-rule`
   * @param array $optParams Optional parameters.
   * @return ExternalAccessRule
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ExternalAccessRule::class);
  }
  /**
   * Lists `ExternalAccessRule` resources in the specified network policy.
   * (externalAccessRules.listProjectsLocationsNetworkPoliciesExternalAccessRules)
   *
   * @param string $parent Required. The resource name of the network policy to
   * query for external access firewall rules. Resource names are schemeless URIs
   * that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1/networkPolicies/my-policy`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter A filter expression that matches resources returned
   * in the response. The expression must specify the field name, a comparison
   * operator, and the value that you want to use for filtering. The value must be
   * a string, a number, or a boolean. The comparison operator must be `=`, `!=`,
   * `>`, or `<`. For example, if you are filtering a list of external access
   * rules, you can exclude the ones named `example-rule` by specifying `name !=
   * "example-rule"`. To filter on multiple expressions, provide each separate
   * expression within parentheses. For example: ``` (name = "example-rule")
   * (createTime > "2021-04-12T08:15:10.40Z") ``` By default, each expression is
   * an `AND` expression. However, you can include `AND` and `OR` expressions
   * explicitly. For example: ``` (name = "example-rule-1") AND (createTime >
   * "2021-04-12T08:15:10.40Z") OR (name = "example-rule-2") ```
   * @opt_param string orderBy Sorts list results by a certain order. By default,
   * returned results are ordered by `name` in ascending order. You can also sort
   * results in descending order based on the `name` value using `orderBy="name
   * desc"`. Currently, only ordering by `name` is supported.
   * @opt_param int pageSize The maximum number of external access rules to return
   * in one page. The service may return fewer than this value. The maximum value
   * is coerced to 1000. The default value of this field is 500.
   * @opt_param string pageToken A page token, received from a previous
   * `ListExternalAccessRulesRequest` call. Provide this to retrieve the
   * subsequent page. When paginating, all other parameters provided to
   * `ListExternalAccessRulesRequest` must match the call that provided the page
   * token.
   * @return ListExternalAccessRulesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsNetworkPoliciesExternalAccessRules($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListExternalAccessRulesResponse::class);
  }
  /**
   * Updates the parameters of a single external access rule. Only fields
   * specified in `update_mask` are applied. (externalAccessRules.patch)
   *
   * @param string $name Output only. The resource name of this external access
   * rule. Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1/networkPolicies/my-
   * policy/externalAccessRules/my-rule`
   * @param ExternalAccessRule $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server will know to ignore the request if it has already been completed. The
   * server guarantees that a request doesn't result in creation of duplicate
   * commitments for at least 60 minutes. For example, consider a situation where
   * you make an initial request and the request times out. If you make the
   * request again with the same request ID, the server can check if the original
   * operation with the same request ID was received, and if so, will ignore the
   * second request. This prevents clients from accidentally creating duplicate
   * commitments. The request ID must be a valid UUID with the exception that zero
   * UUID is not supported (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the `ExternalAccessRule` resource by the update.
   * The fields specified in the `update_mask` are relative to the resource, not
   * the full request. A field will be overwritten if it is in the mask. If the
   * user does not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, ExternalAccessRule $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsNetworkPoliciesExternalAccessRules::class, 'Google_Service_VMwareEngine_Resource_ProjectsLocationsNetworkPoliciesExternalAccessRules');
