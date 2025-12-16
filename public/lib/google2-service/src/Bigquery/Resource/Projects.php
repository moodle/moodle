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

namespace Google\Service\Bigquery\Resource;

use Google\Service\Bigquery\GetServiceAccountResponse;
use Google\Service\Bigquery\ProjectList;

/**
 * The "projects" collection of methods.
 * Typical usage is:
 *  <code>
 *   $bigqueryService = new Google\Service\Bigquery(...);
 *   $projects = $bigqueryService->projects;
 *  </code>
 */
class Projects extends \Google\Service\Resource
{
  /**
   * RPC to get the service account for a project used for interactions with
   * Google Cloud KMS (projects.getServiceAccount)
   *
   * @param string $projectId Required. ID of the project.
   * @param array $optParams Optional parameters.
   * @return GetServiceAccountResponse
   * @throws \Google\Service\Exception
   */
  public function getServiceAccount($projectId, $optParams = [])
  {
    $params = ['projectId' => $projectId];
    $params = array_merge($params, $optParams);
    return $this->call('getServiceAccount', [$params], GetServiceAccountResponse::class);
  }
  /**
   * RPC to list projects to which the user has been granted any project role.
   * Users of this method are encouraged to consider the [Resource
   * Manager](https://cloud.google.com/resource-manager/docs/) API, which provides
   * the underlying data for this method and has more capabilities.
   * (projects.listProjects)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string maxResults `maxResults` unset returns all results, up to 50
   * per page. Additionally, the number of projects in a page may be fewer than
   * `maxResults` because projects are retrieved and then filtered to only
   * projects with the BigQuery API enabled.
   * @opt_param string pageToken Page token, returned by a previous call, to
   * request the next page of results. If not present, no further pages are
   * present.
   * @return ProjectList
   * @throws \Google\Service\Exception
   */
  public function listProjects($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ProjectList::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Projects::class, 'Google_Service_Bigquery_Resource_Projects');
