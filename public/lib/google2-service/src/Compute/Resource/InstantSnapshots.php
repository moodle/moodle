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

namespace Google\Service\Compute\Resource;

use Google\Service\Compute\InstantSnapshot;
use Google\Service\Compute\InstantSnapshotAggregatedList;
use Google\Service\Compute\InstantSnapshotList;
use Google\Service\Compute\Operation;
use Google\Service\Compute\Policy;
use Google\Service\Compute\TestPermissionsRequest;
use Google\Service\Compute\TestPermissionsResponse;
use Google\Service\Compute\ZoneSetLabelsRequest;
use Google\Service\Compute\ZoneSetPolicyRequest;

/**
 * The "instantSnapshots" collection of methods.
 * Typical usage is:
 *  <code>
 *   $computeService = new Google\Service\Compute(...);
 *   $instantSnapshots = $computeService->instantSnapshots;
 *  </code>
 */
class InstantSnapshots extends \Google\Service\Resource
{
  /**
   * Retrieves an aggregated list of instantSnapshots.
   *
   * To prevent failure, Google recommends that you set the `returnPartialSuccess`
   * parameter to `true`. (instantSnapshots.aggregatedList)
   *
   * @param string $project Project ID for this request.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter A filter expression that filters resources listed in
   * the response. Most Compute resources support two types of filter expressions:
   * expressions that support regular expressions and expressions that follow API
   * improvement proposal AIP-160. These two types of filter expressions cannot be
   * mixed in one request.
   *
   * If you want to use AIP-160, your expression must specify the field name, an
   * operator, and the value that you want to use for filtering. The value must be
   * a string, a number, or a boolean. The operator must be either `=`, `!=`, `>`,
   * `<`, `<=`, `>=` or `:`.
   *
   * For example, if you are filtering Compute Engine instances, you can exclude
   * instances named `example-instance` by specifying `name != example-instance`.
   *
   * The `:*` comparison can be used to test whether a key has been defined. For
   * example, to find all objects with `owner` label use: ``` labels.owner:* ```
   *
   * You can also filter nested fields. For example, you could specify
   * `scheduling.automaticRestart = false` to include instances only if they are
   * not scheduled for automatic restarts. You can use filtering on nested fields
   * to filter based onresource labels.
   *
   * To filter on multiple expressions, provide each separate expression within
   * parentheses. For example: ``` (scheduling.automaticRestart = true)
   * (cpuPlatform = "Intel Skylake") ``` By default, each expression is an `AND`
   * expression. However, you can include `AND` and `OR` expressions explicitly.
   * For example: ``` (cpuPlatform = "Intel Skylake") OR (cpuPlatform = "Intel
   * Broadwell") AND (scheduling.automaticRestart = true) ```
   *
   * If you want to use a regular expression, use the `eq` (equal) or `ne` (not
   * equal) operator against a single un-parenthesized expression with or without
   * quotes or against multiple parenthesized expressions. Examples:
   *
   * `fieldname eq unquoted literal` `fieldname eq 'single quoted literal'`
   * `fieldname eq "double quoted literal"` `(fieldname1 eq literal) (fieldname2
   * ne "literal")`
   *
   * The literal value is interpreted as a regular expression using GoogleRE2
   * library syntax. The literal value must match the entire field.
   *
   * For example, to filter for instances that do not end with name "instance",
   * you would use `name ne .*instance`.
   *
   * You cannot combine constraints on multiple fields using regular expressions.
   * @opt_param bool includeAllScopes Indicates whether every visible scope for
   * each scope type (zone, region, global) should be included in the response.
   * For new resource types added after this field, the flag has no effect as new
   * resource types will always include every visible scope for each scope type in
   * response. For resource types which predate this field, if this flag is
   * omitted or false, only scopes of the scope types where the resource type is
   * expected to be found will be included.
   * @opt_param string maxResults The maximum number of results per page that
   * should be returned. If the number of available results is larger than
   * `maxResults`, Compute Engine returns a `nextPageToken` that can be used to
   * get the next page of results in subsequent list requests. Acceptable values
   * are `0` to `500`, inclusive. (Default: `500`)
   * @opt_param string orderBy Sorts list results by a certain order. By default,
   * results are returned in alphanumerical order based on the resource name.
   *
   * You can also sort results in descending order based on the creation timestamp
   * using `orderBy="creationTimestamp desc"`. This sorts results based on the
   * `creationTimestamp` field in reverse chronological order (newest result
   * first). Use this to sort resources like operations so that the newest
   * operation is returned first.
   *
   * Currently, only sorting by `name` or `creationTimestamp desc` is supported.
   * @opt_param string pageToken Specifies a page token to use. Set `pageToken` to
   * the `nextPageToken` returned by a previous list request to get the next page
   * of results.
   * @opt_param bool returnPartialSuccess Opt-in for partial success behavior
   * which provides partial results in case of failure. The default value is
   * false.
   *
   * For example, when partial success behavior is enabled, aggregatedList for a
   * single zone scope either returns all resources in the zone or no resources,
   * with an error code.
   * @opt_param string serviceProjectNumber The Shared VPC service project id or
   * service project number for which aggregated list request is invoked for
   * subnetworks list-usable api.
   * @return InstantSnapshotAggregatedList
   * @throws \Google\Service\Exception
   */
  public function aggregatedList($project, $optParams = [])
  {
    $params = ['project' => $project];
    $params = array_merge($params, $optParams);
    return $this->call('aggregatedList', [$params], InstantSnapshotAggregatedList::class);
  }
  /**
   * Deletes the specified InstantSnapshot resource. Keep in mind that deleting a
   * single instantSnapshot might not necessarily delete all the data on that
   * instantSnapshot. If any data on the instantSnapshot that is marked for
   * deletion is needed for subsequent instantSnapshots, the data will be moved to
   * the next corresponding instantSnapshot.
   *
   * For more information, seeDeleting instantSnapshots. (instantSnapshots.delete)
   *
   * @param string $project Project ID for this request.
   * @param string $zone The name of the zone for this request.
   * @param string $instantSnapshot Name of the InstantSnapshot resource to
   * delete.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId An optional request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server will know to ignore the request if it has already been completed.
   *
   * For example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments.
   *
   * The request ID must be a valid UUID with the exception that zero UUID is not
   * supported (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($project, $zone, $instantSnapshot, $optParams = [])
  {
    $params = ['project' => $project, 'zone' => $zone, 'instantSnapshot' => $instantSnapshot];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Returns the specified InstantSnapshot resource in the specified zone.
   * (instantSnapshots.get)
   *
   * @param string $project Project ID for this request.
   * @param string $zone The name of the zone for this request.
   * @param string $instantSnapshot Name of the InstantSnapshot resource to
   * return.
   * @param array $optParams Optional parameters.
   * @return InstantSnapshot
   * @throws \Google\Service\Exception
   */
  public function get($project, $zone, $instantSnapshot, $optParams = [])
  {
    $params = ['project' => $project, 'zone' => $zone, 'instantSnapshot' => $instantSnapshot];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], InstantSnapshot::class);
  }
  /**
   * Gets the access control policy for a resource. May be empty if no such policy
   * or resource exists. (instantSnapshots.getIamPolicy)
   *
   * @param string $project Project ID for this request.
   * @param string $zone The name of the zone for this request.
   * @param string $resource Name or id of the resource for this request.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int optionsRequestedPolicyVersion Requested IAM Policy version.
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function getIamPolicy($project, $zone, $resource, $optParams = [])
  {
    $params = ['project' => $project, 'zone' => $zone, 'resource' => $resource];
    $params = array_merge($params, $optParams);
    return $this->call('getIamPolicy', [$params], Policy::class);
  }
  /**
   * Creates an instant snapshot in the specified zone. (instantSnapshots.insert)
   *
   * @param string $project Project ID for this request.
   * @param string $zone Name of the zone for this request.
   * @param InstantSnapshot $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId An optional request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server will know to ignore the request if it has already been completed.
   *
   * For example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments.
   *
   * The request ID must be a valid UUID with the exception that zero UUID is not
   * supported (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function insert($project, $zone, InstantSnapshot $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'zone' => $zone, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], Operation::class);
  }
  /**
   * Retrieves the list of InstantSnapshot resources contained within the
   * specified zone. (instantSnapshots.listInstantSnapshots)
   *
   * @param string $project Project ID for this request.
   * @param string $zone The name of the zone for this request.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter A filter expression that filters resources listed in
   * the response. Most Compute resources support two types of filter expressions:
   * expressions that support regular expressions and expressions that follow API
   * improvement proposal AIP-160. These two types of filter expressions cannot be
   * mixed in one request.
   *
   * If you want to use AIP-160, your expression must specify the field name, an
   * operator, and the value that you want to use for filtering. The value must be
   * a string, a number, or a boolean. The operator must be either `=`, `!=`, `>`,
   * `<`, `<=`, `>=` or `:`.
   *
   * For example, if you are filtering Compute Engine instances, you can exclude
   * instances named `example-instance` by specifying `name != example-instance`.
   *
   * The `:*` comparison can be used to test whether a key has been defined. For
   * example, to find all objects with `owner` label use: ``` labels.owner:* ```
   *
   * You can also filter nested fields. For example, you could specify
   * `scheduling.automaticRestart = false` to include instances only if they are
   * not scheduled for automatic restarts. You can use filtering on nested fields
   * to filter based onresource labels.
   *
   * To filter on multiple expressions, provide each separate expression within
   * parentheses. For example: ``` (scheduling.automaticRestart = true)
   * (cpuPlatform = "Intel Skylake") ``` By default, each expression is an `AND`
   * expression. However, you can include `AND` and `OR` expressions explicitly.
   * For example: ``` (cpuPlatform = "Intel Skylake") OR (cpuPlatform = "Intel
   * Broadwell") AND (scheduling.automaticRestart = true) ```
   *
   * If you want to use a regular expression, use the `eq` (equal) or `ne` (not
   * equal) operator against a single un-parenthesized expression with or without
   * quotes or against multiple parenthesized expressions. Examples:
   *
   * `fieldname eq unquoted literal` `fieldname eq 'single quoted literal'`
   * `fieldname eq "double quoted literal"` `(fieldname1 eq literal) (fieldname2
   * ne "literal")`
   *
   * The literal value is interpreted as a regular expression using GoogleRE2
   * library syntax. The literal value must match the entire field.
   *
   * For example, to filter for instances that do not end with name "instance",
   * you would use `name ne .*instance`.
   *
   * You cannot combine constraints on multiple fields using regular expressions.
   * @opt_param string maxResults The maximum number of results per page that
   * should be returned. If the number of available results is larger than
   * `maxResults`, Compute Engine returns a `nextPageToken` that can be used to
   * get the next page of results in subsequent list requests. Acceptable values
   * are `0` to `500`, inclusive. (Default: `500`)
   * @opt_param string orderBy Sorts list results by a certain order. By default,
   * results are returned in alphanumerical order based on the resource name.
   *
   * You can also sort results in descending order based on the creation timestamp
   * using `orderBy="creationTimestamp desc"`. This sorts results based on the
   * `creationTimestamp` field in reverse chronological order (newest result
   * first). Use this to sort resources like operations so that the newest
   * operation is returned first.
   *
   * Currently, only sorting by `name` or `creationTimestamp desc` is supported.
   * @opt_param string pageToken Specifies a page token to use. Set `pageToken` to
   * the `nextPageToken` returned by a previous list request to get the next page
   * of results.
   * @opt_param bool returnPartialSuccess Opt-in for partial success behavior
   * which provides partial results in case of failure. The default value is
   * false.
   *
   * For example, when partial success behavior is enabled, aggregatedList for a
   * single zone scope either returns all resources in the zone or no resources,
   * with an error code.
   * @return InstantSnapshotList
   * @throws \Google\Service\Exception
   */
  public function listInstantSnapshots($project, $zone, $optParams = [])
  {
    $params = ['project' => $project, 'zone' => $zone];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], InstantSnapshotList::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. (instantSnapshots.setIamPolicy)
   *
   * @param string $project Project ID for this request.
   * @param string $zone The name of the zone for this request.
   * @param string $resource Name or id of the resource for this request.
   * @param ZoneSetPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function setIamPolicy($project, $zone, $resource, ZoneSetPolicyRequest $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'zone' => $zone, 'resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setIamPolicy', [$params], Policy::class);
  }
  /**
   * Sets the labels on a instantSnapshot in the given zone. To learn more about
   * labels, read the Labeling Resources documentation.
   * (instantSnapshots.setLabels)
   *
   * @param string $project Project ID for this request.
   * @param string $zone The name of the zone for this request.
   * @param string $resource Name or id of the resource for this request.
   * @param ZoneSetLabelsRequest $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId An optional request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server will know to ignore the request if it has already been completed.
   *
   * For example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments.
   *
   * The request ID must be a valid UUID with the exception that zero UUID is not
   * supported (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function setLabels($project, $zone, $resource, ZoneSetLabelsRequest $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'zone' => $zone, 'resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setLabels', [$params], Operation::class);
  }
  /**
   * Returns permissions that a caller has on the specified resource.
   * (instantSnapshots.testIamPermissions)
   *
   * @param string $project Project ID for this request.
   * @param string $zone The name of the zone for this request.
   * @param string $resource Name or id of the resource for this request.
   * @param TestPermissionsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return TestPermissionsResponse
   * @throws \Google\Service\Exception
   */
  public function testIamPermissions($project, $zone, $resource, TestPermissionsRequest $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'zone' => $zone, 'resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('testIamPermissions', [$params], TestPermissionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstantSnapshots::class, 'Google_Service_Compute_Resource_InstantSnapshots');
