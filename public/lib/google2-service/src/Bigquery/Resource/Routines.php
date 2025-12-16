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

namespace Google\Service\Bigquery\Resource;

use Google\Service\Bigquery\GetIamPolicyRequest;
use Google\Service\Bigquery\ListRoutinesResponse;
use Google\Service\Bigquery\Policy;
use Google\Service\Bigquery\Routine;
use Google\Service\Bigquery\SetIamPolicyRequest;
use Google\Service\Bigquery\TestIamPermissionsRequest;
use Google\Service\Bigquery\TestIamPermissionsResponse;

/**
 * The "routines" collection of methods.
 * Typical usage is:
 *  <code>
 *   $bigqueryService = new Google\Service\Bigquery(...);
 *   $routines = $bigqueryService->routines;
 *  </code>
 */
class Routines extends \Google\Service\Resource
{
  /**
   * Deletes the routine specified by routineId from the dataset.
   * (routines.delete)
   *
   * @param string $projectId Required. Project ID of the routine to delete
   * @param string $datasetId Required. Dataset ID of the routine to delete
   * @param string $routineId Required. Routine ID of the routine to delete
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function delete($projectId, $datasetId, $routineId, $optParams = [])
  {
    $params = ['projectId' => $projectId, 'datasetId' => $datasetId, 'routineId' => $routineId];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params]);
  }
  /**
   * Gets the specified routine resource by routine ID. (routines.get)
   *
   * @param string $projectId Required. Project ID of the requested routine
   * @param string $datasetId Required. Dataset ID of the requested routine
   * @param string $routineId Required. Routine ID of the requested routine
   * @param array $optParams Optional parameters.
   *
   * @opt_param string readMask If set, only the Routine fields in the field mask
   * are returned in the response. If unset, all Routine fields are returned.
   * @return Routine
   * @throws \Google\Service\Exception
   */
  public function get($projectId, $datasetId, $routineId, $optParams = [])
  {
    $params = ['projectId' => $projectId, 'datasetId' => $datasetId, 'routineId' => $routineId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Routine::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set. (routines.getIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param GetIamPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function getIamPolicy($resource, GetIamPolicyRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('getIamPolicy', [$params], Policy::class);
  }
  /**
   * Creates a new routine in the dataset. (routines.insert)
   *
   * @param string $projectId Required. Project ID of the new routine
   * @param string $datasetId Required. Dataset ID of the new routine
   * @param Routine $postBody
   * @param array $optParams Optional parameters.
   * @return Routine
   * @throws \Google\Service\Exception
   */
  public function insert($projectId, $datasetId, Routine $postBody, $optParams = [])
  {
    $params = ['projectId' => $projectId, 'datasetId' => $datasetId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], Routine::class);
  }
  /**
   * Lists all routines in the specified dataset. Requires the READER dataset
   * role. (routines.listRoutines)
   *
   * @param string $projectId Required. Project ID of the routines to list
   * @param string $datasetId Required. Dataset ID of the routines to list
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter If set, then only the Routines matching this filter
   * are returned. The supported format is `routineType:{RoutineType}`, where
   * `{RoutineType}` is a RoutineType enum. For example:
   * `routineType:SCALAR_FUNCTION`.
   * @opt_param string maxResults The maximum number of results to return in a
   * single response page. Leverage the page tokens to iterate through the entire
   * collection.
   * @opt_param string pageToken Page token, returned by a previous call, to
   * request the next page of results
   * @opt_param string readMask If set, then only the Routine fields in the field
   * mask, as well as project_id, dataset_id and routine_id, are returned in the
   * response. If unset, then the following Routine fields are returned: etag,
   * project_id, dataset_id, routine_id, routine_type, creation_time,
   * last_modified_time, and language.
   * @return ListRoutinesResponse
   * @throws \Google\Service\Exception
   */
  public function listRoutines($projectId, $datasetId, $optParams = [])
  {
    $params = ['projectId' => $projectId, 'datasetId' => $datasetId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListRoutinesResponse::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (routines.setIamPolicy)
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
   * This operation may "fail open" without warning. (routines.testIamPermissions)
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
  /**
   * Updates information in an existing routine. The update method replaces the
   * entire Routine resource. (routines.update)
   *
   * @param string $projectId Required. Project ID of the routine to update
   * @param string $datasetId Required. Dataset ID of the routine to update
   * @param string $routineId Required. Routine ID of the routine to update
   * @param Routine $postBody
   * @param array $optParams Optional parameters.
   * @return Routine
   * @throws \Google\Service\Exception
   */
  public function update($projectId, $datasetId, $routineId, Routine $postBody, $optParams = [])
  {
    $params = ['projectId' => $projectId, 'datasetId' => $datasetId, 'routineId' => $routineId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], Routine::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Routines::class, 'Google_Service_Bigquery_Resource_Routines');
