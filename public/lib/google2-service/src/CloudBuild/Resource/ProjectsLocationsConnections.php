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

namespace Google\Service\CloudBuild\Resource;

use Google\Service\CloudBuild\CloudbuildEmpty;
use Google\Service\CloudBuild\Connection;
use Google\Service\CloudBuild\FetchLinkableRepositoriesResponse;
use Google\Service\CloudBuild\HttpBody;
use Google\Service\CloudBuild\ListConnectionsResponse;
use Google\Service\CloudBuild\Operation;
use Google\Service\CloudBuild\Policy;
use Google\Service\CloudBuild\SetIamPolicyRequest;
use Google\Service\CloudBuild\TestIamPermissionsRequest;
use Google\Service\CloudBuild\TestIamPermissionsResponse;

/**
 * The "connections" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudbuildService = new Google\Service\CloudBuild(...);
 *   $connections = $cloudbuildService->projects_locations_connections;
 *  </code>
 */
class ProjectsLocationsConnections extends \Google\Service\Resource
{
  /**
   * Creates a Connection. (connections.create)
   *
   * @param string $parent Required. Project and location where the connection
   * will be created. Format: `projects/locations`.
   * @param Connection $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string connectionId Required. The ID to use for the Connection,
   * which will become the final component of the Connection's resource name.
   * Names must be unique per-project per-location. Allows alphanumeric characters
   * and any of -._~%!$&'()*+,;=@.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Connection $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single connection. (connections.delete)
   *
   * @param string $name Required. The name of the Connection to delete. Format:
   * `projects/locations/connections`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag The current etag of the connection. If an etag is
   * provided and does not match the current etag of the connection, deletion will
   * be blocked and an ABORTED error will be returned.
   * @opt_param bool validateOnly If set, validate the request, but do not
   * actually post it.
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
   * FetchLinkableRepositories get repositories from SCM that are accessible and
   * could be added to the connection. (connections.fetchLinkableRepositories)
   *
   * @param string $connection Required. The name of the Connection. Format:
   * `projects/locations/connections`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Number of results to return in the list. Default to
   * 20.
   * @opt_param string pageToken Page start.
   * @return FetchLinkableRepositoriesResponse
   * @throws \Google\Service\Exception
   */
  public function fetchLinkableRepositories($connection, $optParams = [])
  {
    $params = ['connection' => $connection];
    $params = array_merge($params, $optParams);
    return $this->call('fetchLinkableRepositories', [$params], FetchLinkableRepositoriesResponse::class);
  }
  /**
   * Gets details of a single connection. (connections.get)
   *
   * @param string $name Required. The name of the Connection to retrieve. Format:
   * `projects/locations/connections`.
   * @param array $optParams Optional parameters.
   * @return Connection
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Connection::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set. (connections.getIamPolicy)
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
   * Lists Connections in a given project and location.
   * (connections.listProjectsLocationsConnections)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * Connections. Format: `projects/locations`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Number of results to return in the list.
   * @opt_param string pageToken Page start.
   * @opt_param bool returnPartialSuccess Optional. If set to true, the response
   * will return partial results when some regions are unreachable. If set to
   * false, the response will fail if any region is unreachable.
   * @return ListConnectionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsConnections($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListConnectionsResponse::class);
  }
  /**
   * Updates a single connection. (connections.patch)
   *
   * @param string $name Immutable. The resource name of the connection, in the
   * format `projects/{project}/locations/{location}/connections/{connection_id}`.
   * @param Connection $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing If set to true, and the connection is not found
   * a new connection will be created. In this situation `update_mask` is ignored.
   * The creation will succeed only if the input connection has all the necessary
   * information (e.g a github_config with both user_oauth_token and
   * installation_id properties).
   * @opt_param string etag The current etag of the connection. If an etag is
   * provided and does not match the current etag of the connection, update will
   * be blocked and an ABORTED error will be returned.
   * @opt_param string updateMask The list of fields to be updated.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Connection $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * ProcessWebhook is called by the external SCM for notifying of events.
   * (connections.processWebhook)
   *
   * @param string $parent Required. Project and location where the webhook will
   * be received. Format: `projects/locations`.
   * @param HttpBody $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string webhookKey Arbitrary additional key to find the matching
   * repository for a webhook event if needed.
   * @return CloudbuildEmpty
   * @throws \Google\Service\Exception
   */
  public function processWebhook($parent, HttpBody $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('processWebhook', [$params], CloudbuildEmpty::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (connections.setIamPolicy)
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
   * (connections.testIamPermissions)
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
class_alias(ProjectsLocationsConnections::class, 'Google_Service_CloudBuild_Resource_ProjectsLocationsConnections');
