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

use Google\Service\VMwareEngine\ListLoggingServersResponse;
use Google\Service\VMwareEngine\LoggingServer;
use Google\Service\VMwareEngine\Operation;

/**
 * The "loggingServers" collection of methods.
 * Typical usage is:
 *  <code>
 *   $vmwareengineService = new Google\Service\VMwareEngine(...);
 *   $loggingServers = $vmwareengineService->projects_locations_privateClouds_loggingServers;
 *  </code>
 */
class ProjectsLocationsPrivateCloudsLoggingServers extends \Google\Service\Resource
{
  /**
   * Create a new logging server for a given private cloud.
   * (loggingServers.create)
   *
   * @param string $parent Required. The resource name of the private cloud to
   * create a new Logging Server in. Resource names are schemeless URIs that
   * follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-cloud`
   * @param LoggingServer $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string loggingServerId Required. The user-provided identifier of
   * the `LoggingServer` to be created. This identifier must be unique among
   * `LoggingServer` resources within the parent and becomes the final token in
   * the name URI. The identifier must meet the following requirements: * Only
   * contains 1-63 alphanumeric characters and hyphens * Begins with an
   * alphabetical character * Ends with a non-hyphen character * Not formatted as
   * a UUID * Complies with [RFC
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
  public function create($parent, LoggingServer $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single logging server. (loggingServers.delete)
   *
   * @param string $name Required. The resource name of the logging server to
   * delete. Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/loggingServers/my-logging-server`
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
   * Gets details of a logging server. (loggingServers.get)
   *
   * @param string $name Required. The resource name of the Logging Server to
   * retrieve. Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/loggingServers/my-logging-server`
   * @param array $optParams Optional parameters.
   * @return LoggingServer
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], LoggingServer::class);
  }
  /**
   * Lists logging servers configured for a given private cloud.
   * (loggingServers.listProjectsLocationsPrivateCloudsLoggingServers)
   *
   * @param string $parent Required. The resource name of the private cloud to be
   * queried for logging servers. Resource names are schemeless URIs that follow
   * the conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/us-central1-a/privateClouds/my-cloud`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter A filter expression that matches resources returned
   * in the response. The expression must specify the field name, a comparison
   * operator, and the value that you want to use for filtering. The value must be
   * a string, a number, or a boolean. The comparison operator must be `=`, `!=`,
   * `>`, or `<`. For example, if you are filtering a list of logging servers, you
   * can exclude the ones named `example-server` by specifying `name != "example-
   * server"`. To filter on multiple expressions, provide each separate expression
   * within parentheses. For example: ``` (name = "example-server") (createTime >
   * "2021-04-12T08:15:10.40Z") ``` By default, each expression is an `AND`
   * expression. However, you can include `AND` and `OR` expressions explicitly.
   * For example: ``` (name = "example-server-1") AND (createTime >
   * "2021-04-12T08:15:10.40Z") OR (name = "example-server-2") ```
   * @opt_param string orderBy Sorts list results by a certain order. By default,
   * returned results are ordered by `name` in ascending order. You can also sort
   * results in descending order based on the `name` value using `orderBy="name
   * desc"`. Currently, only ordering by `name` is supported.
   * @opt_param int pageSize The maximum number of logging servers to return in
   * one page. The service may return fewer than this value. The maximum value is
   * coerced to 1000. The default value of this field is 500.
   * @opt_param string pageToken A page token, received from a previous
   * `ListLoggingServersRequest` call. Provide this to retrieve the subsequent
   * page. When paginating, all other parameters provided to
   * `ListLoggingServersRequest` must match the call that provided the page token.
   * @return ListLoggingServersResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsPrivateCloudsLoggingServers($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListLoggingServersResponse::class);
  }
  /**
   * Updates the parameters of a single logging server. Only fields specified in
   * `update_mask` are applied. (loggingServers.patch)
   *
   * @param string $name Output only. The resource name of this logging server.
   * Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/loggingServers/my-logging-server`
   * @param LoggingServer $postBody
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
   * fields to be overwritten in the `LoggingServer` resource by the update. The
   * fields specified in the `update_mask` are relative to the resource, not the
   * full request. A field will be overwritten if it is in the mask. If the user
   * does not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, LoggingServer $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsPrivateCloudsLoggingServers::class, 'Google_Service_VMwareEngine_Resource_ProjectsLocationsPrivateCloudsLoggingServers');
