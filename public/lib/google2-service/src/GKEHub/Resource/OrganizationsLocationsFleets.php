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

namespace Google\Service\GKEHub\Resource;

use Google\Service\GKEHub\ListFleetsResponse;

/**
 * The "fleets" collection of methods.
 * Typical usage is:
 *  <code>
 *   $gkehubService = new Google\Service\GKEHub(...);
 *   $fleets = $gkehubService->organizations_locations_fleets;
 *  </code>
 */
class OrganizationsLocationsFleets extends \Google\Service\Resource
{
  /**
   * Returns all fleets within an organization or a project that the caller has
   * access to. (fleets.listOrganizationsLocationsFleets)
   *
   * @param string $parent Required. The organization or project to list for
   * Fleets under, in the format `organizations/locations` or
   * `projects/locations`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of fleets to return. The
   * service may return fewer than this value. If unspecified, at most 200 fleets
   * will be returned. The maximum value is 1000; values above 1000 will be
   * coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListFleets` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListFleets` must match the call
   * that provided the page token.
   * @return ListFleetsResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsLocationsFleets($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListFleetsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsLocationsFleets::class, 'Google_Service_GKEHub_Resource_OrganizationsLocationsFleets');
