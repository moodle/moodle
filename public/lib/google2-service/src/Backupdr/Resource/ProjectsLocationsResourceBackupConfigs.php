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

use Google\Service\Backupdr\ListResourceBackupConfigsResponse;

/**
 * The "resourceBackupConfigs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $backupdrService = new Google\Service\Backupdr(...);
 *   $resourceBackupConfigs = $backupdrService->projects_locations_resourceBackupConfigs;
 *  </code>
 */
class ProjectsLocationsResourceBackupConfigs extends \Google\Service\Resource
{
  /**
   * Lists ResourceBackupConfigs.
   * (resourceBackupConfigs.listProjectsLocationsResourceBackupConfigs)
   *
   * @param string $parent Required. The project and location for which to
   * retrieve resource backup configs. Format:
   * 'projects/{project_id}/locations/{location}'. In Cloud Backup and DR,
   * locations map to Google Cloud regions, for example **us-central1**.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results.
   * @opt_param string orderBy Optional. Hint for how to order the results.
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will use 100 as default.
   * Maximum value is 500 and values above 500 will be coerced to 500.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListResourceBackupConfigsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsResourceBackupConfigs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListResourceBackupConfigsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsResourceBackupConfigs::class, 'Google_Service_Backupdr_Resource_ProjectsLocationsResourceBackupConfigs');
