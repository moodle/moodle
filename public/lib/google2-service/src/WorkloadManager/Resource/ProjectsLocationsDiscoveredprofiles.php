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

namespace Google\Service\WorkloadManager\Resource;

use Google\Service\WorkloadManager\ListDiscoveredProfilesResponse;

/**
 * The "discoveredprofiles" collection of methods.
 * Typical usage is:
 *  <code>
 *   $workloadmanagerService = new Google\Service\WorkloadManager(...);
 *   $discoveredprofiles = $workloadmanagerService->projects_locations_discoveredprofiles;
 *  </code>
 */
class ProjectsLocationsDiscoveredprofiles extends \Google\Service\Resource
{
  /**
   * List discovered workload profiles
   * (discoveredprofiles.listProjectsLocationsDiscoveredprofiles)
   *
   * @param string $parent Required. Parent value for
   * ListDiscoveredProfilesRequest
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListDiscoveredProfilesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDiscoveredprofiles($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListDiscoveredProfilesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDiscoveredprofiles::class, 'Google_Service_WorkloadManager_Resource_ProjectsLocationsDiscoveredprofiles');
