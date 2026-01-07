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

use Google\Service\VMwareEngine\ListNodeTypesResponse;
use Google\Service\VMwareEngine\NodeType;

/**
 * The "nodeTypes" collection of methods.
 * Typical usage is:
 *  <code>
 *   $vmwareengineService = new Google\Service\VMwareEngine(...);
 *   $nodeTypes = $vmwareengineService->projects_locations_nodeTypes;
 *  </code>
 */
class ProjectsLocationsNodeTypes extends \Google\Service\Resource
{
  /**
   * Gets details of a single `NodeType`. (nodeTypes.get)
   *
   * @param string $name Required. The resource name of the node type to retrieve.
   * Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-proj/locations/us-central1-a/nodeTypes/standard-72`
   * @param array $optParams Optional parameters.
   * @return NodeType
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], NodeType::class);
  }
  /**
   * Lists node types (nodeTypes.listProjectsLocationsNodeTypes)
   *
   * @param string $parent Required. The resource name of the location to be
   * queried for node types. Resource names are schemeless URIs that follow the
   * conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/us-central1-a`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter A filter expression that matches resources returned
   * in the response. The expression must specify the field name, a comparison
   * operator, and the value that you want to use for filtering. The value must be
   * a string, a number, or a boolean. The comparison operator must be `=`, `!=`,
   * `>`, or `<`. For example, if you are filtering a list of node types, you can
   * exclude the ones named `standard-72` by specifying `name != "standard-72"`.
   * To filter on multiple expressions, provide each separate expression within
   * parentheses. For example: ``` (name = "standard-72") (virtual_cpu_count > 2)
   * ``` By default, each expression is an `AND` expression. However, you can
   * include `AND` and `OR` expressions explicitly. For example: ``` (name =
   * "standard-96") AND (virtual_cpu_count > 2) OR (name = "standard-72") ```
   * @opt_param int pageSize The maximum number of node types to return in one
   * page. The service may return fewer than this value. The maximum value is
   * coerced to 1000. The default value of this field is 500.
   * @opt_param string pageToken A page token, received from a previous
   * `ListNodeTypes` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListNodeTypes` must match the
   * call that provided the page token.
   * @return ListNodeTypesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsNodeTypes($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListNodeTypesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsNodeTypes::class, 'Google_Service_VMwareEngine_Resource_ProjectsLocationsNodeTypes');
