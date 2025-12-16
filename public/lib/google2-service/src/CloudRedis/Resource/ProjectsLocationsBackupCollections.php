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

use Google\Service\CloudRedis\BackupCollection;
use Google\Service\CloudRedis\ListBackupCollectionsResponse;

/**
 * The "backupCollections" collection of methods.
 * Typical usage is:
 *  <code>
 *   $redisService = new Google\Service\CloudRedis(...);
 *   $backupCollections = $redisService->projects_locations_backupCollections;
 *  </code>
 */
class ProjectsLocationsBackupCollections extends \Google\Service\Resource
{
  /**
   * Get a backup collection. (backupCollections.get)
   *
   * @param string $name Required. Redis backupCollection resource name using the
   * form: `projects/{project_id}/locations/{location_id}/backupCollections/{backu
   * p_collection_id}` where `location_id` refers to a Google Cloud region.
   * @param array $optParams Optional parameters.
   * @return BackupCollection
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], BackupCollection::class);
  }
  /**
   * Lists all backup collections owned by a consumer project in either the
   * specified location (region) or all locations. If `location_id` is specified
   * as `-` (wildcard), then all regions available to the project are queried, and
   * the results are aggregated.
   * (backupCollections.listProjectsLocationsBackupCollections)
   *
   * @param string $parent Required. The resource name of the backupCollection
   * location using the form: `projects/{project_id}/locations/{location_id}`
   * where `location_id` refers to a Google Cloud region.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of items to return. If
   * not specified, a default value of 1000 will be used by the service.
   * Regardless of the page_size value, the response may include a partial list
   * and a caller should only rely on response's `next_page_token` to determine if
   * there are more clusters left to be queried.
   * @opt_param string pageToken Optional. The `next_page_token` value returned
   * from a previous [ListBackupCollections] request, if any.
   * @return ListBackupCollectionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsBackupCollections($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListBackupCollectionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsBackupCollections::class, 'Google_Service_CloudRedis_Resource_ProjectsLocationsBackupCollections');
