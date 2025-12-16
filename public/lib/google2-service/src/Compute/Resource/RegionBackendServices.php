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

use Google\Service\Compute\BackendService;
use Google\Service\Compute\BackendServiceGroupHealth;
use Google\Service\Compute\BackendServiceList;
use Google\Service\Compute\BackendServiceListUsable;
use Google\Service\Compute\Operation;
use Google\Service\Compute\Policy;
use Google\Service\Compute\RegionSetPolicyRequest;
use Google\Service\Compute\ResourceGroupReference;
use Google\Service\Compute\SecurityPolicyReference;
use Google\Service\Compute\TestPermissionsRequest;
use Google\Service\Compute\TestPermissionsResponse;

/**
 * The "regionBackendServices" collection of methods.
 * Typical usage is:
 *  <code>
 *   $computeService = new Google\Service\Compute(...);
 *   $regionBackendServices = $computeService->regionBackendServices;
 *  </code>
 */
class RegionBackendServices extends \Google\Service\Resource
{
  /**
   * Deletes the specified regional BackendService resource.
   * (regionBackendServices.delete)
   *
   * @param string $project Project ID for this request.
   * @param string $region Name of the region scoping this request.
   * @param string $backendService Name of the BackendService resource to delete.
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
  public function delete($project, $region, $backendService, $optParams = [])
  {
    $params = ['project' => $project, 'region' => $region, 'backendService' => $backendService];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Returns the specified regional BackendService resource.
   * (regionBackendServices.get)
   *
   * @param string $project Project ID for this request.
   * @param string $region Name of the region scoping this request.
   * @param string $backendService Name of the BackendService resource to return.
   * @param array $optParams Optional parameters.
   * @return BackendService
   * @throws \Google\Service\Exception
   */
  public function get($project, $region, $backendService, $optParams = [])
  {
    $params = ['project' => $project, 'region' => $region, 'backendService' => $backendService];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], BackendService::class);
  }
  /**
   * Gets the most recent health check results for this regional BackendService.
   * (regionBackendServices.getHealth)
   *
   * @param string $project
   * @param string $region Name of the region scoping this request.
   * @param string $backendService Name of the BackendService resource for which
   * to get health.
   * @param ResourceGroupReference $postBody
   * @param array $optParams Optional parameters.
   * @return BackendServiceGroupHealth
   * @throws \Google\Service\Exception
   */
  public function getHealth($project, $region, $backendService, ResourceGroupReference $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'region' => $region, 'backendService' => $backendService, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('getHealth', [$params], BackendServiceGroupHealth::class);
  }
  /**
   * Gets the access control policy for a resource. May be empty if no such policy
   * or resource exists. (regionBackendServices.getIamPolicy)
   *
   * @param string $project Project ID for this request.
   * @param string $region The name of the region for this request.
   * @param string $resource Name or id of the resource for this request.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int optionsRequestedPolicyVersion Requested IAM Policy version.
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function getIamPolicy($project, $region, $resource, $optParams = [])
  {
    $params = ['project' => $project, 'region' => $region, 'resource' => $resource];
    $params = array_merge($params, $optParams);
    return $this->call('getIamPolicy', [$params], Policy::class);
  }
  /**
   * Creates a regional BackendService resource in the specified project using the
   * data included in the request. For more information, see Backend services
   * overview. (regionBackendServices.insert)
   *
   * @param string $project Project ID for this request.
   * @param string $region Name of the region scoping this request.
   * @param BackendService $postBody
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
  public function insert($project, $region, BackendService $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'region' => $region, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], Operation::class);
  }
  /**
   * Retrieves the list of regional BackendService resources available to the
   * specified project in the given region.
   * (regionBackendServices.listRegionBackendServices)
   *
   * @param string $project Project ID for this request.
   * @param string $region Name of the region scoping this request.
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
   * @return BackendServiceList
   * @throws \Google\Service\Exception
   */
  public function listRegionBackendServices($project, $region, $optParams = [])
  {
    $params = ['project' => $project, 'region' => $region];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], BackendServiceList::class);
  }
  /**
   * Retrieves a list of all usable backend services in the specified project in
   * the given region. (regionBackendServices.listUsable)
   *
   * @param string $project Project ID for this request.
   * @param string $region Name of the region scoping this request. It must be a
   * string that meets the requirements in RFC1035.
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
   * @return BackendServiceListUsable
   * @throws \Google\Service\Exception
   */
  public function listUsable($project, $region, $optParams = [])
  {
    $params = ['project' => $project, 'region' => $region];
    $params = array_merge($params, $optParams);
    return $this->call('listUsable', [$params], BackendServiceListUsable::class);
  }
  /**
   * Updates the specified regional BackendService resource with the data included
   * in the request. For more information, see  Understanding backend services
   * This method supports PATCH semantics and uses the JSON merge patch format and
   * processing rules. (regionBackendServices.patch)
   *
   * @param string $project Project ID for this request.
   * @param string $region Name of the region scoping this request.
   * @param string $backendService Name of the BackendService resource to patch.
   * @param BackendService $postBody
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
  public function patch($project, $region, $backendService, BackendService $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'region' => $region, 'backendService' => $backendService, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. (regionBackendServices.setIamPolicy)
   *
   * @param string $project Project ID for this request.
   * @param string $region The name of the region for this request.
   * @param string $resource Name or id of the resource for this request.
   * @param RegionSetPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function setIamPolicy($project, $region, $resource, RegionSetPolicyRequest $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'region' => $region, 'resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setIamPolicy', [$params], Policy::class);
  }
  /**
   * Sets the Google Cloud Armor security policy for the specified backend
   * service. For more information, seeGoogle Cloud Armor Overview
   * (regionBackendServices.setSecurityPolicy)
   *
   * @param string $project Project ID for this request.
   * @param string $region Name of the region scoping this request.
   * @param string $backendService Name of the BackendService resource to which
   * the security policy should be set. The name should conform to RFC1035.
   * @param SecurityPolicyReference $postBody
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
  public function setSecurityPolicy($project, $region, $backendService, SecurityPolicyReference $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'region' => $region, 'backendService' => $backendService, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setSecurityPolicy', [$params], Operation::class);
  }
  /**
   * Returns permissions that a caller has on the specified resource.
   * (regionBackendServices.testIamPermissions)
   *
   * @param string $project Project ID for this request.
   * @param string $region The name of the region for this request.
   * @param string $resource Name or id of the resource for this request.
   * @param TestPermissionsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return TestPermissionsResponse
   * @throws \Google\Service\Exception
   */
  public function testIamPermissions($project, $region, $resource, TestPermissionsRequest $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'region' => $region, 'resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('testIamPermissions', [$params], TestPermissionsResponse::class);
  }
  /**
   * Updates the specified regional BackendService resource with the data included
   * in the request. For more information, see  Backend services overview.
   * (regionBackendServices.update)
   *
   * @param string $project Project ID for this request.
   * @param string $region Name of the region scoping this request.
   * @param string $backendService Name of the BackendService resource to update.
   * @param BackendService $postBody
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
  public function update($project, $region, $backendService, BackendService $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'region' => $region, 'backendService' => $backendService, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RegionBackendServices::class, 'Google_Service_Compute_Resource_RegionBackendServices');
