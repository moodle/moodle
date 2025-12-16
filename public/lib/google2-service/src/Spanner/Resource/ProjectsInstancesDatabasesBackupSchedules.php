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

namespace Google\Service\Spanner\Resource;

use Google\Service\Spanner\BackupSchedule;
use Google\Service\Spanner\GetIamPolicyRequest;
use Google\Service\Spanner\ListBackupSchedulesResponse;
use Google\Service\Spanner\Policy;
use Google\Service\Spanner\SetIamPolicyRequest;
use Google\Service\Spanner\SpannerEmpty;
use Google\Service\Spanner\TestIamPermissionsRequest;
use Google\Service\Spanner\TestIamPermissionsResponse;

/**
 * The "backupSchedules" collection of methods.
 * Typical usage is:
 *  <code>
 *   $spannerService = new Google\Service\Spanner(...);
 *   $backupSchedules = $spannerService->projects_instances_databases_backupSchedules;
 *  </code>
 */
class ProjectsInstancesDatabasesBackupSchedules extends \Google\Service\Resource
{
  /**
   * Creates a new backup schedule. (backupSchedules.create)
   *
   * @param string $parent Required. The name of the database that this backup
   * schedule applies to.
   * @param BackupSchedule $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string backupScheduleId Required. The Id to use for the backup
   * schedule. The `backup_schedule_id` appended to `parent` forms the full backup
   * schedule name of the form `projects//instances//databases//backupSchedules/`.
   * @return BackupSchedule
   * @throws \Google\Service\Exception
   */
  public function create($parent, BackupSchedule $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], BackupSchedule::class);
  }
  /**
   * Deletes a backup schedule. (backupSchedules.delete)
   *
   * @param string $name Required. The name of the schedule to delete. Values are
   * of the form `projects//instances//databases//backupSchedules/`.
   * @param array $optParams Optional parameters.
   * @return SpannerEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], SpannerEmpty::class);
  }
  /**
   * Gets backup schedule for the input schedule name. (backupSchedules.get)
   *
   * @param string $name Required. The name of the schedule to retrieve. Values
   * are of the form `projects//instances//databases//backupSchedules/`.
   * @param array $optParams Optional parameters.
   * @return BackupSchedule
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], BackupSchedule::class);
  }
  /**
   * Gets the access control policy for a database or backup resource. Returns an
   * empty policy if a database or backup exists but does not have a policy set.
   * Authorization requires `spanner.databases.getIamPolicy` permission on
   * resource. For backups, authorization requires `spanner.backups.getIamPolicy`
   * permission on resource. For backup schedules, authorization requires
   * `spanner.backupSchedules.getIamPolicy` permission on resource.
   * (backupSchedules.getIamPolicy)
   *
   * @param string $resource REQUIRED: The Cloud Spanner resource for which the
   * policy is being retrieved. The format is `projects//instances/` for instance
   * resources and `projects//instances//databases/` for database resources.
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
   * Lists all the backup schedules for the database.
   * (backupSchedules.listProjectsInstancesDatabasesBackupSchedules)
   *
   * @param string $parent Required. Database is the parent resource whose backup
   * schedules should be listed. Values are of the form
   * projects//instances//databases/
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Number of backup schedules to be returned
   * in the response. If 0 or less, defaults to the server's maximum allowed page
   * size.
   * @opt_param string pageToken Optional. If non-empty, `page_token` should
   * contain a next_page_token from a previous ListBackupSchedulesResponse to the
   * same `parent`.
   * @return ListBackupSchedulesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsInstancesDatabasesBackupSchedules($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListBackupSchedulesResponse::class);
  }
  /**
   * Updates a backup schedule. (backupSchedules.patch)
   *
   * @param string $name Identifier. Output only for the CreateBackupSchedule
   * operation. Required for the UpdateBackupSchedule operation. A globally unique
   * identifier for the backup schedule which cannot be changed. Values are of the
   * form `projects//instances//databases//backupSchedules/a-z*[a-z0-9]` The final
   * segment of the name must be between 2 and 60 characters in length.
   * @param BackupSchedule $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. A mask specifying which fields in the
   * BackupSchedule resource should be updated. This mask is relative to the
   * BackupSchedule resource, not to the request message. The field mask must
   * always be specified; this prevents any future fields from being erased
   * accidentally.
   * @return BackupSchedule
   * @throws \Google\Service\Exception
   */
  public function patch($name, BackupSchedule $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], BackupSchedule::class);
  }
  /**
   * Sets the access control policy on a database or backup resource. Replaces any
   * existing policy. Authorization requires `spanner.databases.setIamPolicy`
   * permission on resource. For backups, authorization requires
   * `spanner.backups.setIamPolicy` permission on resource. For backup schedules,
   * authorization requires `spanner.backupSchedules.setIamPolicy` permission on
   * resource. (backupSchedules.setIamPolicy)
   *
   * @param string $resource REQUIRED: The Cloud Spanner resource for which the
   * policy is being set. The format is `projects//instances/` for instance
   * resources and `projects//instances//databases/` for databases resources.
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
   * Returns permissions that the caller has on the specified database or backup
   * resource. Attempting this RPC on a non-existent Cloud Spanner database will
   * result in a NOT_FOUND error if the user has `spanner.databases.list`
   * permission on the containing Cloud Spanner instance. Otherwise returns an
   * empty set of permissions. Calling this method on a backup that does not exist
   * will result in a NOT_FOUND error if the user has `spanner.backups.list`
   * permission on the containing instance. Calling this method on a backup
   * schedule that does not exist will result in a NOT_FOUND error if the user has
   * `spanner.backupSchedules.list` permission on the containing database.
   * (backupSchedules.testIamPermissions)
   *
   * @param string $resource REQUIRED: The Cloud Spanner resource for which
   * permissions are being tested. The format is `projects//instances/` for
   * instance resources and `projects//instances//databases/` for database
   * resources.
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
class_alias(ProjectsInstancesDatabasesBackupSchedules::class, 'Google_Service_Spanner_Resource_ProjectsInstancesDatabasesBackupSchedules');
