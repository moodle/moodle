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

use Google\Service\DatabaseMigrationService\ListMigrationJobObjectsResponse;
use Google\Service\DatabaseMigrationService\LookupMigrationJobObjectRequest;
use Google\Service\DatabaseMigrationService\MigrationJobObject;
use Google\Service\DatabaseMigrationService\Policy;
use Google\Service\DatabaseMigrationService\SetIamPolicyRequest;
use Google\Service\DatabaseMigrationService\TestIamPermissionsRequest;
use Google\Service\DatabaseMigrationService\TestIamPermissionsResponse;

/**
 * The "objects" collection of methods.
 * Typical usage is:
 *  <code>
 *   $datamigrationService = new Google\Service\DatabaseMigrationService(...);
 *   $objects = $datamigrationService->projects_locations_migrationJobs_objects;
 *  </code>
 */
class ProjectsLocationsMigrationJobsObjects extends \Google\Service\Resource
{
  /**
   * Use this method to get details about a migration job object. (objects.get)
   *
   * @param string $name Required. The name of the migration job object resource
   * to get.
   * @param array $optParams Optional parameters.
   * @return MigrationJobObject
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], MigrationJobObject::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set. (objects.getIamPolicy)
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
   * Use this method to list the objects of a specific migration job.
   * (objects.listProjectsLocationsMigrationJobsObjects)
   *
   * @param string $parent Required. The parent migration job that owns the
   * collection of objects.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of objects to return. Default is 50.
   * The maximum value is 1000; values above 1000 will be coerced to 1000.
   * @opt_param string pageToken Page token received from a previous
   * `ListMigrationJObObjectsRequest` call. Provide this to retrieve the
   * subsequent page. When paginating, all other parameters provided to
   * `ListMigrationJobObjectsRequest` must match the call that provided the page
   * token.
   * @return ListMigrationJobObjectsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsMigrationJobsObjects($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListMigrationJobObjectsResponse::class);
  }
  /**
   * Use this method to look up a migration job object by its source object
   * identifier. (objects.lookup)
   *
   * @param string $parent Required. The parent migration job that owns the
   * collection of objects.
   * @param LookupMigrationJobObjectRequest $postBody
   * @param array $optParams Optional parameters.
   * @return MigrationJobObject
   * @throws \Google\Service\Exception
   */
  public function lookup($parent, LookupMigrationJobObjectRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('lookup', [$params], MigrationJobObject::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (objects.setIamPolicy)
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
   * This operation may "fail open" without warning. (objects.testIamPermissions)
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
class_alias(ProjectsLocationsMigrationJobsObjects::class, 'Google_Service_DatabaseMigrationService_Resource_ProjectsLocationsMigrationJobsObjects');
