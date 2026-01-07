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

use Google\Service\WorkloadManager\ListWorkloadProfilesResponse;
use Google\Service\WorkloadManager\WorkloadProfile;

/**
 * The "workloadProfiles" collection of methods.
 * Typical usage is:
 *  <code>
 *   $workloadmanagerService = new Google\Service\WorkloadManager(...);
 *   $workloadProfiles = $workloadmanagerService->projects_locations_workloadProfiles;
 *  </code>
 */
class ProjectsLocationsWorkloadProfiles extends \Google\Service\Resource
{
  /**
   * Gets details of a single workload. (workloadProfiles.get)
   *
   * @param string $name Required. Name of the resource
   * @param array $optParams Optional parameters.
   * @return WorkloadProfile
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], WorkloadProfile::class);
  }
  /**
   * List workloads (workloadProfiles.listProjectsLocationsWorkloadProfiles)
   *
   * @param string $parent Required. Parent value for ListWorkloadRequest
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListWorkloadProfilesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsWorkloadProfiles($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListWorkloadProfilesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsWorkloadProfiles::class, 'Google_Service_WorkloadManager_Resource_ProjectsLocationsWorkloadProfiles');
