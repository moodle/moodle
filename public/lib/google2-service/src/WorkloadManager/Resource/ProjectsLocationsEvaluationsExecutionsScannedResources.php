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

use Google\Service\WorkloadManager\ListScannedResourcesResponse;

/**
 * The "scannedResources" collection of methods.
 * Typical usage is:
 *  <code>
 *   $workloadmanagerService = new Google\Service\WorkloadManager(...);
 *   $scannedResources = $workloadmanagerService->projects_locations_evaluations_executions_scannedResources;
 *  </code>
 */
class ProjectsLocationsEvaluationsExecutionsScannedResources extends \Google\Service\Resource
{
  /**
   * List all scanned resources for a single Execution.
   * (scannedResources.listProjectsLocationsEvaluationsExecutionsScannedResources)
   *
   * @param string $parent Required. parent for ListScannedResourcesRequest
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Filtering results
   * @opt_param string orderBy Field to sort by. See
   * https://google.aip.dev/132#ordering for more details.
   * @opt_param int pageSize Requested page size. Server may return fewer items
   * than requested. If unspecified, server will pick an appropriate default.
   * @opt_param string pageToken A token identifying a page of results the server
   * should return.
   * @opt_param string rule rule name
   * @return ListScannedResourcesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsEvaluationsExecutionsScannedResources($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListScannedResourcesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsEvaluationsExecutionsScannedResources::class, 'Google_Service_WorkloadManager_Resource_ProjectsLocationsEvaluationsExecutionsScannedResources');
