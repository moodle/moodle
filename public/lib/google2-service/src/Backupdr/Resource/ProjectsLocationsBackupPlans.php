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

namespace Google\Service\Backupdr\Resource;

use Google\Service\Backupdr\BackupPlan;
use Google\Service\Backupdr\ListBackupPlansResponse;
use Google\Service\Backupdr\Operation;

/**
 * The "backupPlans" collection of methods.
 * Typical usage is:
 *  <code>
 *   $backupdrService = new Google\Service\Backupdr(...);
 *   $backupPlans = $backupdrService->projects_locations_backupPlans;
 *  </code>
 */
class ProjectsLocationsBackupPlans extends \Google\Service\Resource
{
  /**
   * Create a BackupPlan (backupPlans.create)
   *
   * @param string $parent Required. The `BackupPlan` project and location in the
   * format `projects/{project}/locations/{location}`. In Cloud BackupDR locations
   * map to GCP regions, for example **us-central1**.
   * @param BackupPlan $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string backupPlanId Required. The name of the `BackupPlan` to
   * create. The name must be unique for the specified project and location.The
   * name must start with a lowercase letter followed by up to 62 lowercase
   * letters, numbers, or hyphens. Pattern, /a-z{,62}/.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes since the first
   * request. For example, consider a situation where you make an initial request
   * and t he request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, BackupPlan $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single BackupPlan. (backupPlans.delete)
   *
   * @param string $name Required. The resource name of the `BackupPlan` to
   * delete. Format:
   * `projects/{project}/locations/{location}/backupPlans/{backup_plan}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes after the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
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
   * Gets details of a single BackupPlan. (backupPlans.get)
   *
   * @param string $name Required. The resource name of the `BackupPlan` to
   * retrieve. Format:
   * `projects/{project}/locations/{location}/backupPlans/{backup_plan}`
   * @param array $optParams Optional parameters.
   * @return BackupPlan
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], BackupPlan::class);
  }
  /**
   * Lists BackupPlans in a given project and location.
   * (backupPlans.listProjectsLocationsBackupPlans)
   *
   * @param string $parent Required. The project and location for which to
   * retrieve `BackupPlans` information. Format:
   * `projects/{project}/locations/{location}`. In Cloud BackupDR, locations map
   * to GCP regions, for e.g. **us-central1**. To retrieve backup plans for all
   * locations, use "-" for the `{location}` value.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Field match expression used to filter the
   * results.
   * @opt_param string orderBy Optional. Field by which to sort the results.
   * @opt_param int pageSize Optional. The maximum number of `BackupPlans` to
   * return in a single response. If not specified, a default value will be chosen
   * by the service. Note that the response may include a partial list and a
   * caller should only rely on the response's next_page_token to determine if
   * there are more instances left to be queried.
   * @opt_param string pageToken Optional. The value of next_page_token received
   * from a previous `ListBackupPlans` call. Provide this to retrieve the
   * subsequent page in a multi-page list of results. When paginating, all other
   * parameters provided to `ListBackupPlans` must match the call that provided
   * the page token.
   * @return ListBackupPlansResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsBackupPlans($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListBackupPlansResponse::class);
  }
  /**
   * Update a BackupPlan. (backupPlans.patch)
   *
   * @param string $name Output only. Identifier. The resource name of the
   * `BackupPlan`. Format:
   * `projects/{project}/locations/{location}/backupPlans/{backup_plan}`
   * @param BackupPlan $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes since the first
   * request. For example, consider a situation where you make an initial request
   * and t he request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Required. The list of fields to update. Field
   * mask is used to specify the fields to be overwritten in the BackupPlan
   * resource by the update. The fields specified in the update_mask are relative
   * to the resource, not the full request. A field will be overwritten if it is
   * in the mask. If the user does not provide a mask then the request will fail.
   * Currently, these fields are supported in update: description, schedules,
   * retention period, adding and removing Backup Rules.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, BackupPlan $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsBackupPlans::class, 'Google_Service_Backupdr_Resource_ProjectsLocationsBackupPlans');
