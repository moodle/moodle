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

namespace Google\Service\Logging\Resource;

use Google\Service\Logging\ListRecentQueriesResponse;

/**
 * The "recentQueries" collection of methods.
 * Typical usage is:
 *  <code>
 *   $loggingService = new Google\Service\Logging(...);
 *   $recentQueries = $loggingService->projects_locations_recentQueries;
 *  </code>
 */
class ProjectsLocationsRecentQueries extends \Google\Service\Resource
{
  /**
   * Lists the RecentQueries that were created by the user making the request.
   * (recentQueries.listProjectsLocationsRecentQueries)
   *
   * @param string $parent Required. The resource to which the listed queries
   * belong. "projects/[PROJECT_ID]/locations/[LOCATION_ID]"
   * "organizations/[ORGANIZATION_ID]/locations/[LOCATION_ID]"
   * "billingAccounts/[BILLING_ACCOUNT_ID]/locations/[LOCATION_ID]"
   * "folders/[FOLDER_ID]/locations/[LOCATION_ID]" For example:projects/my-
   * project/locations/us-central1Note: The location portion of the resource must
   * be specified, but supplying the character - in place of LOCATION_ID will
   * return all recent queries.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Specifies the type ("Logging" or
   * "OpsAnalytics") of the recent queries to list. The only valid value for this
   * field is one of the two allowable type function calls, which are the
   * following: type("Logging") type("OpsAnalytics")
   * @opt_param int pageSize Optional. The maximum number of results to return
   * from this request. Non-positive values are ignored. The presence of
   * nextPageToken in the response indicates that more results might be available.
   * @opt_param string pageToken Optional. If present, then retrieve the next
   * batch of results from the preceding call to this method. pageToken must be
   * the value of nextPageToken from the previous response. The values of other
   * method parameters should be identical to those in the previous call.
   * @return ListRecentQueriesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsRecentQueries($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListRecentQueriesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsRecentQueries::class, 'Google_Service_Logging_Resource_ProjectsLocationsRecentQueries');
