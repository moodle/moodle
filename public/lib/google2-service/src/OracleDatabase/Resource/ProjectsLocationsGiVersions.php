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

namespace Google\Service\OracleDatabase\Resource;

use Google\Service\OracleDatabase\ListGiVersionsResponse;

/**
 * The "giVersions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $oracledatabaseService = new Google\Service\OracleDatabase(...);
 *   $giVersions = $oracledatabaseService->projects_locations_giVersions;
 *  </code>
 */
class ProjectsLocationsGiVersions extends \Google\Service\Resource
{
  /**
   * Lists all the valid Oracle Grid Infrastructure (GI) versions for the given
   * project and location. (giVersions.listProjectsLocationsGiVersions)
   *
   * @param string $parent Required. The parent value for Grid Infrastructure
   * Version in the following format: Format:
   * projects/{project}/locations/{location}.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression for filtering the results of
   * the request. Only the shape, gcp_oracle_zone and gi_version fields are
   * supported in this format: `shape="{shape}"`.
   * @opt_param int pageSize Optional. The maximum number of items to return. If
   * unspecified, a maximum of 50 Oracle Grid Infrastructure (GI) versions will be
   * returned. The maximum value is 1000; values above 1000 will be reset to 1000.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListGiVersionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsGiVersions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListGiVersionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsGiVersions::class, 'Google_Service_OracleDatabase_Resource_ProjectsLocationsGiVersions');
