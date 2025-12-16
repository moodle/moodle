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

use Google\Service\Compute\InstanceGroupManagerResizeRequest;
use Google\Service\Compute\InstanceGroupManagerResizeRequestsListResponse;
use Google\Service\Compute\Operation;

/**
 * The "instanceGroupManagerResizeRequests" collection of methods.
 * Typical usage is:
 *  <code>
 *   $computeService = new Google\Service\Compute(...);
 *   $instanceGroupManagerResizeRequests = $computeService->instanceGroupManagerResizeRequests;
 *  </code>
 */
class InstanceGroupManagerResizeRequests extends \Google\Service\Resource
{
  /**
   * Cancels the specified resize request and removes it from the queue. Cancelled
   * resize request does no longer wait for the resources to be provisioned.
   * Cancel is only possible for requests that are accepted in the queue.
   * (instanceGroupManagerResizeRequests.cancel)
   *
   * @param string $project Project ID for this request.
   * @param string $zone The name of thezone where the managed instance group is
   * located. The name should conform to RFC1035.
   * @param string $instanceGroupManager The name of the managed instance group.
   * The name should conform to RFC1035 or be a resource ID.
   * @param string $resizeRequest The name of the resize request to cancel. The
   * name should conform to RFC1035 or be a resource ID.
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
  public function cancel($project, $zone, $instanceGroupManager, $resizeRequest, $optParams = [])
  {
    $params = ['project' => $project, 'zone' => $zone, 'instanceGroupManager' => $instanceGroupManager, 'resizeRequest' => $resizeRequest];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], Operation::class);
  }
  /**
   * Deletes the specified, inactive resize request. Requests that are still
   * active cannot be deleted. Deleting request does not delete instances that
   * were provisioned previously. (instanceGroupManagerResizeRequests.delete)
   *
   * @param string $project Project ID for this request.
   * @param string $zone The name of thezone where the managed instance group is
   * located. The name should conform to RFC1035.
   * @param string $instanceGroupManager The name of the managed instance group.
   * The name should conform to RFC1035 or be a resource ID.
   * @param string $resizeRequest The name of the resize request to delete. The
   * name should conform to RFC1035 or be a resource ID.
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
  public function delete($project, $zone, $instanceGroupManager, $resizeRequest, $optParams = [])
  {
    $params = ['project' => $project, 'zone' => $zone, 'instanceGroupManager' => $instanceGroupManager, 'resizeRequest' => $resizeRequest];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Returns all of the details about the specified resize request.
   * (instanceGroupManagerResizeRequests.get)
   *
   * @param string $project Project ID for this request.
   * @param string $zone Name of the href="/compute/docs/regions-
   * zones/#available">zone scoping this request. Name should conform to RFC1035.
   * @param string $instanceGroupManager The name of the managed instance group.
   * Name should conform to RFC1035 or be a resource ID.
   * @param string $resizeRequest The name of the resize request. Name should
   * conform to RFC1035 or be a resource ID.
   * @param array $optParams Optional parameters.
   * @return InstanceGroupManagerResizeRequest
   * @throws \Google\Service\Exception
   */
  public function get($project, $zone, $instanceGroupManager, $resizeRequest, $optParams = [])
  {
    $params = ['project' => $project, 'zone' => $zone, 'instanceGroupManager' => $instanceGroupManager, 'resizeRequest' => $resizeRequest];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], InstanceGroupManagerResizeRequest::class);
  }
  /**
   * Creates a new resize request that starts provisioning VMs immediately or
   * queues VM creation. (instanceGroupManagerResizeRequests.insert)
   *
   * @param string $project Project ID for this request.
   * @param string $zone The name of thezone where the managed instance group is
   * located and where the resize request will be created. Name should conform to
   * RFC1035.
   * @param string $instanceGroupManager The name of the managed instance group to
   * which the resize request will be added. Name should conform to RFC1035 or be
   * a resource ID.
   * @param InstanceGroupManagerResizeRequest $postBody
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
  public function insert($project, $zone, $instanceGroupManager, InstanceGroupManagerResizeRequest $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'zone' => $zone, 'instanceGroupManager' => $instanceGroupManager, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], Operation::class);
  }
  /**
   * Retrieves a list of resize requests that are contained in the managed
   * instance group.
   * (instanceGroupManagerResizeRequests.listInstanceGroupManagerResizeRequests)
   *
   * @param string $project Project ID for this request.
   * @param string $zone The name of thezone where the managed instance group is
   * located. The name should conform to RFC1035.
   * @param string $instanceGroupManager The name of the managed instance group.
   * The name should conform to RFC1035.
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
   * @return InstanceGroupManagerResizeRequestsListResponse
   * @throws \Google\Service\Exception
   */
  public function listInstanceGroupManagerResizeRequests($project, $zone, $instanceGroupManager, $optParams = [])
  {
    $params = ['project' => $project, 'zone' => $zone, 'instanceGroupManager' => $instanceGroupManager];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], InstanceGroupManagerResizeRequestsListResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceGroupManagerResizeRequests::class, 'Google_Service_Compute_Resource_InstanceGroupManagerResizeRequests');
