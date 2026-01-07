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

use Google\Service\NetAppFiles\BackupVault;
use Google\Service\NetAppFiles\ListBackupVaultsResponse;
use Google\Service\NetAppFiles\Operation;

/**
 * The "backupVaults" collection of methods.
 * Typical usage is:
 *  <code>
 *   $netappService = new Google\Service\NetAppFiles(...);
 *   $backupVaults = $netappService->projects_locations_backupVaults;
 *  </code>
 */
class ProjectsLocationsBackupVaults extends \Google\Service\Resource
{
  /**
   * Creates new backup vault (backupVaults.create)
   *
   * @param string $parent Required. The location to create the backup vaults, in
   * the format `projects/{project_id}/locations/{location}`
   * @param BackupVault $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string backupVaultId Required. The ID to use for the backupVault.
   * The ID must be unique within the specified location. Must contain only
   * letters, numbers and hyphen, with the first character a letter, the last a
   * letter or a number, and a 63 character maximum.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, BackupVault $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Warning! This operation will permanently delete the backup vault.
   * (backupVaults.delete)
   *
   * @param string $name Required. The backupVault resource name, in the format
   * `projects/{project_id}/locations/{location}/backupVaults/{backup_vault_id}`
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
   * Returns the description of the specified backup vault (backupVaults.get)
   *
   * @param string $name Required. The backupVault resource name, in the format
   * `projects/{project_id}/locations/{location}/backupVaults/{backup_vault_id}`
   * @param array $optParams Optional parameters.
   * @return BackupVault
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], BackupVault::class);
  }
  /**
   * Returns list of all available backup vaults.
   * (backupVaults.listProjectsLocationsBackupVaults)
   *
   * @param string $parent Required. The location for which to retrieve
   * backupVault information, in the format
   * `projects/{project_id}/locations/{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter List filter.
   * @opt_param string orderBy Sort results. Supported values are "name", "name
   * desc" or "" (unsorted).
   * @opt_param int pageSize The maximum number of items to return.
   * @opt_param string pageToken The next_page_token value to use if there are
   * additional results to retrieve for this list request.
   * @return ListBackupVaultsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsBackupVaults($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListBackupVaultsResponse::class);
  }
  /**
   * Updates the settings of a specific backup vault. (backupVaults.patch)
   *
   * @param string $name Identifier. The resource name of the backup vault.
   * Format:
   * `projects/{project_id}/locations/{location}/backupVaults/{backup_vault_id}`.
   * @param BackupVault $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the Backup resource to be updated. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, BackupVault $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsBackupVaults::class, 'Google_Service_NetAppFiles_Resource_ProjectsLocationsBackupVaults');
