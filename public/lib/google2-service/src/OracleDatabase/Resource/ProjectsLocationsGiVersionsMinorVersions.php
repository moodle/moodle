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

use Google\Service\OracleDatabase\ListMinorVersionsResponse;

/**
 * The "minorVersions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $oracledatabaseService = new Google\Service\OracleDatabase(...);
 *   $minorVersions = $oracledatabaseService->projects_locations_giVersions_minorVersions;
 *  </code>
 */
class ProjectsLocationsGiVersionsMinorVersions extends \Google\Service\Resource
{
  /**
   * Lists all the valid minor versions for the given project, location, gi
   * version and shape family.
   * (minorVersions.listProjectsLocationsGiVersionsMinorVersions)
   *
   * @param string $parent Required. The parent value for the MinorVersion
   * resource with the format:
   * projects/{project}/locations/{location}/giVersions/{gi_version}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression for filtering the results of
   * the request. Only shapeFamily and gcp_oracle_zone_id are supported in this
   * format: `shape_family="{shapeFamily}" AND
   * gcp_oracle_zone_id="{gcp_oracle_zone_id}"`.
   * @opt_param int pageSize Optional. The maximum number of items to return. If
   * unspecified, a maximum of 50 System Versions will be returned. The maximum
   * value is 1000; values above 1000 will be reset to 1000.
   * @opt_param string pageToken Optional. A token identifying the requested page
   * of results to return. All fields except the filter should remain the same as
   * in the request that provided this page token.
   * @return ListMinorVersionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsGiVersionsMinorVersions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListMinorVersionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsGiVersionsMinorVersions::class, 'Google_Service_OracleDatabase_Resource_ProjectsLocationsGiVersionsMinorVersions');
