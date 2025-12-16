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

namespace Google\Service\CloudComposer\Resource;

use Google\Service\CloudComposer\ListWorkloadsResponse;

/**
 * The "workloads" collection of methods.
 * Typical usage is:
 *  <code>
 *   $composerService = new Google\Service\CloudComposer(...);
 *   $workloads = $composerService->projects_locations_environments_workloads;
 *  </code>
 */
class ProjectsLocationsEnvironmentsWorkloads extends \Google\Service\Resource
{
  /**
   * Lists workloads in a Cloud Composer environment. Workload is a unit that runs
   * a single Composer component. This method is supported for Cloud Composer
   * environments in versions composer-2.*.*-airflow-*.*.* and newer.
   * (workloads.listProjectsLocationsEnvironmentsWorkloads)
   *
   * @param string $parent Required. The environment name to get workloads for, in
   * the form:
   * "projects/{projectId}/locations/{locationId}/environments/{environmentId}"
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. The list filter. Currently only supports
   * equality on the type field. The value of a field specified in the filter
   * expression must be one ComposerWorkloadType enum option. It's possible to get
   * multiple types using "OR" operator, e.g.: "type=SCHEDULER OR
   * type=CELERY_WORKER". If not specified, all items are returned.
   * @opt_param int pageSize Optional. The maximum number of environments to
   * return.
   * @opt_param string pageToken Optional. The next_page_token value returned from
   * a previous List request, if any.
   * @return ListWorkloadsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsEnvironmentsWorkloads($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListWorkloadsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsEnvironmentsWorkloads::class, 'Google_Service_CloudComposer_Resource_ProjectsLocationsEnvironmentsWorkloads');
