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

namespace Google\Service\DiscoveryEngine\Resource;

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1ListUserLicensesResponse;

/**
 * The "userLicenses" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $userLicenses = $discoveryengineService->projects_locations_userStores_userLicenses;
 *  </code>
 */
class ProjectsLocationsUserStoresUserLicenses extends \Google\Service\Resource
{
  /**
   * Lists the User Licenses.
   * (userLicenses.listProjectsLocationsUserStoresUserLicenses)
   *
   * @param string $parent Required. The parent UserStore resource name, format:
   * `projects/{project}/locations/{location}/userStores/{user_store_id}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string orderBy Optional. The order in which the UserLicenses are
   * listed. The value must be a comma-separated list of fields. Default sorting
   * order is ascending. To specify descending order for a field, append a " desc"
   * suffix. Redundant space characters in the syntax are insignificant. Supported
   * fields: * `license_assignment_state` * `user_principal` * `user_profile` *
   * `last_login_date` * `update_time` If not set, the default ordering is by
   * `user_principal`. Examples: * `user_principal desc` to order by
   * `user_principal` in descending order. * `license_assignment_state` to order
   * by `license_assignment_state` in ascending order. * `last_login_date desc` to
   * order by `last_login_date` in descending order. * `update_time desc` to order
   * by `update_time` in descending order. * `last_login_date desc,
   * user_principal` to order by `last_login_date` in descending order and then by
   * `user_principal` in ascending order.
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, defaults to 10. The maximum value
   * is 50; values above 50 will be coerced to 50. If this field is negative, an
   * INVALID_ARGUMENT error is returned.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListUserLicenses` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListUserLicenses` must match
   * the call that provided the page token.
   * @return GoogleCloudDiscoveryengineV1ListUserLicensesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsUserStoresUserLicenses($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDiscoveryengineV1ListUserLicensesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsUserStoresUserLicenses::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocationsUserStoresUserLicenses');
