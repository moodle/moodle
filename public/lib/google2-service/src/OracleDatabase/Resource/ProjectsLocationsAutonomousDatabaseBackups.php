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

use Google\Service\OracleDatabase\ListAutonomousDatabaseBackupsResponse;

/**
 * The "autonomousDatabaseBackups" collection of methods.
 * Typical usage is:
 *  <code>
 *   $oracledatabaseService = new Google\Service\OracleDatabase(...);
 *   $autonomousDatabaseBackups = $oracledatabaseService->projects_locations_autonomousDatabaseBackups;
 *  </code>
 */
class ProjectsLocationsAutonomousDatabaseBackups extends \Google\Service\Resource
{
  /**
   * Lists the long-term and automatic backups of an Autonomous Database.
   * (autonomousDatabaseBackups.listProjectsLocationsAutonomousDatabaseBackups)
   *
   * @param string $parent Required. The parent value for
   * ListAutonomousDatabaseBackups in the following format:
   * projects/{project}/locations/{location}.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression for filtering the results of
   * the request. Only the **autonomous_database_id** field is supported in the
   * following format: `autonomous_database_id="{autonomous_database_id}"`. The
   * accepted values must be a valid Autonomous Database ID, limited to the naming
   * restrictions of the ID: ^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$). The ID must start
   * with a letter, end with a letter or a number, and be a maximum of 63
   * characters.
   * @opt_param int pageSize Optional. The maximum number of items to return. If
   * unspecified, at most 50 Autonomous DB Backups will be returned. The maximum
   * value is 1000; values above 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListAutonomousDatabaseBackupsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsAutonomousDatabaseBackups($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAutonomousDatabaseBackupsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsAutonomousDatabaseBackups::class, 'Google_Service_OracleDatabase_Resource_ProjectsLocationsAutonomousDatabaseBackups');
