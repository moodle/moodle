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

namespace Google\Service\APIhub\Resource;

use Google\Service\APIhub\ApihubEmpty;
use Google\Service\APIhub\GoogleCloudApihubV1Dependency;
use Google\Service\APIhub\GoogleCloudApihubV1ListDependenciesResponse;

/**
 * The "dependencies" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apihubService = new Google\Service\APIhub(...);
 *   $dependencies = $apihubService->projects_locations_dependencies;
 *  </code>
 */
class ProjectsLocationsDependencies extends \Google\Service\Resource
{
  /**
   * Create a dependency between two entities in the API hub.
   * (dependencies.create)
   *
   * @param string $parent Required. The parent resource for the dependency
   * resource. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudApihubV1Dependency $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string dependencyId Optional. The ID to use for the dependency
   * resource, which will become the final component of the dependency's resource
   * name. This field is optional. * If provided, the same will be used. The
   * service will throw an error if duplicate id is provided by the client. * If
   * not provided, a system generated id will be used. This value should be 4-500
   * characters, and valid characters are `a-z[0-9]-_`.
   * @return GoogleCloudApihubV1Dependency
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudApihubV1Dependency $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudApihubV1Dependency::class);
  }
  /**
   * Delete the dependency resource. (dependencies.delete)
   *
   * @param string $name Required. The name of the dependency resource to delete.
   * Format: `projects/{project}/locations/{location}/dependencies/{dependency}`
   * @param array $optParams Optional parameters.
   * @return ApihubEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], ApihubEmpty::class);
  }
  /**
   * Get details about a dependency resource in the API hub. (dependencies.get)
   *
   * @param string $name Required. The name of the dependency resource to
   * retrieve. Format:
   * `projects/{project}/locations/{location}/dependencies/{dependency}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1Dependency
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApihubV1Dependency::class);
  }
  /**
   * List dependencies based on the provided filter and pagination parameters.
   * (dependencies.listProjectsLocationsDependencies)
   *
   * @param string $parent Required. The parent which owns this collection of
   * dependency resources. Format: `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression that filters the list of
   * Dependencies. A filter expression consists of a field name, a comparison
   * operator, and a value for filtering. The value must be a string. Allowed
   * comparison operator is `=`. Filters are not case sensitive. The following
   * fields in the `Dependency` are eligible for filtering: *
   * `consumer.operation_resource_name` - The operation resource name for the
   * consumer entity involved in a dependency. Allowed comparison operators: `=`.
   * * `consumer.external_api_resource_name` - The external api resource name for
   * the consumer entity involved in a dependency. Allowed comparison operators:
   * `=`. * `supplier.operation_resource_name` - The operation resource name for
   * the supplier entity involved in a dependency. Allowed comparison operators:
   * `=`. * `supplier.external_api_resource_name` - The external api resource name
   * for the supplier entity involved in a dependency. Allowed comparison
   * operators: `=`. Expressions are combined with either `AND` logic operator or
   * `OR` logical operator but not both of them together i.e. only one of the
   * `AND` or `OR` operator can be used throughout the filter string and both the
   * operators cannot be used together. No other logical operators are supported.
   * At most three filter fields are allowed in the filter string and if provided
   * more than that then `INVALID_ARGUMENT` error is returned by the API. For
   * example, `consumer.operation_resource_name =
   * \"projects/p1/locations/global/apis/a1/versions/v1/operations/o1\" OR
   * supplier.operation_resource_name =
   * \"projects/p1/locations/global/apis/a1/versions/v1/operations/o1\"` - The
   * dependencies with either consumer or supplier operation resource name as
   * _projects/p1/locations/global/apis/a1/versions/v1/operations/o1_.
   * @opt_param int pageSize Optional. The maximum number of dependency resources
   * to return. The service may return fewer than this value. If unspecified, at
   * most 50 dependencies will be returned. The maximum value is 1000; values
   * above 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListDependencies` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListDependencies` must match
   * the call that provided the page token.
   * @return GoogleCloudApihubV1ListDependenciesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDependencies($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApihubV1ListDependenciesResponse::class);
  }
  /**
   * Update a dependency based on the update_mask provided in the request. The
   * following fields in the dependency can be updated: * description
   * (dependencies.patch)
   *
   * @param string $name Identifier. The name of the dependency in the API Hub.
   * Format: `projects/{project}/locations/{location}/dependencies/{dependency}`
   * @param GoogleCloudApihubV1Dependency $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to update.
   * @return GoogleCloudApihubV1Dependency
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudApihubV1Dependency $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudApihubV1Dependency::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDependencies::class, 'Google_Service_APIhub_Resource_ProjectsLocationsDependencies');
