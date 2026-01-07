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

use Google\Service\OracleDatabase\ListDbVersionsResponse;

/**
 * The "dbVersions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $oracledatabaseService = new Google\Service\OracleDatabase(...);
 *   $dbVersions = $oracledatabaseService->projects_locations_dbVersions;
 *  </code>
 */
class ProjectsLocationsDbVersions extends \Google\Service\Resource
{
  /**
   * List DbVersions for the given project and location.
   * (dbVersions.listProjectsLocationsDbVersions)
   *
   * @param string $parent Required. The parent value for the DbVersion resource
   * with the format: projects/{project}/locations/{location}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filter expression that matches a subset of
   * the DbVersions to show. The supported filter for dbSystem creation is
   * `db_system_shape = {db_system_shape} AND storage_management =
   * {storage_management}`. If no filter is provided, all DbVersions will be
   * returned.
   * @opt_param int pageSize Optional. The maximum number of items to return. If
   * unspecified, a maximum of 50 DbVersions will be returned. The maximum value
   * is 1000; values above 1000 will be reset to 1000.
   * @opt_param string pageToken Optional. A token identifying the requested page
   * of results to return. All fields except the filter should remain the same as
   * in the request that provided this page token.
   * @return ListDbVersionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDbVersions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListDbVersionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDbVersions::class, 'Google_Service_OracleDatabase_Resource_ProjectsLocationsDbVersions');
