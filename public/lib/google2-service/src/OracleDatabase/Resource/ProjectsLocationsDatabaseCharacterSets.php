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

use Google\Service\OracleDatabase\ListDatabaseCharacterSetsResponse;

/**
 * The "databaseCharacterSets" collection of methods.
 * Typical usage is:
 *  <code>
 *   $oracledatabaseService = new Google\Service\OracleDatabase(...);
 *   $databaseCharacterSets = $oracledatabaseService->projects_locations_databaseCharacterSets;
 *  </code>
 */
class ProjectsLocationsDatabaseCharacterSets extends \Google\Service\Resource
{
  /**
   * List DatabaseCharacterSets for the given project and location.
   * (databaseCharacterSets.listProjectsLocationsDatabaseCharacterSets)
   *
   * @param string $parent Required. The parent value for DatabaseCharacterSets in
   * the following format: projects/{project}/locations/{location}.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression for filtering the results of
   * the request. Only the **character_set_type** field is supported in the
   * following format: `character_set_type="{characterSetType}"`. Accepted values
   * include `DATABASE` and `NATIONAL`.
   * @opt_param int pageSize Optional. The maximum number of DatabaseCharacterSets
   * to return. The service may return fewer than this value. If unspecified, at
   * most 50 DatabaseCharacterSets will be returned. The maximum value is 1000;
   * values above 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListDatabaseCharacterSets` call. Provide this to retrieve the subsequent
   * page. When paginating, all other parameters provided to
   * `ListDatabaseCharacterSets` must match the call that provided the page token.
   * @return ListDatabaseCharacterSetsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDatabaseCharacterSets($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListDatabaseCharacterSetsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDatabaseCharacterSets::class, 'Google_Service_OracleDatabase_Resource_ProjectsLocationsDatabaseCharacterSets');
