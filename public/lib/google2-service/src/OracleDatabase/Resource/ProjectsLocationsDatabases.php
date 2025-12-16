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

use Google\Service\OracleDatabase\Database;
use Google\Service\OracleDatabase\ListDatabasesResponse;

/**
 * The "databases" collection of methods.
 * Typical usage is:
 *  <code>
 *   $oracledatabaseService = new Google\Service\OracleDatabase(...);
 *   $databases = $oracledatabaseService->projects_locations_databases;
 *  </code>
 */
class ProjectsLocationsDatabases extends \Google\Service\Resource
{
  /**
   * Gets details of a single Database. (databases.get)
   *
   * @param string $name Required. The name of the Database resource in the
   * following format: projects/{project}/locations/{region}/databases/{database}
   * @param array $optParams Optional parameters.
   * @return Database
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Database::class);
  }
  /**
   * Lists all the Databases for the given project, location and DbSystem.
   * (databases.listProjectsLocationsDatabases)
   *
   * @param string $parent Required. The parent resource name in the following
   * format: projects/{project}/locations/{region}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression for filtering the results of
   * the request. list for container databases is supported only with a valid
   * dbSystem (full resource name) filter in this format:
   * `dbSystem="projects/{project}/locations/{location}/dbSystems/{dbSystemId}"`
   * @opt_param int pageSize Optional. The maximum number of items to return. If
   * unspecified, a maximum of 50 Databases will be returned. The maximum value is
   * 1000; values above 1000 will be reset to 1000.
   * @opt_param string pageToken Optional. A token identifying the requested page
   * of results to return. All fields except the filter should remain the same as
   * in the request that provided this page token.
   * @return ListDatabasesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDatabases($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListDatabasesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDatabases::class, 'Google_Service_OracleDatabase_Resource_ProjectsLocationsDatabases');
