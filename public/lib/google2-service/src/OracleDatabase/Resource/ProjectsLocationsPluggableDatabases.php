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

use Google\Service\OracleDatabase\ListPluggableDatabasesResponse;
use Google\Service\OracleDatabase\PluggableDatabase;

/**
 * The "pluggableDatabases" collection of methods.
 * Typical usage is:
 *  <code>
 *   $oracledatabaseService = new Google\Service\OracleDatabase(...);
 *   $pluggableDatabases = $oracledatabaseService->projects_locations_pluggableDatabases;
 *  </code>
 */
class ProjectsLocationsPluggableDatabases extends \Google\Service\Resource
{
  /**
   * Gets details of a single PluggableDatabase. (pluggableDatabases.get)
   *
   * @param string $name Required. The name of the PluggableDatabase resource in
   * the following format:
   * projects/{project}/locations/{region}/pluggableDatabases/{pluggable_database}
   * @param array $optParams Optional parameters.
   * @return PluggableDatabase
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], PluggableDatabase::class);
  }
  /**
   * Lists all the PluggableDatabases for the given project, location and
   * Container Database.
   * (pluggableDatabases.listProjectsLocationsPluggableDatabases)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * PluggableDatabases. Format: projects/{project}/locations/{location}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression for filtering the results of
   * the request. List for pluggable databases is supported only with a valid
   * container database (full resource name) filter in this format:
   * `database="projects/{project}/locations/{location}/databases/{database}"`
   * @opt_param int pageSize Optional. The maximum number of PluggableDatabases to
   * return. The service may return fewer than this value.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListPluggableDatabases` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListPluggableDatabases`
   * must match the call that provided the page token.
   * @return ListPluggableDatabasesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsPluggableDatabases($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListPluggableDatabasesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsPluggableDatabases::class, 'Google_Service_OracleDatabase_Resource_ProjectsLocationsPluggableDatabases');
