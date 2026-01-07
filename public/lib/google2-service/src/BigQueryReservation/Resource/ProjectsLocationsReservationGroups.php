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

namespace Google\Service\BigQueryReservation\Resource;

use Google\Service\BigQueryReservation\BigqueryreservationEmpty;
use Google\Service\BigQueryReservation\ListReservationGroupsResponse;
use Google\Service\BigQueryReservation\ReservationGroup;

/**
 * The "reservationGroups" collection of methods.
 * Typical usage is:
 *  <code>
 *   $bigqueryreservationService = new Google\Service\BigQueryReservation(...);
 *   $reservationGroups = $bigqueryreservationService->projects_locations_reservationGroups;
 *  </code>
 */
class ProjectsLocationsReservationGroups extends \Google\Service\Resource
{
  /**
   * Creates a new reservation group. (reservationGroups.create)
   *
   * @param string $parent Required. Project, location. E.g.,
   * `projects/myproject/locations/US`
   * @param ReservationGroup $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string reservationGroupId Required. The reservation group ID. It
   * must only contain lower case alphanumeric characters or dashes. It must start
   * with a letter and must not end with a dash. Its maximum length is 64
   * characters.
   * @return ReservationGroup
   * @throws \Google\Service\Exception
   */
  public function create($parent, ReservationGroup $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], ReservationGroup::class);
  }
  /**
   * Deletes a reservation. Returns `google.rpc.Code.FAILED_PRECONDITION` when
   * reservation has assignments. (reservationGroups.delete)
   *
   * @param string $name Required. Resource name of the reservation group to
   * retrieve. E.g.,
   * `projects/myproject/locations/US/reservationGroups/team1-prod`
   * @param array $optParams Optional parameters.
   * @return BigqueryreservationEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], BigqueryreservationEmpty::class);
  }
  /**
   * Returns information about the reservation group. (reservationGroups.get)
   *
   * @param string $name Required. Resource name of the reservation group to
   * retrieve. E.g.,
   * `projects/myproject/locations/US/reservationGroups/team1-prod`
   * @param array $optParams Optional parameters.
   * @return ReservationGroup
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ReservationGroup::class);
  }
  /**
   * Lists all the reservation groups for the project in the specified location.
   * (reservationGroups.listProjectsLocationsReservationGroups)
   *
   * @param string $parent Required. The parent resource name containing project
   * and location, e.g.: `projects/myproject/locations/US`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of items to return per page.
   * @opt_param string pageToken The next_page_token value returned from a
   * previous List request, if any.
   * @return ListReservationGroupsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsReservationGroups($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListReservationGroupsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsReservationGroups::class, 'Google_Service_BigQueryReservation_Resource_ProjectsLocationsReservationGroups');
