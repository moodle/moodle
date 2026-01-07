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

namespace Google\Service\NetAppFiles\Resource;

use Google\Service\NetAppFiles\BackupPolicy;
use Google\Service\NetAppFiles\ListBackupPoliciesResponse;
use Google\Service\NetAppFiles\Operation;

/**
 * The "backupPolicies" collection of methods.
 * Typical usage is:
 *  <code>
 *   $netappService = new Google\Service\NetAppFiles(...);
 *   $backupPolicies = $netappService->projects_locations_backupPolicies;
 *  </code>
 */
class ProjectsLocationsBackupPolicies extends \Google\Service\Resource
{
  /**
   * Creates new backup policy (backupPolicies.create)
   *
   * @param string $parent Required. The location to create the backup policies
   * of, in the format `projects/{project_id}/locations/{location}`
   * @param BackupPolicy $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string backupPolicyId Required. The ID to use for the backup
   * policy. The ID must be unique within the specified location. Must contain
   * only letters, numbers and hyphen, with the first character a letter, the last
   * a letter or a number, and a 63 character maximum.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, BackupPolicy $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Warning! This operation will permanently delete the backup policy.
   * (backupPolicies.delete)
   *
   * @param string $name Required. The backup policy resource name, in the format
   * `projects/{project_id}/locations/{location}/backupPolicies/{backup_policy_id}
   * `
   * @param array $optParams Optional parameters.
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
   * Returns the description of the specified backup policy by backup_policy_id.
   * (backupPolicies.get)
   *
   * @param string $name Required. The backupPolicy resource name, in the format `
   * projects/{project_id}/locations/{location}/backupPolicies/{backup_policy_id}`
   * @param array $optParams Optional parameters.
   * @return BackupPolicy
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], BackupPolicy::class);
  }
  /**
   * Returns list of all available backup policies.
   * (backupPolicies.listProjectsLocationsBackupPolicies)
   *
   * @param string $parent Required. Parent value for ListBackupPoliciesRequest
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Filtering results
   * @opt_param string orderBy Hint for how to order the results
   * @opt_param int pageSize Requested page size. Server may return fewer items
   * than requested. If unspecified, the server will pick an appropriate default.
   * @opt_param string pageToken A token identifying a page of results the server
   * should return.
   * @return ListBackupPoliciesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsBackupPolicies($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListBackupPoliciesResponse::class);
  }
  /**
   * Updates settings of a specific backup policy. (backupPolicies.patch)
   *
   * @param string $name Identifier. The resource name of the backup policy.
   * Format: `projects/{project_id}/locations/{location}/backupPolicies/{backup_po
   * licy_id}`.
   * @param BackupPolicy $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the Backup Policy resource by the update. The
   * fields specified in the update_mask are relative to the resource, not the
   * full request. A field will be overwritten if it is in the mask. If the user
   * does not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, BackupPolicy $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsBackupPolicies::class, 'Google_Service_NetAppFiles_Resource_ProjectsLocationsBackupPolicies');
