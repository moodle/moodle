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

use Google\Service\NetAppFiles\Backup;
use Google\Service\NetAppFiles\ListBackupsResponse;
use Google\Service\NetAppFiles\Operation;

/**
 * The "backups" collection of methods.
 * Typical usage is:
 *  <code>
 *   $netappService = new Google\Service\NetAppFiles(...);
 *   $backups = $netappService->projects_locations_backupVaults_backups;
 *  </code>
 */
class ProjectsLocationsBackupVaultsBackups extends \Google\Service\Resource
{
  /**
   * Creates a backup from the volume specified in the request The backup can be
   * created from the given snapshot if specified in the request. If no snapshot
   * specified, there'll be a new snapshot taken to initiate the backup creation.
   * (backups.create)
   *
   * @param string $parent Required. The NetApp backupVault to create the backups
   * of, in the format `projects/locations/backupVaults/{backup_vault_id}`
   * @param Backup $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string backupId Required. The ID to use for the backup. The ID
   * must be unique within the specified backupVault. Must contain only letters,
   * numbers and hyphen, with the first character a letter, the last a letter or a
   * number, and a 63 character maximum.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Backup $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Warning! This operation will permanently delete the backup. (backups.delete)
   *
   * @param string $name Required. The backup resource name, in the format `projec
   * ts/{project_id}/locations/{location}/backupVaults/{backup_vault_id}/backups/{
   * backup_id}`
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
   * Returns the description of the specified backup (backups.get)
   *
   * @param string $name Required. The backup resource name, in the format `projec
   * ts/{project_id}/locations/{location}/backupVaults/{backup_vault_id}/backups/{
   * backup_id}`
   * @param array $optParams Optional parameters.
   * @return Backup
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Backup::class);
  }
  /**
   * Returns descriptions of all backups for a backupVault.
   * (backups.listProjectsLocationsBackupVaultsBackups)
   *
   * @param string $parent Required. The backupVault for which to retrieve backup
   * information, in the format
   * `projects/{project_id}/locations/{location}/backupVaults/{backup_vault_id}`.
   * To retrieve backup information for all locations, use "-" for the
   * `{location}` value. To retrieve backup information for all backupVaults, use
   * "-" for the `{backup_vault_id}` value. To retrieve backup information for a
   * volume, use "-" for the `{backup_vault_id}` value and specify volume full
   * name with the filter.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter The standard list filter. If specified, backups will
   * be returned based on the attribute name that matches the filter expression.
   * If empty, then no backups are filtered out. See https://google.aip.dev/160
   * @opt_param string orderBy Sort results. Supported values are "name", "name
   * desc" or "" (unsorted).
   * @opt_param int pageSize The maximum number of items to return. The service
   * may return fewer than this value. The maximum value is 1000; values above
   * 1000 will be coerced to 1000.
   * @opt_param string pageToken The next_page_token value to use if there are
   * additional results to retrieve for this list request.
   * @return ListBackupsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsBackupVaultsBackups($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListBackupsResponse::class);
  }
  /**
   * Update backup with full spec. (backups.patch)
   *
   * @param string $name Identifier. The resource name of the backup. Format: `pro
   * jects/{project_id}/locations/{location}/backupVaults/{backup_vault_id}/backup
   * s/{backup_id}`.
   * @param Backup $postBody
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
  public function patch($name, Backup $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsBackupVaultsBackups::class, 'Google_Service_NetAppFiles_Resource_ProjectsLocationsBackupVaultsBackups');
