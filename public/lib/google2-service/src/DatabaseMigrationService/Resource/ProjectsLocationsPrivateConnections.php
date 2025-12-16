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

namespace Google\Service\DatabaseMigrationService\Resource;

use Google\Service\DatabaseMigrationService\ListPrivateConnectionsResponse;
use Google\Service\DatabaseMigrationService\Operation;
use Google\Service\DatabaseMigrationService\Policy;
use Google\Service\DatabaseMigrationService\PrivateConnection;
use Google\Service\DatabaseMigrationService\SetIamPolicyRequest;
use Google\Service\DatabaseMigrationService\TestIamPermissionsRequest;
use Google\Service\DatabaseMigrationService\TestIamPermissionsResponse;

/**
 * The "privateConnections" collection of methods.
 * Typical usage is:
 *  <code>
 *   $datamigrationService = new Google\Service\DatabaseMigrationService(...);
 *   $privateConnections = $datamigrationService->projects_locations_privateConnections;
 *  </code>
 */
class ProjectsLocationsPrivateConnections extends \Google\Service\Resource
{
  /**
   * Creates a new private connection in a given project and location.
   * (privateConnections.create)
   *
   * @param string $parent Required. The parent that owns the collection of
   * PrivateConnections.
   * @param PrivateConnection $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string privateConnectionId Required. The private connection
   * identifier.
   * @opt_param string requestId Optional. A unique ID used to identify the
   * request. If the server receives two requests with the same ID, then the
   * second request is ignored. It is recommended to always set this value to a
   * UUID. The ID must contain only letters (a-z, A-Z), numbers (0-9), underscores
   * (_), and hyphens (-). The maximum length is 40 characters.
   * @opt_param bool skipValidation Optional. If set to true, will skip
   * validations.
   * @opt_param bool validateOnly Optional. For PSC Interface only - get the
   * tenant project before creating the resource.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, PrivateConnection $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single Database Migration Service private connection.
   * (privateConnections.delete)
   *
   * @param string $name Required. The name of the private connection to delete.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A unique ID used to identify the
   * request. If the server receives two requests with the same ID, then the
   * second request is ignored. It is recommended to always set this value to a
   * UUID. The ID must contain only letters (a-z, A-Z), numbers (0-9), underscores
   * (_), and hyphens (-). The maximum length is 40 characters.
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
   * Gets details of a single private connection. (privateConnections.get)
   *
   * @param string $name Required. The name of the private connection to get.
   * @param array $optParams Optional parameters.
   * @return PrivateConnection
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], PrivateConnection::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set.
   * (privateConnections.getIamPolicy)
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
   * Retrieves a list of private connections in a given project and location.
   * (privateConnections.listProjectsLocationsPrivateConnections)
   *
   * @param string $parent Required. The parent that owns the collection of
   * private connections.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter A filter expression that filters private connections
   * listed in the response. The expression must specify the field name, a
   * comparison operator, and the value that you want to use for filtering. The
   * value must be a string, a number, or a boolean. The comparison operator must
   * be either =, !=, >, or <. For example, list private connections created this
   * year by specifying **createTime %gt; 2021-01-01T00:00:00.000000000Z**.
   * @opt_param string orderBy Order by fields for the result.
   * @opt_param int pageSize Maximum number of private connections to return. If
   * unspecified, at most 50 private connections that are returned. The maximum
   * value is 1000; values above 1000 are coerced to 1000.
   * @opt_param string pageToken Page token received from a previous
   * `ListPrivateConnections` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListPrivateConnections`
   * must match the call that provided the page token.
   * @return ListPrivateConnectionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsPrivateConnections($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListPrivateConnectionsResponse::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (privateConnections.setIamPolicy)
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
   * (privateConnections.testIamPermissions)
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
class_alias(ProjectsLocationsPrivateConnections::class, 'Google_Service_DatabaseMigrationService_Resource_ProjectsLocationsPrivateConnections');
