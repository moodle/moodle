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

namespace Google\Service\CloudRedis\Resource;

use Google\Service\CloudRedis\Backup;
use Google\Service\CloudRedis\ExportBackupRequest;
use Google\Service\CloudRedis\ListBackupsResponse;
use Google\Service\CloudRedis\Operation;

/**
 * The "backups" collection of methods.
 * Typical usage is:
 *  <code>
 *   $redisService = new Google\Service\CloudRedis(...);
 *   $backups = $redisService->projects_locations_backupCollections_backups;
 *  </code>
 */
class ProjectsLocationsBackupCollectionsBackups extends \Google\Service\Resource
{
  /**
   * Deletes a specific backup. (backups.delete)
   *
   * @param string $name Required. Redis backup resource name using the form: `pro
   * jects/{project_id}/locations/{location_id}/backupCollections/{backup_collecti
   * on_id}/backups/{backup_id}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. Idempotent request UUID.
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
   * Exports a specific backup to a customer target Cloud Storage URI.
   * (backups.export)
   *
   * @param string $name Required. Redis backup resource name using the form: `pro
   * jects/{project_id}/locations/{location_id}/backupCollections/{backup_collecti
   * on_id}/backups/{backup_id}`
   * @param ExportBackupRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function export($name, ExportBackupRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('export', [$params], Operation::class);
  }
  /**
   * Gets the details of a specific backup. (backups.get)
   *
   * @param string $name Required. Redis backup resource name using the form: `pro
   * jects/{project_id}/locations/{location_id}/backupCollections/{backup_collecti
   * on_id}/backups/{backup_id}`
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
   * Lists all backups owned by a backup collection.
   * (backups.listProjectsLocationsBackupCollectionsBackups)
   *
   * @param string $parent Required. The resource name of the backupCollection
   * using the form: `projects/{project_id}/locations/{location_id}/backupCollecti
   * ons/{backup_collection_id}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of items to return. If
   * not specified, a default value of 1000 will be used by the service.
   * Regardless of the page_size value, the response may include a partial list
   * and a caller should only rely on response's `next_page_token` to determine if
   * there are more clusters left to be queried.
   * @opt_param string pageToken Optional. The `next_page_token` value returned
   * from a previous [ListBackupCollections] request, if any.
   * @return ListBackupsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsBackupCollectionsBackups($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListBackupsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsBackupCollectionsBackups::class, 'Google_Service_CloudRedis_Resource_ProjectsLocationsBackupCollectionsBackups');
