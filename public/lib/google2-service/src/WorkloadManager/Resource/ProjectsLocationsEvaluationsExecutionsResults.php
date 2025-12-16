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

use Google\Service\WorkloadManager\ListExecutionResultsResponse;

/**
 * The "results" collection of methods.
 * Typical usage is:
 *  <code>
 *   $workloadmanagerService = new Google\Service\WorkloadManager(...);
 *   $results = $workloadmanagerService->projects_locations_evaluations_executions_results;
 *  </code>
 */
class ProjectsLocationsEvaluationsExecutionsResults extends \Google\Service\Resource
{
  /**
   * Lists the result of a single evaluation.
   * (results.listProjectsLocationsEvaluationsExecutionsResults)
   *
   * @param string $parent Required. The execution results. Format:
   * {parent}/evaluations/executions/results
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Filtering results
   * @opt_param int pageSize Requested page size. Server may return fewer items
   * than requested. If unspecified, server will pick an appropriate default.
   * @opt_param string pageToken A token identifying a page of results the server
   * should return.
   * @return ListExecutionResultsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsEvaluationsExecutionsResults($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListExecutionResultsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsEvaluationsExecutionsResults::class, 'Google_Service_WorkloadManager_Resource_ProjectsLocationsEvaluationsExecutionsResults');
