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

use Google\Service\GKEHub\Fleet;
use Google\Service\GKEHub\ListFleetsResponse;
use Google\Service\GKEHub\Operation;

/**
 * The "fleets" collection of methods.
 * Typical usage is:
 *  <code>
 *   $gkehubService = new Google\Service\GKEHub(...);
 *   $fleets = $gkehubService->projects_locations_fleets;
 *  </code>
 */
class ProjectsLocationsFleets extends \Google\Service\Resource
{
  /**
   * Creates a fleet. (fleets.create)
   *
   * @param string $parent Required. The parent (project and location) where the
   * Fleet will be created. Specified in the format `projects/locations`.
   * @param Fleet $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Fleet $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Removes a Fleet. There must be no memberships remaining in the Fleet.
   * (fleets.delete)
   *
   * @param string $name Required. The Fleet resource name in the format
   * `projects/locations/fleets`.
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Returns the details of a fleet. (fleets.get)
   *
   * @param string $name Required. The Fleet resource name in the format
   * `projects/locations/fleets`.
   * @param array $optParams Optional parameters.
   * @return Fleet
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Fleet::class);
  }
  /**
   * Returns all fleets within an organization or a project that the caller has
   * access to. (fleets.listProjectsLocationsFleets)
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
  public function listProjectsLocationsFleets($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListFleetsResponse::class);
  }
  /**
   * Updates a fleet. (fleets.patch)
   *
   * @param string $name Output only. The full, unique resource name of this fleet
   * in the format of `projects/{project}/locations/{location}/fleets/{fleet}`.
   * Each Google Cloud project can have at most one fleet resource, named
   * "default".
   * @param Fleet $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The fields to be updated;
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Fleet $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsFleets::class, 'Google_Service_GKEHub_Resource_ProjectsLocationsFleets');
