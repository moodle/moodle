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

namespace Google\Service\BackupforGKE\Resource;

use Google\Service\BackupforGKE\BackupPlanBinding;
use Google\Service\BackupforGKE\ListBackupPlanBindingsResponse;

/**
 * The "backupPlanBindings" collection of methods.
 * Typical usage is:
 *  <code>
 *   $gkebackupService = new Google\Service\BackupforGKE(...);
 *   $backupPlanBindings = $gkebackupService->projects_locations_backupChannels_backupPlanBindings;
 *  </code>
 */
class ProjectsLocationsBackupChannelsBackupPlanBindings extends \Google\Service\Resource
{
  /**
   * Retrieve the details of a single BackupPlanBinding. (backupPlanBindings.get)
   *
   * @param string $name Required. Fully qualified BackupPlanBinding name. Format:
   * `projects/locations/backupChannels/backupPlanBindings`
   * @param array $optParams Optional parameters.
   * @return BackupPlanBinding
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], BackupPlanBinding::class);
  }
  /**
   * Lists BackupPlanBindings in a given location.
   * (backupPlanBindings.listProjectsLocationsBackupChannelsBackupPlanBindings)
   *
   * @param string $parent Required. The BackupChannel that contains the
   * BackupPlanBindings to list. Format: `projects/locations/backupChannels`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Field match expression used to filter the
   * results.
   * @opt_param string orderBy Optional. Field by which to sort the results.
   * @opt_param int pageSize Optional. The target number of results to return in a
   * single response. If not specified, a default value will be chosen by the
   * service. Note that the response may include a partial list and a caller
   * should only rely on the response's next_page_token to determine if there are
   * more instances left to be queried.
   * @opt_param string pageToken Optional. The value of next_page_token received
   * from a previous `ListBackupPlanBindings` call. Provide this to retrieve the
   * subsequent page in a multi-page list of results. When paginating, all other
   * parameters provided to `ListBackupPlanBindings` must match the call that
   * provided the page token.
   * @return ListBackupPlanBindingsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsBackupChannelsBackupPlanBindings($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListBackupPlanBindingsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsBackupChannelsBackupPlanBindings::class, 'Google_Service_BackupforGKE_Resource_ProjectsLocationsBackupChannelsBackupPlanBindings');
